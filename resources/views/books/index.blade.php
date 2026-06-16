@extends('layouts.app')

@section('title', 'Manajemen Buku - PuSaKap')

@section('content')
<div class="card rounded-3xl p-6 flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row gap-4 justify-between sm:items-center">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-sky-soft text-sky-deep flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13"></path></svg>
            </div>
            <div>
                <h2 class="font-display text-xl font-bold text-slate-800">Koleksi Buku</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kelola daftar buku perpustakaan beserta label RFID-nya</p>
            </div>
        </div>
        <a href="{{ route('books.create') }}" class="w-full sm:w-auto justify-center px-5 py-2.5 bg-gradient-to-r from-sky-deep to-grape-deep hover:opacity-90 text-white rounded-full text-sm font-bold transition-all shadow-lg shadow-sky-mid/40 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah Buku
        </a>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="text-xs uppercase font-bold text-slate-400 bg-slate-50">
                    <th class="p-4">Judul Buku</th>
                    <th class="p-4">Pengarang</th>
                    <th class="p-4">RFID UID</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($books as $book)
                    @php
                        $isAvailable = $book->status === 'available';
                        $statusClass = $isAvailable
                            ? 'bg-mint-soft text-mint-deep'
                            : 'bg-sky-soft text-sky-deep';
                        $dotClass = $isAvailable ? 'bg-mint-deep' : 'bg-sky-deep';
                        $statusText = $isAvailable ? 'Tersedia' : 'Dipinjam';
                    @endphp
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="p-4 font-bold text-slate-700">{{ $book->title }}</td>
                        <td class="p-4 text-slate-500">{{ $book->author }}</td>
                        <td class="p-4">
                            <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-md">{{ $book->rfid_uid }}</span>
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 {{ $statusClass }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>{{ $statusText }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('books.edit', $book) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-all text-xs font-bold flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="delete-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-rose-soft hover:bg-rose-mid/40 text-rose-deep rounded-xl transition-all text-xs font-bold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <div class="h-14 w-14 rounded-full bg-sky-soft flex items-center justify-center text-sky-deep">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13"></path></svg>
                                </div>
                                <span class="text-sm font-semibold">Belum ada buku. Yuk tambahkan buku pertama!</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
