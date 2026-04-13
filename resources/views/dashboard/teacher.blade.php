<div class="space-y-6">
    <x-help-button title="Panduan Dashboard Guru">
        <p>Ini adalah halaman utama untuk guru pembimbing. Di sini kamu bisa:</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Lihat daftar siswa bimbingan kamu</li>
            <li>Setujui atau tolak pengajuan izin/sakit siswa</li>
            <li>Review dokumen yang diupload siswa</li>
            <li>Lihat detail magang setiap siswa</li>
        </ul>
    </x-help-button>

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
            <div class="flex items-center gap-3">
                <select id="filter-academic-year" onchange="onAcademicYearChange()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Semua Tahun</option>
                </select>
                <span id="student-count" class="text-sm text-gray-400"></span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tempat Magang</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Menunggu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="teacher-students-body">
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script type="module">
    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await loadAcademicYears();
        await Promise.all([loadStudents(), loadPendingInbox()]);
    });

    async function loadAcademicYears() {
        try {
            const res = await Auth.apiFetch('/classes/academic-years');
            const years = await res.json();
            const select = document.getElementById('filter-academic-year');
            select.innerHTML = '<option value="">Semua Tahun</option>';
            years.forEach(y => {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                select.appendChild(opt);
            });
        } catch (e) { console.error('Failed to load academic years:', e); }
    }

    window.onAcademicYearChange = () => { loadStudents(); };

    // ── Students table ──────────────────────────────────────────────

    async function loadStudents() {
        try {
            const academicYear = document.getElementById('filter-academic-year')?.value || '';
            let studentsUrl = '/teacher/panel/students';
            if (academicYear) studentsUrl += '?academic_year=' + encodeURIComponent(academicYear);
            const res = await Auth.apiFetch(studentsUrl);
            const students = await res.json();
            document.getElementById('student-count').textContent = `${students.length} murid`;

            const tbody = document.getElementById('teacher-students-body');
            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada murid yang ditugaskan ke Anda.</td></tr>';
                return;
            }

            tbody.innerHTML = students.map(s => {
                const company = s.active_internship?.company || '—';
                const badges = [];
                if (s.pending_permission_count > 0) badges.push(`<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">${s.pending_permission_count} izin</span>`);
                if (s.pending_document_count > 0) badges.push(`<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">${s.pending_document_count} dok</span>`);
                return `<tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${s.name}<br><span class="text-xs text-gray-400 font-normal">${s.email}</span></td>
                    <td class="px-4 py-3 text-sm text-gray-600">${company}</td>
                    <td class="px-4 py-3 text-sm">${badges.join(' ') || '<span class="text-gray-300 text-xs">—</span>'}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="/teacher/students/${s.id}" class="text-blue-600 hover:underline text-xs font-medium">Lihat Detail</a>
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
            document.getElementById('inbox-count').textContent = total > 0 ? `${total} item menunggu` : 'Tidak ada yang menunggu';

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

    // ── Helpers ─────────────────────────────────────────────────────

    function formatDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatType(type) {
        return { sick: 'Sakit', permit: 'Izin', other: 'Lainnya' }[type] || type;
    }
</script>
@endpush
