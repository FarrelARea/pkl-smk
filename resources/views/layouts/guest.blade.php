@php
    $loginHelpTargets = [
        '[data-help-target="login-form"]',
        '[data-help-target="email-field"]',
        '[data-help-target="password-field"]',
        '[data-help-target="remember-field"]',
        '[data-help-target="login-button"]',
        '[data-help-target="support-link"]',
        '[data-help-target="back-link"]',
    ];

    $loginHelpLabels = [
        'login-form' => 'Ini area utama untuk mengisi dan mengirim data login kamu.',
        'email-field' => 'Masukkan email akun yang sudah terdaftar di Simaskansa.',
        'password-field' => 'Masukkan password akun untuk membuka dashboard sesuai aksesmu.',
        'remember-field' => 'Aktifkan ini jika kamu ingin sesi login tetap tersimpan di perangkat ini.',
        'login-button' => 'Tekan tombol ini untuk masuk ke sistem setelah email dan password diisi.',
        'support-link' => 'Kalau belum punya akun atau tidak bisa masuk, hubungi admin sekolah.',
        'back-link' => 'Gunakan ini untuk kembali ke pilihan halaman login sebelumnya.',
    ];
@endphp

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', 'Login') — Simaskansa</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed">
    <!-- Top App Bar -->
    <header class="bg-surface/80 backdrop-blur-lg flex justify-between items-center w-full px-4 py-3 md:px-6 md:py-4 fixed top-0 z-50 safe-top">
        <div class="text-lg md:text-xl font-extrabold text-blue-700 font-headline tracking-tight">
            Simaskansa
        </div>
        <nav class="flex gap-2 md:gap-8 items-center">
            <a class="text-blue-700 font-bold font-label text-xs md:text-sm hover:bg-slate-100 active:bg-slate-200 transition-colors px-2.5 py-1.5 md:px-3 md:py-2 rounded-lg" href="/login">Login</a>
            <button type="button" data-help-open="guest-login-help" class="text-slate-600 font-label text-xs md:text-sm hover:bg-slate-100 active:bg-slate-200 transition-colors px-2.5 py-1.5 md:px-3 md:py-2 rounded-lg">Help</button>
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
            <span class="font-body text-[10px] md:text-xs uppercase tracking-wider text-slate-500">&copy; 2024 Simaskansa</span>
        </div>
    </footer>

    <x-help-button
        id="guest-login-help"
        title="Bantuan Login Simaskansa"
        :floating="true"
        button-class="safe-bottom"
        :targets="$loginHelpTargets"
        :target-labels="$loginHelpLabels"
    >
        <p>Simaskansa adalah sistem manajemen PKL untuk SMKN 1 Tanjungpandan yang membantu siswa, guru, pembimbing industri, dan admin mengelola kegiatan magang dari satu tempat.</p>
        <div class="space-y-3 rounded-xl bg-slate-50 p-4">
            <p class="font-semibold text-slate-800">Yang bisa dilakukan di aplikasi ini:</p>
            <ul class="space-y-2 text-sm text-slate-600">
                <li><span class="font-semibold text-slate-800">Siswa:</span> absensi, jurnal harian, izin, dan melihat evaluasi PKL.</li>
                <li><span class="font-semibold text-slate-800">Guru:</span> memantau perkembangan siswa bimbingan.</li>
                <li><span class="font-semibold text-slate-800">Pembimbing:</span> memonitor siswa di tempat industri.</li>
                <li><span class="font-semibold text-slate-800">Admin:</span> mengelola data pengguna, perusahaan, dan proses PKL.</li>
            </ul>
        </div>
        <div class="space-y-2 rounded-xl border border-blue-100 bg-blue-50/80 p-4">
            <p class="font-semibold text-slate-800">Penjelasan layar login:</p>
            <ul class="space-y-2 text-sm text-slate-600">
                <li><span class="font-semibold text-slate-800">Email</span> untuk akun yang sudah terdaftar.</li>
                <li><span class="font-semibold text-slate-800">Password</span> untuk masuk ke dashboard sesuai akun.</li>
                <li><span class="font-semibold text-slate-800">Ingat saya</span> menyimpan sesi login lebih lama di perangkat ini.</li>
                <li><span class="font-semibold text-slate-800">Masuk</span> mengirim data login ke sistem.</li>
                <li><span class="font-semibold text-slate-800">Hubungi admin / Kembali</span> membantu jika akun belum tersedia atau ingin pindah halaman login.</li>
            </ul>
        </div>
        <p class="text-xs text-slate-500">Tekan <span class="font-semibold text-slate-700">Tunjukkan di layar</span> untuk menyorot bagian penting di halaman login.</p>
    </x-help-button>

    @vite(['resources/js/auth.js'])
    @stack('scripts')
</body>
</html>
