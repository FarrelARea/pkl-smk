@extends('layouts.app')

@section('title', 'Rekap Penilaian PKL')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Rekap Penilaian PKL</h1>
            <p class="text-sm text-on-surface-variant">Lihat dan export data penilaian terstruktur per jurusan</p>
        </div>
        <x-help-button title="Panduan Rekap Penilaian">
            <p>Di halaman ini kamu bisa melihat rekap penilaian PKL.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Lihat data penilaian per kelas/jurusan</li>
                <li>Filter berdasarkan kelas, status, dan tahun akademik</li>
                <li>Export data penilaian ke Excel</li>
                <li>Lihat detail penilaian setiap siswa</li>
            </ul>
        </x-help-button>
        <button onclick="exportData()" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider flex items-center gap-2 hover:shadow-md transition-shadow">
            <span class="material-symbols-outlined text-sm">download</span> Export Excel
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex items-end gap-4 flex-wrap">
        <div class="w-64">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Kelas / Jurusan</label>
            <select id="filter-class" onchange="loadRecap()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Semua Kelas</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Status</label>
            <select id="filter-status" onchange="loadRecap()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Semua</option>
                <option value="submitted">Submitted</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        <div class="w-48">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Tahun Akademik</label>
            <select id="filter-academic-year" onchange="onAcademicYearChange()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Semua</option>
            </select>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10">
            <p class="text-2xl font-bold text-primary" id="summary-total">0</p>
            <p class="text-xs text-on-surface-variant mt-1">Total Penilaian</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10">
            <p class="text-2xl font-bold text-tertiary" id="summary-submitted">0</p>
            <p class="text-xs text-on-surface-variant mt-1">Submitted</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10">
            <p class="text-2xl font-bold text-secondary" id="summary-draft">0</p>
            <p class="text-xs text-on-surface-variant mt-1">Draft</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/10">
            <p class="text-2xl font-bold text-on-surface" id="summary-avg">—</p>
            <p class="text-xs text-on-surface-variant mt-1">Rata-rata Keseluruhan</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Siswa</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Kelas</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Guru</th>
                    <th class="px-6 py-4 text-center text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">TP1</th>
                    <th class="px-6 py-4 text-center text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">TP2</th>
                    <th class="px-6 py-4 text-center text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">TP3</th>
                    <th class="px-6 py-4 text-center text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">TP4</th>
                    <th class="px-6 py-4 text-center text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Rata-rata</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-right text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="10" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
            </tbody>
        </table>
        </div>
        <div id="pagination"></div>
    </div>
</div>

