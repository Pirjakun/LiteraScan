@extends('layouts.app')

@section('title', ($book->exists ? 'Edit Buku' : 'Tambah Buku') . ' - LiteraScan')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-600 mb-4 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke daftar buku
    </a>

    <div class="card rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute -top-12 -right-10 w-36 h-36 bg-peach-soft rounded-full blur-2xl pointer-events-none"></div>

        <div class="mb-8 relative flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-peach-soft text-peach-deep flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13"></path></svg>
            </div>
            <div>
                <h2 class="font-display text-xl font-bold text-slate-800">
                    {{ $book->exists ? 'Edit Data Buku' : 'Tambah Buku Baru' }}
                </h2>
                <p class="text-xs text-slate-400">
                    {{ $book->exists ? 'Perbarui judul, pengarang, dan label RFID buku' : 'Masukkan data buku baru dan kaitkan nomor tag RFID' }}
                </p>
            </div>
        </div>

        <form action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" method="POST" class="flex flex-col gap-5 relative">
            @csrf
            @if($book->exists)
                @method('PUT')
            @endif

            <div class="flex flex-col gap-2">
                <label for="title" class="text-xs font-bold text-slate-600">Judul Buku</label>
                <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}" required placeholder="Contoh: Dongeng Si Kancil"
                       class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-peach-mid focus:bg-white transition-all">
            </div>

            <div class="flex flex-col gap-2">
                <label for="author" class="text-xs font-bold text-slate-600">Pengarang / Penulis</label>
                <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}" required placeholder="Contoh: Penerbit Cerdas"
                       class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-peach-mid focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-1 @if($book->exists) md:grid-cols-2 @endif gap-5">
                <div class="flex flex-col gap-2">
                    <label for="rfid_uid" class="text-xs font-bold text-slate-600">RFID UID Buku</label>
                    <div class="relative flex gap-2">
                        <input type="text" name="rfid_uid" id="rfid_uid" value="{{ old('rfid_uid', $book->rfid_uid) }}" required placeholder="Contoh: 04 E5 D7 08 C1 2A 81"
                               class="flex-grow px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm font-mono focus:outline-none focus:border-peach-mid focus:bg-white transition-all">
                        <button type="button" id="btn-scan-rfid" class="px-4 py-3 rounded-2xl border border-peach-mid bg-peach-soft text-peach-deep hover:bg-peach-mid hover:text-white transition-all text-xs font-bold flex items-center gap-1.5 shrink-0">
                            <span class="scan-icon relative flex h-2 w-2 hidden">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-peach-deep opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-peach-deep"></span>
                            </span>
                            <span class="btn-text">Pindai Otomatis</span>
                        </button>
                    </div>
                    <span id="scan-status" class="text-[11px] text-slate-400">Tempelkan tag buku di alat pemindai untuk mendapat nomor ini.</span>
                </div>

                @if($book->exists)
                    <div class="flex flex-col gap-2">
                        <label for="status" class="text-xs font-bold text-slate-600">Status Buku</label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-sm focus:outline-none focus:border-peach-mid focus:bg-white transition-all">
                            <option value="available" {{ old('status', $book->status) == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="borrowed" {{ old('status', $book->status) == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        </select>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 mt-3 pt-5 border-t border-slate-100">
                <a href="{{ route('books.index') }}" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-500 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-peach-deep to-rose-deep hover:opacity-90 text-white rounded-full text-sm font-bold transition-all shadow-lg shadow-peach-mid/40">
                    {{ $book->exists ? 'Simpan Perubahan' : 'Tambah Buku' }}
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
        btnScan.classList.remove('bg-peach-soft', 'text-peach-deep', 'border-peach-mid');
        btnScan.classList.add('bg-rose-500', 'text-white', 'border-rose-500');
        scanIcon.classList.remove('hidden');
        btnText.textContent = 'Batal';
        scanStatus.textContent = 'Menghubungkan ke pemindai...';
        scanStatus.className = 'text-[11px] text-peach-deep font-semibold animate-pulse';

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
        btnScan.classList.add('bg-peach-soft', 'text-peach-deep', 'border-peach-mid');
        scanIcon.classList.add('hidden');
        btnText.textContent = 'Pindai Otomatis';
        scanStatus.textContent = 'Tempelkan tag buku di alat pemindai untuk mendapat nomor ini.';
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
