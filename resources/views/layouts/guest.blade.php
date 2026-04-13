<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', 'Login') — SMKN1 Tanjungpandan</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed">
    <!-- Top App Bar -->
    <header class="bg-surface/80 backdrop-blur-lg flex justify-between items-center w-full px-4 py-3 md:px-6 md:py-4 fixed top-0 z-50 safe-top">
        <div class="text-lg md:text-xl font-extrabold text-blue-700 font-headline tracking-tight">
            SMKN1 Tanjungpandan
        </div>
        <nav class="flex gap-2 md:gap-8 items-center">
            <a class="text-blue-700 font-bold font-label text-xs md:text-sm hover:bg-slate-100 active:bg-slate-200 transition-colors px-2.5 py-1.5 md:px-3 md:py-2 rounded-lg" href="/login">Login</a>
            <a class="text-slate-600 font-label text-xs md:text-sm hover:bg-slate-100 active:bg-slate-200 transition-colors px-2.5 py-1.5 md:px-3 md:py-2 rounded-lg" href="#">Help</a>
        </nav>
    </header>

    <main class="min-h-[100dvh] flex items-center justify-center pt-14 pb-16 px-4 md:pt-16 md:pb-24 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 w-2/3 md:w-1/3 h-1/3 bg-primary-fixed opacity-20 rounded-full blur-[120px] -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-1/2 md:w-1/4 h-1/4 bg-tertiary-fixed opacity-15 rounded-full blur-[100px] -ml-20 -mb-20"></div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-transparent fixed bottom-0 w-full z-50 safe-bottom">
        <div class="flex justify-center gap-4 md:gap-8 py-3 md:py-6 w-full items-center px-4">
            <span class="font-body text-[10px] md:text-xs uppercase tracking-wider text-slate-500">&copy; 2024 SMKN1 Tanjungpandan</span>
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
