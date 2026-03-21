@extends('layouts.app')

@section('title', 'Supervisors Management')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Supervisors Management</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Manage supervisors and assign them to companies.
        </p>
    </div>
    <div>
        <button onclick="openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Add Supervisor
        </button>
    </div>
</header>

<!-- Search & Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex justify-between items-center border-b border-surface-container gap-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Supervisors</h2>
        <div class="flex gap-3 items-end">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Search supervisors..." type="text">
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
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Company</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td></tr>
        </tbody>
    </table>
    <div id="pagination"></div>
</div>

<!-- CRUD Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Supervisor'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-name">Name</label>
                <input id="field-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-email">Email</label>
                <input id="field-email" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="email">
            </div>
            <div id="password-field">
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-password">Password</label>
                <input id="field-password" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="password" minlength="6">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-company-create">Company</label>
                <select id="field-company-create" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select a company...</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Save</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- Reset Password Modal -->
@component('partials.modal', ['id' => 'reset-password-modal', 'title' => 'Reset Password'])
    <form id="reset-password-form" onsubmit="event.preventDefault(); submitResetPassword();">
        <input type="hidden" id="reset-supervisor-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">New Password</label>
                <input id="reset-password-field" type="password" required minlength="6" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Confirm Password</label>
                <input id="reset-password-confirm-field" type="password" required minlength="6" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('reset-password-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Reset Password</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- Assign to Company Modal -->
@component('partials.modal', ['id' => 'assign-modal', 'title' => 'Assign to Company'])
    <form id="assign-form" onsubmit="event.preventDefault(); submitAssignCompany();">
        <input type="hidden" id="assign-supervisor-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-company">Company</label>
                <select id="field-company" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select a company...</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('assign-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Assign</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete, renderTable, renderPagination, editBtn, deleteBtn } = AdminUtils;

    const ENDPOINT = '/supervisors';
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
                { key: 'email' },
                { key: 'company', render: (row) => row.company ? `<span class="px-2 py-0.5 bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-full text-[0.6rem] font-bold">${row.company.name}</span>` : '<span class="text-outline text-xs">None</span>' },
            ], (row) => `
                <div class="flex justify-end gap-1">
                    ${editBtn(row.id)}
                    <button onclick="resetPassword(${row.id}, '${row.name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-warning transition-colors" title="Reset Password"><span class="material-symbols-outlined text-sm">lock_reset</span></button>
                    <button onclick="assignCompany(${row.id})" class="p-2 text-on-surface-variant hover:text-tertiary transition-colors"><span class="material-symbols-outlined text-sm">business</span></button>
                    ${deleteBtn(row.id, row.name)}
                </div>
            `);

            renderPagination('pagination', meta, loadData);
        } catch (e) {
            console.error(e);
            showToast('Failed to load supervisors', 'error');
        }
    }

    // ── Open Create Modal ────────────────────────────────
    async function openCreateModal() {
        document.getElementById('item-id').value = '';
        document.getElementById('crud-form').reset();
        document.getElementById('field-password').required = true;
        document.getElementById('field-company-create').required = true;
        document.getElementById('password-field').style.display = '';
        document.getElementById('crud-modal-title').textContent = 'Add Supervisor';
        await AdminUtils.populateSelect('field-company-create', '/companies');
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
            document.getElementById('field-email').value = item.email || '';
            document.getElementById('field-password').value = '';
            document.getElementById('field-password').required = false;
            document.getElementById('field-company-create').required = false;
            document.getElementById('password-field').style.display = 'none';
            await AdminUtils.populateSelect('field-company-create', '/companies');
            document.getElementById('field-company-create').value = item.company_id || '';
            document.getElementById('crud-modal-title').textContent = 'Edit Supervisor';
            showModal('crud-modal');
        } catch (e) {
            console.error(e);
            showToast('Failed to load supervisor details', 'error');
        }
    }

    // ── Delete Item ──────────────────────────────────────
    async function deleteItem(id, name) {
        if (!confirmDelete(name)) return;
        try {
            await Auth.apiFetch(`${ENDPOINT}/${id}`, { method: 'DELETE' });
            showToast('Supervisor deleted successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast('Failed to delete supervisor', 'error');
        }
    }

    // ── Save Item ────────────────────────────────────────
    async function saveItem() {
        const id = document.getElementById('item-id').value;
        const payload = {
            name: document.getElementById('field-name').value,
            email: document.getElementById('field-email').value,
        };
        const password = document.getElementById('field-password').value;
        if (password) payload.password = password;
        const companyId = document.getElementById('field-company-create').value;
        if (companyId) payload.company_id = parseInt(companyId);

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
            showToast(id ? 'Supervisor updated successfully' : 'Supervisor created successfully');
            loadData(id ? currentPage : 1);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to save supervisor', 'error');
        }
    }

    // ── Assign Company ───────────────────────────────────
    async function assignCompany(id) {
        document.getElementById('assign-supervisor-id').value = id;
        document.getElementById('assign-form').reset();
        document.getElementById('assign-supervisor-id').value = id;

        try {
            const res = await Auth.apiFetch('/companies');
            const json = await res.json();
            const companies = Array.isArray(json.data) ? json.data : (json.data?.data || []);

            const select = document.getElementById('field-company');
            select.innerHTML = '<option value="">Select a company...</option>';
            companies.forEach(c => {
                select.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });

            showModal('assign-modal');
        } catch (e) {
            console.error(e);
            showToast('Failed to load companies', 'error');
        }
    }

    async function submitAssignCompany() {
        const supervisorId = document.getElementById('assign-supervisor-id').value;
        const companyId = document.getElementById('field-company').value;

        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${supervisorId}/assign-company`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ company_id: parseInt(companyId) }),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Assignment failed');
            }

            hideModal('assign-modal');
            showToast('Supervisor assigned to company successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to assign company', 'error');
        }
    }

    // ── Reset Password ───────────────────────────────────
    async function resetPassword(id, name) {
        document.getElementById('reset-supervisor-id').value = id;
        document.getElementById('reset-password-field').value = '';
        document.getElementById('reset-password-confirm-field').value = '';
        document.getElementById('reset-password-modal-title').textContent = `Reset Password for ${name}`;
        showModal('reset-password-modal');
    }

    async function submitResetPassword() {
        const supervisorId = document.getElementById('reset-supervisor-id').value;
        const password = document.getElementById('reset-password-field').value;
        const confirm = document.getElementById('reset-password-confirm-field').value;

        if (password !== confirm) {
            showToast('Passwords do not match', 'error');
            return;
        }

        try {
            const res = await Auth.apiFetch('/admin/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: parseInt(supervisorId),
                    new_password: password,
                    new_password_confirmation: confirm,
                }),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.error || err.message || 'Failed to reset password');
            }

            hideModal('reset-password-modal');
            showToast('Password reset successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to reset password', 'error');
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
    window.assignCompany = assignCompany;
    window.submitAssignCompany = submitAssignCompany;
    window.resetPassword = resetPassword;
    window.submitResetPassword = submitResetPassword;

    // ── Init ─────────────────────────────────────────────
    loadData();
</script>
@endpush
