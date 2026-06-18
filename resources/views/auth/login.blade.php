<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PuSaKap</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (via CDN to match layout) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['Fredoka', 'sans-serif'],
                    },
                    colors: {
                        sky: {
                            deep: '#0284c7',
                            soft: '#e0f2fe',
                        },
                        grape: {
                            deep: '#7c3aed',
                            soft: '#f3e8ff',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at 10% 0%, rgba(96, 165, 250, 0.20), transparent 35%),
                radial-gradient(circle at 90% 5%, rgba(253, 224, 71, 0.22), transparent 35%),
                radial-gradient(circle at 50% 100%, rgba(37, 99, 235, 0.12), transparent 40%),
                #f7fafc;
            min-height: 100vh;
        }
        .font-display { font-family: 'Fredoka', sans-serif; }
        .card { 
            background: #ffffff; 
            border: 1px solid #eef2f7; 
            box-shadow: 0 10px 30px -18px rgba(30, 41, 59, 0.25); 
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo / Brand Header -->
        <div class="flex justify-center mb-8">
            <img src="{{ asset('images/sang-cakap-logo.png') }}" alt="PuSaKap - Perpustakaan Sang Cakap" class="h-16 w-auto">
        </div>

        <!-- Glassmorphic / Dashboard Themed Login Card -->
        <div class="card rounded-[32px] p-8 sm:p-10 transition-all duration-300 hover:shadow-xl">
            <div class="mb-6 text-center">
                <h2 class="font-display text-2xl font-bold text-slate-800 tracking-tight">Login Admin</h2>
                <p class="text-xs text-slate-400 mt-1">Silakan masuk untuk mengelola perpustakaan</p>
            </div>

            <!-- Flash Message / Error Alerts -->
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-xs font-medium">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email field -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-slate-700 text-sm placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all duration-300"
                            placeholder="admin@email.com">
                    </div>
                </div>

                <!-- Password field -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-500 uppercase tracking-wider pl-1">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200/80 rounded-2xl text-slate-700 text-sm placeholder-slate-400 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-all duration-300"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-500 cursor-pointer select-none pl-1">
                        <input type="checkbox" name="remember" id="remember" class="accent-sky-600 h-4 w-4 rounded-md border-slate-300 bg-slate-50">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-sky-500 to-sky-600 hover:opacity-90 text-white font-bold text-sm rounded-full shadow-lg shadow-sky-500/20 active:scale-[0.99] transition-all duration-300">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-slate-400 text-[10px] mt-8 uppercase tracking-widest font-semibold">
            &copy; {{ date('Y') }} PuSaKap &middot; Perpustakaan Sang Cakap
        </p>
    </div>

</body>
</html>
