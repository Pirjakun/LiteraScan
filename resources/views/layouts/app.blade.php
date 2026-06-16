<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LITERA-SCAN - Perpustakaan Pintar')</title>
    <!-- Fonts & Static Assets Compiled via Vite -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at 10% 0%, rgba(125, 211, 252, 0.18), transparent 35%),
                radial-gradient(circle at 90% 5%, rgba(196, 181, 253, 0.18), transparent 35%),
                radial-gradient(circle at 50% 100%, rgba(110, 231, 183, 0.15), transparent 40%),
                #f7fafc;
        }
        .font-display { font-family: 'Fredoka', sans-serif; }
        .card { background: #ffffff; border: 1px solid #eef2f7; box-shadow: 0 10px 30px -18px rgba(30, 41, 59, 0.25); }
        ::selection { background: #7dd3fc; color: #0c4a6e; }
        /* friendly scrollbar */
        ::-webkit-scrollbar { height: 10px; width: 10px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    </style>
    @yield('styles')
</head>
<body class="text-slate-700 min-h-screen flex flex-col justify-between antialiased">

    <!-- Navbar -->
    <header class="w-full sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-sky-deep to-grape-deep flex items-center justify-center shadow-lg shadow-sky-mid/40">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-display text-xl sm:text-2xl font-700 font-bold tracking-tight text-slate-800 leading-none">Litera<span class="text-sky-deep">Scan</span></h1>
                        <p class="text-[9px] sm:text-[11px] text-slate-400 font-semibold tracking-wide mt-0.5">Perpustakaan Pintar Sekolah</p>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-1.5">
                    <a href="{{ url('/') }}" class="px-4 py-2 rounded-full text-sm font-bold transition-all flex items-center gap-2 {{ Request::is('/') ? 'bg-sky-soft text-sky-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('students.index') }}" class="px-4 py-2 rounded-full text-sm font-bold transition-all flex items-center gap-2 {{ Request::is('students*') ? 'bg-grape-soft text-grape-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-1-7.87"></path></svg>
                        Siswa
                    </a>
                    <a href="{{ route('books.index') }}" class="px-4 py-2 rounded-full text-sm font-bold transition-all flex items-center gap-2 {{ Request::is('books*') ? 'bg-peach-soft text-peach-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Buku
                    </a>
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-full text-sm font-bold transition-all flex items-center gap-2 {{ Request::is('transactions*') ? 'bg-rose-soft text-rose-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Transaksi
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <!-- Indikator status sistem -->
                <div class="flex items-center gap-2 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-full bg-mint-soft text-mint-deep text-xs font-bold">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-mint-mid opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-mint-deep"></span>
                    </span>
                    <span class="hidden sm:inline">Sistem Online</span>
                    <span class="sm:hidden">Online</span>
                </div>

                <!-- Tombol menu hamburger (hanya tampil di mobile) -->
                <button type="button" data-mobile-toggle aria-label="Buka menu" aria-expanded="false" class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-2xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>

        <!-- Mobile nav (dropdown saat hamburger ditekan) -->
        <nav id="mobile-menu" class="md:hidden hidden flex-col gap-1.5 px-5 pb-4 pt-1 border-t border-slate-100">
            <a href="{{ url('/') }}" class="px-4 py-2.5 rounded-2xl text-sm font-bold flex items-center gap-3 {{ Request::is('/') ? 'bg-sky-soft text-sky-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="{{ route('students.index') }}" class="px-4 py-2.5 rounded-2xl text-sm font-bold flex items-center gap-3 {{ Request::is('students*') ? 'bg-grape-soft text-grape-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-1-7.87"></path></svg>
                Siswa
            </a>
            <a href="{{ route('books.index') }}" class="px-4 py-2.5 rounded-2xl text-sm font-bold flex items-center gap-3 {{ Request::is('books*') ? 'bg-peach-soft text-peach-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Buku
            </a>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2.5 rounded-2xl text-sm font-bold flex items-center gap-3 {{ Request::is('transactions*') ? 'bg-rose-soft text-rose-deep' : 'text-slate-500 hover:bg-slate-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                Transaksi
            </a>
        </nav>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl w-full mx-auto px-5 sm:px-6 py-8 flex-grow">

        <!-- Alerts container -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-mint-soft border border-mint-mid/50 text-mint-deep text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-soft border border-rose-mid/50 text-rose-deep text-sm font-medium flex flex-col gap-1.5">
                <div class="flex items-center gap-3 font-bold">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Ada yang perlu diperbaiki:
                </div>
                <ul class="list-disc list-inside text-xs pl-8 flex flex-col gap-0.5 font-normal">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="w-full bg-white/60 backdrop-blur-sm py-4 border-t border-slate-100 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} LiteraScan &middot; Perpustakaan Pintar Sekolah Dasar</span>
            <div class="flex items-center gap-2 font-semibold">
                <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-mint-deep"></span>Database</span>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-mint-deep"></span>Alat Pemindai RFID</span>
            </div>
        </div>
    </footer>

    <!-- SPA Router with Skeleton Loader -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Intercept all local anchor clicks
            document.body.addEventListener('click', e => {
                const link = e.target.closest('a');
                if (!link) return;
                
                // Keep default browser action for modifier keys
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                
                try {
                    const url = new URL(link.href, window.location.href);
                    if (url.origin !== window.location.origin) return; // External link
                    
                    // Exclude anchor-only links, form actions, target opens or downloads
                    if (link.getAttribute('target') || link.getAttribute('download') || link.href.includes('#')) return;
                    
                    // Exclude specific api routes or files
                    if (url.pathname.startsWith('/api') || url.pathname.endsWith('.php')) return;
                    
                    e.preventDefault();
                    navigateTo(url.pathname + url.search);
                } catch(err) {
                    // Fallback to default browser navigation if error parsing URL
                }
            });

            // Buka/tutup menu hamburger di mobile (delegasi agar tetap jalan setelah header di-swap SPA)
            document.body.addEventListener('click', e => {
                const toggle = e.target.closest('[data-mobile-toggle]');
                if (!toggle) return;
                const menu = document.getElementById('mobile-menu');
                if (!menu) return;
                const isOpen = menu.classList.toggle('hidden') === false;
                menu.classList.toggle('flex', isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            // Handle back/forward history navigation
            window.addEventListener('popstate', () => {
                navigateTo(window.location.pathname + window.location.search, false);
            });
        });

        function getSkeletonForUrl(urlPath) {
            const path = urlPath.split('?')[0]; // Hapus query string

            // 1. Skeleton untuk Formulir (Tambah/Edit) - max-w-2xl centered
            if (path.endsWith('/create') || path.includes('/edit') || /\/(students|books)\/[^\/]+\/(edit|create)/.test(path)) {
                return `
                    <div class="animate-pulse flex flex-col gap-4 max-w-2xl mx-auto py-2">
                        <!-- Back button placeholder -->
                        <div class="h-4 bg-slate-200 rounded-lg w-32 mb-2"></div>
                        
                        <!-- Form Card -->
                        <div class="bg-white rounded-3xl p-8 border border-slate-100 flex flex-col gap-6 shadow-sm">
                            <!-- Header Form -->
                            <div class="flex items-center gap-3 mb-2">
                                <div class="h-12 w-12 rounded-2xl bg-slate-200 shrink-0"></div>
                                <div class="space-y-2">
                                    <div class="h-6 bg-slate-200 rounded-xl w-48"></div>
                                    <div class="h-4 bg-slate-200 rounded-lg w-72"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-5">
                                <div class="space-y-2">
                                    <div class="h-4 bg-slate-200 rounded-lg w-20"></div>
                                    <div class="h-11 bg-slate-50 rounded-2xl w-full"></div>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-4 bg-slate-200 rounded-lg w-24"></div>
                                    <div class="h-11 bg-slate-50 rounded-2xl w-full"></div>
                                </div>
                                <div class="space-y-2">
                                    <div class="h-4 bg-slate-200 rounded-lg w-16"></div>
                                    <div class="h-11 bg-slate-50 rounded-2xl w-full"></div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-5 border-t border-slate-100 mt-2">
                                <div class="h-10 bg-slate-100 rounded-full w-20"></div>
                                <div class="h-10 bg-slate-200 rounded-full w-28"></div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // 2. Skeleton untuk Dashboard Utama
            if (path === '/' || path === '/dashboard' || path === '') {
                return `
                    <div class="animate-pulse flex flex-col gap-6 max-w-7xl mx-auto py-2">
                        <!-- Greeting -->
                        <div class="flex flex-col gap-2">
                            <div class="h-8 bg-slate-200 rounded-2xl w-72 sm:w-96"></div>
                            <div class="h-4 bg-slate-200 rounded-xl w-64 sm:w-80"></div>
                        </div>
                        
                        <!-- Main Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left Column -->
                            <div class="lg:col-span-1 flex flex-col gap-6">
                                <div class="h-[300px] bg-slate-200 rounded-3xl"></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="h-28 bg-slate-200 rounded-2xl"></div>
                                    <div class="h-28 bg-slate-200 rounded-2xl"></div>
                                    <div class="h-28 bg-slate-200 rounded-2xl"></div>
                                    <div class="h-28 bg-slate-200 rounded-2xl"></div>
                                    <div class="col-span-2 h-28 bg-slate-200 rounded-2xl"></div>
                                </div>
                            </div>
                            
                            <!-- Right Column (Table) -->
                            <div class="lg:col-span-2">
                                <div class="h-[580px] bg-white rounded-3xl p-6 border border-slate-100 flex flex-col gap-4 shadow-sm">
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="space-y-2">
                                            <div class="h-6 bg-slate-200 rounded-xl w-36"></div>
                                            <div class="h-4 bg-slate-200 rounded-lg w-48"></div>
                                        </div>
                                        <div class="h-6 bg-slate-200 rounded-full w-20"></div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
                                        <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
                                        <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
                                        <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
                                        <div class="h-12 bg-slate-100 rounded-xl w-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // 3. Skeleton Default untuk Tabel List (Siswa & Buku) - Header inside card
            return `
                <div class="animate-pulse max-w-7xl mx-auto py-2">
                    <!-- Table Card -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 flex flex-col gap-6 shadow-sm">
                        <!-- Header inside the card -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-between sm:items-center pb-2">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-2xl bg-slate-200 shrink-0"></div>
                                <div class="space-y-2">
                                    <div class="h-6 bg-slate-200 rounded-xl w-36"></div>
                                    <div class="h-4 bg-slate-200 rounded-lg w-64"></div>
                                </div>
                            </div>
                            <div class="h-10 bg-slate-200 rounded-full w-full sm:w-36"></div>
                        </div>
                        
                        <!-- Table structure -->
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <div class="h-10 bg-slate-50 w-full mb-1"></div>
                            <div class="space-y-3 p-2">
                                <div class="h-12 bg-slate-100/60 rounded-xl w-full"></div>
                                <div class="h-12 bg-slate-100/60 rounded-xl w-full"></div>
                                <div class="h-12 bg-slate-100/60 rounded-xl w-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function navigateTo(url, push = true) {
            const main = document.querySelector('main');
            
            // Clean up dashboard polling to prevent multiple intervals running
            if (window.pollingTimer) {
                clearInterval(window.pollingTimer);
            }
            if (window.countdownTimer) {
                clearInterval(window.countdownTimer);
            }

            // Tampilkan skeleton loader hanya jika fetch belum selesai dalam 150ms.
            // Timer disimpan agar bisa dibatalkan saat konten asli sudah datang,
            // supaya skeleton tidak menimpa konten (race condition di server cepat).
            let contentLoaded = false;
            main.style.opacity = '0.4';
            const skeletonTimer = setTimeout(() => {
                if (contentLoaded) return;
                main.innerHTML = getSkeletonForUrl(url);
                main.style.opacity = '1';
            }, 150);

            // Fetch halaman baru
            fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                // Konten sudah datang: batalkan skeleton agar tidak menimpa konten asli.
                contentLoaded = true;
                clearTimeout(skeletonTimer);

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update Title
                document.title = doc.title;

                // Update Main Content
                const newMain = doc.querySelector('main');
                if (newMain) {
                    main.innerHTML = newMain.innerHTML;
                    main.style.opacity = '1';
                }

                // Update Navbar Active States
                const newHeader = doc.querySelector('header');
                if (newHeader) {
                    document.querySelector('header').innerHTML = newHeader.innerHTML;
                }
                
                // Jalankan script yang ada di halaman yang baru dimuat
                const scripts = doc.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    document.body.appendChild(newScript);
                    newScript.parentNode.removeChild(newScript);
                });

                if (push) {
                    history.pushState(null, '', url);
                }
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(err => {
                clearTimeout(skeletonTimer);
                console.error('SPA Navigation error, falling back:', err);
                window.location.href = url; // Fallback jika gagal
            });
        }

        // Global delete form confirmation interception
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.classList.contains('delete-form') && !form.dataset.confirmed) {
                e.preventDefault(); // Stop submission
                
                const modal = document.getElementById('delete-modal');
                const modalContent = modal.querySelector('.transform');
                const confirmBtn = document.getElementById('delete-modal-confirm');
                const cancelBtn = document.getElementById('delete-modal-cancel');
                
                const titleElement = document.getElementById('delete-modal-title');
                const msgElement = document.getElementById('delete-modal-message');
                
                // Customize message based on route
                const action = form.getAttribute('action') || '';
                if (action.includes('students')) {
                    titleElement.textContent = 'Hapus Siswa?';
                    msgElement.textContent = 'Menghapus data siswa juga akan menghapus data kartu RFID yang dikaitkan dengan siswa tersebut.';
                } else if (action.includes('books')) {
                    titleElement.textContent = 'Hapus Buku?';
                    msgElement.textContent = 'Menghapus buku dari koleksi akan menghilangkan riwayat transaksi buku tersebut.';
                } else if (action.includes('transactions')) {
                    titleElement.textContent = 'Hapus Transaksi?';
                    msgElement.textContent = 'Menghapus riwayat transaksi ini dapat mempengaruhi status ketersediaan buku terkait.';
                } else {
                    titleElement.textContent = 'Hapus Data?';
                    msgElement.textContent = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.';
                }
                
                // Show modal with animations
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
                
                // Handle Confirm
                confirmBtn.onclick = function () {
                    form.dataset.confirmed = 'true';
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    modalContent.classList.remove('scale-100');
                    modalContent.classList.add('scale-95');
                    form.submit();
                };
                
                // Handle Cancel
                cancelBtn.onclick = function () {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    modalContent.classList.remove('scale-100');
                    modalContent.classList.add('scale-95');
                };
            }
        });

        // Global RFID UID automatic input formatter (Uppercase Hex spaced every 2 characters)
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'rfid_uid') {
                const input = e.target;
                const originalVal = input.value;
                const upperVal = originalVal.toUpperCase();
                
                // Keep only hexadecimal characters (0-9, A-F)
                const hexOnly = upperVal.replace(/[^0-9A-F]/g, '');
                
                // Format with a space every two characters
                let formatted = '';
                for (let i = 0; i < hexOnly.length; i += 2) {
                    if (i > 0) {
                        formatted += ' ';
                    }
                    formatted += hexOnly.substr(i, 2);
                }
                
                if (originalVal !== formatted) {
                    const selectionStart = input.selectionStart;
                    const oldLen = originalVal.length;
                    
                    input.value = formatted;
                    
                    // Adjust cursor position based on changes
                    const newLen = formatted.length;
                    const cursorAdjustment = newLen - oldLen;
                    let newCursorPos = selectionStart + cursorAdjustment;
                    
                    // Prevent cursor going below 0 or beyond text length
                    newCursorPos = Math.max(0, Math.min(newLen, newCursorPos));
                    input.setSelectionRange(newCursorPos, newCursorPos);
                }
            }
        });
    </script>
    <style>
        main {
            transition: opacity 0.15s ease-in-out;
        }
    </style>

    <!-- Custom Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-100 transform scale-95 transition-all duration-300 flex flex-col items-center text-center gap-4">
            <!-- Warning Icon -->
            <div class="h-14 w-14 rounded-full bg-rose-soft text-rose-deep flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-display text-lg font-extrabold text-slate-800" id="delete-modal-title">Hapus Data?</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed" id="delete-modal-message">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="flex items-center gap-3 w-full mt-2">
                <button type="button" id="delete-modal-cancel" class="flex-1 px-4 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-500 transition-all">
                    Batal
                </button>
                <button type="button" id="delete-modal-confirm" class="flex-1 px-4 py-2.5 rounded-full bg-rose-deep hover:opacity-90 text-white text-xs font-bold transition-all shadow-lg shadow-rose-mid/30">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
