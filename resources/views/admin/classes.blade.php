@extends('layouts.app')

@section('title', 'Classes Management')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Classes Management</h1>
        </div>
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-surface-container-high rounded-md">
            <button onclick="exportData()" class="px-3 py-1.5 text-on-surface font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">download</span> Export
            </button>
            <label class="px-3 py-1.5 text-on-surface font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-sm">upload</span> Import
                <input type="file" id="import-file" accept=".xlsx,.xls" class="hidden" onchange="importData(this)">
            </label>
        </div>
        <button onclick="openCreateModal()" class="primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider px-5 py-2.5 flex items-center gap-2 shadow-md hover:shadow-lg transition-shadow">
            <span class="material-symbols-outlined text-sm">add</span>
            Add Class
        </button>
    </div>
    </div>

    {{-- Filters --}}
    <div class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Search</label>
            <input type="text" id="filter-search" placeholder="Class name..." class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onkeyup="debounceSearch()">
        </div>
        <div class="w-64">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">School</label>
            <select id="filter-school" onchange="loadData()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Schools</option>
            </select>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Per Page</label>
            <select id="filter-per-page" onchange="loadData()" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <table class="w-full">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Name</th>
                    <th class="px-6 py-4 text-left text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">School</th>
                    <th class="px-6 py-4 text-right text-[0.65rem] font-bold text-on-surface-variant uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td>
                </tr>
            </tbody>
        </table>
        <div id="pagination"></div>
    </div>
</div>

{{-- Modal --}}
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Class'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem()">
        <input type="hidden" id="form-id">
        <div class="space-y-4">
            <div>
                <label for="form-name" class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Name</label>
                <input type="text" id="form-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Class name">
            </div>
            <div>
                <label for="form-academic-year" class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Academic Year</label>
                <input type="text" id="form-academic-year" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="2025/2026" maxlength="9">
            </div>
            <div>
                <label for="form-school" class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">School</label>
                <select id="form-school" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select School</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 rounded-md text-xs font-bold uppercase tracking-wider text-on-surface-variant hover:bg-surface-container-high transition-colors">Cancel</button>
            <button type="submit" class="primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider px-5 py-2.5 shadow-md hover:shadow-lg transition-shadow">Save</button>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    let currentPage = 1;
    let allItems = [];
    let searchTimeout = null;

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
            AdminUtils.showToast('Failed to load classes', 'error');
        }
    }

    async function editItem(id) {
        const item = allItems.find(i => i.id === id);
        if (!item) return;

        document.getElementById('form-id').value = item.id;
        document.getElementById('form-name').value = item.name;
        document.getElementById('form-academic-year').value = item.academic_year || '';
        document.getElementById('form-school').value = item.school_id || item.school?.id || '';
        document.getElementById('crud-modal-title').textContent = 'Edit Class';
        AdminUtils.showModal('crud-modal');
    }

    async function deleteItem(id, name) {
        if (!AdminUtils.confirmDelete(name)) return;

        try {
            const res = await Auth.apiFetch('/classes/' + id, { method: 'DELETE' });
            if (res.ok) {
                AdminUtils.showToast('Class deleted successfully');
                loadData(currentPage);
            } else {
                const json = await res.json();
                AdminUtils.showToast(json.message || 'Failed to delete class', 'error');
            }
        } catch (e) {
            console.error('Failed to delete class:', e);
            AdminUtils.showToast('Failed to delete class', 'error');
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
                AdminUtils.showToast(id ? 'Class updated successfully' : 'Class created successfully');
                loadData(currentPage);
            } else {
                const json = await res.json();
                AdminUtils.showToast(json.message || 'Failed to save class', 'error');
            }
        } catch (e) {
            console.error('Failed to save class:', e);
            AdminUtils.showToast('Failed to save class', 'error');
        }
    }

    function openCreateModal() {
        document.getElementById('form-id').value = '';
        document.getElementById('form-name').value = '';
        document.getElementById('form-academic-year').value = '';
        document.getElementById('form-school').value = '';
        document.getElementById('crud-modal-title').textContent = 'Add Class';
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
            showToast('Failed to export classes', 'error');
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
            
            showToast('Classes imported successfully');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to import classes', 'error');
        }
        
        input.value = '';
    }

    // Initialize
    if (Auth.requireAuth()) {
        AdminUtils.populateSelect('filter-school', '/schools');
        AdminUtils.populateSelect('form-school', '/schools');
        loadData();
    }
</script>
@endpush
