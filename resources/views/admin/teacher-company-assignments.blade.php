@extends('layouts.app')

@section('title', 'Penugasan Perusahaan ke Guru')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Penugasan Perusahaan ke Guru</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Tentukan perusahaan mana yang bisa diakses oleh guru tertentu untuk pengelolaan titik absensi.
        </p>
    </div>
    <x-help-button title="Panduan Penugasan Perusahaan">
        <p>Di halaman ini kamu bisa mengatur akses guru ke perusahaan.</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Tetapkan perusahaan yang bisa dikelola oleh guru</li>
            <li>Guru hanya bisa menambah titik absensi pada perusahaan yang ditugaskan</li>
            <li>Hapus penugasan yang sudah tidak berlaku</li>
        </ul>
    </x-help-button>
    <div class="flex items-center gap-2">
        <div data-help-target="import-export" class="flex items-center gap-2 px-3 py-1.5 bg-surface-container-high rounded-md">
            <button onclick="downloadTemplate()" class="px-3 py-1.5 text-on-surface font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">description</span> Template
            </button>
            <button onclick="exportData()" class="px-3 py-1.5 text-on-surface font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">download</span> Export
            </button>
            <label class="px-3 py-1.5 text-on-surface font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-sm">upload</span> Import
                <input type="file" id="import-file" accept=".xlsx,.xls" class="hidden" onchange="importData(this)">
            </label>
        </div>
        <button onclick="openAssignModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Penugasan
        </button>
    </div>
</header>

<!-- Filter & Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex flex-wrap justify-between items-center border-b border-surface-container gap-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Daftar Penugasan</h2>
        <div class="flex flex-wrap gap-3 items-end">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-56 transition-all" placeholder="Cari guru atau perusahaan..." type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Filter Guru</label>
                <select id="filter-teacher" class="px-3 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="">Semua Guru</option>
                </select>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Guru</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Perusahaan</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Industri</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Ditugaskan</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
        </tbody>
    </table>
    </div>
</div>

<!-- Assign Modal -->
@component('partials.modal', ['id' => 'assign-modal', 'title' => 'Tambah Penugasan Perusahaan ke Guru'])
<div class="space-y-5">
    <!-- Teacher search -->
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Guru <span class="text-red-500">*</span></label>
        <div class="relative mb-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
            <input id="teacher-search" type="text" placeholder="Cari guru..." class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary">
        </div>
        <div id="teacher-list" class="max-h-40 overflow-y-auto border border-outline-variant/20 rounded-lg divide-y divide-surface-container">
            <p class="px-3 py-2 text-xs text-outline">Memuat...</p>
        </div>
        <input type="hidden" id="selected-teacher-id">
        <p id="selected-teacher-name" class="mt-1 text-xs text-primary font-medium hidden"></p>
    </div>

    <!-- Company multi-select -->
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Perusahaan <span class="text-red-500">*</span> <span class="text-outline font-normal normal-case">(bisa pilih lebih dari satu)</span></label>
        <div class="relative mb-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
            <input id="company-search" type="text" placeholder="Cari perusahaan..." class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary">
        </div>
        <div class="flex items-center gap-3 mb-1">
            <button type="button" onclick="selectAllCompanies()" class="text-xs text-primary hover:underline">Pilih Semua</button>
            <button type="button" onclick="clearCompanies()" class="text-xs text-outline hover:underline">Hapus Pilihan</button>
            <span id="selected-count" class="text-xs text-on-surface-variant ml-auto"></span>
        </div>
        <div id="company-list" class="max-h-52 overflow-y-auto border border-outline-variant/20 rounded-lg divide-y divide-surface-container">
            <p class="px-3 py-2 text-xs text-outline">Memuat...</p>
        </div>
    </div>

    <div id="modal-error" class="hidden text-sm text-red-600 bg-red-50 p-3 rounded-lg"></div>

    <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="AdminUtils.hideModal('assign-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
        <button type="button" onclick="submitAssignment()" id="save-btn" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Simpan</button>
    </div>
