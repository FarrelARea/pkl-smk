@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="space-y-6">
    {{-- Judul --}}
    <div class="flex items-center gap-4">
        <a href="/dashboard" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900" id="student-name">Memuat...</h1>
            <p class="text-sm text-gray-500" id="student-info"></p>
        </div>
        <x-help-button title="Panduan Detail Siswa">
            <p>Di halaman ini kamu bisa melihat detail lengkap siswa bimbingan.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Lihat informasi magang siswa</li>
                <li>Review daily log dan kehadiran</li>
                <li>Berikan penilaian PKL</li>
                <li>Setujui atau tolak dokumen siswa</li>
            </ul>
        </x-help-button>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
            <p class="text-xs text-gray-500 mt-1">Izin Menunggu</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-purple-600" id="stats-score">—</p>
            <p class="text-xs text-gray-500 mt-1">Nilai Saat Ini</p>
        </div>
    </div>

    {{-- Navigasi Tab --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="border-b border-gray-200 px-6">
            <nav class="flex gap-1 -mb-px" id="tab-nav">
                <button data-tab="assessment" onclick="switchTab('assessment')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600">Penilaian</button>
                <button data-tab="evaluation" onclick="switchTab('evaluation')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Evaluasi</button>
                <button data-tab="permissions" onclick="switchTab('permissions')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Pengajuan Izin</button>
                <button data-tab="logs" onclick="switchTab('logs')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Log Aktivitas</button>
                <button data-tab="attendance" onclick="switchTab('attendance')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Kehadiran</button>
                <button data-tab="documents" onclick="switchTab('documents')" class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">Dokumen</button>
            </nav>
        </div>

        {{-- Tab Penilaian --}}
        <div id="tab-assessment" class="tab-content p-6">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-800">Penilaian Terstruktur</h4>
                <span id="assessment-status-badge"></span>
            </div>
            <input type="hidden" id="eval-student-id">
            <input type="hidden" id="assessment-id">

            <div id="assessment-loading" class="text-sm text-gray-400 text-center py-4">Memuat template...</div>
            <div id="assessment-no-template" class="hidden text-sm text-yellow-600 text-center py-4">
                Belum ada template penilaian untuk kelas siswa ini. Hubungi admin.
            </div>

            <div id="assessment-form" class="hidden">
                <div id="assessment-sections" class="space-y-4"></div>

                <div class="mt-4 border-t border-gray-100 pt-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Guru Pembimbing</label>
                    <textarea id="assessment-teacher-notes" rows="2" placeholder="Catatan penilaian..."
class="w-full px-3 py-1.5 h-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div id="eval-error" class="hidden mt-2 text-sm text-red-600"></div>
                <div id="eval-success" class="hidden mt-2 text-sm text-green-600"></div>

                <div class="flex gap-2 mt-3">
                    <button onclick="submitAssessment('draft')" id="save-draft-btn"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                        Simpan Draft
                    </button>
                    <button onclick="submitAssessment('submitted')" id="submit-btn"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                        Submit Penilaian
                    </button>
                </div>
            </div>
        </div>

        <div id="tab-evaluation" class="tab-content hidden p-6">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-800">Evaluasi Ringkas</h4>
                <span id="evaluation-status-badge"></span>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nilai</label>
                    <input id="evaluation-score" type="number" min="0" max="100" placeholder="0-100" class="w-full px-3 py-1.5 h-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Perusahaan</label>
                    <input id="evaluation-company" type="text" disabled class="w-full px-3 py-1.5 h-8 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Evaluasi</label>
                <textarea id="evaluation-comments" rows="5" placeholder="Tulis evaluasi singkat..." class="w-full px-3 py-1.5 h-8 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500"></textarea>
            </div>
            <div id="simple-eval-error" class="hidden mt-2 text-sm text-red-600"></div>
            <div id="simple-eval-success" class="hidden mt-2 text-sm text-green-600"></div>
            <div class="flex gap-2 mt-3">
                <button onclick="submitSimpleEvaluation()" id="submit-simple-evaluation-btn" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                    Simpan Evaluasi
                </button>
            </div>
        </div>

        {{-- Tab Pengajuan Izin --}}
        <div id="tab-permissions" class="tab-content hidden p-6">
            <div class="flex items-end gap-3 mb-4 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                    <input type="date" id="perm-date-from" onchange="loadPermissions(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                    <input type="date" id="perm-date-to" onchange="loadPermissions(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <button onclick="clearFilter('perm')" class="text-xs text-gray-500 hover:text-gray-700 pb-1.5">Reset</button>
            </div>
            <div id="permissions-list">
                <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
            </div>
        </div>

        {{-- Tab Log Aktivitas --}}
        <div id="tab-logs" class="tab-content hidden p-6">
            <div class="flex items-end gap-3 mb-4 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                    <input type="date" id="logs-date-from" onchange="loadLogs(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                    <input type="date" id="logs-date-to" onchange="loadLogs(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <button onclick="clearFilter('logs')" class="text-xs text-gray-500 hover:text-gray-700 pb-1.5">Reset</button>
            </div>
            <div id="logs-list">
                <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
            </div>
        </div>

        {{-- Tab Kehadiran --}}
        <div id="tab-attendance" class="tab-content hidden p-6">
            <div class="flex items-end gap-3 mb-4 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                    <input type="date" id="att-date-from" onchange="loadAttendanceHistory(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                    <input type="date" id="att-date-to" onchange="loadAttendanceHistory(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <button onclick="resetAttendanceFilter()" class="text-xs text-gray-500 hover:text-gray-700 pb-1.5">Reset</button>
            </div>
            <div id="attendance-list">
                <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
            </div>
        </div>

        {{-- Tab Dokumen --}}
        <div id="tab-documents" class="tab-content hidden p-6">
            <div class="flex items-end gap-3 mb-4 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                    <input type="date" id="docs-date-from" onchange="loadDocuments(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                    <input type="date" id="docs-date-to" onchange="loadDocuments(studentId)" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <button onclick="clearFilter('docs')" class="text-xs text-gray-500 hover:text-gray-700 pb-1.5">Reset</button>
            </div>
            <div id="documents-list">
                <p class="text-sm text-gray-400 text-center py-4">Memuat...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    const studentId = parseInt(window.location.pathname.split('/').pop());
    let currentTab = 'assessment';
    let currentAssessmentData = null;

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        document.getElementById('eval-student-id').value = studentId;
        await loadStats(studentId);
        switchTab('assessment');
    });

    // ── Perpindahan Tab ────────────────────────────────────────────────

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

        if (tab === 'assessment') await loadAssessment(studentId);
        if (tab === 'evaluation') await loadEvaluation(studentId);
        if (tab === 'permissions') await loadPermissions(studentId);
        if (tab === 'logs') await loadLogs(studentId);
        if (tab === 'attendance') await loadAttendanceHistory(studentId);
        if (tab === 'documents') await loadDocuments(studentId);
    };

    // ── Statistik ─────────────────────────────────────────────────────

    async function loadStats(sid) {
        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${sid}/stats`);
            const data = await res.json();
            document.getElementById('student-name').textContent = data.student_name || 'Siswa';
            document.getElementById('student-info').textContent = data.company_name
                ? `PKL di ${data.company_name}`
                : '';
            document.getElementById('stats-present').textContent = data.total_present;
            document.getElementById('stats-logs').textContent = data.total_logs;
            document.getElementById('stats-permission-pending').textContent = data.permission_requests?.pending || 0;
            document.getElementById('stats-score').textContent = data.evaluation?.score ?? '—';
        } catch (e) { console.error('Gagal memuat statistik:', e); }
    }

    // ── Kehadiran / Clock History ──────────────────────────────────────

    async function loadAttendanceHistory(sid) {
        try {
            const from = document.getElementById('att-date-from').value;
            const to = document.getElementById('att-date-to').value;
            const url = `/teacher/panel/students/${sid}/stats${from ? `?start_date=${from}&end_date=${to}` : ''}`;
            const res = await Auth.apiFetch(url);
            const data = await res.json();
            renderAttendanceList(data.clock_history || []);
        } catch (e) {
            console.error('Gagal memuat kehadiran:', e);
            document.getElementById('attendance-list').innerHTML = '<p class="text-sm text-red-500 text-center py-4">Gagal memuat data kehadiran</p>';
        }
    }

    function renderAttendanceList(history) {
        const el = document.getElementById('attendance-list');
        if (!history.length) {
            el.innerHTML = '<p class="text-sm text-gray-400 text-center py-10">Tidak ada riwayat kehadiran di rentang tanggal ini.</p>';
            return;
        }
        el.innerHTML = `<div class="overflow-x-auto"><table class="w-full text-left border-collapse">
            <thead><tr class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Clock In</th>
                <th class="px-4 py-3">Clock Out</th>
                <th class="px-4 py-3">Durasi Kerja</th>
                <th class="px-4 py-3 text-right">Catatan</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                ${history.map(entry => {
                    const ci = entry.clock_in ? formatTime(entry.clock_in) : '—';
                    const co = entry.clock_out ? formatTime(entry.clock_out) : '—';
                    const dur = entry.clock_in && entry.clock_out ? calcDuration(entry.clock_in, entry.clock_out) : '—';
                    const rangeIcon = entry.all_clocks?.[0] ? (entry.all_clocks[0].within_range ? '<span class="text-green-600" title="Di area perusahaan">✓ Di area</span>' : '<span class="text-amber-600" title="Luar area perusahaan">⚠ Luar area</span>') : '';
                    return `<tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">${formatDate(entry.date)}</td>
                        <td class="px-4 py-3 text-sm">${ci} ${rangeIcon}</td>
                        <td class="px-4 py-3 text-sm">${co}</td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-600">${dur}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-400">${entry.all_clocks?.length > 2 ? `${entry.all_clocks.length - 2}x catatan tambahan` : ''}</td>
                    </tr>`;
                }).join('')}
            </tbody>
        </table></div>`;
    }

    window.loadAttendanceHistory = loadAttendanceHistory;
    window.resetAttendanceFilter = () => { setDefaultDateRange('att'); loadAttendanceHistory(studentId); };

    // ── Evaluasi ──────────────────────────────────────────────────────

    async function loadEvaluation(sid) {
        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${sid}/stats`);
            const data = await res.json();
            document.getElementById('evaluation-score').value = data.evaluation?.score ?? '';
            document.getElementById('evaluation-comments').value = data.evaluation?.comments || '';
            document.getElementById('evaluation-company').value = data.company_name || 'Belum ada magang aktif';
            document.getElementById('evaluation-status-badge').innerHTML = data.evaluation
                ? '<span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">Sudah diisi</span>'
                : '<span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Belum diisi</span>';
            document.getElementById('stats-score').textContent = data.evaluation?.score ?? '—';
        } catch (e) {
            console.error('Gagal memuat evaluasi:', e);
        }
    }

    window.submitSimpleEvaluation = async () => {
        const score = parseInt(document.getElementById('evaluation-score').value);
        const comments = document.getElementById('evaluation-comments').value.trim();
        const errorEl = document.getElementById('simple-eval-error');
        const successEl = document.getElementById('simple-eval-success');
        const btn = document.getElementById('submit-simple-evaluation-btn');

        errorEl.classList.add('hidden');
        successEl.classList.add('hidden');

        if (Number.isNaN(score) || score < 0 || score > 100) {
            errorEl.textContent = 'Nilai harus antara 0 dan 100.';
            errorEl.classList.remove('hidden');
            return;
        }

        const original = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${studentId}/evaluate`, {
                method: 'POST',
                body: JSON.stringify({ score, comments: comments || null }),
            });
            const data = await res.json();
            if (!res.ok) {
                errorEl.textContent = data.error || data.message || 'Gagal menyimpan evaluasi.';
                errorEl.classList.remove('hidden');
                return;
            }
            successEl.textContent = 'Evaluasi berhasil disimpan.';
            successEl.classList.remove('hidden');
            await loadStats(studentId);
            await loadEvaluation(studentId);
        } catch (e) {
            console.error(e);
            errorEl.textContent = 'Terjadi kesalahan.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.textContent = original;
        }
    };

    // ── Penilaian ─────────────────────────────────────────────────────

    async function loadAssessment(sid) {
        const loadingEl = document.getElementById('assessment-loading');
        const noTplEl = document.getElementById('assessment-no-template');
        const formEl = document.getElementById('assessment-form');
        const statusBadgeEl = document.getElementById('assessment-status-badge');

        loadingEl.classList.remove('hidden');
        noTplEl.classList.add('hidden');
        formEl.classList.add('hidden');
        statusBadgeEl.innerHTML = '';
        document.getElementById('assessment-id').value = '';
        currentAssessmentData = null;

        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${sid}/assessment`);
            if (res.status === 404) {
                loadingEl.classList.add('hidden');
                noTplEl.classList.remove('hidden');
                document.getElementById('stats-score').textContent = '—';
                return;
            }
            const data = await res.json();
            currentAssessmentData = data;

            loadingEl.classList.add('hidden');
            formEl.classList.remove('hidden');

            if (data.assessment) {
                document.getElementById('assessment-id').value = data.assessment.id;
                document.getElementById('assessment-teacher-notes').value = data.assessment.teacher_notes || '';
                const status = data.assessment.status;
                statusBadgeEl.innerHTML = status === 'submitted'
                    ? '<span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Sudah Dikirim</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Draf</span>';
                renderAssessmentFromExisting(data);
                document.getElementById('stats-score').textContent = data.overall_average ?? '—';
            } else {
                document.getElementById('assessment-teacher-notes').value = '';
                statusBadgeEl.innerHTML = '<span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Belum Dinilai</span>';
                renderAssessmentFromTemplate(data.template);
                document.getElementById('stats-score').textContent = '—';
            }
        } catch (e) {
            console.error('Gagal memuat penilaian:', e);
            loadingEl.classList.add('hidden');
            noTplEl.classList.remove('hidden');
        }
    }

    function renderAssessmentFromTemplate(template) {
        const container = document.getElementById('assessment-sections');
        container.innerHTML = '';
        (template.sections || []).forEach(section => {
            let html = `
            <div class="bg-gray-50 rounded-lg p-4" data-section-number="${section.number}">
                <h5 class="font-semibold text-sm text-gray-800 mb-3">TP ${section.number}: ${section.title}</h5>
                <div class="space-y-2">`;
            (section.indicators || []).forEach(ind => {
                html += indicatorScoreRow(section.number, ind.number, ind.description);
                (ind.children || []).forEach(child => {
                    html += indicatorScoreRow(section.number, child.number, child.description, true);
                });
            });
            html += `</div>
                <button type="button" onclick="addCustomIndicator(this, '${section.number}')" class="mt-2 text-xs text-blue-600 hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Tambah Indikator
                </button>
                <div class="mt-2 text-right">
                    <span class="text-xs text-gray-500">Rata-rata TP ${section.number}: </span>
                    <span class="text-sm font-bold text-gray-700 section-avg" data-section="${section.number}">—</span>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    function renderAssessmentFromExisting(data) {
        const container = document.getElementById('assessment-sections');
        container.innerHTML = '';
        const snapshot = data.assessment.template_snapshot;
        const scoresByKey = {};
        (data.sections || []).forEach(sec => {
            (sec.scores || []).forEach(s => {
                scoresByKey[s.section_number + '|' + s.indicator_number] = s;
            });
        });

        (snapshot.sections || []).forEach(section => {
            let html = `
            <div class="bg-gray-50 rounded-lg p-4" data-section-number="${section.number}">
                <h5 class="font-semibold text-sm text-gray-800 mb-3">TP ${section.number}: ${section.title}</h5>
                <div class="space-y-2">`;
            (section.indicators || []).forEach(ind => {
                const s = scoresByKey[section.number + '|' + ind.number];
                html += indicatorScoreRow(section.number, ind.number, ind.description, false, s?.score, s?.notes);
                (ind.children || []).forEach(child => {
                    const cs = scoresByKey[section.number + '|' + child.number];
                    html += indicatorScoreRow(section.number, child.number, child.description, true, cs?.score, cs?.notes);
                });
            });
            // Indikator tambahan
            (data.sections || []).forEach(sec => {
                (sec.scores || []).forEach(s => {
                    if (s.is_additional && String(s.section_number) === String(section.number)) {
                        html += indicatorScoreRow(section.number, s.indicator_number, s.indicator_description, false, s.score, s.notes, true);
                    }
                });
            });
            html += `</div>
                <button type="button" onclick="addCustomIndicator(this, '${section.number}')" class="mt-2 text-xs text-blue-600 hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Tambah Indikator
                </button>
                <div class="mt-2 text-right">
                    <span class="text-xs text-gray-500">Rata-rata TP ${section.number}: </span>
                    <span class="text-sm font-bold text-gray-700 section-avg" data-section="${section.number}">—</span>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        });
        recalcAverages();
    }

    function indicatorScoreRow(sectionNum, indNum, indDesc, isSub = false, score = '', notes = '', isAdditional = false) {
        return `
        <div class="score-row flex items-center gap-2 ${isSub ? 'ml-6' : ''}" data-section="${sectionNum}" data-indicator="${indNum}" data-additional="${isAdditional}">
            ${isSub ? '<span class="text-gray-400 text-xs">↳</span>' : ''}
            <span class="text-xs text-gray-600 w-12 shrink-0 font-medium">${indNum}</span>
            <span class="ind-desc text-xs text-gray-700 flex-1 ${isAdditional ? 'italic' : ''}" data-desc="${indDesc.replace(/"/g, '&quot;')}">${indDesc}${isAdditional ? ' (tambahan)' : ''}</span>
            <input type="number" class="score-input w-16 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:border-blue-500"
                min="75" max="95" placeholder="75-95" value="${score || ''}" onchange="recalcAverages()">
            <input type="text" class="notes-input w-24 px-2 py-1 border border-gray-200 rounded text-xs focus:outline-none focus:border-blue-500"
                placeholder="Ket." value="${(notes || '').replace(/"/g, '&quot;')}">
        </div>`;
    }

    function recalcAverages() {
        const sections = document.querySelectorAll('[data-section-number]');
        let totalSum = 0, totalCount = 0;
        sections.forEach(sec => {
            const sectionNum = sec.dataset.sectionNumber;
            const inputs = sec.querySelectorAll('.score-input');
            let sum = 0, count = 0;
            inputs.forEach(inp => {
                const v = parseInt(inp.value);
                if (!isNaN(v)) { sum += v; count++; }
            });
            const avg = count > 0 ? (sum / count).toFixed(1) : '—';
            const avgEl = sec.querySelector(`.section-avg[data-section="${sectionNum}"]`);
            if (avgEl) avgEl.textContent = avg;
            totalSum += sum;
            totalCount += count;
        });
        document.getElementById('stats-score').textContent = totalCount > 0 ? (totalSum / totalCount).toFixed(1) : '—';
    }

    window.recalcAverages = recalcAverages;

    window.addCustomIndicator = (btn, sectionNum) => {
        const container = btn.previousElementSibling;
        const existingCustom = container.querySelectorAll('.score-row[data-additional="true"]').length;
        const customNum = sectionNum + '.C' + (existingCustom + 1);
        const desc = prompt('Deskripsi indikator tambahan:');
        if (!desc) return;
        container.insertAdjacentHTML('beforeend', indicatorScoreRow(sectionNum, customNum, desc, false, '', '', true));
    };

    window.submitAssessment = async (status) => {
        const errorEl = document.getElementById('eval-error');
        const successEl = document.getElementById('eval-success');
        const assessmentId = document.getElementById('assessment-id').value;
        errorEl.classList.add('hidden');
        successEl.classList.add('hidden');

        const scores = [];
        document.querySelectorAll('.score-row').forEach(row => {
            const scoreInput = row.querySelector('.score-input');
            const notesInput = row.querySelector('.notes-input');
            const val = parseInt(scoreInput.value);
            if (isNaN(val)) return;
            if (val < 75 || val > 95) { scoreInput.classList.add('border-red-500'); return; }
            scoreInput.classList.remove('border-red-500');
            scores.push({
                section_number: row.dataset.section,
                indicator_number: row.dataset.indicator,
                indicator_description: row.querySelector('.ind-desc')?.dataset?.desc || '',
                score: val,
                notes: notesInput?.value?.trim() || null,
                is_additional: row.dataset.additional === 'true',
            });
        });

        if (scores.length === 0) {
            errorEl.textContent = 'Isi minimal satu indikator dengan nilai.';
            errorEl.classList.remove('hidden');
            return;
        }
        if (scores.find(s => s.score < 75 || s.score > 95)) {
            errorEl.textContent = 'Nilai harus antara 75 dan 95.';
            errorEl.classList.remove('hidden');
            return;
        }

        const payload = {
            scores,
            teacher_notes: document.getElementById('assessment-teacher-notes').value.trim() || null,
            status,
        };

        const btn = status === 'draft' ? document.getElementById('save-draft-btn') : document.getElementById('submit-btn');
        const origText = btn.textContent;
        btn.textContent = 'Menyimpan...';
        btn.disabled = true;

        try {
            let res;
            if (assessmentId) {
                res = await Auth.apiFetch(`/teacher/panel/assessments/${assessmentId}`, { method: 'PUT', body: JSON.stringify(payload) });
            } else {
                payload.internship_id = currentAssessmentData?.internship_id;
                res = await Auth.apiFetch(`/teacher/panel/students/${studentId}/assessment`, { method: 'POST', body: JSON.stringify(payload) });
            }
            const data = await res.json();
            if (!res.ok) {
                errorEl.textContent = data.message || 'Gagal menyimpan penilaian.';
                errorEl.classList.remove('hidden');
                return;
            }
            successEl.textContent = status === 'draft' ? 'Draf berhasil disimpan.' : 'Penilaian berhasil dikirim.';
            successEl.classList.remove('hidden');
            await loadAssessment(studentId);
        } catch (e) {
            console.error(e);
            errorEl.textContent = 'Terjadi kesalahan.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.textContent = origText;
            btn.disabled = false;
        }
    };

    // ── Pengajuan Izin ─────────────────────────────────────────────────

    async function loadPermissions(sid) {
        const el = document.getElementById('permissions-list');
        el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Memuat...</p>';
        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${sid}/permissions`);
            let perms = await res.json();
            perms = filterByDateRange(perms, 'perm', 'request_date');
            if (perms.length === 0) {
                el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Tidak ada pengajuan izin.</p>';
                return;
            }
            el.innerHTML = `<div class="space-y-2">${perms.map(p => `
                <div class="border border-gray-100 rounded-lg p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-sm font-medium text-gray-800">${formatType(p.type)}</span>
                            <span class="ml-2 text-xs text-gray-500">${formatDate(p.request_date)}${p.end_date && p.end_date !== p.request_date ? ' s/d ' + formatDate(p.end_date) : ''}</span>
                            <span class="ml-2 ${badgeCls(p.status)}">${p.status}</span>
                            ${p.reason ? `<p class="text-sm text-gray-600 mt-1">${p.reason}</p>` : ''}
                            ${p.handler_note ? `<p class="text-xs text-gray-400 mt-1">Catatan: ${p.handler_note}</p>` : ''}
                        </div>
                        ${p.status === 'pending' ? `
                        <div class="flex gap-2 shrink-0">
                            <button onclick="approvePermission(${p.id}, this)" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">Setuju</button>
                            <button onclick="rejectPermission(${p.id}, this)" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg">Tolak</button>
                        </div>` : ''}
                    </div>
                </div>`).join('')}</div>`;
        } catch (e) { console.error('Gagal memuat pengajuan izin:', e); }
    }

    window.approvePermission = async (id, btn) => {
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await loadPermissions(studentId);
    };

    window.rejectPermission = async (id, btn) => {
        const note = prompt('Catatan penolakan (opsional):');
        if (note === null) return;
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/panel/permissions/${id}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
        await loadPermissions(studentId);
    };

    // ── Log Aktivitas ────────────────────────────────────────────────

    async function loadLogs(sid) {
        const el = document.getElementById('logs-list');
        el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Memuat...</p>';
        try {
            const res = await Auth.apiFetch(`/teacher/panel/students/${sid}/logs`);
            let logs = await res.json();
            logs = filterByDateRange(logs, 'logs', 'log_date');
            if (logs.length === 0) {
                el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Belum ada log aktivitas.</p>';
                return;
            }
            el.innerHTML = `<div class="space-y-3">${logs.map(log => {
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
                            ${log.comments?.length > 0 ? `<span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">${log.comments.length} komentar</span>` : ''}
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
            }).join('')}</div>`;
        } catch (e) { console.error('Gagal memuat log:', e); }
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
        await Auth.apiFetch(`/teacher/panel/daily-logs/${logId}/comments`, { method: 'POST', body: JSON.stringify({ comment }) });
        await loadLogs(studentId);
    };

    window.reviewLog = async (logId, status) => {
        let note = null;
        if (status === 'needs_revision') {
            note = prompt('Catatan untuk murid (opsional):');
            if (note === null) return;
        }
        await Auth.apiFetch(`/teacher/panel/daily-logs/${logId}/review`, { method: 'POST', body: JSON.stringify({ status, note }) });
        await loadLogs(studentId);
    };

    // ── Dokumen ──────────────────────────────────────────────────────

    async function loadDocuments(sid) {
        const el = document.getElementById('documents-list');
        el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Memuat...</p>';
        try {
            const res = await Auth.apiFetch(`/teacher/students/${sid}/documents`);
            let docs = await res.json();
            docs = filterByDateRange(docs, 'docs', 'created_at');
            if (!docs.length) {
                el.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Belum ada dokumen yang diupload.</p>';
                return;
            }
            el.innerHTML = `<div class="space-y-2">${docs.map(d => `
                <div class="border border-gray-100 rounded-lg p-4 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800">${d.title}</p>
                        <p class="text-xs text-gray-500 mt-0.5">${d.file_type?.toUpperCase()} · ${formatDate(d.created_at)}</p>
                        <span class="text-xs ${badgeCls(d.status)} mt-1 inline-block">${d.status}</span>
                        ${d.teacher_note ? `<p class="text-xs text-gray-400 mt-1">Catatan: ${d.teacher_note}</p>` : ''}
                    </div>
                    <div class="flex gap-2 shrink-0 items-center">
                        <a href="/storage/${d.file_path}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat</a>
                        ${d.status === 'pending' ? `
                        <button onclick="approveDoc(${d.id}, this)" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">Setuju</button>
                        <button onclick="rejectDoc(${d.id}, this)" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg">Tolak</button>` : ''}
                        ${['approved', 'rejected'].includes(d.status) ? `
                        <button onclick="cancelDocApproval(${d.id}, this)" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1 rounded-lg">Batal</button>` : ''}
                    </div>
                </div>`).join('')}</div>`;
        } catch (e) { console.error('Gagal memuat dokumen:', e); }
    }

    window.approveDoc = async (id, btn) => {
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/documents/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
        await loadDocuments(studentId);
    };

    window.rejectDoc = async (id, btn) => {
        const note = prompt('Catatan penolakan (wajib):');
        if (!note) return;
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/documents/${id}/reject`, { method: 'POST', body: JSON.stringify({ teacher_note: note }) });
        await loadDocuments(studentId);
    };

    window.cancelDocApproval = async (id, btn) => {
        btn.disabled = true; btn.textContent = '...';
        await Auth.apiFetch(`/teacher/documents/${id}/cancel-approval`, { method: 'POST', body: JSON.stringify({}) });
        await loadDocuments(studentId);
    };

    // ── Fungsi Bantu ───────────────────────────────────────────────────

    // ── Fungsi Bantu Filter Tanggal ──────────────────────────────────

    function filterByDateRange(items, prefix, dateField) {
        const from = document.getElementById(`${prefix}-date-from`)?.value;
        const to = document.getElementById(`${prefix}-date-to`)?.value;
        if (!from && !to) return items;

        return items.filter(item => {
            const d = (item[dateField] || '').substring(0, 10);
            if (!d) return true;
            if (from && d < from) return false;
            if (to && d > to) return false;
            return true;
        });
    }

    window.clearFilter = (prefix) => {
        document.getElementById(`${prefix}-date-from`).value = '';
        document.getElementById(`${prefix}-date-to`).value = '';
        if (prefix === 'perm') loadPermissions(studentId);
        if (prefix === 'logs') loadLogs(studentId);
        if (prefix === 'docs') loadDocuments(studentId);
    };

    window.loadPermissions = loadPermissions;
    window.loadLogs = loadLogs;
    window.loadDocuments = loadDocuments;
    window.studentId = studentId;

    function formatDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function formatType(type) {
        return { sick: 'Sakit', permit: 'Izin', other: 'Lainnya' }[type] || type;
    }

    function badgeCls(status) {
        return {
            pending: 'px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700',
            approved: 'px-2 py-0.5 rounded-full bg-green-100 text-green-700',
            rejected: 'px-2 py-0.5 rounded-full bg-red-100 text-red-700',
        }[status] || 'px-2 py-0.5 rounded-full bg-gray-100 text-gray-600';
    }

    function formatTime(str) {
        if (!str) return '';
        return new Date(str).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    function calcDuration(ci, co) {
        const diff = new Date(co) - new Date(ci);
        const hours = Math.floor(diff / 3600000);
        const mins = Math.round((diff % 3600000) / 60000);
        return `${hours}j ${mins}m`;
    }

    function setDefaultDateRange(prefix) {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const first = `${y}-${m}-01`;
        const lastDay = new Date(y, now.getMonth() + 1, 0).getDate();
        const last = `${y}-${m}-${String(lastDay).padStart(2, '0')}`;
        document.getElementById(`${prefix}-date-from`).value = first;
        document.getElementById(`${prefix}-date-to`).value = last;
    }

    // Init attendance date range on page load (current month default)
    waitForAuth(() => {
        setDefaultDateRange('att');
    });
</script>
@endpush
