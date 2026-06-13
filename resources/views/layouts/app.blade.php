<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LITERA-SCAN - Perpustakaan Pintar')</title>
    <!-- Fonts & Static Assets Compiled via Vite -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-full bg-mint-soft text-mint-deep text-xs font-bold">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-mint-mid opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-mint-deep"></span>
                    </span>
                    <span class="hidden sm:inline">Sistem Online</span>
                </div>
            </div>
        </div>

        <!-- Mobile nav -->
        <nav class="md:hidden flex items-center gap-1.5 px-5 pb-3 overflow-x-auto">
            <a href="{{ url('/') }}" class="px-4 py-1.5 rounded-full text-sm font-bold whitespace-nowrap {{ Request::is('/') ? 'bg-sky-soft text-sky-deep' : 'text-slate-500 bg-slate-100' }}">Dashboard</a>
            <a href="{{ route('students.index') }}" class="px-4 py-1.5 rounded-full text-sm font-bold whitespace-nowrap {{ Request::is('students*') ? 'bg-grape-soft text-grape-deep' : 'text-slate-500 bg-slate-100' }}">Siswa</a>
            <a href="{{ route('books.index') }}" class="px-4 py-1.5 rounded-full text-sm font-bold whitespace-nowrap {{ Request::is('books*') ? 'bg-peach-soft text-peach-deep' : 'text-slate-500 bg-slate-100' }}">Buku</a>
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

    @yield('scripts')
</body>
</html>