</div>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete } = AdminUtils;

    let allAssignments = [];
    let allTeachers = [];
    let allCompanies = [];
    let filteredCompanies = [];
    let selectedCompanyIds = new Set();
    let searchTimeout = null;

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await Promise.all([loadData(), loadTeachersAndCompanies()]);
        setupSearch();
    });

    async function loadData() {
        try {
            const res = await Auth.apiFetch('/admin/teacher-company-assignments');
            allAssignments = await res.json();
            renderTable(allAssignments);
            populateFilterTeacher();
        } catch (e) {
            console.error(e);
            showToast('Gagal memuat data', 'error');
        }
    }

    async function loadTeachersAndCompanies() {
        try {
            const [tRes, cRes] = await Promise.all([
                Auth.apiFetch('/teachers?per_page=1000'),
                Auth.apiFetch('/companies?per_page=1000'),
            ]);
            const tJson = await tRes.json();
            const cJson = await cRes.json();
            allTeachers = tJson.data?.data || tJson.data || tJson;
            allCompanies = cJson.data?.data || cJson.data || cJson;
            filteredCompanies = [...allCompanies];
        } catch (e) {
            console.error('Failed to load teachers/companies', e);
        }
    }

    function renderTable(assignments) {
        const tbody = document.getElementById('data-table');
        const query = document.getElementById('search-input').value.toLowerCase();
        const teacherFilter = document.getElementById('filter-teacher').value;

        let filtered = assignments;
        if (teacherFilter) filtered = filtered.filter(a => String(a.teacher_id) === teacherFilter);
        if (query) filtered = filtered.filter(a =>
            a.teacher?.name?.toLowerCase().includes(query) ||
            a.company?.name?.toLowerCase().includes(query)
        );

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Tidak ada penugasan ditemukan.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(a => {
            const date = new Date(a.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            return `<tr class="hover:bg-surface-container/50 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-on-surface">${a.teacher?.name || '—'}</td>
                <td class="px-6 py-4 text-sm text-on-surface">${a.company?.name || '—'}</td>
                <td class="px-6 py-4 text-sm text-on-surface-variant">${a.company?.industry || '—'}</td>
                <td class="px-6 py-4 text-sm text-on-surface-variant">${date}</td>
                <td class="px-6 py-4 text-right">
                    <button onclick="deleteAssignment(${a.id}, '${(a.teacher?.name || '').replace(/'/g, "\\'")} → ${(a.company?.name || '').replace(/'/g, "\\'")}')"
                        class="p-2 text-on-surface-variant hover:text-error transition-colors" title="Hapus">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    function populateFilterTeacher() {
        const sel = document.getElementById('filter-teacher');
        sel.innerHTML = '<option value="">Semua Guru</option>';
        const unique = [...new Map(allAssignments.map(a => [a.teacher_id, a.teacher])).entries()];
        unique.forEach(([id, t]) => {
            if (t) sel.innerHTML += `<option value="${id}">${t.name}</option>`;
        });
    }

    function setupSearch() {
        document.getElementById('search-input').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => renderTable(allAssignments), 200);
        });
        document.getElementById('filter-teacher').addEventListener('change', () => renderTable(allAssignments));
    }

    window.openAssignModal = () => {
        document.getElementById('selected-teacher-id').value = '';
        document.getElementById('selected-teacher-name').classList.add('hidden');
        document.getElementById('teacher-search').value = '';
        document.getElementById('company-search').value = '';
        document.getElementById('modal-error').classList.add('hidden');
        selectedCompanyIds = new Set();
        filteredCompanies = [...allCompanies];
        renderTeacherList('');
        renderCompanyList('');
        showModal('assign-modal');
    };

    function renderTeacherList(query) {
        const container = document.getElementById('teacher-list');
        const filtered = allTeachers.filter(t =>
            t.name.toLowerCase().includes(query.toLowerCase()) ||
            t.email.toLowerCase().includes(query.toLowerCase())
        );
        if (filtered.length === 0) {
            container.innerHTML = '<p class="px-3 py-2 text-xs text-outline">Tidak ada guru ditemukan.</p>';
            return;
        }
        container.innerHTML = filtered.map(t => `
            <div onclick="selectTeacher(${t.id}, '${t.name.replace(/'/g, "\\'")}')"
                class="px-3 py-2.5 cursor-pointer hover:bg-surface-container text-sm flex items-center justify-between teacher-item"
                data-id="${t.id}">
                <span>${t.name}</span>
                <span class="text-xs text-outline">${t.email}</span>
            </div>
        `).join('');
    }

    function renderCompanyList(query) {
        const container = document.getElementById('company-list');
        filteredCompanies = allCompanies.filter(c =>
            c.name.toLowerCase().includes(query.toLowerCase()) ||
            (c.industry || '').toLowerCase().includes(query.toLowerCase())
        );
        if (filteredCompanies.length === 0) {
            container.innerHTML = '<p class="px-3 py-2 text-xs text-outline">Tidak ada perusahaan ditemukan.</p>';
            return;
        }
        container.innerHTML = filteredCompanies.map(c => {
            const checked = selectedCompanyIds.has(c.id);
            return `<label class="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-surface-container">
                <input type="checkbox" value="${c.id}" ${checked ? 'checked' : ''}
                    onchange="toggleCompany(${c.id})"
                    class="rounded border-outline-variant/40 text-primary focus:ring-primary">
                <div>
                    <p class="text-sm text-on-surface">${c.name}</p>
                    <p class="text-xs text-on-surface-variant">${c.industry || '—'}</p>
                </div>
            </label>`;
        }).join('');
        updateSelectedCount();
    }

    window.selectTeacher = (id, name) => {
        document.getElementById('selected-teacher-id').value = id;
        const nameEl = document.getElementById('selected-teacher-name');
        nameEl.textContent = `✓ Dipilih: ${name}`;
        nameEl.classList.remove('hidden');
        document.querySelectorAll('.teacher-item').forEach(el => {
            el.classList.toggle('bg-primary/10', Number(el.dataset.id) === id);
        });
    };

    window.toggleCompany = (id) => {
        selectedCompanyIds.has(id) ? selectedCompanyIds.delete(id) : selectedCompanyIds.add(id);
        updateSelectedCount();
    };

    window.selectAllCompanies = () => {
        filteredCompanies.forEach(c => selectedCompanyIds.add(c.id));
        renderCompanyList(document.getElementById('company-search').value);
    };

    window.clearCompanies = () => {
        selectedCompanyIds.clear();
        renderCompanyList(document.getElementById('company-search').value);
    };

    function updateSelectedCount() {
        document.getElementById('selected-count').textContent =
            selectedCompanyIds.size > 0 ? `${selectedCompanyIds.size} perusahaan dipilih` : '';
    }

    document.getElementById('teacher-search').addEventListener('input', e => renderTeacherList(e.target.value));
    document.getElementById('company-search').addEventListener('input', e => renderCompanyList(e.target.value));

    window.submitAssignment = async () => {
        const teacherId = document.getElementById('selected-teacher-id').value;
        const errorEl = document.getElementById('modal-error');
        const btn = document.getElementById('save-btn');
        errorEl.classList.add('hidden');

        if (!teacherId) { errorEl.textContent = 'Pilih guru terlebih dahulu.'; errorEl.classList.remove('hidden'); return; }
        if (selectedCompanyIds.size === 0) { errorEl.textContent = 'Pilih minimal satu perusahaan.'; errorEl.classList.remove('hidden'); return; }

        btn.textContent = 'Menyimpan...';
        btn.disabled = true;

        try {
            const results = await Promise.all([...selectedCompanyIds].map(companyId =>
                Auth.apiFetch('/admin/teacher-company-assignments', {
                    method: 'POST',
                    body: JSON.stringify({ teacher_id: parseInt(teacherId), company_id: companyId }),
                })
            ));
            const failed = results.filter(r => !r.ok).length;
            hideModal('assign-modal');
            showToast(failed > 0 ? `${results.length - failed} berhasil, ${failed} sudah ada` : `${results.length} penugasan berhasil disimpan`, failed > 0 ? 'warning' : 'success');
            await loadData();
        } catch (e) {
            errorEl.textContent = 'Terjadi kesalahan.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.textContent = 'Simpan';
            btn.disabled = false;
        }
    };

    window.deleteAssignment = async (id, label) => {
        if (!await confirmDelete(label)) return;
        try {
            const res = await Auth.apiFetch(`/admin/teacher-company-assignments/${id}`, { method: 'DELETE' });
            if (res.ok) {
                showToast('Penugasan dihapus', 'success');
                await loadData();
            }
        } catch (e) {
            showToast('Gagal menghapus', 'error');
        }
    };

    // ── Import/Export/Template ──────────────────────────────
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/teacher-company-assignments', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'penugasan-perusahaan.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal export penugasan perusahaan', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        try {
            const res = await fetch('/api/v1/import/teacher-company-assignments', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
                body: formData,
            });
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Import failed');
            }
            showToast('Penugasan perusahaan berhasil diimport');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Gagal import penugasan perusahaan', 'error');
        }
        input.value = '';
    }

    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/teacher-company-assignments/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-penugasan-perusahaan.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal download template', 'error');
        }
    }
</script>
@endpush
