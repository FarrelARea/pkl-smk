<!-- Header Section -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Ringkasan Sistem</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Kelola kemitraan institusi dan pantau metrik magang di seluruh jaringan.
        </p>
        <x-help-button title="Panduan Dashboard Admin">
            <p>Ini adalah halaman utama untuk admin. Di sini kamu bisa:</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Lihat ringkasan data sekolah, siswa, magang, dan perusahaan</li>
                <li>Cari dan kelola mitra institusi</li>
                <li>Daftarkan sekolah baru</li>
                <li>Pantau aktivitas terbaru di sistem</li>
                <li>Export laporan</li>
            </ul>
        </x-help-button>
    </div>
    <div class="flex gap-3">
        <a href="/admin/schools" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Sekolah Baru
        </a>
    </div>
</header>

<!-- Stats Grid (Bento Style) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <a href="/admin/schools" class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] col-span-1 border border-outline-variant/10 hover:shadow-lg transition-shadow block">
        <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total Sekolah</p>
        <div class="flex items-baseline gap-2">
            <span id="stat-schools" class="text-4xl font-extrabold text-primary">—</span>
        </div>
        <div class="mt-4 h-1.5 w-full bg-primary-fixed rounded-full overflow-hidden">
            <div id="bar-schools" class="h-full bg-primary rounded-full transition-all duration-700" style="width:0%"></div>
        </div>
    </a>
    <a href="/admin/students" class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] col-span-1 border border-outline-variant/10 hover:shadow-lg transition-shadow block">
        <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total Siswa</p>
        <div class="flex items-baseline gap-2">
            <span id="stat-students" class="text-4xl font-extrabold text-on-surface">—</span>
        </div>
        <div class="mt-4 h-1.5 w-full bg-secondary-fixed rounded-full overflow-hidden">
            <div id="bar-students" class="h-full bg-secondary rounded-full transition-all duration-700" style="width:0%"></div>
        </div>
    </a>
    <a href="/admin/internships" class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] col-span-1 border border-outline-variant/10 hover:shadow-lg transition-shadow block">
        <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Magang Aktif</p>
        <div class="flex items-baseline gap-2">
            <span id="stat-internships" class="text-4xl font-extrabold text-tertiary">—</span>
        </div>
        <div class="mt-4 h-1.5 w-full bg-tertiary-fixed rounded-full overflow-hidden">
            <div id="bar-internships" class="h-full bg-tertiary rounded-full transition-all duration-700" style="width:0%"></div>
        </div>
    </a>
    <a href="/admin/companies" class="bg-primary-container p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.1)] col-span-1 text-white relative overflow-hidden group hover:shadow-lg transition-shadow block">
        <div class="relative z-10">
            <p class="text-[0.7rem] font-bold text-primary-fixed uppercase tracking-widest mb-1">Perusahaan</p>
            <div class="flex items-baseline gap-2">
                <span id="stat-companies" class="text-4xl font-extrabold">—</span>
            </div>
            <p class="mt-4 text-xs text-primary-fixed/80">Organisasi mitra</p>
        </div>
        <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl opacity-10 group-hover:scale-110 transition-transform">business</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Schools Table Section -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
            <div class="p-6 flex justify-between items-center bg-white border-b border-surface-container">
                <h2 class="text-xl font-bold tracking-tight font-headline">Mitra Institusi</h2>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                    <input id="school-search" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Cari sekolah..." type="text">
                </div>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low">
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Institusi</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Siswa Aktif</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="schools-table" class="divide-y divide-surface-container">
                    <tr><td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
                </tbody>
            </table>
            <div id="schools-pagination"></div>
        </div>
    </div>

    <!-- Right Sidebar Components -->
    <div class="space-y-8">
        <!-- Ringkasan Cepat -->
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] border border-outline-variant/10">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2 font-headline">
                <span class="material-symbols-outlined text-primary">insights</span>
                Ringkasan Cepat
            </h3>
            <div class="space-y-3">
                <a href="/admin/teachers" class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg hover:bg-surface-container-high transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-base">person_apron</span>
                        <span class="text-sm font-medium">Total Guru</span>
                    </div>
                    <span id="stat-teachers" class="text-sm font-extrabold text-secondary">—</span>
                </a>
                <a href="/admin/supervisors" class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg hover:bg-surface-container-high transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-tertiary text-base">supervisor_account</span>
                        <span class="text-sm font-medium">Supervisor Perusahaan</span>
                    </div>
                    <span id="stat-supervisors" class="text-sm font-extrabold text-tertiary">—</span>
                </a>
                <a href="/admin/daily-logs" class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg hover:bg-surface-container-high transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-base">book_2</span>
                        <span class="text-sm font-medium">Jurnal Hari Ini</span>
                    </div>
                    <span id="stat-logs-today" class="text-sm font-extrabold text-primary">—</span>
                </a>
                <a href="/admin/attendance" class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg hover:bg-surface-container-high transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-on-surface text-base">event_available</span>
                        <span class="text-sm font-medium">Absensi Hari Ini</span>
                    </div>
                    <span id="stat-attendance-today" class="text-sm font-extrabold">—</span>
                </a>
            </div>
        </div>

        <!-- Magang Terbaru -->
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] border border-outline-variant/10">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2 font-headline">
                <span class="material-symbols-outlined text-primary">history</span>
                Magang Terbaru
            </h3>
            <div id="recent-internships" class="space-y-3">
                <p class="text-sm text-on-surface-variant text-center py-4">Memuat...</p>
            </div>
            <a href="/admin/internships" class="mt-4 block text-center text-xs font-bold text-primary hover:underline uppercase tracking-wider">Lihat Semua →</a>
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    const { renderPagination } = AdminUtils;
    let schoolsPage = 1;
    let schoolsSearch = '';
    let searchTimeout = null;

    // ── Fetch a single count from paginated endpoint ──────
    async function fetchTotal(endpoint) {
        try {
            const res = await Auth.apiFetch(`${endpoint}?per_page=1`);
            const json = await res.json();
            return json.total ?? json.meta?.total ?? 0;
        } catch {
            return 0;
        }
    }

    // ── Animate counter ───────────────────────────────────
    function animateCount(el, target) {
        if (!el) return;
        const duration = 600;
        const start = performance.now();
        function step(now) {
            const progress = Math.min((now - start) / duration, 1);
            el.textContent = Math.round(progress * target);
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ── Load Stats ────────────────────────────────────────
    async function loadStats() {
        const [schools, students, internships, companies, teachers, supervisors] = await Promise.all([
            fetchTotal('/schools'),
            fetchTotal('/students'),
            fetchTotal('/internships?status=active'),
            fetchTotal('/companies'),
            fetchTotal('/teachers'),
            fetchTotal('/supervisors'),
        ]);

        animateCount(document.getElementById('stat-schools'), schools);
        animateCount(document.getElementById('stat-students'), students);
        animateCount(document.getElementById('stat-internships'), internships);
        animateCount(document.getElementById('stat-companies'), companies);
        animateCount(document.getElementById('stat-teachers'), teachers);
        animateCount(document.getElementById('stat-supervisors'), supervisors);

        // Progress bars relative to the max across all stats
        const maxMain = Math.max(schools, students, internships, companies) || 1;
        setTimeout(() => {
            const barSchools = document.getElementById('bar-schools');
            const barStudents = document.getElementById('bar-students');
            const barInternships = document.getElementById('bar-internships');
            if (barSchools) barSchools.style.width = Math.round((schools / maxMain) * 100) + '%';
            if (barStudents) barStudents.style.width = Math.round((students / maxMain) * 100) + '%';
            if (barInternships) barInternships.style.width = Math.round((internships / maxMain) * 100) + '%';
        }, 100);
    }

    // ── Load Today's Logs Count ───────────────────────────
    async function loadTodayStats() {
        const today = new Date().toISOString().slice(0, 10);
        try {
            const [logsRes, attRes] = await Promise.all([
                Auth.apiFetch(`/daily-logs?per_page=1&date=${today}`),
                Auth.apiFetch(`/attendance?per_page=1&date=${today}`),
            ]);
            const logsJson = await logsRes.json();
            const attJson = await attRes.json();
            const logsEl = document.getElementById('stat-logs-today');
            const attEl = document.getElementById('stat-attendance-today');
            if (logsEl) logsEl.textContent = logsJson.total ?? logsJson.meta?.total ?? 0;
            if (attEl) attEl.textContent = attJson.total ?? attJson.meta?.total ?? 0;
        } catch {
            // silently fail — today stats are optional
        }
    }

    // ── Load Schools Table ────────────────────────────────
    async function loadSchools(page = 1) {
        schoolsPage = page;
        const tbody = document.getElementById('schools-table');
        try {
            const params = new URLSearchParams({ page, per_page: 8 });
            if (schoolsSearch) params.set('search', schoolsSearch);

            const res = await Auth.apiFetch(`/schools?${params}`);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;

            if (items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Tidak ada sekolah ditemukan</td></tr>`;
            } else {
                tbody.innerHTML = items.map(school => `
                    <tr class="hover:bg-surface-container-high transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-on-secondary-container">account_balance</span>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface">${escHtml(school.name)}</p>
                                    <p class="text-xs text-on-surface-variant">${escHtml(school.address || '—')}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm font-medium">${school.students_count ?? 0} Siswa</p>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <a href="/admin/schools" class="p-2 text-on-surface-variant hover:text-primary transition-colors inline-flex">
                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                            </a>
                        </td>
                    </tr>
                `).join('');
            }

            renderPagination('schools-pagination', meta, loadSchools);
        } catch (e) {
            console.error(e);
            tbody.innerHTML = `<tr><td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Gagal memuat data sekolah</td></tr>`;
        }
    }

    // ── Load Recent Internships ───────────────────────────
    async function loadRecentInternships() {
        const container = document.getElementById('recent-internships');
        try {
            const res = await Auth.apiFetch('/internships?per_page=5&sort=created_at&order=desc');
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);

            if (items.length === 0) {
                container.innerHTML = `<p class="text-sm text-on-surface-variant text-center py-4">Belum ada data magang</p>`;
                return;
            }

            const statusColors = {
                active: 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                completed: 'bg-primary-fixed text-on-primary-fixed-variant',
                cancelled: 'bg-error-container text-on-error-container',
            };

            const statusLabels = {
                active: 'Aktif',
                completed: 'Selesai',
                cancelled: 'Dibatalkan',
            };

            container.innerHTML = items.map(item => {
                const statusCls = statusColors[item.status] || 'bg-surface-container-highest text-on-surface-variant';
                const statusLabel = statusLabels[item.status] || item.status;
                const studentName = item.student?.name ?? '—';
                const companyName = item.company?.name ?? '—';
                return `
                    <div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-sm text-on-secondary-container">work</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate">${escHtml(studentName)}</p>
                            <p class="text-xs text-on-surface-variant truncate">${escHtml(companyName)}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[0.6rem] font-bold uppercase tracking-widest flex-shrink-0 ${statusCls}">${statusLabel}</span>
                    </div>
                `;
            }).join('');
        } catch (e) {
            console.error(e);
            container.innerHTML = `<p class="text-sm text-on-surface-variant text-center py-4">Gagal memuat data magang</p>`;
        }
    }

    // ── HTML escape helper ────────────────────────────────
    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Search ────────────────────────────────────────────
    document.getElementById('school-search').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            schoolsSearch = e.target.value.trim();
            loadSchools(1);
        }, 300);
    });

    // ── Init ──────────────────────────────────────────────
    loadStats();
    loadTodayStats();
    loadSchools();
    loadRecentInternships();
</script>
@endpush
