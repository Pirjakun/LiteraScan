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
                    <label for="rfid_uid" class="text-xs font-bold text-slate-600">RFID UID Kartu</label>
                    <input type="text" name="rfid_uid" id="rfid_uid" value="{{ old('rfid_uid', $student->rfid_uid) }}" required placeholder="Contoh: A2 42 E2 2E"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm font-mono focus:outline-none focus:border-grape-mid focus:bg-white transition-all">
                    <span class="text-[11px] text-slate-400">Tempelkan kartu anggota di alat pemindai untuk mendapat nomor ini.</span>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="major" class="text-xs font-bold text-slate-600">Jurusan / Program Studi</label>
                <input type="text" name="major" id="major" value="{{ old('major', $student->major) }}" required placeholder="Contoh: Kelas 5A"
                       class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:border-grape-mid focus:bg-white transition-all">
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
