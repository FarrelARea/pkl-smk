<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', 'Login') — Simaskansa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Fredoka', sans-serif;
            background: url('{{ asset('images/background-simaskansa.png') }}') no-repeat center center;
            background-size: cover;
            padding:0;
            background-position: center;
        }

        #login-btn::after {
            content: '';
            position: absolute;
            right: 25px;
            top: 50%;
            transform: translateY(-50%);
            height: 110px;
            width: auto;
            aspect-ratio: 1;
            background: url('/images/papperplane.png') no-repeat center center;
            background-size: contain;
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface selection:bg-primary-fixed">
    <div class="min-h-screen flex relative">
        <!-- Top-left Logo -->
        <div class="absolute top-4 left-4 z-10 bg-white/90 backdrop-blur rounded-xl p-2 shadow-lg">
            <img src="{{ asset('images/logo-simaskansa-removed.png') }}"
                 alt="Simaskansa"
                 style="width: 240px; height: auto; display: block;">
        </div>

        <!-- Papperplane -->
        <img src="{{ asset('images/papperplane-long.png') }}"
             alt=""
             class="absolute left-0 z-10"
             style="top: 100px; width: 200px; height: auto;">

        <!-- Left Panel - Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative items-start justify-center">
            <div class="flex flex-col items-start max-w-lg mx-12 mt-[11.5vh]">
                <!-- "Sistem" -->
                <span class="text-[#061f58] text-[40px] font-semibold tracking-wide">
                    Sistem
                </span>

                <!-- "Manajemen PKL" -->
                <h1 class="text-[#0542e9] text-[40px] font-semibold relative w-fit">
                    Manajemen PKL
                    <img src="/images/top-right-splatter.png" class="absolute -top-3 -right-10 w-10 h-10 object-contain pointer-events-none select-none">
                </h1>

                <!-- Yellow accent line -->
                <div class="w-20 h-1 bg-yellow-400 rounded-full"></div>

                <!-- Tagline -->
                <p class="text-[#0c1368] text-[24px] max-w-md">
                    Menghubungkan dunia pendidikan<br>
                    dan industri. Kelola magang dengan<br>
                    mudah, tepat, dan profesional
                </p>

                <!-- Logos -->
                    <div class="flex items-center gap-6">
                        <img src="{{ asset('images/logo-provinsi.png') }}"
                             alt="Logo Provinsi"
                             class="h-[200px] w-auto object-contain">
                        <div class="w-px h-[200px] bg-gray-300"></div>
                        <img src="{{ asset('images/logo-smkn.png') }}"
                             alt="Logo SMKN"
                             class="h-[200px] w-auto object-contain">
                    </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center p-4 lg:pr-[5vw] lg:pl-0">
            <div class="bg-white rounded-2xl shadow-2xl p-[5%] h-fit">
                <div class="w-full">
                <div class="mb-8">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-center w-20 h-20 bg-blue-50 rounded-2xl shrink-0">
                            <span class="material-symbols-outlined text-blue-600" style="font-size:48px">login</span>
                        </div>
                        <div>
                            <h2 class="text-[36px] font-bold text-[#091e5d] leading-tight relative w-fit">
                                Hai! Selamat Datang
                                <img src="/images/top-right-splatter.png" class="absolute -top-4 -right-10 w-12 h-12 object-contain pointer-events-none select-none">
                            </h2>
                            <p class="text-[24px] text-[#091e5d] leading-snug opacity-70">Masuk ke akun SIMASKANSA kamu</p>
                        </div>
                    </div>
                </div>

                <div id="error-message" class="hidden mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-base"></div>

                <form id="login-form" data-help-target="login-form" class="space-y-5">
                    <div class="space-y-2" data-help-target="email-field">
                        <label class="font-semibold text-base text-gray-700" for="email">Email</label>
                        <div class="flex items-center h-16 bg-white border border-gray-200 rounded-xl focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:border-blue-500 transition-all">
                            <div class="flex items-center justify-center w-12 h-12 m-1.5 bg-blue-50 rounded-lg shrink-0">
                                <span class="material-symbols-outlined text-blue-600 text-xl">mail</span>
                            </div>
                            <input class="flex-1 pr-4 outline-none placeholder:text-gray-400 text-base bg-transparent" id="email" placeholder="nama@institusi.ac.id" type="email" inputmode="email" autocomplete="email" required>
                        </div>
                    </div>

                    <div class="space-y-2" data-help-target="password-field">
                        <div class="flex justify-between items-center gap-4">
                            <label class="font-semibold text-base text-gray-700" for="password">Password</label>
                            <a class="text-base font-medium text-blue-600 hover:underline shrink-0" href="#">Lupa kata sandi?</a>
                        </div>
                        <div class="flex items-center h-16 bg-white border border-gray-200 rounded-xl focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:border-blue-500 transition-all">
                            <div class="flex items-center justify-center w-12 h-12 m-1.5 bg-blue-50 rounded-lg shrink-0">
                                <span class="material-symbols-outlined text-blue-600 text-xl">lock</span>
                            </div>
                            <input class="flex-1 pr-4 outline-none placeholder:text-gray-400 text-base bg-transparent tracking-[0.2em]" id="password" placeholder="••••••••" type="password" autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="flex items-center" data-help-target="remember-field">
                        <input id="remember" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-50 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="remember" class="ml-2 text-base text-gray-600">Ingat saya selama 30 hari</label>
                    </div>

                    <button type="submit" id="login-btn" data-help-target="login-button"
                        class="w-full h-20 relative flex items-center justify-center gap-2 text-white font-bold italic text-[36px] uppercase tracking-wider font-['Poppins',sans-serif] rounded-2xl shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/35 hover:brightness-110 active:scale-[0.98] active:opacity-90 transition-all duration-200 ease-out"
                        style="background: linear-gradient(to top left, rgba(0,133,237,0.45) 0%, rgba(0,133,237,0.1) 35%, transparent 50%), linear-gradient(105deg, #0058e6 75%, #3b82f6 90%, #0085ed 95%);">
                        Masuk
                    </button>
                </form>

                <div class="mt-8">
                    <p class="flex items-center justify-center gap-6 text-gray-400 text-[18px]">
                        <span class="h-px flex-1 max-w-[20%] bg-gray-300"></span>
                        <span class="shrink-0 font-semibold">© 2026 SIMASKANSA ✦</span>
                        <span class="h-px flex-1 max-w-[20%] bg-gray-300"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

    @vite(['resources/js/auth.js'])
    <script type="module">
        if (Auth.redirectIfAuth()) {
            // redirecting...
        } else {
            const form = document.getElementById('login-form');
            const errorEl = document.getElementById('error-message');
            const loginBtn = document.getElementById('login-btn');
            const rememberCheckbox = document.getElementById('remember');

            // Restore remembered email
            const rememberedEmail = localStorage.getItem('remembered_email');
            if (rememberedEmail) {
                document.getElementById('email').value = rememberedEmail;
                rememberCheckbox.checked = true;
            }

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
                        throw new Error(data.message || data.error || 'Email atau password salah');
                    }

                    // Handle remember me
                    if (rememberCheckbox.checked) {
                        localStorage.setItem('remembered_email', document.getElementById('email').value);
                    } else {
                        localStorage.removeItem('remembered_email');
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
</body>
</html>
