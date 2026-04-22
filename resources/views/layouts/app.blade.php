<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Academia Curator</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-on-surface">
    <!-- Simplified Top Navigation Bar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-xl shadow-[0px_12px_32px_rgba(25,28,30,0.06)] flex items-center px-6 h-16">
        <span class="text-xl font-bold text-slate-900 font-headline">Academia Curator</span>
        <!-- Mobile hamburger -->
        <button id="sidebar-toggle" class="ml-auto md:hidden p-2 text-slate-500 hover:bg-slate-50 rounded-full">
            <span class="material-symbols-outlined">menu</span>
        </button>
    </nav>

    <!-- Side Navigation Bar -->
    <aside id="sidebar" class="h-screen w-64 fixed left-0 top-0 bg-slate-50 flex flex-col pt-20 pb-6 font-headline text-sm font-medium z-40 transition-transform duration-300 max-md:-translate-x-full max-md:shadow-2xl">
        <div class="px-6 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-container rounded-lg flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">school</span>
                </div>
                <div>
                    <h3 class="text-lg font-black text-blue-800 leading-tight">Curator Portal</h3>
                    <p class="text-xs text-slate-500">Manajemen Akademik</p>
                </div>
            </div>
        </div>
        <nav class="flex-1 overflow-y-auto" id="sidebar-nav">
            <!-- Overview Group -->
            <div class="sidebar-group" data-group="overview">
                <button class="sidebar-group-header w-full flex items-center justify-between px-4 py-2 text-slate-500 hover:bg-slate-200/50">
                    <span class="text-xs font-bold uppercase tracking-wider">Ringkasan</span>
                    <span class="material-symbols-outlined text-sm chevron transition-transform duration-200">expand_more</span>
                </button>
                <div class="sidebar-group-content hidden">
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('dashboard*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/dashboard">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Student Menu (visible only for students) -->
            <div class="sidebar-group student-only hidden" data-group="student-menu">
                <button class="sidebar-group-header w-full flex items-center justify-between px-4 py-2 text-slate-500 hover:bg-slate-200/50">
                    <span class="text-xs font-bold uppercase tracking-wider">Magang Saya</span>
                    <span class="material-symbols-outlined text-sm chevron transition-transform duration-200">expand_more</span>
                </button>
                <div class="sidebar-group-content hidden">
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 text-slate-600 hover:bg-slate-200/50 cursor-pointer transition-all duration-200 hover:translate-x-1" href="/student/attendance">
                        <span class="material-symbols-outlined">fact_check</span>
                        <span>Riwayat Kehadiran</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 text-slate-600 hover:bg-slate-200/50 cursor-pointer transition-all duration-200 hover:translate-x-1" href="/student/daily-logs">
                        <span class="material-symbols-outlined">description</span>
                        <span>Daily Log</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 text-slate-600 hover:bg-slate-200/50 cursor-pointer transition-all duration-200 hover:translate-x-1" href="/student/evaluations">
                        <span class="material-symbols-outlined">rate_review</span>
                        <span>Penilaian</span>
                    </a>
                </div>
            </div>

            <!-- Supervisor Menu (visible only for company supervisors) -->
            <div class="sidebar-group supervisor-only hidden" data-group="supervisor-menu">
                <button class="sidebar-group-header w-full flex items-center justify-between px-4 py-2 text-slate-500 hover:bg-slate-200/50">
                    <span class="text-xs font-bold uppercase tracking-wider">Perusahaan</span>
                    <span class="material-symbols-outlined text-sm chevron transition-transform duration-200">expand_more</span>
                </button>
                <div class="sidebar-group-content hidden">
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('supervisor/attendance-points*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/supervisor/attendance-points">
                        <span class="material-symbols-outlined">location_on</span>
                        <span>Titik Absensi</span>
                    </a>
                </div>
            </div>

            <!-- Teacher Menu (visible only for teachers) -->
            <div class="sidebar-group teacher-only hidden" data-group="teacher-menu">
                <button class="sidebar-group-header w-full flex items-center justify-between px-4 py-2 text-slate-500 hover:bg-slate-200/50">
                    <span class="text-xs font-bold uppercase tracking-wider">Magang</span>
                    <span class="material-symbols-outlined text-sm chevron transition-transform duration-200">expand_more</span>
                </button>
                <div class="sidebar-group-content hidden">
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('teacher/attendance-points*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/teacher/attendance-points">
                        <span class="material-symbols-outlined">location_on</span>
                        <span>Titik Absensi</span>
                    </a>
                </div>
            </div>

            <!-- Academic Group (admin only) -->
            <div class="sidebar-group admin-only hidden" data-group="academic">
                <button class="sidebar-group-header w-full flex items-center justify-between px-4 py-2 text-slate-500 hover:bg-slate-200/50">
                    <span class="text-xs font-bold uppercase tracking-wider">Akademik</span>
                    <span class="material-symbols-outlined text-sm chevron transition-transform duration-200">expand_more</span>
                </button>
                <div class="sidebar-group-content hidden">
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/schools*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/schools">
                        <span class="material-symbols-outlined">account_balance</span>
                        <span>Sekolah</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/classes*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/classes">
                        <span class="material-symbols-outlined">class</span>
                        <span>Kelas</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/teachers*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/teachers">
                        <span class="material-symbols-outlined">person</span>
                        <span>Guru</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/students*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/students">
                        <span class="material-symbols-outlined">school</span>
                        <span>Siswa</span>
                    </a>
                </div>
            </div>

            <!-- Internship Group (admin only) -->
            <div class="sidebar-group admin-only hidden" data-group="internship">
                <button class="sidebar-group-header w-full flex items-center justify-between px-4 py-2 text-slate-500 hover:bg-slate-200/50">
                    <span class="text-xs font-bold uppercase tracking-wider">Magang</span>
                    <span class="material-symbols-outlined text-sm chevron transition-transform duration-200">expand_more</span>
                </button>
                <div class="sidebar-group-content hidden">
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/companies*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/companies">
                        <span class="material-symbols-outlined">business</span>
                        <span>Perusahaan</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/supervisors*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/supervisors">
                        <span class="material-symbols-outlined">supervisor_account</span>
                        <span>Pembimbing Lapangan</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/internships*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/internships">
                        <span class="material-symbols-outlined">work</span>
                        <span>Magang</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/daily-logs*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/daily-logs">
                        <span class="material-symbols-outlined">description</span>
                        <span>Daily Log</span>
                    </a>
                </div>
            </div>

            <!-- Monitoring Group (admin only) -->
            <div class="sidebar-group admin-only hidden" data-group="monitoring">
                <button class="sidebar-group-header w-full flex items-center justify-between px-4 py-2 text-slate-500 hover:bg-slate-200/50">
                    <span class="text-xs font-bold uppercase tracking-wider">Pemantauan</span>
                    <span class="material-symbols-outlined text-sm chevron transition-transform duration-200">expand_more</span>
                </button>
                <div class="sidebar-group-content hidden">
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/attendance*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/attendance">
                        <span class="material-symbols-outlined">fact_check</span>
                        <span>Kehadiran</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/evaluations*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/evaluations">
                        <span class="material-symbols-outlined">rate_review</span>
                        <span>Penilaian</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/assessment-templates*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/assessment-templates">
                        <span class="material-symbols-outlined">assignment</span>
                        <span>Template Penilaian</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/teacher-assignments*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/teacher-assignments">
                        <span class="material-symbols-outlined">assignment_ind</span>
                        <span>Penugasan Guru</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/teacher-company-assignments*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/teacher-company-assignments">
                        <span class="material-symbols-outlined">business_center</span>
                        <span>Penugasan Perusahaan</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-2 pl-8 {{ request()->is('admin/attendance-points*') ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:bg-slate-200/50' }} cursor-pointer transition-all duration-200 hover:translate-x-1" href="/admin/attendance-points">
                        <span class="material-symbols-outlined">location_on</span>
                        <span>Titik Absensi</span>
                    </a>
                </div>
            </div>
        </nav>
        <div class="mt-auto pt-6 border-t border-slate-200/50 space-y-1">
            <div class="px-4 mb-3 mx-2">
                <p id="user-name" class="font-semibold text-sm text-on-surface truncate"></p>
                <p id="user-role" class="text-xs text-slate-500"></p>
            </div>
            <button id="logout-btn" class="flex items-center gap-3 px-4 py-2 text-slate-600 hover:bg-slate-200/50 mx-2 rounded-lg cursor-pointer transition-all duration-200 hover:translate-x-1 w-full">
                <span class="material-symbols-outlined">logout</span>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <!-- Sidebar overlay for mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-30 hidden md:hidden"></div>

    <!-- Main Content -->
    <main class="ml-0 md:ml-64 pt-20 px-8 pb-12 min-h-screen">
        @yield('content')
    </main>

    @vite(['resources/js/auth.js', 'resources/js/admin-utils.js'])
    @stack('scripts')
    <script>
        // Sidebar toggle for mobile
        const toggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (toggle && sidebar && overlay) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('max-md:-translate-x-full');
                overlay.classList.toggle('hidden');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.add('max-md:-translate-x-full');
                overlay.classList.add('hidden');
            });
        }

        // Collapsible sidebar groups
        document.querySelectorAll('.sidebar-group-header').forEach(header => {
            header.addEventListener('click', () => {
                const group = header.closest('.sidebar-group');
                const content = group.querySelector('.sidebar-group-content');
                const chevron = group.querySelector('.chevron');

                content.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });
        });

        // Auto-expand group containing current page
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar-group').forEach(group => {
            const activeLink = group.querySelector(`a[href="${currentPath}"], a[href="${currentPath}/}"]`);
            if (activeLink) {
                const content = group.querySelector('.sidebar-group-content');
                const chevron = group.querySelector('.chevron');
                content.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            }
        });

        // Role-based menu filtering
        function updateMenuBasedOnRole(userRole) {
            const adminMenus = document.querySelectorAll('.admin-only');
            const studentMenus = document.querySelectorAll('.student-only');
            const teacherMenus = document.querySelectorAll('.teacher-only');
            const supervisorMenus = document.querySelectorAll('.supervisor-only');

            // Hide all role-specific menus first
            adminMenus.forEach(menu => menu.classList.add('hidden'));
            studentMenus.forEach(menu => menu.classList.add('hidden'));
            teacherMenus.forEach(menu => menu.classList.add('hidden'));
            supervisorMenus.forEach(menu => menu.classList.add('hidden'));

            if (userRole === 'student') {
                studentMenus.forEach(menu => menu.classList.remove('hidden'));
            } else if (userRole === 'school_admin' || userRole === 'superadmin') {
                adminMenus.forEach(menu => menu.classList.remove('hidden'));
            } else if (userRole === 'teacher') {
                teacherMenus.forEach(menu => menu.classList.remove('hidden'));
            } else if (userRole === 'company_supervisor') {
                supervisorMenus.forEach(menu => menu.classList.remove('hidden'));
            }
        }

        // Wait for Auth module to load, then initialize menu
        function waitForAuth(callback, maxWait = 5000) {
            const start = Date.now();
            const check = () => {
                if (window.Auth) {
                    callback();
                } else if (Date.now() - start < maxWait) {
                    setTimeout(check, 50);
                }
            };
            check();
        }

        waitForAuth(() => {
            // Check user role and update menu visibility
            (async function initializeMenu() {
                try {
                    const res = await Auth.apiFetch('/auth/me');
                    const data = await res.json();
                    const user = data.data || data;
                    window.currentUserId = user.id;
                    updateMenuBasedOnRole(user.role);
                } catch (err) {
                    console.error('Failed to initialize menu:', err);
                }
            })();

            // Logout
            document.getElementById('logout-btn')?.addEventListener('click', () => {
                Auth.removeToken();
                window.location.href = '/login';
            });
        });
    </script>
</body>
</html>