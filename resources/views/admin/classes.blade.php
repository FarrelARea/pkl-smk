@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 data-help-target="page-title" class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Manajemen Kelas</h1>
        </div>
    <div data-help-target="header-actions" class="flex items-center gap-2">
        <x-help-button title="Panduan Manajemen Kelas">
            <p>Di halaman ini kamu bisa mengelola data kelas.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Tambah kelas baru</li>
                <li>Edit dan hapus data kelas</li>
                <li>Filter kelas berdasarkan sekolah</li>
            </ul>
        </x-help-button>
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
        <button onclick="openCreateModal()" data-help-target="add-button" class="primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider px-5 py-2.5 flex items-center gap-2 shadow-md hover:shadow-lg transition-shadow">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Kelas
        </button>
    </div>
    </div>

    {{-- Filters --}}
    <div data-help-target="filter-bar" class="flex items-end gap-4 flex-wrap">
        <div data-help-target="search-field">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Cari</label>
            <input type="text" id="filter-search" placeholder="Nama kelas..." class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onkeyup="debounceSearch()">
        </div>
        <div id="filter-school-group" class="w-64">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Sekolah</label>
            <select id="filter-school" onchange="loadData()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Semua Sekolah</option>
            </select>
            <p id="filter-school-locked" class="hidden w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm text-on-surface"></p>
        </div>
        <div data-help-target="per-page-field">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Per Halaman</label>
            <select id="filter-per-page" onchange="loadData()" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div data-help-target="data-table" class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Nama</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Sekolah</th>
                    <th class="px-6 py-4 text-right text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td>
                </tr>
            </tbody>
        </table>
        </div>
        <div id="pagination"></div>
    </div>
</div>

