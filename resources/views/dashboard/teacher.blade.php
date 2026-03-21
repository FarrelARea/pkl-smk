<div class="space-y-6">
    {{-- Global Pending Inbox --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Inbox Persetujuan</h3>
            <span id="inbox-count" class="text-sm text-gray-400"></span>
        </div>
        <div id="inbox-panel" class="p-6">
            <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
        </div>
    </div>

    {{-- Daftar Murid Tanggungan --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Siswa Bimbingan</h3>
            <span id="student-count" class="text-sm text-gray-400"></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempat Magang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="teacher-students-body">
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Panel Detail Murid --}}
    <div id="student-detail-panel" class="hidden bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Detail: <span id="detail-student-name"></span></h3>
            <button onclick="closeDetail()" class="text-gray-400 hover:text-gray-600 text-sm">Tutup</button>
        </div>

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 px-6">
            <nav class="flex gap-1 -mb-px" id="tab-nav">
                <button data-tab="stats" onclick="switchTab('stats')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600">Statistik & Nilai</button>
                <button data-tab="permissions" onclick="switchTab('permissions')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Pengajuan Izin</button>
                <button data-tab="logs" onclick="switchTab('logs')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Log Aktivitas</button>
                <button data-tab="documents" onclick="switchTab('documents')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Dokumen</button>
            </nav>
        </div>

        {{-- Tab: Statistik & Penilaian --}}
        <div id="tab-stats" class="tab-content p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-green-600" id="stats-present">0</p>
                    <p class="text-xs text-gray-500 mt-1">Hari Hadir</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600" id="stats-logs">0</p>
                    <p class="text-xs text-gray-500 mt-1">Daily Log</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600" id="stats-permission-pending">0</p>
                    <p class="text-xs text-gray-500 mt-1">Izin Pending</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600" id="stats-score">—</p>
                    <p class="text-xs text-gray-500 mt-1">Nilai Saat Ini</p>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-4">
                <h4 class="font-medium text-gray-800 mb-3">Penilaian</h4>
                <input type="hidden" id="eval-student-id">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nilai (0–100)</label>
                        <input type="number" id="eval-score" min="0" max="100" placeholder="0-100"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Komentar (opsional)</label>
                        <input type="text" id="eval-comments" placeholder="Catatan penilaian..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <div id="eval-error" class="hidden mt-2 text-sm text-red-600"></div>
                <div id="eval-success" class="hidden mt-2 text-sm text-green-600"></div>
                <button onclick="submitEvaluation()" id="eval-btn"
                    class="mt-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                    Simpan Penilaian
                </button>
            </div>
        </div>

        {{-- Tab: Pengajuan Izin --}}
        <div id="tab-permissions" class="tab-content hidden p-6">
            <div id="permissions-list">
                <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
            </div>
        </div>

        {{-- Tab: Log Aktivitas --}}
        <div id="tab-logs" class="tab-content hidden p-6">
            <div id="logs-list">
                <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
            </div>
        </div>

        {{-- Tab: Dokumen --}}
        <div id="tab-documents" class="tab-content hidden p-6">
            <div id="documents-list">
                <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    let currentStudentId = null;
    let currentTab = 'stats';

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await Promise.all([loadStudents(), loadPendingInbox()]);
    });

    // ── Students table ──────────────────────────────────────────────

    async function loadStudents() {
        try {
            const res = await Auth.apiFetch('/teacher/panel/students');
            const students = await res.json();
            console.log('[teacher] students response:', typeof students, Array.isArray(students), students);
            document.getElementById('student-count').textContent = `${students.length} murid`;

            const tbody = document.getElementById('teacher-students-body');
            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada murid yang ditugaskan ke Anda.</td></tr>';
                return;
            }

            tbody.innerHTML = students.map(s => {
                const company = s.active_internship?.company || '—';
                const badges = [];
                if (s.pending_permission_count > 0) badges.push(`<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">✉ ${s.pending_permission_count} izin</span>`);
                if (s.pending_document_count > 0) badges.push(`<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">📄 ${s.pending_document_count} dok</span>`);
                return `<tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${s.name}<br><span class="text-xs text-gray-400 font-normal">${s.email}</span></td>
                    <td class="px-4 py-3 text-sm text-gray-600">${company}</td>
                    <td class="px-4 py-3 text-sm">${badges.join(' ') || '<span class="text-gray-300 text-xs">—</span>'}</td>
                    <td class="px-4 py-3 text-sm">
                        <button onclick="openDetail(${s.id}, '${s.name.replace(/'/g, "\\'")}')"
                            class="text-blue-600 hover:underline text-xs font-medium">Lihat Detail</button>
                    </td>
                </tr>`;
            }).join('');
        } catch (e) {
            console.error('Failed to load students:', e);
        }
    }

    // ── Global Pending Inbox ────────────────────────────────────────

    async function loadPendingInbox() {
        try {
            const res = await Auth.apiFetch('/teacher/panel/pending');
            const data = await res.json();
            const total = (data.permissions?.length || 0) + (data.documents?.length || 0);
            document.getElementById('inbox-count').textContent = total > 0 ? `${total} item pending` : 'Tidak ada pending';

            const panel = document.getElementById('inbox-panel');
            if (total === 0) {
                panel.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Tidak ada item yang menunggu persetujuan.</p>';
                return;
            }

            let html = '';

            if (data.permissions?.length > 0) {
                html += `<div class="mb-4">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Izin / Sakit (${data.permissions.length})</h4>
                    <div class="space-y-2">
                        ${data.permissions.map(p => `
                        <div class="flex items-center justify-between bg-yellow-50 rounded-lg px-4 py-2 gap-3">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900">${p.student?.name}</span>
                                <span class="text-gray-500 ml-2">${formatType(p.type)}</span>
                                <span class="text-gray-400 ml-2">${formatDate(p.request_date)}${p.end_date && p.end_date !== p.request_date ? ' s/d ' + formatDate(p.end_date) : ''}</span>
                                ${p.reason ? `<span class="text-gray-500 ml-2">— ${p.reason}</span>` : ''}
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <button onclick="inboxApprovePermission(${p.id}, this)" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">Setuju</button>
                                <button onclick="inboxRejectPermission(${p.id}, this)" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg">Tolak</button>
                            </div>
                        </div>`).join('')}
                    </div>
                </div>`;
            }

            if (data.documents?.length > 0) {
                html += `<div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Dokumen (${data.documents.length})</h4>
                    <div class="space-y-2">
                        ${data.documents.map(d => `
                        <div class="flex items-center justify-between bg-blue-50 rounded-lg px-4 py-2 gap-3">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900">${d.student?.name}</span>
                                <span class="text-gray-600 ml-2">${d.title}</span>
                                <span class="text-gray-400 ml-2 text-xs">${d.file_type?.toUpperCase()}</span>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <button onclick="inboxApproveDoc(${d.id}, this)" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">Setuju</button>
                                <button onclick="inboxRejectDoc(${d.id}, this)" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg">Tolak</button>
                            </div>
                        </div>`).join('')}
                    </div>
                </div>`;
            }

            panel.innerHTML = html;
        } catch (e) {
            console.error('Failed to load pending inbox:', e);
        }
    }

    window.inboxApprovePermission = async (id, btn) => {
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await loadPendingInbox();
        await loadStudents();
    };

    window.inboxRejectPermission = async (id, btn) => {
        const note = prompt('Catatan penolakan (opsional):');
        if (note === null) return;
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
        await loadPendingInbox();
        await loadStudents();
    };

    window.inboxApproveDoc = async (id, btn) => {
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/documents/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await loadPendingInbox();
        await loadStudents();
    };

    window.inboxRejectDoc = async (id, btn) => {
        const note = prompt('Catatan penolakan (wajib):');
        if (!note) return;
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/documents/${id}/reject`, { method: 'POST', body: JSON.stringify({ teacher_note: note }) });
        await loadPendingInbox();
        await loadStudents();
    };

    // ── Detail Panel ────────────────────────────────────────────────

    window.openDetail = async (studentId, studentName) => {
        currentStudentId = studentId;
        document.getElementById('detail-student-name').textContent = studentName;
        document.getElementById('eval-student-id').value = studentId;
        document.getElementById('student-detail-panel').classList.remove('hidden');
        switchTab('stats');
        document.getElementById('student-detail-panel').scrollIntoView({ behavior: 'smooth' });
    };

    window.closeDetail = () => {
        document.getElementById('student-detail-panel').classList.add('hidden');
        currentStudentId = null;
    };

    window.switchTab = async (tab) => {
        currentTab = tab;
        document.querySelectorAll('.tab-btn').forEach(btn => {
            const active = btn.dataset.tab === tab;
            btn.classList.toggle('border-blue-600', active);
            btn.classList.toggle('text-blue-600', active);
            btn.classList.toggle('border-transparent', !active);
            btn.classList.toggle('text-gray-500', !active);
        });
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById(`tab-${tab}`).classList.remove('hidden');

        if (!currentStudentId) return;
        if (tab === 'stats') await loadStats(currentStudentId);
        if (tab === 'permissions') await loadPermissions(currentStudentId);
        if (tab === 'logs') await loadLogs(currentStudentId);
        if (tab === 'documents') await loadDocuments(currentStudentId);
    };

    // ── Stats tab ───────────────────────────────────────────────────

    async function loadStats(studentId) {
        ['stats-present','stats-logs','stats-permission-pending'].forEach(id => document.getElementById(id).textContent = '...');
        document.getElementById('stats-score').textContent = '...';
        document.getElementById('eval-error').classList.add('hidden');
        document.getElementById('eval-success').classList.add('hidden');

        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${studentId}/stats`);
            const data = await res.json();
            document.getElementById('stats-present').textContent = data.total_present;
            document.getElementById('stats-logs').textContent = data.total_logs;
            document.getElementById('stats-permission-pending').textContent = data.permission_requests?.pending || 0;
            document.getElementById('stats-score').textContent = data.evaluation?.score ?? '—';

            if (data.evaluation) {
                document.getElementById('eval-score').value = data.evaluation.score;
                document.getElementById('eval-comments').value = data.evaluation.comments || '';
            } else {
                document.getElementById('eval-score').value = '';
                document.getElementById('eval-comments').value = '';
            }
        } catch (e) { console.error('Failed to load stats:', e); }
    }

    window.submitEvaluation = async () => {
        const studentId = document.getElementById('eval-student-id').value;
        const score = document.getElementById('eval-score').value;
        const comments = document.getElementById('eval-comments').value.trim();
        const errorEl = document.getElementById('eval-error');
        const successEl = document.getElementById('eval-success');
        const btn = document.getElementById('eval-btn');

        errorEl.classList.add('hidden');
        successEl.classList.add('hidden');

        if (score === '' || score < 0 || score > 100) {
            errorEl.textContent = 'Nilai harus antara 0 dan 100.';
            errorEl.classList.remove('hidden');
            return;
        }

        btn.textContent = 'Menyimpan...';
        btn.disabled = true;

        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${studentId}/evaluate`, {
                method: 'POST',
                body: JSON.stringify({ score: parseInt(score), comments }),
            });
            const data = await res.json();
            if (!res.ok) {
                errorEl.textContent = data.error || data.message || 'Gagal menyimpan.';
                errorEl.classList.remove('hidden');
                return;
            }
            successEl.textContent = 'Penilaian berhasil disimpan.';
            successEl.classList.remove('hidden');
            document.getElementById('stats-score').textContent = score;
        } catch (e) {
            errorEl.textContent = 'Terjadi kesalahan.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.textContent = 'Simpan Penilaian';
            btn.disabled = false;
        }
    };

    // ── Permissions tab ─────────────────────────────────────────────

    async function loadPermissions(studentId) {
        const el = document.getElementById('permissions-list');
        el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Memuat...</p>';
        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${studentId}/permissions`);
            const perms = await res.json();
            if (perms.length === 0) {
                el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Tidak ada pengajuan izin.</p>';
                return;
            }
            el.innerHTML = `<div class="space-y-2">
                ${perms.map(p => `
                <div class="border border-gray-100 rounded-lg p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-sm font-medium text-gray-800">${formatType(p.type)}</span>
                            <span class="ml-2 text-xs text-gray-500">${formatDate(p.request_date)}${p.end_date && p.end_date !== p.request_date ? ' s/d ' + formatDate(p.end_date) : ''}</span>
                            <span class="ml-2 ${statusBadge(p.status)}">${p.status}</span>
                            ${p.reason ? `<p class="text-sm text-gray-600 mt-1">${p.reason}</p>` : ''}
                            ${p.handler_note ? `<p class="text-xs text-gray-400 mt-1">Catatan: ${p.handler_note}</p>` : ''}
                        </div>
                        ${p.status === 'pending' ? `
                        <div class="flex gap-2 shrink-0">
                            <button onclick="approvePermission(${p.id}, this)" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">Setuju</button>
                            <button onclick="rejectPermission(${p.id}, this)" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg">Tolak</button>
                        </div>` : ''}
                    </div>
                </div>`).join('')}
            </div>`;
        } catch (e) { console.error('Failed to load permissions:', e); }
    }

    window.approvePermission = async (id, btn) => {
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await loadPermissions(currentStudentId);
        await loadStudents();
        await loadPendingInbox();
    };

    window.rejectPermission = async (id, btn) => {
        const note = prompt('Catatan penolakan (opsional):');
        if (note === null) return;
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
        await loadPermissions(currentStudentId);
        await loadStudents();
        await loadPendingInbox();
    };

    // ── Logs tab ────────────────────────────────────────────────────

    async function loadLogs(studentId) {
        const el = document.getElementById('logs-list');
        el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Memuat...</p>';
        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${studentId}/logs`);
            const logs = await res.json();
            if (logs.length === 0) {
                el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Belum ada log aktivitas.</p>';
                return;
            }
            el.innerHTML = `<div class="space-y-3">
                ${logs.map(log => {
                    const reviewBadge = log.review_status === 'approved'
                        ? '<span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Disetujui</span>'
                        : log.review_status === 'needs_revision'
                        ? '<span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Perlu Revisi</span>'
                        : '<span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Belum Direview</span>';
                    return `
                <div class="border border-gray-100 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-800">${formatDate(log.log_date)}</span>
                            ${reviewBadge}
                            ${log.comments?.length > 0 ? `<span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">💬 ${log.comments.length}</span>` : ''}
                        </div>
                        <div class="flex gap-2">
                            <button onclick="reviewLog(${log.id}, 'approved')" class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded-lg">Setujui</button>
                            <button onclick="reviewLog(${log.id}, 'needs_revision')" class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-2 py-1 rounded-lg">Perlu Revisi</button>
                            <button onclick="toggleCommentForm(${log.id})" class="text-xs text-blue-600 hover:underline">+ Komentar</button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 whitespace-pre-wrap">${log.activities || '—'}</p>
                    ${log.review_note ? `<p class="text-xs text-gray-400 mt-1 italic">Catatan review: ${log.review_note}</p>` : ''}
                    ${log.comments?.length > 0 ? `
                    <div class="mt-3 space-y-1 border-t border-gray-50 pt-2">
                        ${log.comments.map(c => `
                        <div class="text-xs rounded px-3 py-1.5 ${c.author_role === 'student' ? 'bg-green-50' : 'bg-blue-50'}">
                            <span class="font-medium ${c.author_role === 'student' ? 'text-green-800' : 'text-blue-800'}">${c.author?.name || (c.author_role === 'student' ? 'Murid' : 'Guru')}</span>
                            <span class="text-gray-500 ml-1">${formatDate(c.created_at)}</span>
                            <p class="text-gray-700 mt-0.5">${c.comment}</p>
                        </div>`).join('')}
                    </div>` : ''}
                    <div id="comment-form-${log.id}" class="hidden mt-3">
                        <div class="flex gap-2">
                            <input type="text" id="comment-input-${log.id}" placeholder="Tulis komentar..."
                                class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                            <button onclick="submitComment(${log.id})" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">Kirim</button>
                        </div>
                    </div>
                </div>`;
                }).join('')}
            </div>`;
        } catch (e) { console.error('Failed to load logs:', e); }
    }

    window.toggleCommentForm = (logId) => {
        const form = document.getElementById(`comment-form-${logId}`);
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) document.getElementById(`comment-input-${logId}`).focus();
    };

    window.submitComment = async (logId) => {
        const input = document.getElementById(`comment-input-${logId}`);
        const comment = input.value.trim();
        if (!comment) return;
        input.disabled = true;
        await Auth.apiFetch(`/teacher/panel/daily-logs/${logId}/comments`, {
            method: 'POST',
            body: JSON.stringify({ comment }),
        });
        await loadLogs(currentStudentId);
    };

    window.reviewLog = async (logId, status) => {
        let note = null;
        if (status === 'needs_revision') {
            note = prompt('Catatan untuk murid (opsional):');
            if (note === null) return; // user cancel
        }
        await Auth.apiFetch(`/teacher/panel/daily-logs/${logId}/review`, {
            method: 'POST',
            body: JSON.stringify({ status, note }),
        });
        await loadLogs(currentStudentId);
    };

    // ── Documents tab ───────────────────────────────────────────────

    async function loadDocuments(studentId) {
        const el = document.getElementById('documents-list');
        el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Memuat...</p>';
        try {
            const res = await Auth.apiFetch(`/teacher/students/${studentId}/documents`);
            const docs = await res.json();
            if (!docs.length) {
                el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Belum ada dokumen yang diupload.</p>';
                return;
            }
            el.innerHTML = `<div class="space-y-2">
                ${docs.map(d => `
                <div class="border border-gray-100 rounded-lg p-4 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800">${d.title}</p>
                        <p class="text-xs text-gray-500 mt-0.5">${d.file_type?.toUpperCase()} · ${formatDate(d.created_at)}</p>
                        <span class="text-xs ${statusBadge(d.status)} mt-1 inline-block">${d.status}</span>
                        ${d.teacher_note ? `<p class="text-xs text-gray-400 mt-1">Catatan: ${d.teacher_note}</p>` : ''}
                    </div>
                    <div class="flex gap-2 shrink-0 items-center">
                        <a href="/storage/${d.file_path}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat</a>
                        ${d.status === 'pending' ? `
                        <button onclick="approveDoc(${d.id}, this)" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">Setuju</button>
                        <button onclick="rejectDoc(${d.id}, this)" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg">Tolak</button>` : ''}
                    </div>
                </div>`).join('')}
            </div>`;
        } catch (e) { console.error('Failed to load documents:', e); }
    }

    window.approveDoc = async (id, btn) => {
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/documents/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await loadDocuments(currentStudentId);
        await loadStudents();
        await loadPendingInbox();
    };

    window.rejectDoc = async (id, btn) => {
        const note = prompt('Catatan penolakan (wajib):');
        if (!note) return;
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/documents/${id}/reject`, { method: 'POST', body: JSON.stringify({ teacher_note: note }) });
        await loadDocuments(currentStudentId);
        await loadStudents();
        await loadPendingInbox();
    };

    // ── Helpers ─────────────────────────────────────────────────────

    function formatDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatType(type) {
        return { sick: 'Sakit', permit: 'Izin', other: 'Lainnya' }[type] || type;
    }

    function statusBadge(status) {
        return {
            pending: 'px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700',
            approved: 'px-2 py-0.5 rounded-full bg-green-100 text-green-700',
            rejected: 'px-2 py-0.5 rounded-full bg-red-100 text-red-700',
        }[status] || 'px-2 py-0.5 rounded-full bg-gray-100 text-gray-600';
    }
</script>
@endpush
