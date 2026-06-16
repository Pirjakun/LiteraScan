@extends('layouts.app')

@section('title', 'Tambah Pinjam Manual - PuSaKap')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-slate-600 mb-4 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke daftar transaksi
    </a>

    <div class="card rounded-3xl p-8 relative overflow-hidden">
        <div class="absolute -top-12 -right-10 w-36 h-36 bg-rose-soft rounded-full blur-2xl pointer-events-none"></div>

        <div class="mb-8 relative flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-rose-soft text-rose-deep flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-display text-xl font-bold text-slate-800">Tambah Pinjam Manual</h2>
                <p class="text-xs text-slate-400">Pilih siswa dan buku untuk mencatat peminjaman tanpa tap RFID</p>
            </div>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST" class="flex flex-col gap-5 relative">
            @csrf

            @php
                $selectedStudentText = '';
                if (old('student_id')) {
                    $st = \App\Models\Student::find(old('student_id'));
                    if ($st) {
                        $selectedStudentText = $st->name . ' (NIS: ' . $st->nim . ')';
                    }
                }
                
                $selectedBookText = '';
                if (old('book_id')) {
                    $bk = \App\Models\Book::find(old('book_id'));
                    if ($bk) {
                        $selectedBookText = $bk->title;
                    }
                }
            @endphp

            <div class="flex flex-col gap-2 relative" id="student-search-container">
                <label class="text-xs font-bold text-slate-600">Pilih Siswa / Anggota</label>
                <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}" required>
                <div class="relative">
                    <input type="text" id="student_search_input" value="{{ $selectedStudentText }}" placeholder="Cari nama atau NIS siswa..." autocomplete="off" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-rose-mid focus:bg-white transition-all pr-10">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <!-- Dropdown list -->
                <div id="student_dropdown_list" class="absolute top-[100%] left-0 right-0 z-30 mt-1 max-h-60 overflow-y-auto bg-white rounded-2xl border border-slate-100 shadow-xl hidden py-1">
                    @foreach($students as $st)
                        <div class="student-option px-4 py-2.5 hover:bg-rose-soft/40 cursor-pointer text-sm text-slate-700 transition-all flex flex-col gap-0.5" 
                             data-id="{{ $st->id }}" 
                             data-search-text="{{ strtolower($st->name) }} {{ strtolower($st->nim) }}">
                            <span class="font-bold text-slate-800">{{ $st->name }}</span>
                            <span class="text-xs text-slate-400 font-mono">NIS: {{ $st->nim }}</span>
                        </div>
                    @endforeach
                    <div id="student_no_results" class="px-4 py-3 text-slate-400 text-sm italic hidden">Siswa tidak ditemukan</div>
                </div>
            </div>

            <div class="flex flex-col gap-2 relative" id="book-search-container">
                <label class="text-xs font-bold text-slate-600">Pilih Buku (Hanya yang berstatus Tersedia)</label>
                <input type="hidden" name="book_id" id="book_id" value="{{ old('book_id') }}" required>
                <div class="relative">
                    <input type="text" id="book_search_input" value="{{ $selectedBookText }}" placeholder="Cari judul buku atau RFID..." autocomplete="off" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-rose-mid focus:bg-white transition-all pr-10"
                           @if($books->isEmpty()) disabled @endif>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <!-- Dropdown list -->
                <div id="book_dropdown_list" class="absolute top-[100%] left-0 right-0 z-30 mt-1 max-h-60 overflow-y-auto bg-white rounded-2xl border border-slate-100 shadow-xl hidden py-1">
                    @foreach($books as $bk)
                        <div class="book-option px-4 py-2.5 hover:bg-rose-soft/40 cursor-pointer text-sm text-slate-700 transition-all flex flex-col gap-0.5" 
                             data-id="{{ $bk->id }}" 
                             data-search-text="{{ strtolower($bk->title) }} {{ strtolower($bk->rfid_uid) }}">
                            <span class="font-bold text-slate-800">{{ $bk->title }}</span>
                            <span class="text-xs text-slate-400 font-mono">RFID: {{ $bk->rfid_uid }}</span>
                        </div>
                    @endforeach
                    <div id="book_no_results" class="px-4 py-3 text-slate-400 text-sm italic hidden">Buku tidak ditemukan</div>
                </div>
                @if($books->isEmpty())
                    <span class="text-xs text-rose-deep font-semibold mt-1">Saat ini tidak ada buku yang berstatus tersedia untuk dipinjam.</span>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 mt-3 pt-5 border-t border-slate-100">
                <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-sm font-bold text-slate-500 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-rose-deep to-peach-deep hover:opacity-90 text-white rounded-full text-sm font-bold transition-all shadow-lg shadow-rose-mid/40">
                    Simpan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        // --- SISWA SEARCH ---
        const studentHiddenInput = document.getElementById('student_id');
        const studentSearchInput = document.getElementById('student_search_input');
        const studentDropdown = document.getElementById('student_dropdown_list');
        const studentOptions = document.querySelectorAll('.student-option');
        const studentNoResults = document.getElementById('student_no_results');

        studentSearchInput.addEventListener('focus', () => {
            studentDropdown.classList.remove('hidden');
        });

        studentSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            let hasResults = false;

            studentOptions.forEach(opt => {
                const searchText = opt.getAttribute('data-search-text');
                if (searchText.includes(query)) {
                    opt.classList.remove('hidden');
                    hasResults = true;
                } else {
                    opt.classList.add('hidden');
                }
            });

            if (hasResults) {
                studentNoResults.classList.add('hidden');
            } else {
                studentNoResults.classList.remove('hidden');
            }
            
            studentHiddenInput.value = '';
        });

        studentOptions.forEach(opt => {
            opt.addEventListener('click', () => {
                const id = opt.getAttribute('data-id');
                const name = opt.querySelector('span:first-child').textContent;
                const nis = opt.querySelector('.font-mono').textContent;
                
                studentHiddenInput.value = id;
                studentSearchInput.value = `${name} (${nis})`;
                studentDropdown.classList.add('hidden');
            });
        });

        // --- BUKU SEARCH ---
        const bookHiddenInput = document.getElementById('book_id');
        const bookSearchInput = document.getElementById('book_search_input');
        const bookDropdown = document.getElementById('book_dropdown_list');
        const bookOptions = document.querySelectorAll('.book-option');
        const bookNoResults = document.getElementById('book_no_results');

        if (bookSearchInput) {
            bookSearchInput.addEventListener('focus', () => {
                bookDropdown.classList.remove('hidden');
            });

            bookSearchInput.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase().trim();
                let hasResults = false;

                bookOptions.forEach(opt => {
                    const searchText = opt.getAttribute('data-search-text');
                    if (searchText.includes(query)) {
                        opt.classList.remove('hidden');
                        hasResults = true;
                    } else {
                        opt.classList.add('hidden');
                    }
                });

                if (hasResults) {
                    bookNoResults.classList.add('hidden');
                } else {
                    bookNoResults.classList.remove('hidden');
                }

                bookHiddenInput.value = '';
            });

            bookOptions.forEach(opt => {
                opt.addEventListener('click', () => {
                    const id = opt.getAttribute('data-id');
                    const title = opt.querySelector('span:first-child').textContent;
                    
                    bookHiddenInput.value = id;
                    bookSearchInput.value = title;
                    bookDropdown.classList.add('hidden');
                });
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            const studentContainer = document.getElementById('student-search-container');
            if (studentContainer && !studentContainer.contains(e.target)) {
                studentDropdown.classList.add('hidden');
                if (!studentHiddenInput.value) {
                    studentSearchInput.value = '';
                }
            }
            const bookContainer = document.getElementById('book-search-container');
            if (bookContainer && !bookContainer.contains(e.target)) {
                bookDropdown.classList.add('hidden');
                if (!bookHiddenInput.value) {
                    bookSearchInput.value = '';
                }
            }
        });
    })();
</script>
@endsection
