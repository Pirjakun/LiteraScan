@extends('layouts.app')

@section('title', 'Tambah Pinjam Manual - LiteraScan')

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

            <div class="flex flex-col gap-2">
                <label for="student_id" class="text-xs font-bold text-slate-600">Pilih Siswa / Anggota</label>
                <select name="student_id" id="student_id" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-sm focus:outline-none focus:border-rose-mid focus:bg-white transition-all">
                    <option value="" disabled selected>-- Pilih Siswa --</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" {{ old('student_id') == $st->id ? 'selected' : '' }}>{{ $st->name }} (NIS: {{ $st->nim }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label for="book_id" class="text-xs font-bold text-slate-600">Pilih Buku (Hanya yang berstatus Tersedia)</label>
                <select name="book_id" id="book_id" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 text-sm focus:outline-none focus:border-rose-mid focus:bg-white transition-all">
                    <option value="" disabled selected>-- Pilih Buku --</option>
                    @foreach($books as $bk)
                        <option value="{{ $bk->id }}" {{ old('book_id') == $bk->id ? 'selected' : '' }}>{{ $bk->title }} (RFID: {{ $bk->rfid_uid }})</option>
                    @endforeach
                </select>
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
@endsection
