<div class="space-y-6">
    <x-help-button title="Panduan Dashboard Guru">
        <p>Dashboard ini merangkum pekerjaan utama guru pembimbing.</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Lihat ringkasan siswa, magang aktif, dan kehadiran hari ini</li>
            <li>Proses izin/sakit dan cek dokumen siswa</li>
            <li>Buka detail siswa untuk memberi nilai dan review aktivitas</li>
        </ul>
    </x-help-button>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5" id="teacher-summary-cards">
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Siswa bimbingan</p>
            <p class="mt-2 text-3xl font-bold text-gray-900" id="summary-students">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Magang aktif</p>
            <p class="mt-2 text-3xl font-bold text-blue-700" id="summary-internships">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Izin / sakit pending</p>
            <p class="mt-2 text-3xl font-bold text-amber-600" id="summary-permissions">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Dokumen pending</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600" id="summary-documents">0</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">Hadir hari ini</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600" id="summary-attendance">0</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Internship related</p>
                    <h3 class="text-lg font-semibold text-gray-900">Pekerjaan utama magang</h3>
                </div>
                <button type="button" onclick="refreshTeacherDashboard()" class="px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    Refresh
                </button>
            </div>
            <div class="p-6 grid gap-4 md:grid-cols-2">
                <button type="button" onclick="openTeacherModal('permissions')" class="text-left rounded-2xl border border-amber-200 bg-amber-50 p-5 hover:border-amber-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-amber-900">Izin / sakit</p>
                            <p class="mt-1 text-sm text-amber-800">Setujui atau tolak pengajuan siswa tanpa pindah halaman.</p>
                        </div>
                        <span class="material-symbols-outlined text-amber-700">approval</span>
                    </div>
                    <p class="mt-4 text-2xl font-bold text-amber-700" id="internship-permission-count">0</p>
                </button>

                <button type="button" onclick="openTeacherModal('attendance')" class="text-left rounded-2xl border border-emerald-200 bg-emerald-50 p-5 hover:border-emerald-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-emerald-900">Kehadiran hari ini</p>
                            <p class="mt-1 text-sm text-emerald-800">Lihat siapa yang hadir pada tanggal yang dipilih.</p>
                        </div>
                        <span class="material-symbols-outlined text-emerald-700">calendar_month</span>
                    </div>
                    <p class="mt-4 text-2xl font-bold text-emerald-700" id="internship-attendance-count">0</p>
                </button>

                <button type="button" onclick="openTeacherModal('scores')" class="text-left rounded-2xl border border-blue-200 bg-blue-50 p-5 hover:border-blue-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-blue-900">Penilaian siswa</p>
                            <p class="mt-1 text-sm text-blue-800">Buka detail siswa untuk input atau update nilai.</p>
                        </div>
                        <span class="material-symbols-outlined text-blue-700">grading</span>
                    </div>
                    <p class="mt-4 text-sm font-medium text-blue-800">Daftar siap dinilai</p>
                </button>

                <button type="button" onclick="openTeacherModal('documents')" class="text-left rounded-2xl border border-indigo-200 bg-indigo-50 p-5 hover:border-indigo-300 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-indigo-900">Dokumen siswa</p>
                            <p class="mt-1 text-sm text-indigo-800">Review file yang diunggah siswa dalam scope Anda.</p>
                        </div>
                        <span class="material-symbols-outlined text-indigo-700">folder_open</span>
                    </div>
                    <p class="mt-4 text-2xl font-bold text-indigo-700" id="internship-document-count">0</p>
                </button>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">School related</p>
                <h3 class="text-lg font-semibold text-gray-900">Kelas & siswa</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Kelas yang terhubung</p>
                    <div id="teacher-class-list" class="mt-3 flex flex-wrap gap-2">
                        <span class="text-sm text-gray-400">Memuat...</span>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <p class="text-sm text-gray-500">Siswa bimbingan</p>
                        <span class="text-xs text-gray-400" id="teacher-student-count-label"></span>
                    </div>
                    <div id="teacher-student-quick-list" class="space-y-2">
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
            <a href="/teacher/attendance-points" class="rounded-xl border border-gray-200 px-4 py-4 hover:bg-gray-50 transition-colors">
                <p class="font-medium text-gray-900">Titik absensi</p>
                <p class="mt-1 text-sm text-gray-500">Kelola lokasi absensi perusahaan yang dibina.</p>
            </a>
            <button type="button" onclick="openTeacherModal('scores')" class="rounded-xl border border-gray-200 px-4 py-4 text-left hover:bg-gray-50 transition-colors">
                <p class="font-medium text-gray-900">Buka daftar penilaian</p>
                <p class="mt-1 text-sm text-gray-500">Cari siswa lalu masuk ke halaman detail penilaian.</p>
            </button>
            <button type="button" onclick="openTeacherModal('attendance')" class="rounded-xl border border-gray-200 px-4 py-4 text-left hover:bg-gray-50 transition-colors">
                <p class="font-medium text-gray-900">Kalender kehadiran</p>
                <p class="mt-1 text-sm text-gray-500">Lihat daftar hadir berdasarkan tanggal.</p>
            </button>
        </div>
    </section>
