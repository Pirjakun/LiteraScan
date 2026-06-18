#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// --- Konfigurasi WiFi ---
const char* ssid     = "IT2.4G";
const char* password = "KosongDelapan12";

// --- Konfigurasi Server API Laravel ---
// Ganti IP di bawah ini dengan IP laptop/server Laravel Anda jika berubah
const char* serverUrl = "https://r4v4na-pusakap-dashboard.hf.space/api/rfid/tap";

// --- Konfigurasi Pin RFID ---
#define SDA_PIN  5
#define SCK_PIN  4
#define MOSI_PIN 2
#define MISO_PIN 15 
#define RST_PIN  18

// --- Konfigurasi Pin Aktuator ---
#define BUZZER_PIN 23
#define LED_PIN    22

// --- Konfigurasi Pin OLED (I2C) ---
#define OLED_SDA 21
#define OLED_SCL 19

// --- Konfigurasi Resolusi OLED ---
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64

// Membuat objek MFRC522 (RFID)
MFRC522 rfid(SDA_PIN, RST_PIN); 

// Membuat objek OLED
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

// Status tampilan untuk menghindari flickering
bool isStandby = false;

// Sesi lokal untuk mencegah lag HTTPS pinger
unsigned long sessionStartTime = 0;
bool isSessionActive = false;

// Objek HTTP Client Global untuk Keep-Alive (re-use SSL session)
WiFiClientSecure clientSecure;
WiFiClient clientPlain;
HTTPClient http;
bool isHttpInitialized = false;

void setup() {
  Serial.begin(115200);
  delay(500); 
  
  // Matikan Watchdog Loop agar ESP32 tidak reset saat melakukan handshake SSL (HTTPS) yang lambat
  disableLoopWDT();
  
  // 1. Inisialisasi OLED terlebih dahulu agar status terlihat sejak awal
  Wire.begin(OLED_SDA, OLED_SCL);
  if(!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println("Gagal menemukan layar OLED!");
    for(;;);
  }

  // Tampilkan pesan awal bootup
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 10);
  display.println("PuSaKap");
  display.setCursor(0, 30);
  display.println("Memulai perangkat...");
  display.display();
  delay(1000);

  // 2. Inisialisasi Aktuator
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_PIN, LOW);
  
  // 3. Inisialisasi RFID
  SPI.begin(SCK_PIN, MISO_PIN, MOSI_PIN, SDA_PIN); 
  rfid.PCD_Init(); 

  // 4. Menghubungkan ke Jaringan WiFi
  display.clearDisplay();
  display.setCursor(0, 20);
  display.println("Menghubungkan WiFi...");
  display.display();

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  Serial.println("\nWiFi Terhubung!");

  tampilanAwal();
}

void loop() {
  // Selalu cek koneksi WiFi
  if (WiFi.status() != WL_CONNECTED) return;

  // Timeout sesi lokal (60 detik) untuk menghindari pinger HTTPS yang lambat
  if (isSessionActive && (millis() - sessionStartTime > 60000)) {
    isSessionActive = false;
    tampilanAwal();
  }

  // Cek keberadaan kartu baru
  if ( ! rfid.PICC_IsNewCardPresent()) return;
  if ( ! rfid.PICC_ReadCardSerial()) return;

  // Membaca UID dengan format spasi per 2 karakter HEX (A2 42 E2 2E)
  String content = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
     content.concat(String(rfid.uid.uidByte[i] < 0x10 ? " 0" : " "));
     content.concat(String(rfid.uid.uidByte[i], HEX));
  }
  content.toUpperCase();
  String uidKartu = content.substring(1);
  
  Serial.println("\nUID Terdeteksi: " + uidKartu);

  // Kirim data ke API Laravel
  kirimDataKeServer(uidKartu);

  // Reset reader dan antenna agar bisa membaca kartu yang sama kembali
  resetRFIDReader();
}

void kirimDataKeServer(String uid) {
  if (!isHttpInitialized) {
    if (String(serverUrl).startsWith("https")) {
      clientSecure.setInsecure(); // Abaikan verifikasi SSL agar mudah
      // Batasi ukuran buffer TLS untuk stabilitas memori
      clientSecure.setBufferSizes(2048, 1024);
      http.begin(clientSecure, serverUrl);
    } else {
      http.begin(clientPlain, serverUrl);
    }
    http.setTimeout(10000); // Timeout 10 detik agar Laravel sempat memproses Firebase tanpa putus
    isHttpInitialized = true;
  }

  // Set header secara dinamis per request
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Connection", "keep-alive");

  StaticJsonDocument<200> doc;
  doc["uid"] = uid;
  String jsonPayload;
  serializeJson(doc, jsonPayload);

  int httpResponseCode = http.POST(jsonPayload);
  prosesResponsServer(httpResponseCode, &http);
  
  // Jika koneksi error/gagal, paksa reset/tutup client agar melakukan handshake ulang di tap berikutnya
  if (httpResponseCode < 0) {
    http.end();
    isHttpInitialized = false;
  }
}

