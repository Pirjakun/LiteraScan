@extends('layouts.app')

@section('title', ($student->exists ? 'Edit Siswa' : 'Tambah Siswa') . ' - LiteraScan')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('students.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-600 mb-4 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke daftar siswa
    </a>

    <div class="card rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute -top-12 -right-10 w-36 h-36 bg-grape-soft rounded-full blur-2xl pointer-events-none"></div>

        <div class="mb-8 relative flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-grape-soft text-grape-deep flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div>
                <h2 class="font-display text-xl font-bold text-slate-800">
                    {{ $student->exists ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}
                </h2>
                <p class="text-xs text-slate-400">
                    {{ $student->exists ? 'Perbarui identitas siswa dan kartu RFID' : 'Masukkan identitas siswa dan kaitkan kartu RFID' }}
                </p>
            </div>
        </div>

        <form action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" method="POST" class="flex flex-col gap-5 relative">
            @csrf
            @if($student->exists)
                @method('PUT')
            @endif

            <div class="flex flex-col gap-2">
                <label for="name" class="text-xs font-bold text-slate-600">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" required placeholder="Contoh: Ayu Lestari"
                       class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-grape-mid focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-2">
                    <label for="nim" class="text-xs font-bold text-slate-600">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nim" id="nim" value="{{ old('nim', $student->nim) }}" required placeholder="Contoh: 10303"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm font-mono focus:outline-none focus:border-grape-mid focus:bg-white transition-all">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="major" class="text-xs font-bold text-slate-600">Kelas</label>
                    <input type="text" name="major" id="major" value="{{ old('major', $student->major) }}" required placeholder="Contoh: 3A"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-grape-mid focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="rfid_uid" class="text-xs font-bold text-slate-600">RFID UID Kartu</label>
                <div class="relative flex gap-2">
                    <input type="text" name="rfid_uid" id="rfid_uid" value="{{ old('rfid_uid', $student->rfid_uid) }}" required placeholder="Contoh: A2 42 E2 2E"
                           class="flex-grow px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm font-mono focus:outline-none focus:border-grape-mid focus:bg-white transition-all">
                    <button type="button" id="btn-scan-rfid" class="px-4 py-3 rounded-2xl border border-grape-mid bg-grape-soft text-grape-deep hover:bg-grape-mid hover:text-white transition-all text-xs font-bold flex items-center gap-1.5 shrink-0">
                        <span class="scan-icon relative flex h-2 w-2 hidden">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-grape-deep opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-grape-deep"></span>
                        </span>
                        <span class="btn-text">Pindai Otomatis</span>
                    </button>
                </div>
                <span id="scan-status" class="text-[11px] text-slate-400">Tempelkan kartu anggota di alat pemindai untuk mendapat nomor ini.</span>
            </div>

            <div class="flex items-center justify-end gap-3 mt-3 pt-5 border-t border-slate-100">
                <a href="{{ route('students.index') }}" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-500 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-grape-deep to-sky-deep hover:opacity-90 text-white rounded-full text-sm font-bold transition-all shadow-lg shadow-grape-mid/40">
                    {{ $student->exists ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    const btnScan = document.getElementById('btn-scan-rfid');
    const inputRfid = document.getElementById('rfid_uid');
    const scanStatus = document.getElementById('scan-status');
    if (!btnScan || !inputRfid || !scanStatus) return;

    const scanIcon = btnScan.querySelector('.scan-icon');
    const btnText = btnScan.querySelector('.btn-text');
    
    let pollingInterval = null;

    btnScan.addEventListener('click', () => {
        if (pollingInterval) {
            stopPolling();
        } else {
            startPolling();
        }
    });

    function startPolling() {
        btnScan.classList.remove('bg-grape-soft', 'text-grape-deep', 'border-grape-mid');
        btnScan.classList.add('bg-rose-500', 'text-white', 'border-rose-500');
        scanIcon.classList.remove('hidden');
        btnText.textContent = 'Batal';
        scanStatus.textContent = 'Menghubungkan ke pemindai...';
        scanStatus.className = 'text-[11px] text-grape-deep font-semibold animate-pulse';

        // Clear existing scan on server first
        fetch('/api/clear-scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(() => {
            // Check if user clicked 'Batal' during the network call
            if (!btnScan.classList.contains('bg-rose-500')) return;

            scanStatus.textContent = 'Mendengarkan alat pemindai... Silakan tap kartu Anda pada pembaca RFID.';

            pollingInterval = setInterval(() => {
                fetch('/api/last-scan')
                .then(res => res.json())
                .then(data => {
                    if (data.uid) {
                        inputRfid.value = data.uid;
                        inputRfid.dispatchEvent(new Event('input', { bubbles: true }));
                        
                        stopPolling();
                        scanStatus.textContent = 'Kartu berhasil dibaca dan dimasukkan!';
                        scanStatus.className = 'text-[11px] text-mint-deep font-bold';
                        
                        fetch('/api/clear-scan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                    }
                })
                .catch(err => console.error('Error polling RFID:', err));
            }, 1000);
        })
        .catch(err => {
            console.error('Failed to clear scan cache:', err);
            stopPolling();
            scanStatus.textContent = 'Gagal terhubung ke pemindai. Silakan coba lagi.';
            scanStatus.className = 'text-[11px] text-rose-500 font-bold';
        });
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
        btnScan.classList.remove('bg-rose-500', 'text-white', 'border-rose-500');
        btnScan.classList.add('bg-grape-soft', 'text-grape-deep', 'border-grape-mid');
        scanIcon.classList.add('hidden');
        btnText.textContent = 'Pindai Otomatis';
        scanStatus.textContent = 'Tempelkan kartu anggota di alat pemindai untuk mendapat nomor ini.';
        scanStatus.className = 'text-[11px] text-slate-400';
    }

    // Clean up interval when page changes (due to SPA navigation)
    const observer = new MutationObserver((mutations, obs) => {
        if (!document.body.contains(btnScan)) {
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
            obs.disconnect();
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();
</script>
@endsection
