@extends('layouts.app')

@section('title', 'Template Penilaian')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Template Penilaian</h1>
            <p class="text-sm text-on-surface-variant">Kelola template penilaian PKL per jurusan</p>
        </div>
        <x-help-button title="Panduan Template Penilaian">
            <p>Di halaman ini kamu bisa mengelola template penilaian PKL.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Buat template penilaian baru per kelas/jurusan</li>
                <li>Tambahkan bagian (Tujuan Pembelajaran) dan indikator</li>
                <li>Edit dan hapus template yang sudah ada</li>
            </ul>
        </x-help-button>
        <button onclick="openCreateModal()" class="primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider px-5 py-2.5 flex items-center gap-2 shadow-md hover:shadow-lg transition-shadow">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Template
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex items-end gap-4 flex-wrap">
        <div class="w-48">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Tahun Akademik</label>
            <select id="filter-academic-year" onchange="onAcademicYearChange()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Semua</option>
            </select>
        </div>
        <div class="w-64">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Class / Jurusan</label>
            <select id="filter-class" onchange="loadData()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Semua Kelas</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Nama</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Kelas</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Bagian</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-right text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
            </tbody>
        </table>
        </div>
        <div id="pagination"></div>
    </div>
</div>

{{-- Create/Edit Template Modal --}}
<div id="template-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="AdminUtils.hideModal('template-modal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto relative">
            <div class="flex justify-between items-center p-6 border-b border-surface-container">
                <h3 id="template-modal-title" class="text-lg font-bold font-headline">Tambah Template</h3>
                <button onclick="AdminUtils.hideModal('template-modal')" class="p-1 text-on-surface-variant hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <form id="template-form" onsubmit="event.preventDefault(); saveTemplate()">
                    <input type="hidden" id="form-id">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Nama Template</label>
                                <input type="text" id="form-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. Observasi Penilaian AKL">
                            </div>
                            <div>
                                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Class / Jurusan</label>
                                <select id="form-class" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Pilih Kelas</option>
                                </select>
                            </div>
                        </div>

                        {{-- Sections Container --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest">Bagian (Tujuan Pembelajaran)</label>
                                <button type="button" onclick="addSection()" class="text-xs font-bold text-primary hover:text-primary/80 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">add</span> Tambah Bagian
                                </button>
                            </div>
                            <div id="sections-container" class="space-y-4"></div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="AdminUtils.hideModal('template-modal')" class="px-5 py-2.5 rounded-md text-xs font-bold uppercase tracking-wider text-on-surface-variant hover:bg-surface-container-high transition-colors">Batal</button>
                        <button type="submit" class="primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider px-5 py-2.5 shadow-md hover:shadow-lg transition-shadow">Simpan Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- View Template Detail Modal --}}
<div id="detail-modal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" onclick="AdminUtils.hideModal('detail-modal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto relative">
            <div class="flex justify-between items-center p-6 border-b border-surface-container">
                <h3 id="detail-modal-title" class="text-lg font-bold font-headline">Detail Template</h3>
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
    let sectionCounter = 0;

    // ── Data Loading ──────────────────────────────────────
    async function loadData(page = 1) {
        currentPage = page;
        const classId = document.getElementById('filter-class').value;
        const academicYear = document.getElementById('filter-academic-year').value;
        let url = '/assessment-templates?page=' + page;
        if (classId) url += '&class_id=' + classId;
        if (academicYear) url += '&academic_year=' + encodeURIComponent(academicYear);

        try {
            const res = await Auth.apiFetch(url);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;
            allItems = items;

            AdminUtils.renderTable('table-body', items, [
                { key: 'name' },
                { key: 'school_class', render: (row) => row.school_class?.name || '—' },
                { key: 'sections', render: (row) => row.sections ? row.sections.length + ' sections' : '—' },
                { key: 'is_active', render: (row) => row.is_active
                    ? '<span class="px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-widest bg-tertiary-fixed text-on-tertiary-fixed-variant">Active</span>'
                    : '<span class="px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-widest bg-surface-container-highest text-on-surface-variant">Inactive</span>'
                },
            ], (row) => `
                <div class="flex justify-end gap-1">
                    <button onclick="viewItem(${row.id})" class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">visibility</span></button>
                    <button onclick="editItem(${row.id})" class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">edit</span></button>
                    <button onclick="deleteItem(${row.id}, '${(row.name || '').replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-error transition-colors"><span class="material-symbols-outlined text-sm">delete</span></button>
                </div>
            `);

            AdminUtils.renderPagination('pagination', meta, (p) => loadData(p));
        } catch (e) {
            console.error('Gagal memuat data template:', e);
            AdminUtils.showToast('Gagal memuat data template', 'error');
        }
    }

    // ── Section Builder ───────────────────────────────────
    function sectionHTML(sectionIdx, data = {}) {
        const indicators = data.indicators || [];
        let indicatorRows = '';
        indicators.forEach((ind, i) => {
            indicatorRows += indicatorHTML(sectionIdx, i, ind);
            if (ind.children && ind.children.length > 0) {
                ind.children.forEach((child, ci) => {
                    indicatorRows += subIndicatorHTML(sectionIdx, i, ci, child);
                });
            }
        });

        return `
        <div class="section-block bg-surface-container-low rounded-lg p-4 border border-outline-variant/20" data-section="${sectionIdx}">
            <div class="flex items-center gap-3 mb-3">
                <span class="text-xs font-bold text-on-surface-variant uppercase">TP</span>
                <input type="number" class="section-number w-16 px-2 py-1.5 bg-surface-container-lowest border border-outline-variant/20 rounded text-sm" placeholder="#" value="${data.number || ''}" min="1" required>
                <input type="text" class="section-title flex-1 px-3 py-1.5 bg-surface-container-lowest border border-outline-variant/20 rounded text-sm" placeholder="Judul bagian..." value="${(data.title || '').replace(/"/g, '&quot;')}" required>
                <button type="button" onclick="removeSection(this)" class="p-1 text-error/70 hover:text-error"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            <div class="indicators-container space-y-2 ml-4" data-section="${sectionIdx}">
                ${indicatorRows}
            </div>
            <button type="button" onclick="addIndicator(${sectionIdx})" class="mt-2 ml-4 text-xs font-bold text-primary/80 hover:text-primary flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">add</span> Tambah Indikator
            </button>
        </div>`;
    }

    function indicatorHTML(sectionIdx, indIdx, data = {}) {
        return `
        <div class="indicator-row flex items-center gap-2" data-indicator="${indIdx}">
            <input type="text" class="ind-number w-16 px-2 py-1.5 bg-surface-container-lowest border border-outline-variant/20 rounded text-xs" placeholder="1.1" value="${data.number || ''}">
            <input type="text" class="ind-description flex-1 px-3 py-1.5 bg-surface-container-lowest border border-outline-variant/20 rounded text-xs" placeholder="Indicator description..." value="${(data.description || '').replace(/"/g, '&quot;')}">
            <button type="button" onclick="addSubIndicator(${sectionIdx}, this)" class="p-1 text-primary/60 hover:text-primary" title="Add sub-indicator"><span class="material-symbols-outlined text-sm">subdirectory_arrow_right</span></button>
            <button type="button" onclick="removeIndicator(this)" class="p-1 text-error/60 hover:text-error"><span class="material-symbols-outlined text-sm">close</span></button>
        </div>`;
    }

    function subIndicatorHTML(sectionIdx, indIdx, subIdx, data = {}) {
        return `
        <div class="sub-indicator-row flex items-center gap-2 ml-8" data-parent-indicator="${indIdx}">
            <span class="material-symbols-outlined text-sm text-outline">subdirectory_arrow_right</span>
            <input type="text" class="sub-number w-16 px-2 py-1.5 bg-surface-container-lowest border border-outline-variant/20 rounded text-xs" placeholder="1.1.1" value="${data.number || ''}">
            <input type="text" class="sub-description flex-1 px-3 py-1.5 bg-surface-container-lowest border border-outline-variant/20 rounded text-xs" placeholder="Sub-indicator description..." value="${(data.description || '').replace(/"/g, '&quot;')}">
            <button type="button" onclick="removeIndicator(this)" class="p-1 text-error/60 hover:text-error"><span class="material-symbols-outlined text-sm">close</span></button>
        </div>`;
    }

    function addSection(data = {}) {
        sectionCounter++;
        const container = document.getElementById('sections-container');
        container.insertAdjacentHTML('beforeend', sectionHTML(sectionCounter, data));
    }

    function removeSection(btn) {
        btn.closest('.section-block').remove();
    }

    function addIndicator(sectionIdx) {
        const container = document.querySelector(`.indicators-container[data-section="${sectionIdx}"]`);
        const idx = container.querySelectorAll('.indicator-row').length;
        container.insertAdjacentHTML('beforeend', indicatorHTML(sectionIdx, idx));
    }

    function addSubIndicator(sectionIdx, btn) {
        const indicatorRow = btn.closest('.indicator-row');
        const indIdx = indicatorRow.dataset.indicator;
        // Insert after this indicator row (and any existing sub-indicators of this indicator)
        let insertAfter = indicatorRow;
        let next = indicatorRow.nextElementSibling;
        while (next && next.classList.contains('sub-indicator-row') && next.dataset.parentIndicator === indIdx) {
            insertAfter = next;
            next = next.nextElementSibling;
        }
        const subIdx = indicatorRow.parentElement.querySelectorAll(`.sub-indicator-row[data-parent-indicator="${indIdx}"]`).length;
        insertAfter.insertAdjacentHTML('afterend', subIndicatorHTML(sectionIdx, indIdx, subIdx));
    }

    function removeIndicator(btn) {
        const row = btn.closest('.indicator-row, .sub-indicator-row');
        // If removing a parent indicator, also remove its sub-indicators
        if (row.classList.contains('indicator-row')) {
            const indIdx = row.dataset.indicator;
            const container = row.parentElement;
            container.querySelectorAll(`.sub-indicator-row[data-parent-indicator="${indIdx}"]`).forEach(el => el.remove());
        }
        row.remove();
    }

    // ── Collect Form Data ─────────────────────────────────
    function collectSections() {
        const sections = [];
        document.querySelectorAll('.section-block').forEach((block, sIdx) => {
            const number = parseInt(block.querySelector('.section-number').value);
            const title = block.querySelector('.section-title').value;
            const indicators = [];
            const indicatorRows = block.querySelectorAll('.indicator-row');

            indicatorRows.forEach((row, iIdx) => {
                const indNumber = row.querySelector('.ind-number').value;
                const indDesc = row.querySelector('.ind-description').value;
                if (!indNumber || !indDesc) return;

                const children = [];
                const indIdx = row.dataset.indicator;
                block.querySelectorAll(`.sub-indicator-row[data-parent-indicator="${indIdx}"]`).forEach(sub => {
                    const subNum = sub.querySelector('.sub-number').value;
                    const subDesc = sub.querySelector('.sub-description').value;
                    if (subNum && subDesc) {
                        children.push({ number: subNum, description: subDesc, order: children.length });
                    }
                });

                indicators.push({
                    number: indNumber,
                    description: indDesc,
                    order: indicators.length,
                    children: children.length > 0 ? children : undefined,
                });
            });

            if (number && title && indicators.length > 0) {
                sections.push({ number, title, order: sIdx, indicators });
            }
        });
        return sections;
    }

    // ── CRUD Operations ───────────────────────────────────
    function openCreateModal() {
        document.getElementById('form-id').value = '';
        document.getElementById('form-name').value = '';
        document.getElementById('form-class').value = '';
        document.getElementById('sections-container').innerHTML = '';
        sectionCounter = 0;
        // Add default 4 sections
        addSection({ number: 1, title: 'Menerapkan soft skills yang dibutuhkan dalam dunia kerja (tempat PKL)' });
        addSection({ number: 2, title: 'Menerapkan norma, POS dan K3LH yang ada pada dunia kerja (tempat PKL)' });
        addSection({ number: 3, title: 'Menerapkan kompetensi teknis yang sudah dipelajari di sekolah dan/atau baru dipelajari pada dunia kerja (tempat PKL)' });
        addSection({ number: 4, title: 'Memahami alur bisnis dan pelayanan dunia kerja tempat PKL dan wawasan wirausaha' });
        document.getElementById('template-modal-title').textContent = 'Tambah Template';
        AdminUtils.showModal('template-modal');
    }

    async function editItem(id) {
        try {
            const res = await Auth.apiFetch('/assessment-templates/' + id);
            const template = await res.json();

            document.getElementById('form-id').value = template.id;
            document.getElementById('form-name').value = template.name;
            document.getElementById('form-class').value = template.class_id;
            document.getElementById('sections-container').innerHTML = '';
            sectionCounter = 0;

            (template.sections || []).forEach(section => {
                addSection(section);
            });

            document.getElementById('template-modal-title').textContent = 'Edit Template';
            AdminUtils.showModal('template-modal');
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Failed to load template', 'error');
        }
    }

    async function saveTemplate() {
        const id = document.getElementById('form-id').value;
        const sections = collectSections();

        if (sections.length === 0) {
            AdminUtils.showToast('Please add at least one section with indicators', 'error');
            return;
        }

        const data = {
            name: document.getElementById('form-name').value,
            class_id: document.getElementById('form-class').value,
            sections,
        };

        const url = id ? '/assessment-templates/' + id : '/assessment-templates';
        const method = id ? 'PUT' : 'POST';

        try {
            const res = await Auth.apiFetch(url, {
                method,
                body: JSON.stringify(data),
            });

            if (res.ok) {
                AdminUtils.hideModal('template-modal');
                AdminUtils.showToast(id ? 'Template updated successfully' : 'Template created successfully');
                loadData(currentPage);
            } else {
                const json = await res.json();
                AdminUtils.showToast(json.message || 'Failed to save template', 'error');
            }
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Failed to save template', 'error');
        }
    }

    async function deleteItem(id, name) {
        if (!AdminUtils.confirmDelete(name)) return;

        try {
            const res = await Auth.apiFetch('/assessment-templates/' + id, { method: 'DELETE' });
            if (res.ok) {
                AdminUtils.showToast('Template deleted successfully');
                loadData(currentPage);
            } else {
                const json = await res.json();
                AdminUtils.showToast(json.message || 'Failed to delete template', 'error');
            }
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Failed to delete template', 'error');
        }
    }

    async function viewItem(id) {
        try {
            const res = await Auth.apiFetch('/assessment-templates/' + id);
            const template = await res.json();

            let html = `
                <div class="mb-4">
                    <div class="flex items-center gap-3 mb-1">
                        <h4 class="text-lg font-bold text-on-surface">${template.name}</h4>
                        ${template.is_active
                            ? '<span class="px-2 py-0.5 rounded-full text-[0.6rem] font-bold uppercase bg-tertiary-fixed text-on-tertiary-fixed-variant">Active</span>'
                            : '<span class="px-2 py-0.5 rounded-full text-[0.6rem] font-bold uppercase bg-surface-container-highest text-on-surface-variant">Inactive</span>'}
                    </div>
                    <p class="text-sm text-on-surface-variant">Class: ${template.school_class?.name || '—'}</p>
                </div>
            `;

            (template.sections || []).forEach(section => {
                html += `
                <div class="mb-4">
                    <h5 class="font-bold text-sm text-on-surface mb-2">TP ${section.number}: ${section.title}</h5>
                    <div class="space-y-1 ml-4">
                `;
                (section.indicators || []).forEach(ind => {
                    html += `<div class="text-sm text-on-surface-variant">${ind.number}. ${ind.description}</div>`;
                    (ind.children || []).forEach(child => {
                        html += `<div class="text-sm text-on-surface-variant ml-6">${child.number}. ${child.description}</div>`;
                    });
                });
                html += `</div></div>`;
            });

            document.getElementById('detail-content').innerHTML = html;
            document.getElementById('detail-modal-title').textContent = 'Detail Template';
            AdminUtils.showModal('detail-modal');
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Failed to load template detail', 'error');
        }
    }

    // ── Expose to window ──────────────────────────────────
    window.loadData = loadData;
    window.editItem = editItem;
    window.deleteItem = deleteItem;
    window.viewItem = viewItem;
    window.openCreateModal = openCreateModal;
    window.addSection = addSection;
    window.removeSection = removeSection;
    window.addIndicator = addIndicator;
    window.addSubIndicator = addSubIndicator;
    window.removeIndicator = removeIndicator;
    window.saveTemplate = saveTemplate;
    window.onAcademicYearChange = onAcademicYearChange;

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
        const academicYear = document.getElementById('filter-academic-year').value;
        const classUrl = academicYear ? '/classes?academic_year=' + encodeURIComponent(academicYear) : '/classes';
        AdminUtils.populateSelect('filter-class', classUrl);
        loadData();
    }

    // ── Init ──────────────────────────────────────────────
    if (Auth.requireAuth()) {
        await loadAcademicYears();
        AdminUtils.populateSelect('filter-class', '/classes');
        AdminUtils.populateSelect('form-class', '/classes');
        loadData();
    }
</script>
@endpush
