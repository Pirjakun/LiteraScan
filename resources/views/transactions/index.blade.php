@extends('layouts.app')

@section('title', 'Transaksi Sirkulasi - PuSaKap')

@section('content')
<div class="card rounded-3xl p-6 flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row gap-4 justify-between sm:items-center">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-rose-soft text-rose-deep flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-display text-xl font-bold text-slate-800">Transaksi Sirkulasi</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kelola data peminjaman, pengembalian, dan denda secara manual</p>
            </div>
        </div>
        <a href="{{ route('transactions.create') }}" class="w-full sm:w-auto justify-center px-5 py-2.5 bg-gradient-to-r from-rose-deep to-peach-deep hover:opacity-90 text-white rounded-full text-sm font-bold transition-all shadow-lg shadow-rose-mid/40 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pinjam Manual
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 pb-2 border-b border-slate-100">
        <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ !$status ? 'bg-rose-soft text-rose-deep' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
            Semua ({{ \App\Models\Transaction::count() }})
        </a>
        <a href="{{ route('transactions.index', ['status' => 'borrowed']) }}" class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $status === 'borrowed' ? 'bg-sky-soft text-sky-deep' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
            Sedang Dipinjam ({{ \App\Models\Transaction::whereNull('returned_at')->count() }})
        </a>
        <a href="{{ route('transactions.index', ['status' => 'returned']) }}" class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $status === 'returned' ? 'bg-mint-soft text-mint-deep' : 'bg-slate-50 text-slate-500 hover:bg-slate-100' }}">
            Sudah Dikembalikan ({{ \App\Models\Transaction::whereNotNull('returned_at')->count() }})
        </a>
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="text-xs uppercase font-bold text-slate-400 bg-slate-50">
                    <th class="p-4">Anggota</th>
                    <th class="p-4">Buku</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Tanggal Pinjam</th>
                    <th class="p-4">Tanggal Kembali</th>
                    <th class="p-4">Denda</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $tx)
                    @php
                        $isBorrowed = $tx->returned_at === null;
                        $statusClass = $isBorrowed ? 'bg-sky-soft text-sky-deep' : 'bg-mint-soft text-mint-deep';
                        $statusText = $isBorrowed ? 'Dipinjam' : 'Dikembalikan';
                        $dotClass = $isBorrowed ? 'bg-sky-deep' : 'bg-mint-deep';
                    @endphp
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="p-4">
                            @if($tx->student)
                                <div class="font-bold text-slate-700">{{ $tx->student->name }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">NIS: {{ $tx->student->nim }}</div>
                            @else
                                <span class="text-slate-400 italic">Anggota dihapus</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($tx->book)
                                <div class="font-bold text-slate-700 leading-snug">{{ $tx->book->title }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $tx->book->rfid_uid }}</div>
                            @else
                                <span class="text-slate-400 italic">Buku dihapus</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1 {{ $statusClass }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>{{ $statusText }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-500 font-mono text-xs">
                            {{ $tx->borrowed_at->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="p-4 text-slate-500 font-mono text-xs">
                            @if($tx->returned_at)
                                {{ $tx->returned_at->translatedFormat('d M Y, H:i') }}
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="p-4 font-bold">
                            @if($tx->jumlah_denda > 0)
                                <span class="text-rose-deep bg-rose-soft px-2 py-0.5 rounded-lg text-xs">
                                    Rp {{ number_format($tx->jumlah_denda, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">
                                @if($isBorrowed)
                                    <form action="{{ route('transactions.return', $tx) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 bg-mint-soft hover:bg-mint-deep/20 text-mint-deep rounded-xl transition-all text-xs font-bold flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Kembalikan
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('transactions.destroy', $tx) }}" method="POST" class="delete-form inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-rose-soft hover:bg-rose-mid/40 text-rose-deep rounded-xl transition-all text-xs font-bold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <div class="h-14 w-14 rounded-full bg-rose-soft flex items-center justify-center text-rose-deep">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold">Belum ada data transaksi sirkulasi.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="pt-4">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
