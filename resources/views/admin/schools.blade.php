@extends('layouts.app')

@section('title', 'Schools Management')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Schools Management</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Manage partner schools and their contact information.
        </p>
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
        <button onclick="openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Add School
        </button>
    </div>
</header>

<!-- Search & Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex justify-between items-center border-b border-surface-container gap-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Schools</h2>
        <div class="flex gap-3 items-end">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Search schools..." type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Per Page</label>
                <select id="per-page-select" class="px-3 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Name</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Address</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Phone</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td></tr>
        </tbody>
    </table>
    <div id="pagination"></div>
</div>

<!-- Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'School'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-name">Name</label>
                <input id="field-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-address">Address</label>
                <input id="field-address" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-phone">Phone</label>
                <input id="field-phone" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-email">Email</label>
                <input id="field-email" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="email">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Save</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete, renderTable, renderPagination, editBtn, deleteBtn } = AdminUtils;

    const ENDPOINT = '/schools';
    let currentPage = 1;
    let searchQuery = '';
    let searchTimeout = null;

    function getPerPage() {
        return document.getElementById('per-page-select').value || 15;
    }

    // ── Load Data ────────────────────────────────────────
    async function loadData(page = 1) {
        currentPage = page;
        try {
            const perPage = getPerPage();
            const params = new URLSearchParams({ page, per_page: perPage });
            if (searchQuery) params.set('search', searchQuery);

            const res = await Auth.apiFetch(`${ENDPOINT}?${params}`);
            const json = await res.json();

            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;

            renderTable('data-table', items, [
                { key: 'name' },
                { key: 'address' },
                { key: 'phone' },
                { key: 'email' },
            ], (row) => `
                <div class="flex justify-end gap-1">
                    ${editBtn(row.id)}
                    ${deleteBtn(row.id, row.name)}
                </div>
            `);

            renderPagination('pagination', meta, loadData);
        } catch (e) {
            console.error(e);
            showToast('Failed to load schools', 'error');
        }
    }

    // ── Open Create Modal ────────────────────────────────
    function openCreateModal() {
        document.getElementById('item-id').value = '';
        document.getElementById('crud-form').reset();
        document.getElementById('crud-modal-title').textContent = 'Add School';
        showModal('crud-modal');
    }

    // ── Edit Item ────────────────────────────────────────
    async function editItem(id) {
        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${id}`);
            const json = await res.json();
            const item = json.data || json;

            document.getElementById('item-id').value = item.id;
            document.getElementById('field-name').value = item.name || '';
            document.getElementById('field-address').value = item.address || '';
            document.getElementById('field-phone').value = item.phone || '';
            document.getElementById('field-email').value = item.email || '';
            document.getElementById('crud-modal-title').textContent = 'Edit School';
            showModal('crud-modal');
        } catch (e) {
            console.error(e);
            showToast('Failed to load school details', 'error');
        }
    }

    // ── Delete Item ──────────────────────────────────────
    async function deleteItem(id, name) {
        if (!confirmDelete(name)) return;
        try {
            await Auth.apiFetch(`${ENDPOINT}/${id}`, { method: 'DELETE' });
            showToast('School deleted successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast('Failed to delete school', 'error');
        }
    }

    // ── Save Item ────────────────────────────────────────
    async function saveItem() {
        const id = document.getElementById('item-id').value;
        const payload = {
            name: document.getElementById('field-name').value,
            address: document.getElementById('field-address').value,
            phone: document.getElementById('field-phone').value,
            email: document.getElementById('field-email').value,
        };

        try {
            const method = id ? 'PUT' : 'POST';
            const url = id ? `${ENDPOINT}/${id}` : ENDPOINT;
            const res = await Auth.apiFetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Validation error');
            }

            hideModal('crud-modal');
            showToast(id ? 'School updated successfully' : 'School created successfully');
            loadData(id ? currentPage : 1);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to save school', 'error');
        }
    }

    // ── Search ───────────────────────────────────────────
    document.getElementById('search-input').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = e.target.value.trim();
            loadData(1);
        }, 300);
    });

    // ── Per Page Select ──────────────────────────────────
    document.getElementById('per-page-select').addEventListener('change', () => {
        currentPage = 1;
        loadData(1);
    });

    // ── Expose to window ─────────────────────────────────
    window.loadData = loadData;
    window.editItem = editItem;
    window.deleteItem = deleteItem;
    window.saveItem = saveItem;
    window.openCreateModal = openCreateModal;
    window.exportData = exportData;
    window.importData = importData;

    // ── Import/Export ─────────────────────────────────────
    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/schools', {
                headers: {
                    'Authorization': 'Bearer ' + Auth.getToken(),
                },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'schools.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Failed to export schools', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('file', file);
        
        try {
            const res = await fetch('/api/v1/import/schools', {
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
            
            showToast('Schools imported successfully');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to import schools', 'error');
        }
        
        input.value = '';
    }

    // ── Init ─────────────────────────────────────────────
    loadData();
</script>
@endpush