</div>

<div id="teacher-modal-backdrop" class="hidden fixed inset-0 z-40 bg-slate-900/50"></div>
<div id="teacher-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-2xl bg-white shadow-2xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500" id="teacher-modal-kicker">Detail</p>
                <h3 class="text-lg font-semibold text-gray-900" id="teacher-modal-title">Memuat...</h3>
            </div>
            <button type="button" onclick="closeTeacherModal()" class="p-2 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" id="teacher-modal-body">
            <p class="text-sm text-gray-400 text-center py-10">Memuat...</p>
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    const teacherDashboardState = {
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

    window.refreshTeacherDashboard = async () => {
        await Promise.all([
            loadTeacherSummary(),
            loadTeacherPermissionsOverview(),
            loadTeacherScoresOverview(),
            loadTeacherDocumentsOverview(),
            loadTeacherAttendanceOverview(),
        ]);
    };

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await window.refreshTeacherDashboard();
    });

    async function loadTeacherSummary() {
        const res = await Auth.apiFetch('/teacher/panel/summary');
        const data = await res.json();
        teacherDashboardState.summary = data;

        document.getElementById('summary-students').textContent = data.summary.assigned_students || 0;
        document.getElementById('summary-internships').textContent = data.summary.active_internships || 0;
        document.getElementById('summary-permissions').textContent = data.summary.pending_permissions || 0;
        document.getElementById('summary-documents').textContent = data.summary.pending_documents || 0;
        document.getElementById('summary-attendance').textContent = data.summary.attendance_today_count || 0;

        document.getElementById('internship-permission-count').textContent = `${data.summary.pending_permissions || 0} item`;
        document.getElementById('internship-document-count').textContent = `${data.summary.pending_documents || 0} file`;
        document.getElementById('internship-attendance-count').textContent = `${data.summary.attendance_today_count || 0} hadir`;

        renderTeacherClasses(data.school_scope?.classes || []);
        renderTeacherStudents(data.students || []);
    }

    function renderTeacherClasses(classes) {
        const container = document.getElementById('teacher-class-list');
        if (!classes.length) {
            container.innerHTML = '<span class="text-sm text-gray-400">Belum ada kelas terhubung.</span>';
            return;
        }

        container.innerHTML = classes.map(item => `
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm">
                <span>${item.name}</span>
                <span class="text-xs text-slate-500">${item.academic_year || 'Tanpa tahun'}</span>
            </span>
        `).join('');
    }

    function renderTeacherStudents(students) {
        const container = document.getElementById('teacher-student-quick-list');
        document.getElementById('teacher-student-count-label').textContent = `${students.length} siswa`;

        if (!students.length) {
            container.innerHTML = '<p class="text-sm text-gray-400">Belum ada siswa bimbingan.</p>';
            return;
        }

        container.innerHTML = students.slice(0, 6).map(student => `
            <a href="/teacher/students/${student.id}" class="block rounded-xl border border-gray-200 px-4 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">${student.name}</p>
                        <p class="text-xs text-gray-500 mt-1">${student.class_names?.join(', ') || 'Tanpa kelas'}</p>
                    </div>
                    <span class="text-xs text-blue-600 font-medium">Buka</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">${student.active_internship?.company ? `PKL di ${student.active_internship.company}` : 'Belum ada magang aktif'}</p>
            </a>
        `).join('');
    }

    async function loadTeacherPermissionsOverview() {
        const res = await Auth.apiFetch('/teacher/panel/permissions');
        teacherDashboardState.permissions = await res.json();
        rerenderActiveTeacherModal('permissions');
    }

    async function loadTeacherScoresOverview() {
        const res = await Auth.apiFetch('/teacher/panel/scores');
        teacherDashboardState.scores = await res.json();
        rerenderActiveTeacherModal('scores');
    }

    async function loadTeacherDocumentsOverview() {
        const res = await Auth.apiFetch('/teacher/panel/documents');
        teacherDashboardState.documents = await res.json();
        rerenderActiveTeacherModal('documents');
    }

    async function loadTeacherAttendanceOverview(date = currentDateInput()) {
        const res = await Auth.apiFetch(`/teacher/panel/attendance-calendar?date=${encodeURIComponent(date)}`);
        const data = await res.json();
        teacherDashboardState.attendance = data.attendance || [];
        rerenderActiveTeacherModal('attendance', data.date);
    }

    window.openTeacherModal = async (type) => {
        teacherDashboardState.activeModal = type;
        document.getElementById('teacher-modal-backdrop').classList.remove('hidden');
        document.getElementById('teacher-modal').classList.remove('hidden');
        await renderTeacherModal(type);
    };

    window.closeTeacherModal = () => {
        teacherDashboardState.activeModal = null;
        document.getElementById('teacher-modal-backdrop').classList.add('hidden');
        document.getElementById('teacher-modal').classList.add('hidden');
    };

    function rerenderActiveTeacherModal(type, date = currentDateInput()) {
        if (teacherDashboardState.activeModal === type) {
            renderTeacherModal(type, date);
        }
    }

    async function renderTeacherModal(type, date = currentDateInput()) {
        const kicker = document.getElementById('teacher-modal-kicker');
        const title = document.getElementById('teacher-modal-title');
        const body = document.getElementById('teacher-modal-body');

        if (type === 'permissions') {
            kicker.textContent = 'Internship related';
            title.textContent = 'Izin / sakit siswa';
            renderPermissionsModal(body);
            return;
        }

        if (type === 'scores') {
            kicker.textContent = 'Internship related';
            title.textContent = 'Penilaian siswa';
            renderScoresModal(body);
            return;
        }

        if (type === 'documents') {
            kicker.textContent = 'Internship related';
            title.textContent = 'Dokumen siswa';
            renderDocumentsModal(body);
            return;
        }

        if (type === 'attendance') {
            kicker.textContent = 'Internship related';
            title.textContent = 'Kalender kehadiran';
            renderAttendanceModal(body, date);
        }
    }

    function renderPermissionsModal(body) {
        const permissions = teacherDashboardState.permissions || [];
        if (!permissions.length) {
            body.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Belum ada pengajuan izin atau sakit.</p>';
            return;
        }

        body.innerHTML = `<div class="space-y-3">${permissions.map(item => `
            <div class="rounded-2xl border border-gray-200 p-4 flex items-start justify-between gap-4">
                <div>
                    <p class="font-medium text-gray-900">${item.student_name}</p>
                    <p class="text-sm text-gray-600 mt-1">${formatPermissionType(item.type)} · ${formatDate(item.request_date)}${item.end_date && item.end_date !== item.request_date ? ` s/d ${formatDate(item.end_date)}` : ''}</p>
                    <p class="text-sm text-gray-500 mt-2">${item.reason || 'Tanpa alasan tambahan.'}</p>
                    ${item.handler_note ? `<p class="text-xs text-gray-400 mt-2">Catatan: ${item.handler_note}</p>` : ''}
                </div>
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="${permissionBadgeClass(item.status)}">${item.status}</span>
                    ${item.status === 'pending' ? `
                        <div class="flex gap-2">
                            <button type="button" onclick="approveTeacherPermission(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700">Setuju</button>
                            <button type="button" onclick="rejectTeacherPermission(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200">Tolak</button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `).join('')}</div>`;
    }

    function renderScoresModal(body) {
        const scores = teacherDashboardState.scores || [];
        if (!scores.length) {
            body.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Belum ada siswa untuk dinilai.</p>';
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
                        <span class="${scoreBadgeClass(item.assessment?.status)}">${scoreStatusLabel(item.assessment?.status)}</span>
                        <p class="text-xs text-gray-400 mt-2">${item.assessment?.updated_at ? 'Update ' + formatDateTime(item.assessment.updated_at) : 'Belum dinilai'}</p>
                    </div>
                </div>
            </a>
        `).join('')}</div>`;
    }

    function renderDocumentsModal(body) {
        const documents = teacherDashboardState.documents || [];
        if (!documents.length) {
            body.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Belum ada dokumen yang dapat ditinjau.</p>';
            return;
        }

        body.innerHTML = `<div class="space-y-3">${documents.map(item => `
            <div class="rounded-2xl border border-gray-200 p-4 flex items-start justify-between gap-4">
                <div>
                    <p class="font-medium text-gray-900">${item.title}</p>
                    <p class="text-sm text-gray-600 mt-1">${item.student_name} · ${item.file_type?.toUpperCase() || 'FILE'}</p>
                    <p class="text-xs text-gray-400 mt-2">Upload ${formatDateTime(item.created_at)}</p>
                    ${item.teacher_note ? `<p class="text-xs text-gray-400 mt-2">Catatan: ${item.teacher_note}</p>` : ''}
                </div>
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="${documentBadgeClass(item.status)}">${item.status}</span>
                    <a href="/storage/${item.file_path}" target="_blank" class="text-xs font-medium text-blue-600 hover:underline">Lihat file</a>
                    ${item.status === 'pending' ? `
                        <div class="flex gap-2">
                            <button type="button" onclick="approveTeacherDocument(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700">Setuju</button>
                            <button type="button" onclick="rejectTeacherDocument(${item.id})" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200">Tolak</button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `).join('')}</div>`;
    }

    function renderAttendanceModal(body, selectedDate) {
        const attendance = teacherDashboardState.attendance || [];
        body.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <p class="text-sm text-gray-500">Pilih tanggal untuk melihat siapa yang tercatat hadir.</p>
                    </div>
                    <input type="date" id="teacher-attendance-date" value="${selectedDate}" onchange="changeTeacherAttendanceDate(this.value)" class="px-3 py-1.5 h-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
                    ${attendance.filter(item => item.status === 'present').length} siswa hadir dari ${attendance.length} data kehadiran yang tercatat.
                </div>
                ${attendance.length ? `<div class="space-y-3">${attendance.map(item => `
                    <div class="rounded-2xl border border-gray-200 p-4 flex items-start justify-between gap-4">
                        <div>
                            <p class="font-medium text-gray-900">${item.student_name}</p>
                            <p class="text-sm text-gray-500 mt-1">${formatDate(item.attendance_date)}</p>
                            ${item.notes ? `<p class="text-sm text-gray-500 mt-2">${item.notes}</p>` : ''}
                        </div>
                        <span class="${attendanceBadgeClass(item.status)}">${attendanceStatusLabel(item.status)}</span>
                    </div>
                `).join('')}</div>` : '<p class="text-sm text-gray-400 text-center py-10">Belum ada data kehadiran pada tanggal ini.</p>'}
            </div>
        `;
    }

    window.changeTeacherAttendanceDate = async (value) => {
        await loadTeacherAttendanceOverview(value);
    };

    window.approveTeacherPermission = async (id) => {
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await Promise.all([loadTeacherPermissionsOverview(), loadTeacherSummary()]);
    };

    window.rejectTeacherPermission = async (id) => {
        const note = prompt('Catatan penolakan (opsional):');
        if (note === null) return;
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
        await Promise.all([loadTeacherPermissionsOverview(), loadTeacherSummary()]);
    };

    window.approveTeacherDocument = async (id) => {
        await Auth.apiFetch(`/teacher/documents/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await Promise.all([loadTeacherDocumentsOverview(), loadTeacherSummary()]);
    };

    window.rejectTeacherDocument = async (id) => {
        const note = prompt('Catatan penolakan (wajib):');
        if (!note) return;
        await Auth.apiFetch(`/teacher/documents/${id}/reject`, { method: 'POST', body: JSON.stringify({ teacher_note: note }) });
        await Promise.all([loadTeacherDocumentsOverview(), loadTeacherSummary()]);
    };

    function currentDateInput() {
        return new Date().toISOString().slice(0, 10);
    }

    function formatDate(value) {
        if (!value) return '—';
        return new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatDateTime(value) {
        if (!value) return '—';
        return new Date(value).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function formatPermissionType(type) {
        return { sick: 'Sakit', permit: 'Izin', other: 'Lainnya' }[type] || type;
    }

    function permissionBadgeClass(status) {
        return {
            pending: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700',
            approved: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700',
            rejected: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700',
        }[status] || 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function scoreBadgeClass(status) {
        return {
            submitted: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700',
            draft: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700',
        }[status] || 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function scoreStatusLabel(status) {
        return {
            submitted: 'Sudah dikirim',
            draft: 'Draft',
        }[status] || 'Belum dinilai';
    }

    function documentBadgeClass(status) {
        return {
            pending: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700',
            approved: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700',
            rejected: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700',
        }[status] || 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function attendanceBadgeClass(status) {
        return {
            present: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700',
            sick: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700',
            permission: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700',
            absent: 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700',
        }[status] || 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600';
    }

    function attendanceStatusLabel(status) {
        return {
            present: 'Hadir',
            sick: 'Sakit',
            permission: 'Izin',
            absent: 'Alpa',
        }[status] || status;
    }
</script>
@endpush
