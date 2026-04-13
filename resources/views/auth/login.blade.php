@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-[480px] lg:max-w-[1100px] lg:grid lg:grid-cols-12 lg:gap-8 lg:items-center z-10">
    <!-- Left Side: Branding (desktop only) -->
    <div class="hidden lg:flex lg:col-span-5 flex-col space-y-6 pr-12">
        <div class="space-y-2">
            <span class="font-label text-xs uppercase tracking-[0.1rem] text-primary font-bold">The Digital Curator</span>
            <h1 class="font-headline text-5xl font-extrabold leading-[1.1] text-on-surface">
                Sistem Manajemen PKL.
            </h1>
        </div>
        <p class="text-on-surface-variant text-lg leading-relaxed max-w-md">
            Menghubungkan dunia pendidikan dan industri. Kelola magang dengan mudah dan profesional.
        </p>
        <div class="flex items-center gap-4 pt-4">
            <div class="flex -space-x-3">
                <img class="w-10 h-10 rounded-full border-2 border-surface shadow-sm object-cover" alt="Student" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3tOjsKDXA57xhXCH9jbSrXrZA5efLjOwtQMYzCLRSXevWOxm1WlMGZP4Q3phLlDuf2YkM4amK9elQaQIJTABowQ1_3PDNuMCQsoPe5tY8f6HLOwvwkr9LfaxJYdnhqwxM3AzcOFmgN2qyEYRwPtysBFeoFCPrSduJHYctulTXPQok3RXqorvJr-ibbjUWHKwHneGjxH-gaoz5mFBIDWvM_Ea8DUuDvMijittRpeQurBA0RzOwQnxV9UftKZ6k9YF9tfnzDXyUPeU">
                <img class="w-10 h-10 rounded-full border-2 border-surface shadow-sm object-cover" alt="Teacher" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAIlm6W8Uyg4rMidi3YuvUDpd_4_dj2AWLdbp_U36zMwqC2uwKarQCYMtWhjSjYwL8_7MY7zy1kRKfihjM1i5yklr-PjFAQAPNHciGkWoJnUHt6JFe84-zAFh2QOBNgzFM3Jeby7UoDRvT5dgFtqmyaxJ9d2KziIXo_10Xvt2dbdayTAOZeeKRd2QhbNtDVJzjZTw3OjOUsbszR6C2QO48xuXkUlfW46DPccH0lGC8pnl2RGlPQhMWyBInIz-uIN_MwARJYD6Q5qSI">
                <img class="w-10 h-10 rounded-full border-2 border-surface shadow-sm object-cover" alt="Supervisor" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDcYQ5TJX0sjl-fxpSU9hldyJFz-tYwP5ooLziS5rbI1YTzWZdap38GYwjZw2rt9YbRKAjRaSc3oba-h_p4ZyrNGMK7wObmhFNJK6Obipq35BC1PxIc9DY1Yl0F4vvhpMPju8hIOW3FKjCggOHUib7WwxvH6SKz0BXd51MulBAsaviVvlnARxbCAwvv88vo-WQL9uXDD2Bk_AauTffhcS02UgMQBjWozPqsEtt2HekbxHUHiUIt-1jqguuWMAtrgFC0ze6IdUjsQw0">
            </div>
            <span class="text-sm font-medium text-on-surface-variant italic">Dipercaya 2.000+ Institusi</span>
        </div>
    </div>

    <!-- Role Selection Card -->
    <div class="lg:col-span-7 flex justify-center lg:justify-end">
        <div class="glass-panel ghost-shadow w-full rounded-2xl p-6 sm:p-8 lg:p-10 border border-outline-variant/10">
            <!-- Mobile branding -->
            <div class="flex flex-col items-center mb-6 lg:hidden">
                <div class="w-14 h-14 rounded-2xl bg-primary-fixed flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-primary text-[32px]">school</span>
                </div>
                <h1 class="font-headline text-xl font-extrabold text-on-surface">Sistem PKL</h1>
            </div>

            <div class="mb-6 sm:mb-8 text-center lg:text-left">
                <h2 class="font-headline text-xl sm:text-2xl font-bold text-on-surface mb-1">Selamat Datang</h2>
                <p class="text-on-surface-variant font-label text-xs sm:text-sm uppercase tracking-wide">Pilih role kamu untuk login</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <a href="/login/student" class="flex flex-col items-center gap-2 sm:gap-3 p-4 sm:p-6 rounded-xl bg-surface-container-low hover:bg-primary-fixed active:bg-primary-fixed border border-outline-variant/10 hover:border-primary/20 transition-all group">
                    <span class="material-symbols-outlined text-primary text-[28px] sm:text-[36px] group-hover:scale-110 transition-transform">school</span>
                    <span class="text-xs sm:text-sm font-bold text-on-surface">Siswa</span>
                    <span class="text-[10px] sm:text-xs text-on-surface-variant text-center leading-tight">Login sebagai siswa PKL</span>
                </a>
                <a href="/login/teacher" class="flex flex-col items-center gap-2 sm:gap-3 p-4 sm:p-6 rounded-xl bg-surface-container-low hover:bg-primary-fixed active:bg-primary-fixed border border-outline-variant/10 hover:border-primary/20 transition-all group">
                    <span class="material-symbols-outlined text-primary text-[28px] sm:text-[36px] group-hover:scale-110 transition-transform">history_edu</span>
                    <span class="text-xs sm:text-sm font-bold text-on-surface">Guru</span>
                    <span class="text-[10px] sm:text-xs text-on-surface-variant text-center leading-tight">Login sebagai guru pembimbing</span>
                </a>
                <a href="/login/supervisor" class="flex flex-col items-center gap-2 sm:gap-3 p-4 sm:p-6 rounded-xl bg-surface-container-low hover:bg-primary-fixed active:bg-primary-fixed border border-outline-variant/10 hover:border-primary/20 transition-all group">
                    <span class="material-symbols-outlined text-primary text-[28px] sm:text-[36px] group-hover:scale-110 transition-transform">business</span>
                    <span class="text-xs sm:text-sm font-bold text-on-surface">Pembimbing</span>
                    <span class="text-[10px] sm:text-xs text-on-surface-variant text-center leading-tight">Pembimbing industri</span>
                </a>
                <a href="/login/admin" class="flex flex-col items-center gap-2 sm:gap-3 p-4 sm:p-6 rounded-xl bg-surface-container-low hover:bg-primary-fixed active:bg-primary-fixed border border-outline-variant/10 hover:border-primary/20 transition-all group">
                    <span class="material-symbols-outlined text-primary text-[28px] sm:text-[36px] group-hover:scale-110 transition-transform">admin_panel_settings</span>
                    <span class="text-xs sm:text-sm font-bold text-on-surface">Admin</span>
                    <span class="text-[10px] sm:text-xs text-on-surface-variant text-center leading-tight">Admin sekolah</span>
                </a>
            </div>

            <div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-outline-variant/10 text-center">
                <p class="text-xs sm:text-sm text-on-surface-variant">
                    Belum punya akun?
                    <a class="text-primary font-bold hover:underline" href="#">Hubungi admin</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
