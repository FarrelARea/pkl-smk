@extends('layouts.guest')

@section('title', 'Login ' . $roleLabel)

@section('content')
<div class="w-full max-w-[480px] lg:max-w-[1100px] lg:grid lg:grid-cols-12 lg:gap-8 lg:items-center z-10">
    <!-- Left Side: Branding (desktop only) -->
    <div class="hidden lg:flex lg:col-span-5 flex-col space-y-6 pr-12">
        <div class="space-y-2">
            <span class="font-label text-xs uppercase tracking-[0.1rem] text-primary font-bold">Simaskansa</span>
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

    <!-- Login Card -->
    <div class="lg:col-span-7 flex justify-center lg:justify-end">
        <div class="glass-panel ghost-shadow w-full rounded-2xl p-6 sm:p-8 lg:p-10 border border-outline-variant/10">
            <!-- Role badge + title -->
            <div class="mb-6 sm:mb-8 text-center lg:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-3 justify-center lg:justify-start">
                    <div class="w-12 h-12 sm:w-12 sm:h-12 rounded-xl bg-primary-fixed flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-[28px]">{{ $roleIcon }}</span>
                    </div>
                    <div class="text-center sm:text-left">
                        <h2 class="font-headline text-xl sm:text-2xl font-bold text-on-surface">Login {{ $roleLabel }}</h2>
                        <p class="text-on-surface-variant font-label text-xs sm:text-sm uppercase tracking-wide">Masukkan data login kamu</p>
                    </div>
                </div>
            </div>

            <div id="error-message" class="hidden mb-4 p-3 bg-error-container border border-error/20 text-on-error-container rounded-lg text-xs sm:text-sm"></div>

            <form id="login-form" data-help-target="login-form" class="space-y-4 sm:space-y-6">
                <div class="space-y-1.5" data-help-target="email-field">
                    <label class="font-label text-[11px] sm:text-xs font-bold uppercase tracking-wider text-on-surface-variant ml-1" for="email">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">mail</span>
                        <input class="w-full pl-10 sm:pl-12 pr-4 py-3 sm:py-3 bg-surface-container-highest border border-outline-variant/20 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-outline/60 text-sm" id="email" placeholder="nama@institusi.ac.id" type="email" inputmode="email" autocomplete="email" required>
                    </div>
                </div>
                <div class="space-y-1.5" data-help-target="password-field">
                    <div class="flex justify-between items-center px-1">
                        <label class="font-label text-[11px] sm:text-xs font-bold uppercase tracking-wider text-on-surface-variant" for="password">Password</label>
                        <a class="text-[11px] sm:text-xs font-bold text-primary hover:underline active:opacity-70" href="#">Lupa?</a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">lock</span>
                        <input class="w-full pl-10 sm:pl-12 pr-4 py-3 sm:py-3 bg-surface-container-highest border border-outline-variant/20 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-outline/60 text-sm" id="password" placeholder="••••••••" type="password" autocomplete="current-password" required>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-1" data-help-target="remember-field">
                    <input class="w-5 h-5 sm:w-4 sm:h-4 rounded-sm border-outline-variant text-primary focus:ring-primary" id="remember" type="checkbox">
                    <label class="text-xs sm:text-sm text-on-surface-variant select-none" for="remember">Ingat saya selama 30 hari</label>
                </div>
                <button type="submit" id="login-btn" data-help-target="login-button" class="w-full primary-gradient text-white py-3.5 sm:py-4 rounded-xl font-headline font-bold text-sm uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] active:opacity-80 transition-all">
                    Masuk
                </button>
            </form>

            <div class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-outline-variant/10 text-center">
                <a class="text-xs sm:text-sm text-on-surface-variant hover:text-primary active:text-primary/70 transition-colors inline-flex items-center gap-1 py-2" href="/login" data-help-target="back-link">
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">arrow_back</span>
                    Kembali ke pilihan role
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    // Redirect if already authenticated
    if (Auth.redirectIfAuth()) {
        // redirecting...
    } else {
        const form = document.getElementById('login-form');
        const errorEl = document.getElementById('error-message');
        const loginBtn = document.getElementById('login-btn');
        const role = @json($role);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorEl.classList.add('hidden');

            loginBtn.disabled = true;
            loginBtn.textContent = 'Memproses...';

            try {
                const res = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                    }),
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'Email atau password salah');
                }

                Auth.setToken(data.access_token);
                window.location.href = '/dashboard';
            } catch (err) {
                errorEl.textContent = err.message;
                errorEl.classList.remove('hidden');
                loginBtn.disabled = false;
                loginBtn.textContent = 'Masuk';
            }
        });
    }
</script>
@endpush