{{-- Detail Modal --}}
<div id="detail-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="AdminUtils.hideModal('detail-modal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto relative">
            <div class="flex justify-between items-center p-6 border-b border-surface-container">
                <h3 id="detail-modal-title" class="text-lg font-bold font-headline">Detail Penilaian</h3>
                <button onclick="AdminUtils.hideModal('detail-modal')" class="p-1 text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div id="detail-content" class="p-6"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    let currentPage = 1;
    let allItems = [];

    async function loadRecap(page = 1) {
        currentPage = page;
        const classId = document.getElementById('filter-class').value;
        const status = document.getElementById('filter-status').value;

        const academicYear = document.getElementById('filter-academic-year').value;

        let url = '/student-assessments?page=' + page + '&per_page=15';
        if (classId) url += '&class_id=' + classId;
        if (status) url += '&status=' + status;
        if (academicYear) url += '&academic_year=' + encodeURIComponent(academicYear);

        try {
            const res = await Auth.apiFetch(url);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;
            allItems = items;

            // Update summary
            updateSummary(items, meta);

            const tbody = document.getElementById('table-body');
            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="10" class="px-6 py-12 text-center text-on-surface-variant">Belum ada data penilaian.</td></tr>';
                AdminUtils.renderPagination('pagination', meta, (p) => loadRecap(p));
                return;
            }

            // We need to fetch scores for each assessment to show section averages
            // Load details for visible items
            const detailPromises = items.map(item =>
                Auth.apiFetch('/student-assessments/' + item.id).then(r => r.json())
            );
            const details = await Promise.all(detailPromises);

            tbody.innerHTML = items.map((row, idx) => {
                const detail = details[idx];
                const sectionAvgs = {};
                (detail.sections || []).forEach(sec => {
                    sectionAvgs[sec.section_number] = sec.average;
                });

                const statusBadge = row.status === 'submitted'
                    ? '<span class="px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-widest bg-tertiary-fixed text-on-tertiary-fixed-variant">Submitted</span>'
                    : '<span class="px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-widest bg-surface-container-highest text-on-surface-variant">Draft</span>';

                const studentClasses = row.student?.classes?.map(c => c.name).join(', ') || '—';

                return `<tr class="hover:bg-surface-container-high transition-colors border-b border-surface-container">
                    <td class="px-6 py-4 text-sm font-medium text-on-surface">${row.student?.name || '—'}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">${studentClasses}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">${row.teacher?.name || '—'}</td>
                    <td class="px-6 py-4 text-sm text-center font-semibold">${sectionAvgs['1'] ?? '—'}</td>
                    <td class="px-6 py-4 text-sm text-center font-semibold">${sectionAvgs['2'] ?? '—'}</td>
                    <td class="px-6 py-4 text-sm text-center font-semibold">${sectionAvgs['3'] ?? '—'}</td>
                    <td class="px-6 py-4 text-sm text-center font-semibold">${sectionAvgs['4'] ?? '—'}</td>
                    <td class="px-6 py-4 text-sm text-center font-bold text-primary">${detail.overall_average ?? '—'}</td>
                    <td class="px-6 py-4 text-sm">${statusBadge}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="viewDetail(${row.id})" class="p-2 text-on-surface-variant hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                        </button>
                    </td>
                </tr>`;
            }).join('');

            AdminUtils.renderPagination('pagination', meta, (p) => loadRecap(p));
        } catch (e) {
            console.error('Failed to load recap:', e);
            AdminUtils.showToast('Gagal memuat data penilaian', 'error');
        }
    }

    function updateSummary(items, meta) {
        const total = meta.total || items.length;
        const submitted = items.filter(i => i.status === 'submitted').length;
        const draft = items.filter(i => i.status === 'draft').length;

        document.getElementById('summary-total').textContent = total;
        document.getElementById('summary-submitted').textContent = submitted;
        document.getElementById('summary-draft').textContent = draft;
        document.getElementById('summary-avg').textContent = '—';
    }

    async function viewDetail(id) {
        try {
            const res = await Auth.apiFetch('/student-assessments/' + id);
            const data = await res.json();
            const assessment = data.assessment;
            const snapshot = assessment.template_snapshot || {};

            let html = `
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-on-surface">${assessment.student?.name || '—'}</h4>
                        <p class="text-sm text-on-surface-variant">Guru: ${assessment.teacher?.name || '—'} &middot; Tempat PKL: ${assessment.internship?.company?.name || '—'}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-primary">${data.overall_average ?? '—'}</p>
                        <p class="text-xs text-on-surface-variant">Rata-rata</p>
                    </div>
                </div>
            `;

            (data.sections || []).forEach(sec => {
                const sectionInfo = (snapshot.sections || []).find(s => String(s.number) === String(sec.section_number));
                const title = sectionInfo ? `TP ${sectionInfo.number}: ${sectionInfo.title}` : `Section ${sec.section_number}`;

                html += `
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="font-bold text-sm text-on-surface">${title}</h5>
                        <span class="text-sm font-bold text-primary">${sec.average}</span>
                    </div>
                    <div class="space-y-1 ml-2">`;

                (sec.scores || []).forEach(score => {
                    const additional = score.is_additional ? ' <span class="text-xs text-primary/60 italic">(tambahan)</span>' : '';
                    html += `
                    <div class="flex items-center justify-between text-sm py-1 border-b border-surface-container/50">
                        <span class="text-on-surface-variant"><span class="font-medium text-on-surface">${score.indicator_number}</span> ${score.indicator_description}${additional}</span>
                        <div class="flex items-center gap-3">
                            ${score.notes ? `<span class="text-xs text-on-surface-variant">${score.notes}</span>` : ''}
                            <span class="font-bold text-on-surface w-8 text-right">${score.score}</span>
                        </div>
                    </div>`;
                });

                html += `</div></div>`;
            });

            if (assessment.teacher_notes) {
                html += `
                <div class="mt-4 pt-4 border-t border-surface-container">
                    <h5 class="font-bold text-sm text-on-surface mb-1">Catatan Guru Pembimbing</h5>
                    <p class="text-sm text-on-surface-variant">${assessment.teacher_notes}</p>
                </div>`;
            }

            // Documents section
            const docs = data.documents || [];
            html += `
            <div class="mt-4 pt-4 border-t border-surface-container">
                <h5 class="font-bold text-sm text-on-surface mb-3">Dokumen Laporan Siswa</h5>`;

            if (docs.length === 0) {
                html += `<p class="text-sm text-on-surface-variant">Belum ada dokumen yang diupload.</p>`;
            } else {
                html += `<div class="space-y-2">`;
                docs.forEach(doc => {
                    const statusStyles = {
                        pending: 'bg-surface-container-highest text-on-surface-variant',
                        approved: 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                        rejected: 'bg-error-container text-on-error-container',
                    };
                    const statusCls = statusStyles[doc.status] || statusStyles.pending;
                    const date = new Date(doc.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

                    html += `
                    <div class="flex items-center justify-between bg-surface-container-low rounded-lg px-4 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-on-surface truncate">${doc.title}</p>
                            <p class="text-xs text-on-surface-variant">${(doc.file_type || '').toUpperCase()} &middot; ${date}</p>
                            ${doc.teacher_note ? `<p class="text-xs text-on-surface-variant mt-0.5 italic">Catatan: ${doc.teacher_note}</p>` : ''}
                        </div>
                        <div class="flex items-center gap-2 ml-3 shrink-0">
                            <span class="px-2.5 py-1 rounded-full text-[0.6rem] font-bold uppercase tracking-widest ${statusCls}">${doc.status}</span>
                            <a href="/storage/${doc.file_path}" target="_blank" class="p-1.5 text-primary hover:text-primary/80 transition-colors" title="Lihat dokumen">
                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                            </a>
                        </div>
                    </div>`;
                });
                html += `</div>`;
            }
            html += `</div>`;

            document.getElementById('detail-content').innerHTML = html;
            document.getElementById('detail-modal-title').textContent = 'Detail Penilaian — ' + (assessment.student?.name || '');
            AdminUtils.showModal('detail-modal');
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Gagal memuat detail penilaian', 'error');
        }
    }

    async function exportData() {
        const classId = document.getElementById('filter-class').value;
        const academicYear = document.getElementById('filter-academic-year').value;
        let url = '/api/v1/export/assessments';
        const params = [];
        if (classId) params.push('class_id=' + classId);
        if (academicYear) params.push('academic_year=' + encodeURIComponent(academicYear));
        if (params.length) url += '?' + params.join('&');

        try {
            const res = await fetch(url, {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const a = document.createElement('a');
            a.href = window.URL.createObjectURL(blob);
            a.download = 'rekap-penilaian-pkl.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(a.href);
            document.body.removeChild(a);
            AdminUtils.showToast('Export berhasil');
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Gagal export data', 'error');
        }
    }

    async function loadAcademicYears() {
        try {
            const res = await Auth.apiFetch('/classes/academic-years');
            const years = await res.json();
            const select = document.getElementById('filter-academic-year');
            select.innerHTML = '<option value="">Semua</option>';
            years.forEach(y => {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                select.appendChild(opt);
            });
        } catch (e) { console.error('Failed to load academic years:', e); }
    }

    function onAcademicYearChange() {
        // Reload class dropdown filtered by academic year, then reload data
        const academicYear = document.getElementById('filter-academic-year').value;
        const classUrl = academicYear ? '/classes?academic_year=' + encodeURIComponent(academicYear) : '/classes';
        AdminUtils.populateSelect('filter-class', classUrl);
        loadRecap();
    }

    // Expose to window
    window.loadRecap = loadRecap;
    window.viewDetail = viewDetail;
    window.exportData = exportData;
    window.onAcademicYearChange = onAcademicYearChange;

    // Init
    if (Auth.requireAuth()) {
        await loadAcademicYears();
        AdminUtils.populateSelect('filter-class', '/classes');
        loadRecap();
    }
</script>
@endpush
