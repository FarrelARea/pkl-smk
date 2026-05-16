@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-[480px] lg:max-w-[1100px] lg:grid lg:grid-cols-12 lg:gap-8 lg:items-center z-10">
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
    </div>

    <div class="lg:col-span-7 flex justify-center lg:justify-end">
        <div class="glass-panel ghost-shadow w-full rounded-2xl p-6 sm:p-8 lg:p-10 border border-outline-variant/10">
            <div class="mb-6 sm:mb-8 text-center lg:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-3 justify-center lg:justify-start">
                    <div class="w-12 h-12 sm:w-12 sm:h-12 rounded-xl bg-primary-fixed flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-[28px]">login</span>
                    </div>
                    <div class="text-center sm:text-left">
                        <h2 class="font-headline text-xl sm:text-2xl font-bold text-on-surface">Masuk ke akun Anda</h2>
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
                <p class="text-xs sm:text-sm text-on-surface-variant">
                    Belum punya akun?
                    <a class="text-primary font-bold hover:underline" href="#" data-help-target="support-link">Hubungi admin</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    if (Auth.redirectIfAuth()) {
        // redirecting...
    } else {
        const form = document.getElementById('login-form');
        const errorEl = document.getElementById('error-message');
        const loginBtn = document.getElementById('login-btn');

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