void cekStatusSesiServer() {
  HTTPClient http;
  String statusUrl = String(serverUrl);
  statusUrl.replace("/rfid/tap", "/session-status");
  
  WiFiClientSecure clientSecure;
  WiFiClient clientPlain;
  
  if (statusUrl.startsWith("https")) {
    clientSecure.setInsecure(); // Abaikan verifikasi SSL agar mudah
    http.begin(clientSecure, statusUrl);
  } else {
    http.begin(clientPlain, statusUrl);
  }

  http.setTimeout(1500); // Timeout 1.5 detik untuk session status check
  
  int httpResponseCode = http.GET();
  if (httpResponseCode == 200) {
    String responseBody = http.getString();
    StaticJsonDocument<200> resDoc;
    deserializeJson(resDoc, responseBody);
    
    // Jika server merespons bahwa sesi di cache sudah timeout, reset OLED ke standby
    if (resDoc["command"] == "RESET_STANDBY") {
      if (!isStandby) {
        tampilanAwal();
      }
    }
  }
  http.end();

  // Re-inisialisasi reader jika SPI terganggu oleh aktifitas Wi-Fi
  rfid.PCD_Init();
}

void prosesResponsServer(int responseCode, HTTPClient* http) {
  display.clearDisplay();
  display.setTextColor(SSD1306_WHITE);
  display.setTextSize(1);
  isStandby = false;

  if (responseCode == 200) {
    String responseBody = http->getString();
    StaticJsonDocument<300> resDoc;
    deserializeJson(resDoc, responseBody);

    String status = resDoc["status"];
    String line1 = resDoc["line1"];
    String line2 = resDoc["line2"];
    String line3 = resDoc["line3"];

    if (status == "session_active") {
      // Sesi Anggota Terbuka
      isSessionActive = true;
      sessionStartTime = millis();
      
      display.setCursor(0, 10);
      display.println(line1);
      display.setCursor(0, 30);
      display.println(line2);
      display.setCursor(0, 45);
      display.println("yang ingin dipinjam.");
      display.display();

      // Beep Sukses Pendek
      digitalWrite(BUZZER_PIN, HIGH);
      digitalWrite(LED_PIN, HIGH);
      delay(200);
      digitalWrite(BUZZER_PIN, LOW);
      digitalWrite(LED_PIN, LOW);

    } else if (status == "session_closed") {
      // Sesi ditutup manual
      isSessionActive = false;
      
      display.setCursor(0, 20);
      display.println(line1);
      display.setCursor(0, 40);
      display.println(line2);
      display.display();

      // Beep Dua Kali Pendek
      for (int i = 0; i < 2; i++) {
        digitalWrite(BUZZER_PIN, HIGH);
        digitalWrite(LED_PIN, HIGH);
        delay(100);
        digitalWrite(BUZZER_PIN, LOW);
        digitalWrite(LED_PIN, LOW);
        delay(100);
      }
      delay(1500);
      tampilanAwal();

    } else if (status == "success") {
      // Transaksi Peminjaman / Pengembalian Sukses
      isSessionActive = false;
      
      display.setCursor(0, 5);
      display.println(line1);
      display.setCursor(0, 25);
      display.println(line2);
      if (line3 != "null" && line3 != "") {
        display.setCursor(0, 45);
        display.println(line3);
      }
      display.display();

      // Beep Sukses Panjang
      digitalWrite(BUZZER_PIN, HIGH);
      digitalWrite(LED_PIN, HIGH);
      delay(800);
      digitalWrite(BUZZER_PIN, LOW);
      digitalWrite(LED_PIN, LOW);
      
      delay(3000); // Tahan layar 3 detik agar pengguna bisa membaca
      tampilanAwal();

    } else {
      // Gagal / Error
      isSessionActive = false;
      
      display.setCursor(0, 20);
      display.println(line1);
      display.setCursor(0, 40);
      display.println(line2);
      display.display();

      bunyiGagal();
      delay(1500);
      tampilanAwal();
    }
  } else {
    // Jika gagal terhubung ke server Laravel (Server mati / IP salah / Timeout)
    isSessionActive = false;
    
    display.setCursor(0, 25);
    display.println("KONEKSI ERROR");
    display.display();
    
    bunyiGagal();
    delay(1500);
    tampilanAwal();
  }
}

void tampilanAwal() {
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 10);
  display.println("PuSaKap");
  display.setCursor(0, 35);
  display.println("Silakan Tap Kartu");
  display.setCursor(0, 45);
  display.println("Keanggotaan Anda...");
  display.display(); 
  isStandby = true;
}

void bunyiGagal() {
  for (int i = 0; i < 3; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    digitalWrite(LED_PIN, HIGH);
    delay(150);
    digitalWrite(BUZZER_PIN, LOW);
    digitalWrite(LED_PIN, LOW);
    delay(100); 
  }
}

void resetRFIDReader() {
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  rfid.PCD_AntennaOff();
  delay(150); // Delay 150ms untuk mematikan tag RFID sepenuhnya
  rfid.PCD_AntennaOn();
  rfid.PCD_Init();
}
