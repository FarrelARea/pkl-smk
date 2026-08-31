<div class="space-y-6">
    <x-help-button title="Panduan Dashboard Pembimbing">
        <p>Dashboard ini merangkum pekerjaan utama pembimbing perusahaan.</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Lihat ringkasan siswa magang, dokumen, dan kehadiran hari ini</li>
            <li>Proses izin siswa dan review aktivitas harian</li>
            <li>Buka detail siswa untuk memberi evaluasi pembimbing dan tindak lanjut</li>
        </ul>
    </x-help-button>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5" id="supervisor-summary-cards">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Siswa magang</p>
            <p class="mt-2 text-3xl font-bold text-gray-900" id="supervisor-summary-students">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Magang aktif</p>
            <p class="mt-2 text-3xl font-bold text-cyan-700" id="supervisor-summary-internships">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Izin pending</p>
            <p class="mt-2 text-3xl font-bold text-amber-600" id="supervisor-summary-permissions">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Dokumen pending</p>
            <p class="mt-2 text-3xl font-bold text-fuchsia-600" id="supervisor-summary-documents">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Hadir hari ini</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600" id="supervisor-summary-attendance">0</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-600">Company related</p>
                    <h3 class="text-lg font-semibold text-gray-900">Pekerjaan utama pembimbing</h3>
                </div>
                <button type="button" onclick="refreshSupervisorDashboard()" class="px-3 py-2 text-sm font-medium text-cyan-700 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition-colors">
                    Refresh
                </button>
            </div>
            <div class="p-6 grid gap-4 md:grid-cols-2">
                <button type="button" onclick="openSupervisorModal('permissions')" class="text-left rounded-2xl border border-amber-200 bg-amber-50 p-5 hover:border-amber-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-amber-900">Izin / sakit</p>
                            <p class="mt-1 text-sm text-amber-800">Tinjau pengajuan siswa yang magang di perusahaan Anda.</p>
                        </div>
                        <span class="material-symbols-outlined text-amber-700">approval</span>
                    </div>
                    <p class="mt-4 text-2xl font-bold text-amber-700" id="supervisor-permission-count">0</p>
                </button>

                <button type="button" onclick="openSupervisorModal('attendance')" class="text-left rounded-2xl border border-emerald-200 bg-emerald-50 p-5 hover:border-emerald-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-emerald-900">Kehadiran hari ini</p>
                            <p class="mt-1 text-sm text-emerald-800">Pantau siswa yang hadir pada tanggal yang dipilih.</p>
                        </div>
                        <span class="material-symbols-outlined text-emerald-700">calendar_month</span>
                    </div>
                    <p class="mt-4 text-2xl font-bold text-emerald-700" id="supervisor-attendance-count">0</p>
                </button>

                <button type="button" onclick="openSupervisorModal('scores')" class="text-left rounded-2xl border border-cyan-200 bg-cyan-50 p-5 hover:border-cyan-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-cyan-900">Evaluasi pembimbing</p>
                            <p class="mt-1 text-sm text-cyan-800">Buka detail siswa untuk memberi nilai dan catatan lapangan.</p>
                        </div>
                        <span class="material-symbols-outlined text-cyan-700">grading</span>
                    </div>
                    <p class="mt-4 text-sm font-medium text-cyan-800">Daftar siap dievaluasi</p>
                </button>

                <button type="button" onclick="openSupervisorModal('documents')" class="text-left rounded-2xl border border-fuchsia-200 bg-fuchsia-50 p-5 hover:border-fuchsia-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-fuchsia-900">Dokumen siswa</p>
                            <p class="mt-1 text-sm text-fuchsia-800">Review berkas siswa dalam lingkup perusahaan Anda.</p>
                        </div>
                        <span class="material-symbols-outlined text-fuchsia-700">folder_open</span>
                    </div>
                    <p class="mt-4 text-2xl font-bold text-fuchsia-700" id="supervisor-document-count">0</p>
                </button>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Company scope</p>
                <h3 class="text-lg font-semibold text-gray-900">Perusahaan & siswa</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Perusahaan</p>
                    <div id="supervisor-company-chip" class="mt-3 flex flex-wrap gap-2">
                        <span class="text-sm text-gray-400">Memuat...</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <p class="text-sm text-gray-500">Siswa magang</p>
                        <span class="text-xs text-gray-400" id="supervisor-student-count-label"></span>
                    </div>
                    <div id="supervisor-student-quick-list" class="space-y-2">
                        <p class="text-sm text-gray-400">Memuat...</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Other</p>
            <h3 class="text-lg font-semibold text-gray-900">Akses cepat lainnya</h3>
        </div>
        <div class="p-6 grid gap-3 md:grid-cols-3">
            <a href="/supervisor/attendance-points" class="rounded-xl border border-gray-200 px-4 py-4 hover:bg-gray-50 transition-colors">
                <p class="font-medium text-gray-900">Titik absensi</p>
                <p class="mt-1 text-sm text-gray-500">Kelola lokasi absensi perusahaan.</p>
            </a>
            <button type="button" onclick="openSupervisorModal('scores')" class="rounded-xl border border-gray-200 px-4 py-4 text-left hover:bg-gray-50 transition-colors">
                <p class="font-medium text-gray-900">Buka daftar evaluasi</p>
                <p class="mt-1 text-sm text-gray-500">Cari siswa lalu masuk ke halaman detail pembimbing.</p>
            </button>
            <button type="button" onclick="openSupervisorModal('attendance')" class="rounded-xl border border-gray-200 px-4 py-4 text-left hover:bg-gray-50 transition-colors">
                <p class="font-medium text-gray-900">Kalender kehadiran</p>
                <p class="mt-1 text-sm text-gray-500">Lihat daftar hadir berdasarkan tanggal.</p>
            </button>
        </div>
    </section>