{{-- Modal --}}
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Kelas'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem()">
        <input type="hidden" id="form-id">
        <div class="space-y-4">
            <div>
                <label for="form-name" class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Nama</label>
                <input type="text" id="form-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Nama kelas">
            </div>
            <div>
                <label for="form-academic-year" class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Tahun Akademik</label>
                <input type="text" id="form-academic-year" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="2025/2026" maxlength="9">
            </div>
            <div>
                <label for="form-school" class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Sekolah</label>
                <select id="form-school" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Sekolah</option>
                </select>
                <p id="form-school-locked" class="hidden w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm text-on-surface"></p>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 rounded-md text-xs font-bold uppercase tracking-wider text-on-surface-variant hover:bg-surface-container-high transition-colors">Batal</button>
            <button type="submit" class="primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider px-5 py-2.5 shadow-md hover:shadow-lg transition-shadow">Simpan</button>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    let currentPage = 1;
    let allItems = [];
    let searchTimeout = null;
    let currentUser = null;

    function updateLockedSchoolField(selectId, textId, schoolId, schoolName) {
        const select = document.getElementById(selectId);
        const text = document.getElementById(textId);
        if (!select || !text) return;

        if (currentUser?.role === 'superadmin') {
            select.classList.remove('hidden');
            text.classList.add('hidden');
            text.textContent = '';
            return;
        }

        select.classList.add('hidden');
        text.classList.remove('hidden');
        text.textContent = schoolName || 'Sekolah tidak ditemukan';
        if (schoolId) {
            select.value = schoolId;
        }
    }

    async function resolveSchoolName(schoolId) {
        if (!schoolId) return '';
        const existing = document.querySelector(`#form-school option[value="${schoolId}"]`) || document.querySelector(`#filter-school option[value="${schoolId}"]`);
        if (existing?.textContent) {
            return existing.textContent;
        }

        try {
            const res = await Auth.apiFetch(`/schools/${schoolId}`);
            const json = await res.json();
            const school = json.data || json;
            return school.name || '';
        } catch {
            return '';
        }
    }

    function debounceSearch() {
        clearTimeout(searchTimeout);
        currentPage = 1;
        searchTimeout = setTimeout(() => loadData(), 500);
    }

    async function loadData(page = 1) {
        currentPage = page;
        const schoolId = document.getElementById('filter-school').value;
        const search = document.getElementById('filter-search').value;
        const perPage = document.getElementById('filter-per-page').value || 15;
        let url = '/classes?page=' + page + '&per_page=' + perPage;
        if (schoolId) url += '&school_id=' + schoolId;
        if (search) url += '&search=' + encodeURIComponent(search);

        try {
            const res = await Auth.apiFetch(url);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;
            allItems = items;

            AdminUtils.renderTable('table-body', items, [
                { key: 'name' },
                { key: 'school', render: (row) => row.school?.name || '—' },
            ], (row) => AdminUtils.editBtn(row.id) + AdminUtils.deleteBtn(row.id, row.name));

            AdminUtils.renderPagination('pagination', meta, (p) => loadData(p));
        } catch (e) {
            console.error('Failed to load classes:', e);
            AdminUtils.showToast('Gagal memuat data kelas', 'error');
        }
    }

    async function editItem(id) {
        const item = allItems.find(i => i.id === id);
        if (!item) return;

        document.getElementById('form-id').value = item.id;
        document.getElementById('form-name').value = item.name;
        document.getElementById('form-academic-year').value = item.academic_year || '';
        const schoolId = item.school_id || item.school?.id || '';
        document.getElementById('form-school').value = schoolId;
        updateLockedSchoolField('form-school', 'form-school-locked', schoolId, item.school?.name || await resolveSchoolName(schoolId));
        document.getElementById('crud-modal-title').textContent = 'Edit Kelas';
        AdminUtils.showModal('crud-modal');
    }

    async function deleteItem(id, name) {
        if (!AdminUtils.confirmDelete(name)) return;

        try {
            const res = await Auth.apiFetch('/classes/' + id, { method: 'DELETE' });
            if (res.ok) {
                AdminUtils.showToast('Kelas berhasil dihapus');
                loadData(currentPage);
            } else {
                const json = await res.json();
                AdminUtils.showToast(json.message || 'Gagal menghapus kelas', 'error');
            }
        } catch (e) {
            console.error('Failed to delete class:', e);
            AdminUtils.showToast('Gagal menghapus kelas', 'error');
        }
    }

    async function saveItem() {
        const id = document.getElementById('form-id').value;
        const data = {
            name: document.getElementById('form-name').value,
            academic_year: document.getElementById('form-academic-year').value,
            school_id: document.getElementById('form-school').value,
        };

        const url = id ? '/classes/' + id : '/classes';
        const method = id ? 'PUT' : 'POST';

        try {
            const res = await Auth.apiFetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });

            if (res.ok) {
                AdminUtils.hideModal('crud-modal');
                AdminUtils.showToast(id ? 'Kelas berhasil diperbarui' : 'Kelas berhasil ditambahkan');
                loadData(currentPage);
            } else {
                const json = await res.json();
                AdminUtils.showToast(json.message || 'Gagal menyimpan kelas', 'error');
            }
        } catch (e) {
            console.error('Failed to save class:', e);
            AdminUtils.showToast('Gagal menyimpan kelas', 'error');
        }
    }

    async function openCreateModal() {
        document.getElementById('form-id').value = '';
        document.getElementById('form-name').value = '';
        document.getElementById('form-academic-year').value = '';
        const schoolId = currentUser?.role === 'superadmin' ? '' : (currentUser?.school_id || '');
        document.getElementById('form-school').value = schoolId;
        updateLockedSchoolField('form-school', 'form-school-locked', schoolId, await resolveSchoolName(schoolId));
        document.getElementById('crud-modal-title').textContent = 'Tambah Kelas';
        AdminUtils.showModal('crud-modal');
    }

    // Make functions global
    window.loadData = loadData;
    window.editItem = editItem;
    window.deleteItem = deleteItem;
    window.saveItem = saveItem;
    window.openCreateModal = openCreateModal;
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/classes', {
                headers: {
                    'Authorization': 'Bearer ' + Auth.getToken(),
                },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'classes.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal export kelas', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('file', file);
        
        try {
            const res = await fetch('/api/v1/import/classes', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + Auth.getToken(),
                },
                body: formData,
            });
            
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Import failed');
            }
            
            showToast('Kelas berhasil diimport');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Gagal import kelas', 'error');
        }
        
        input.value = '';
    }

    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/classes/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-kelas.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal download template', 'error');
        }
    }

    async function initPage() {
        if (!Auth.requireAuth()) return;

        const res = await Auth.apiFetch('/auth/me');
        currentUser = await res.json();
        currentUser = currentUser.data || currentUser;

        await AdminUtils.populateSelect('filter-school', '/schools');
        await AdminUtils.populateSelect('form-school', '/schools');

        if (currentUser.role !== 'superadmin') {
            const schoolId = currentUser.school_id || '';
            const schoolName = await resolveSchoolName(schoolId);
            document.getElementById('filter-school').value = schoolId;
            document.getElementById('form-school').value = schoolId;
            updateLockedSchoolField('filter-school', 'filter-school-locked', schoolId, schoolName);
            updateLockedSchoolField('form-school', 'form-school-locked', schoolId, schoolName);
        } else {
            updateLockedSchoolField('filter-school', 'filter-school-locked', '', '');
            updateLockedSchoolField('form-school', 'form-school-locked', '', '');
        }

        loadData();
    }

    initPage();
</script>
@endpush
