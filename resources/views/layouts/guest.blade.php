<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — ScholarFlow Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed">
    <!-- Top App Bar -->
    <header class="bg-surface flex justify-between items-center w-full px-6 py-4 fixed top-0 z-50">
        <div class="text-xl font-extrabold text-blue-700 font-headline tracking-tight">
            ScholarFlow Pro
        </div>
        <nav class="hidden md:flex gap-8 items-center">
            <a class="text-blue-700 font-bold font-label text-sm hover:bg-slate-100 transition-colors px-3 py-2 rounded-lg" href="/login">Login</a>
            <a class="text-slate-600 font-label text-sm hover:bg-slate-100 transition-colors px-3 py-2 rounded-lg" href="#">Help</a>
        </nav>
        <div class="md:hidden">
            <span class="material-symbols-outlined text-on-surface">menu</span>
        </div>
    </header>

    <main class="min-h-screen flex items-center justify-center pt-16 pb-24 px-4 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-primary-fixed opacity-20 rounded-full blur-[120px] -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-1/4 h-1/4 bg-tertiary-fixed opacity-15 rounded-full blur-[100px] -ml-20 -mb-20"></div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-transparent fixed bottom-0 w-full z-50">
        <div class="flex justify-center gap-8 py-6 w-full items-center">
            <span class="font-body text-xs uppercase tracking-wider text-slate-500">&copy; 2024 ScholarFlow Pro. Academic Precision System.</span>
            <div class="hidden md:flex gap-6">
                <a class="font-body text-xs uppercase tracking-wider text-slate-500 hover:text-blue-700 underline transition-colors" href="#">Privacy Policy</a>
                <a class="font-body text-xs uppercase tracking-wider text-slate-500 hover:text-blue-700 underline transition-colors" href="#">Terms of Service</a>
                <a class="font-body text-xs uppercase tracking-wider text-slate-500 hover:text-blue-700 underline transition-colors" href="#">Support</a>
            </div>
        </div>
    </footer>

    @vite(['resources/js/auth.js'])
    @stack('scripts')
</body>
</html>
