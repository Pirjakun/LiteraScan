@extends('layouts.app')

@section('title', 'Dashboard - PuSaKap')

@section('styles')
<style>
    .glow-active { box-shadow: 0 18px 40px -20px rgba(37, 99, 235, 0.55); border-color: #60a5fa !important; }
    .glow-idle   { box-shadow: 0 12px 30px -22px rgba(37, 99, 235, 0.4); }
    @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    .floaty { animation: floaty 3s ease-in-out infinite; }
</style>
@endsection

@section('content')

<!-- Greeting -->
@php
    $jam = (int) \Carbon\Carbon::now('Asia/Jakarta')->format('H');
    $sapaan = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));
@endphp
<div class="mb-6">
    <h2 class="font-display text-2xl sm:text-3xl font-bold text-slate-800">{{ $sapaan }}, selamat datang di <span class="text-sky-deep">Pu</span><span class="text-grape-mid">SaKap</span>! 👋</h2>
    <p class="text-sm text-slate-500 mt-1">Perpustakaan Sang Cakap untuk memantau peminjaman dan pengembalian buku</p>
</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left Column: Active Session & Metrics -->
    <div class="lg:col-span-1 flex flex-col gap-6">

        <!-- Active Session Card -->
        <div id="session-card" class="card rounded-3xl p-6 transition-all duration-500 flex flex-col justify-between min-h-[300px] glow-idle relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-36 h-36 bg-sky-soft rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-10 w-36 h-36 bg-grape-soft rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative">
                <div class="flex justify-between items-center mb-5">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Status Pemindai</span>
                    <span id="session-badge" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> MENUNGGU
                    </span>
                </div>

                <div id="session-info" class="flex flex-col gap-4">
                    <!-- Default Standby Content -->
                    <div class="py-4 flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-full bg-sky-soft flex items-center justify-center mb-4 text-sky-deep floaty">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13a2 2 0 100-4 2 2 0 000 4zm0 0v6a2 2 0 002 2h10a2 2 0 002-2v-6m-7-9l4 4m0 0l-4 4m4-4H9"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-700">Menunggu Kartu Siswa</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-[220px] leading-relaxed">Tempelkan kartu anggota pada alat pemindai untuk memulai peminjaman.</p>
                    </div>
                </div>
            </div>

            <!-- Footer of Session Card -->
            <div class="relative mt-5 pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-400 font-semibold">
                <span>Batas waktu sesi: 10 detik</span>
                <span id="session-timer" class="font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">00:00</span>
            </div>
        </div>

        <!-- Quick Metrics Grid -->
        <div class="grid grid-cols-2 gap-4">
            <div class="card rounded-2xl p-4 flex flex-col gap-1">
                <div class="h-9 w-9 rounded-xl bg-grape-soft text-grape-deep flex items-center justify-center mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"></path></svg>
                </div>
                <span id="metric-students" class="text-3xl font-extrabold text-slate-800">--</span>
                <span class="text-xs font-semibold text-slate-400">Total Anggota</span>
            </div>
            <div class="card rounded-2xl p-4 flex flex-col gap-1">
                <div class="h-9 w-9 rounded-xl bg-peach-soft text-peach-deep flex items-center justify-center mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13"></path></svg>
                </div>
                <span id="metric-books" class="text-3xl font-extrabold text-slate-800">--</span>
                <span class="text-xs font-semibold text-slate-400">Total Buku</span>
            </div>
            <div class="card rounded-2xl p-4 flex flex-col gap-1">
                <div class="h-9 w-9 rounded-xl bg-mint-soft text-mint-deep flex items-center justify-center mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span id="metric-available" class="text-3xl font-extrabold text-mint-deep">--</span>
                <span class="text-xs font-semibold text-slate-400">Buku Tersedia</span>
            </div>
            <div class="card rounded-2xl p-4 flex flex-col gap-1">
                <div class="h-9 w-9 rounded-xl bg-sky-soft text-sky-deep flex items-center justify-center mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span id="metric-borrowed" class="text-3xl font-extrabold text-sky-deep">--</span>
                <span class="text-xs font-semibold text-slate-400">Sedang Dipinjam</span>
            </div>
            <div class="col-span-2 card rounded-2xl p-4 flex flex-col gap-1 border-l-4 border-l-rose-400 bg-rose-50/50">
                <div class="h-9 w-9 rounded-xl bg-rose-soft text-rose-deep flex items-center justify-center mb-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span id="metric-fines" class="text-3xl font-extrabold text-rose-deep">Rp --</span>
                <span class="text-xs font-semibold text-slate-400">Total Denda Terkumpul</span>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Transactions Table -->
    <div class="lg:col-span-2 flex flex-col gap-6">
        <div class="card rounded-3xl p-6 flex flex-col gap-4 h-full">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Aktivitas Terkini</h2>
                    <p class="text-xs text-slate-400">Catatan peminjaman & pengembalian buku</p>
                </div>
                <div class="px-3 py-1 rounded-full bg-slate-100 text-xs font-bold text-slate-500" id="tx-count">
                    0 Transaksi
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100 flex-grow">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="text-xs uppercase font-bold text-slate-400 bg-slate-50">
                            <th class="p-4">Anggota</th>
                            <th class="p-4">Buku</th>
                            <th class="p-4">Kegiatan</th>
                            <th class="p-4">Waktu</th>
                            <th class="p-4">Denda</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-rows" class="divide-y divide-slate-100">
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let lastActiveSession = null;
    window.countdownTimer = null;
    let secondsLeft = 0;

    function startCountdown(duration) {
        clearInterval(window.countdownTimer);
        secondsLeft = duration;
        updateCountdownUI();
        window.countdownTimer = setInterval(() => {
            secondsLeft--;
            if (secondsLeft <= 0) { clearInterval(window.countdownTimer); secondsLeft = 0; }
            updateCountdownUI();
        }, 1000);
    }

    function updateCountdownUI() {
        const m = Math.floor(secondsLeft / 60).toString().padStart(2, '0');
        const s = (secondsLeft % 60).toString().padStart(2, '0');
        document.getElementById('session-timer').innerText = `${m}:${s}`;
    }

    function fetchDashboardData() {
        fetch(window.location.href, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('metric-students').innerText = data.total_students;
            document.getElementById('metric-books').innerText = data.total_books;
            document.getElementById('metric-available').innerText = data.available_books;
            document.getElementById('metric-borrowed').innerText = data.borrowed_books;
            document.getElementById('metric-fines').innerText = 'Rp ' + data.total_fines.toLocaleString('id-ID');

            document.getElementById('tx-count').innerText = `${data.recent_transactions.length} Transaksi`;

            const tbody = document.getElementById('transaction-rows');
            if (data.recent_transactions.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="p-10 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <div class="h-14 w-14 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <span class="text-sm font-semibold">Belum ada aktivitas hari ini</span>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = data.recent_transactions.map(tx => {
                    const isBorrow = tx.type === 'borrow';
                    const badgeClass = isBorrow
                        ? 'bg-sky-soft text-sky-deep'
                        : 'bg-mint-soft text-mint-deep';
                    const dot = isBorrow ? 'bg-sky-deep' : 'bg-mint-deep';
                    const typeText = isBorrow ? 'Dipinjam' : 'Dikembalikan';
                    return `
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="p-4">
                                <div class="font-bold text-slate-700">${tx.student_name}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">${tx.student_nim}</div>
                            </td>
                            <td class="p-4 font-semibold text-slate-600">${tx.book_title}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 ${badgeClass}">
                                    <span class="h-1.5 w-1.5 rounded-full ${dot}"></span>${typeText}
                                </span>
                            </td>
                            <td class="p-4 text-xs text-slate-500 font-medium">
                                <div class="font-bold text-slate-600">${tx.borrowed_human || '-'}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">${tx.borrowed_at || '-'}</div>
                            </td>
                            <td class="p-4 text-xs font-bold ${tx.jumlah_denda > 0 ? 'text-rose-600 bg-rose-50/50 rounded-lg' : 'text-slate-400'}">
                                ${tx.jumlah_denda > 0 ? 'Rp ' + tx.jumlah_denda.toLocaleString('id-ID') : '-'}
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            const sessionCard = document.getElementById('session-card');
            const sessionBadge = document.getElementById('session-badge');
            const sessionInfo = document.getElementById('session-info');

            if (data.active_session) {
                sessionCard.className = "card rounded-3xl p-6 transition-all duration-500 flex flex-col justify-between min-h-[300px] glow-active relative overflow-hidden";
                sessionBadge.className = "px-3 py-1 rounded-full text-xs font-bold bg-grape-soft text-grape-deep flex items-center gap-1.5";
                sessionBadge.innerHTML = `<span class="h-1.5 w-1.5 rounded-full bg-grape-deep animate-pulse"></span> AKTIF`;

                sessionInfo.innerHTML = `
                    <div class="flex flex-col gap-4 py-2 relative">
                        <div class="flex items-center gap-3">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-tr from-grape-deep to-sky-deep flex items-center justify-center text-white shadow-lg shadow-grape-mid/40">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Anggota Aktif</div>
                                <h3 class="text-lg font-extrabold text-slate-800 leading-tight mt-0.5">${data.active_session.name}</h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 bg-slate-50 rounded-2xl p-3 text-xs">
                            <div>
                                <div class="text-slate-400 font-bold uppercase text-[9px]">NIS</div>
                                <div class="font-mono text-slate-700 font-bold mt-0.5">${data.active_session.nim}</div>
                            </div>
                            <div>
                                <div class="text-slate-400 font-bold uppercase text-[9px]">Kelas</div>
                                <div class="text-slate-700 font-semibold mt-0.5">${data.active_session.major}</div>
                            </div>
                        </div>
                        <p class="text-xs text-grape-deep font-semibold flex items-center gap-2 bg-grape-soft rounded-xl p-3">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Silakan tempelkan buku untuk dipinjam atau dikembalikan.
                        </p>
                    </div>
                `;

                if (JSON.stringify(lastActiveSession) !== JSON.stringify(data.active_session)) {
                    startCountdown(10);
                }
            } else {
                sessionCard.className = "card rounded-3xl p-6 transition-all duration-500 flex flex-col justify-between min-h-[300px] glow-idle relative overflow-hidden";
                sessionBadge.className = "px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 flex items-center gap-1.5";
                sessionBadge.innerHTML = `<span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> MENUNGGU`;

                sessionInfo.innerHTML = `
                    <div class="py-4 flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-full bg-sky-soft flex items-center justify-center mb-4 text-sky-deep floaty">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13a2 2 0 100-4 2 2 0 000 4zm0 0v6a2 2 0 002 2h10a2 2 0 002-2v-6m-7-9l4 4m0 0l-4 4m4-4H9"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-700">Menunggu Kartu Siswa</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-[220px] leading-relaxed">Tempelkan kartu anggota pada alat pemindai untuk memulai peminjaman.</p>
                    </div>
                `;

                clearInterval(window.countdownTimer);
                document.getElementById('session-timer').innerText = "00:00";
            }

            lastActiveSession = data.active_session;
        });
    }

    window.pollingTimer = null;

    function startPolling() {
        clearInterval(window.pollingTimer);
        window.pollingTimer = setInterval(fetchDashboardData, 3000);
    }

    function stopPolling() {
        clearInterval(window.pollingTimer);
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            fetchDashboardData();
            startPolling();
        } else {
            stopPolling();
        }
    });

    // Mulai polling pertama kali
    fetchDashboardData();
    startPolling();
</script>
@endsection