</div>

<div id="supervisor-modal-backdrop" class="hidden fixed inset-0 z-40 bg-slate-900/50"></div>
<div id="supervisor-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500" id="supervisor-modal-kicker">Detail</p>
                <h3 class="text-lg font-semibold text-gray-900" id="supervisor-modal-title">Memuat...</h3>
            </div>
            <button type="button" onclick="closeSupervisorModal()" class="p-2 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" id="supervisor-modal-body">
            <p class="text-sm text-gray-400 text-center py-10">Memuat...</p>
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    const supervisorDashboardState = {
        summary: null,
        permissions: [],
        scores: [],
        documents: [],
        attendance: [],
        activeModal: null,
    };

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    window.refreshSupervisorDashboard = async () => {
        await Promise.all([
            loadSupervisorSummary(),
            loadSupervisorPermissionsOverview(),
            loadSupervisorScoresOverview(),
            loadSupervisorDocumentsOverview(),
            loadSupervisorAttendanceOverview(),
        ]);
    };

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await window.refreshSupervisorDashboard();
    });

    async function loadSupervisorSummary() {
        const res = await Auth.apiFetch('/supervisor/panel/summary');
        const data = await res.json();
        supervisorDashboardState.summary = data;

        document.getElementById('supervisor-summary-students').textContent = data.summary.assigned_students || 0;
        document.getElementById('supervisor-summary-internships').textContent = data.summary.active_internships || 0;
        document.getElementById('supervisor-summary-permissions').textContent = data.summary.pending_permissions || 0;
        document.getElementById('supervisor-summary-documents').textContent = data.summary.pending_documents || 0;
        document.getElementById('supervisor-summary-attendance').textContent = data.summary.attendance_today_count || 0;

        document.getElementById('supervisor-permission-count').textContent = `${data.summary.pending_permissions || 0} item`;
        document.getElementById('supervisor-document-count').textContent = `${data.summary.pending_documents || 0} file`;
        document.getElementById('supervisor-attendance-count').textContent = `${data.summary.attendance_today_count || 0} hadir`;

        renderSupervisorCompany(data.company_scope?.company);
        renderSupervisorStudents(data.students || []);
    }

    function renderSupervisorCompany(company) {
        const container = document.getElementById('supervisor-company-chip');
        if (!company?.name) {
            container.innerHTML = '<span class="text-sm text-gray-400">Belum ada perusahaan terhubung.</span>';
            return;
        }

        container.innerHTML = `<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-cyan-50 text-cyan-700 text-sm font-medium">${company.name}</span>`;
    }

    function renderSupervisorStudents(students) {
        const container = document.getElementById('supervisor-student-quick-list');
        document.getElementById('supervisor-student-count-label').textContent = `${students.length} siswa`;

        if (!students.length) {
            container.innerHTML = '<p class="text-sm text-gray-400">Belum ada siswa magang.</p>';
            return;
        }

        container.innerHTML = students.slice(0, 6).map(student => `
            <a href="/supervisor/students/${student.id}" class="block rounded-xl border border-gray-200 px-4 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">${student.name}</p>
                        <p class="text-xs text-gray-500 mt-1">${student.class_names?.join(', ') || 'Tanpa kelas'}</p>
                    </div>
                    <span class="text-xs text-cyan-600 font-medium">Buka</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">${student.active_internship?.company ? `PKL di ${student.active_internship.company}` : 'Belum ada magang aktif'}</p>
            </a>
        `).join('');
    }

    async function loadSupervisorPermissionsOverview() {
        const res = await Auth.apiFetch('/supervisor/panel/permissions');
        supervisorDashboardState.permissions = await res.json();
        rerenderActiveSupervisorModal('permissions');
    }

    async function loadSupervisorScoresOverview() {
        const res = await Auth.apiFetch('/supervisor/panel/scores');
        supervisorDashboardState.scores = await res.json();
        rerenderActiveSupervisorModal('scores');
    }

    async function loadSupervisorDocumentsOverview() {
        const res = await Auth.apiFetch('/supervisor/panel/documents');
        supervisorDashboardState.documents = await res.json();
        rerenderActiveSupervisorModal('documents');
    }

    async function loadSupervisorAttendanceOverview(date = currentSupervisorDateInput()) {
        const res = await Auth.apiFetch(`/supervisor/panel/attendance-calendar?date=${encodeURIComponent(date)}`);
        const data = await res.json();
        supervisorDashboardState.attendance = data.attendance || [];
        rerenderActiveSupervisorModal('attendance', data.date);
    }

    window.openSupervisorModal = async (type) => {
        supervisorDashboardState.activeModal = type;
        document.getElementById('supervisor-modal-backdrop').classList.remove('hidden');
        document.getElementById('supervisor-modal').classList.remove('hidden');
        await renderSupervisorModal(type);
    };

    window.closeSupervisorModal = () => {
        supervisorDashboardState.activeModal = null;
        document.getElementById('supervisor-modal-backdrop').classList.add('hidden');
        document.getElementById('supervisor-modal').classList.add('hidden');
    };

    function rerenderActiveSupervisorModal(type, date = currentSupervisorDateInput()) {
        if (supervisorDashboardState.activeModal === type) {
            renderSupervisorModal(type, date);
        }
    }

    async function renderSupervisorModal(type, date = currentSupervisorDateInput()) {
        const kicker = document.getElementById('supervisor-modal-kicker');
        const title = document.getElementById('supervisor-modal-title');
        const body = document.getElementById('supervisor-modal-body');

        if (type === 'permissions') {
            kicker.textContent = 'Company related';
            title.textContent = 'Izin / sakit siswa';
            renderSupervisorPermissionsModal(body);
            return;
        }

        if (type === 'scores') {
            kicker.textContent = 'Company related';
            title.textContent = 'Evaluasi pembimbing';
            renderSupervisorScoresModal(body);
            return;
        }

        if (type === 'documents') {
            kicker.textContent = 'Company related';
            title.textContent = 'Dokumen siswa';
            renderSupervisorDocumentsModal(body);
            return;
        }

        if (type === 'attendance') {
            kicker.textContent = 'Company related';
            title.textContent = 'Kalender kehadiran';
            renderSupervisorAttendanceModal(body, date);
        }
    }

    function renderSupervisorPermissionsModal(body) {
        const permissions = supervisorDashboardState.permissions || [];
        if (!permissions.length) {
            body.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Belum ada pengajuan izin atau sakit.</p>';
            return;
        }

        body.innerHTML = `<div class="space-y-3">${permissions.map(item => `
            <div class="rounded-2xl border border-gray-200 p-4 flex items-start justify-between gap-4">
                <div>
                    <p class="font-medium text-gray-900">${item.student_name}</p>
                    <p class="text-sm text-gray-600 mt-1">${formatSupervisorPermissionType(item.type)} · ${formatSupervisorDate(item.request_date)}${item.end_date && item.end_date !== item.request_date ? ` s/d ${formatSupervisorDate(item.end_date)}` : ''}</p>
                    <p class="text-sm text-gray-500 mt-2">${item.reason || 'Tanpa alasan tambahan.'}</p>
                    ${item.handler_note ? `<p class="text-xs text-gray-400 mt-2">Catatan: ${item.handler_note}</p>` : ''}
                </div>
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="${supervisorPermissionBadgeClass(item.status)}">${item.status}</span>
                    ${item.status === 'pending' ? `
                        <div class="flex gap-2">
                            <button type="button" onclick="approveSupervisorPermission(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700">Setuju</button>
                            <button type="button" onclick="rejectSupervisorPermission(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200">Tolak</button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `).join('')}</div>`;
    }

    function renderSupervisorScoresModal(body) {
        const scores = supervisorDashboardState.scores || [];
        if (!scores.length) {
            body.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Belum ada siswa untuk dievaluasi.</p>';
            return;
        }

        body.innerHTML = `<div class="space-y-3">${scores.map(item => `
            <a href="${item.detail_url}" class="block rounded-2xl border border-gray-200 p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-medium text-gray-900">${item.student_name}</p>
                        <p class="text-sm text-gray-500 mt-1">${item.student_email}</p>
                        <p class="text-sm text-gray-600 mt-2">${item.company_name ? `PKL di ${item.company_name}` : 'Belum ada magang aktif'}</p>
                    </div>
                    <div class="text-right">
                        <span class="${supervisorScoreBadgeClass(item.evaluation?.score)}">${supervisorScoreStatusLabel(item.evaluation?.score)}</span>
                        <p class="text-xs text-gray-400 mt-2">${item.evaluation?.updated_at ? 'Update ' + formatSupervisorDateTime(item.evaluation.updated_at) : 'Belum dievaluasi'}</p>
                    </div>
                </div>
            </a>
        `).join('')}</div>`;
    }

    function renderSupervisorDocumentsModal(body) {
        const documents = supervisorDashboardState.documents || [];
        if (!documents.length) {
            body.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Belum ada dokumen yang dapat ditinjau.</p>';
            return;
        }

        body.innerHTML = `<div class="space-y-3">${documents.map(item => `
            <div class="rounded-2xl border border-gray-200 p-4 flex items-start justify-between gap-4">
                <div>
                    <p class="font-medium text-gray-900">${item.title}</p>
                    <p class="text-sm text-gray-600 mt-1">${item.student_name} · ${item.file_type?.toUpperCase() || 'FILE'}</p>
                    <p class="text-xs text-gray-400 mt-2">Upload ${formatSupervisorDateTime(item.created_at)}</p>
                    ${item.teacher_note ? `<p class="text-xs text-gray-400 mt-2">Catatan: ${item.teacher_note}</p>` : ''}
                </div>
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="${supervisorDocumentBadgeClass(item.status)}">${item.status}</span>
                    <a href="/storage/${item.file_path}" target="_blank" class="text-xs font-medium text-cyan-600 hover:underline">Lihat file</a>
                    ${item.status === 'pending' ? `
                        <div class="flex gap-2">
                            <button type="button" onclick="approveSupervisorDocument(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700">Setuju</button>
                            <button type="button" onclick="rejectSupervisorDocument(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200">Tolak</button>
                        </div>
                    ` : ''}
                    ${['approved', 'rejected'].includes(item.status) ? `
                        <button type="button" onclick="cancelSupervisorDocument(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">Batal</button>
                    ` : ''}
                </div>
            </div>
        `).join('')}</div>`;
    }

    function renderSupervisorAttendanceModal(body, selectedDate) {
        const attendance = supervisorDashboardState.attendance || [];
        body.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <p class="text-sm text-gray-500">Pilih tanggal untuk melihat siapa yang tercatat hadir.</p>
                    </div>
                    <input type="date" id="supervisor-attendance-date" value="${selectedDate}" onchange="changeSupervisorAttendanceDate(this.value)" class="px-3 py-1.5 h-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-cyan-500">
                </div>
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
                    ${attendance.filter(item => item.status === 'present').length} siswa hadir dari ${attendance.length} data kehadiran yang tercatat.
                </div>
                ${attendance.length ? `<div class="space-y-3">${attendance.map(item => `
                    <div class="rounded-2xl border border-gray-200 p-4 flex items-start justify-between gap-4">
                        <div>
                            <p class="font-medium text-gray-900">${item.student_name}</p>
                            <p class="text-sm text-gray-500 mt-1">${formatSupervisorDate(item.attendance_date)}</p>
                            ${item.clock_in || item.all_clocks ? renderSupervisorClockTimes(item) : ''}
                            ${item.notes ? `<p class="text-sm text-gray-500 mt-2">${item.notes}</p>` : ''}
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="${supervisorAttendanceBadgeClass(item.status)}">${supervisorAttendanceStatusLabel(item.status)}</span>
                            ${item.clock_out ? `<span class="text-xs text-gray-400">🕒 Total: ${calcWorkHoursSupervisor(item.clock_in, item.clock_out)}</span>` : ''}
                        </div>
                    </div>
                `).join('')}</div>` : '<p class="text-sm text-gray-400 text-center py-10">Belum ada data kehadiran pada tanggal ini.</p>'}
            </div>
        `;
    }

    function renderSupervisorClockTimes(item) {
        if (item.clock_in) {
            const ci = new Date(item.clock_in);
            const formatted = ci.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            return `<p class="text-xs text-blue-600 mt-1">Clock In: ${formatted}${item.all_clocks ? ` (${item.all_clocks[0]?.within_range ? '✓ di area' : '⚠ luar area'})` : ''}</p>`;
        }
        if (item.all_clocks?.length) {
            const clocks = item.all_clocks.map(c => {
                const t = new Date(c.time);
                const timeStr = t.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                const range = c.within_range ? '✓' : '⚠';
                return `${timeStr} (${range})`;
            }).join(', ');
            return `<p class="text-xs text-gray-500 mt-1">Catatan: ${clocks}</p>`;
        }
        return '';
    }

    function calcWorkHoursSupervisor(clockIn, clockOut) {
        if (!clockIn || !clockOut) return '';
        const diff = new Date(clockOut) - new Date(clockIn);
        const hours = Math.floor(diff / 3600000);
        const mins = Math.round((diff % 3600000) / 60000);
        return `${hours}j ${mins}m`;
    }

    window.changeSupervisorAttendanceDate = async (value) => {
        await loadSupervisorAttendanceOverview(value);
    };

    window.approveSupervisorPermission = async (id) => {
        await Auth.apiFetch(`/supervisor/panel/permissions/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await Promise.all([loadSupervisorPermissionsOverview(), loadSupervisorSummary()]);
    };

    window.rejectSupervisorPermission = async (id) => {
        const note = prompt('Catatan penolakan (opsional):');
        if (note === null) return;
        await Auth.apiFetch(`/supervisor/panel/permissions/${id}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
        await Promise.all([loadSupervisorPermissionsOverview(), loadSupervisorSummary()]);
    };

    window.approveSupervisorDocument = async (id) => {
        await Auth.apiFetch(`/supervisor/documents/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await Promise.all([loadSupervisorDocumentsOverview(), loadSupervisorSummary()]);
    };

    window.rejectSupervisorDocument = async (id) => {
        const note = prompt('Catatan penolakan (wajib):');
        if (!note) return;
        await Auth.apiFetch(`/supervisor/documents/${id}/reject`, { method: 'POST', body: JSON.stringify({ teacher_note: note }) });
        await Promise.all([loadSupervisorDocumentsOverview(), loadSupervisorSummary()]);
    };

    window.cancelSupervisorDocument = async (id) => {
        await Auth.apiFetch(`/supervisor/documents/${id}/cancel-approval`, { method: 'POST', body: JSON.stringify({}) });
        await Promise.all([loadSupervisorDocumentsOverview(), loadSupervisorSummary()]);
    };

    function currentSupervisorDateInput() {
        return new Date().toISOString().slice(0, 10);
    }

    function formatSupervisorDate(value) {
        if (!value) return '—';
        return new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatSupervisorDateTime(value) {
        if (!value) return '—';
        return new Date(value).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function formatSupervisorPermissionType(type) {
        return { sick: 'Sakit', permit: 'Izin', other: 'Lainnya' }[type] || type;
    }

    function supervisorPermissionBadgeClass(status) {
        return {
            pending: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700',
            approved: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700',
            rejected: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700',
        }[status] || 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function supervisorScoreBadgeClass(score) {
        return score !== undefined && score !== null
            ? 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-700'
            : 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function supervisorScoreStatusLabel(score) {
        return score !== undefined && score !== null ? `Nilai ${score}` : 'Belum dievaluasi';
    }

    function supervisorDocumentBadgeClass(status) {
        return {
            pending: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700',
            approved: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700',
            rejected: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700',
        }[status] || 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function supervisorAttendanceBadgeClass(status) {
        return {
            present: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700',
            sick: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700',
            permission: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700',
            absent: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700',
        }[status] || 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function supervisorAttendanceStatusLabel(status) {
        return {
            present: 'Hadir',
            sick: 'Sakit',
            permission: 'Izin',
            absent: 'Alpa',
        }[status] || status;
    }
</script>
@endpush
