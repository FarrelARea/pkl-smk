@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-[1100px] grid grid-cols-1 lg:grid-cols-12 gap-8 items-center z-10">
    <!-- Left Side: Branding/Identity -->
    <div class="hidden lg:flex lg:col-span-5 flex-col space-y-6 pr-12">
        <div class="space-y-2">
            <span class="font-label text-xs uppercase tracking-[0.1rem] text-primary font-bold">The Digital Curator</span>
            <h1 class="font-headline text-5xl font-extrabold leading-[1.1] text-on-surface">
                Academic Precision System.
            </h1>
        </div>
        <p class="text-on-surface-variant text-lg leading-relaxed max-w-md">
            Seamlessly bridging the gap between academia and industry. Manage internships with editorial clarity and professional rigor.
        </p>
        <div class="flex items-center gap-4 pt-4">
            <div class="flex -space-x-3">
                <img class="w-10 h-10 rounded-full border-2 border-surface shadow-sm object-cover" alt="Student" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3tOjsKDXA57xhXCH9jbSrXrZA5efLjOwtQMYzCLRSXevWOxm1WlMGZP4Q3phLlDuf2YkM4amK9elQaQIJTABowQ1_3PDNuMCQsoPe5tY8f6HLOwvwkr9LfaxJYdnhqwxM3AzcOFmgN2qyEYRwPtysBFeoFCPrSduJHYctulTXPQok3RXqorvJr-ibbjUWHKwHneGjxH-gaoz5mFBIDWvM_Ea8DUuDvMijittRpeQurBA0RzOwQnxV9UftKZ6k9YF9tfnzDXyUPeU">
                <img class="w-10 h-10 rounded-full border-2 border-surface shadow-sm object-cover" alt="Teacher" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAIlm6W8Uyg4rMidi3YuvUDpd_4_dj2AWLdbp_U36zMwqC2uwKarQCYMtWhjSjYwL8_7MY7zy1kRKfihjM1i5yklr-PjFAQAPNHciGkWoJnUHt6JFe84-zAFh2QOBNgzFM3Jeby7UoDRvT5dgFtqmyaxJ9d2KziIXo_10Xvt2dbdayTAOZeeKRd2QhbNtDVJzjZTw3OjOUsbszR6C2QO48xuXkUlfW46DPccH0lGC8pnl2RGlPQhMWyBInIz-uIN_MwARJYD6Q5qSI">
                <img class="w-10 h-10 rounded-full border-2 border-surface shadow-sm object-cover" alt="Supervisor" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDcYQ5TJX0sjl-fxpSU9hldyJFz-tYwP5ooLziS5rbI1YTzWZdap38GYwjZw2rt9YbRKAjRaSc3oba-h_p4ZyrNGMK7wObmhFNJK6Obipq35BC1PxIc9DY1Yl0F4vvhpMPju8hIOW3FKjCggOHUib7WwxvH6SKz0BXd51MulBAsaviVvlnARxbCAwvv88vo-WQL9uXDD2Bk_AauTffhcS02UgMQBjWozPqsEtt2HekbxHUHiUIt-1jqguuWMAtrgFC0ze6IdUjsQw0">
            </div>
            <span class="text-sm font-medium text-on-surface-variant italic">Trusted by 2,000+ Institutions</span>
        </div>
    </div>

    <!-- Right Side: Login Card -->
    <div class="lg:col-span-7 flex justify-center lg:justify-end">
        <div class="glass-panel ghost-shadow w-full max-w-[480px] rounded-xl p-8 lg:p-10 border border-outline-variant/10">
            <div class="mb-10 text-center lg:text-left">
                <h2 class="font-headline text-2xl font-bold text-on-surface mb-2">Welcome Back</h2>
                <p class="text-on-surface-variant font-label text-sm uppercase tracking-wide">Enter your credentials to continue</p>
            </div>

            <div id="error-message" class="hidden mb-4 p-3 bg-error-container border border-error/20 text-on-error-container rounded-lg text-sm"></div>

            <!-- Role Selector -->
            <div class="mb-8">
                <label class="font-label text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant mb-4 block">Select Your Role</label>
                <div class="grid grid-cols-2 gap-3" id="role-selector">
                    <button type="button" data-role="student" class="role-btn flex flex-col items-center gap-2 p-3 rounded-lg bg-surface-container-low hover:bg-primary-fixed border border-transparent transition-all group">
                        <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">school</span>
                        <span class="text-xs font-semibold">Student</span>
                    </button>
                    <button type="button" data-role="teacher" class="role-btn flex flex-col items-center gap-2 p-3 rounded-lg bg-surface-container-low hover:bg-primary-fixed border border-transparent transition-all group">
                        <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">history_edu</span>
                        <span class="text-xs font-semibold">Teacher</span>
                    </button>
                    <button type="button" data-role="company_supervisor" class="role-btn flex flex-col items-center gap-2 p-3 rounded-lg bg-surface-container-low hover:bg-primary-fixed border border-transparent transition-all group">
                        <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">business</span>
                        <span class="text-xs font-semibold">Supervisor</span>
                    </button>
                    <button type="button" data-role="school_admin" class="role-btn flex flex-col items-center gap-2 p-3 rounded-lg bg-surface-container-low hover:bg-primary-fixed border border-transparent transition-all group">
                        <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">admin_panel_settings</span>
                        <span class="text-xs font-semibold">Admin</span>
                    </button>
                </div>
                <input type="hidden" id="selected-role" name="role" value="">
            </div>

            <form id="login-form" class="space-y-6">
                <div class="space-y-1.5">
                    <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant ml-1" for="email">Work Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">mail</span>
                        <input class="w-full pl-12 pr-4 py-3 bg-surface-container-highest border border-outline-variant/20 rounded-lg focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-outline/60 text-sm" id="email" placeholder="name@institution.edu" type="email" required>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center px-1">
                        <label class="font-label text-xs font-bold uppercase tracking-wider text-on-surface-variant" for="password">Security Key</label>
                        <a class="text-xs font-bold text-primary hover:underline" href="#">Forgot?</a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">lock</span>
                        <input class="w-full pl-12 pr-4 py-3 bg-surface-container-highest border border-outline-variant/20 rounded-lg focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all placeholder:text-outline/60 text-sm" id="password" placeholder="••••••••" type="password" required>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-1">
                    <input class="w-4 h-4 rounded-sm border-outline-variant text-primary focus:ring-primary" id="remember" type="checkbox">
                    <label class="text-sm text-on-surface-variant select-none" for="remember">Keep me authenticated for 30 days</label>
                </div>
                <button type="submit" id="login-btn" class="w-full primary-gradient text-white py-4 rounded-lg font-headline font-bold text-sm uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all">
                    Initialize Session
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-outline-variant/10 text-center">
                <p class="text-sm text-on-surface-variant">
                    New to the system?
                    <a class="text-primary font-bold hover:underline" href="#">Request access</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    // Role selector toggle
    const roleButtons = document.querySelectorAll('.role-btn');
    const roleInput = document.getElementById('selected-role');

    roleButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Reset all buttons
            roleButtons.forEach(b => {
                b.classList.remove('bg-primary-fixed', 'text-on-primary-fixed', 'border-primary/20');
                b.classList.add('bg-surface-container-low', 'hover:bg-primary-fixed', 'border-transparent');
                const icon = b.querySelector('.material-symbols-outlined');
                icon.style.fontVariationSettings = '';
            });
            // Highlight selected
            btn.classList.add('bg-primary-fixed', 'text-on-primary-fixed', 'border-primary/20');
            btn.classList.remove('bg-surface-container-low', 'hover:bg-primary-fixed', 'border-transparent');
            const icon = btn.querySelector('.material-symbols-outlined');
            icon.style.fontVariationSettings = '"FILL" 1';
            roleInput.value = btn.dataset.role;
        });
    });

    // Redirect if already authenticated
    if (Auth.redirectIfAuth()) {
        // redirecting...
    } else {
        const form = document.getElementById('login-form');
        const errorEl = document.getElementById('error-message');
        const loginBtn = document.getElementById('login-btn');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorEl.classList.add('hidden');

            if (!roleInput.value) {
                errorEl.textContent = 'Please select your role before logging in.';
                errorEl.classList.remove('hidden');
                return;
            }

            loginBtn.disabled = true;
            loginBtn.textContent = 'Authenticating...';

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
                    throw new Error(data.message || 'Invalid email or password');
                }

                Auth.setToken(data.access_token);
                window.location.href = '/dashboard';
            } catch (err) {
                errorEl.textContent = err.message;
                errorEl.classList.remove('hidden');
                loginBtn.disabled = false;
                loginBtn.textContent = 'Initialize Session';
            }
        });
    }
</script>
@endpush
