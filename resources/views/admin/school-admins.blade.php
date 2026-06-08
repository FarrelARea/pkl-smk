@extends('layouts.app')

@section('title', 'Manajemen Admin Sekolah')

@section('content')
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-[26px] font-extrabold text-on-surface tracking-tight mb-2 font-headline">Manajemen Admin Sekolah</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Superadmin dapat membuat dan mengelola admin sekolah yang hanya mengakses data sekolah masing-masing.
        </p>
    </div>
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
        <button onclick="openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">person_add</span> Tambah Admin Sekolah
        </button>
    </div>
</header>

<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex justify-between items-center border-b border-surface-container gap-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Admin Sekolah</h2>
        <div class="flex gap-3 items-end">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-11 pr-4 py-1.5 h-8 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Cari admin sekolah..." type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Per Halaman</label>
                <select id="per-page-select" class="px-3 py-1.5 h-8 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Nama</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Sekolah</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="data-table" class="divide-y divide-surface-container">
                <tr><td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="pagination"></div>
</div>

@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Admin Sekolah'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-name">Nama</label>
                <input id="field-name" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-email">Email</label>
                <input id="field-email" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="email">
            </div>
            <div id="password-field">
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-password">Password</label>
                <input id="field-password" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="password" minlength="6">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-school">Sekolah</label>
                <select id="field-school" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih sekolah...</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Simpan</button>
            </div>
        </div>
    </form>
@endcomponent

@component('partials.modal', ['id' => 'reset-password-modal', 'title' => 'Reset Password'])
    <form id="reset-password-form" onsubmit="event.preventDefault(); submitResetPassword();">
        <input type="hidden" id="reset-admin-id">
        <div class="space-y-4">
            <p id="reset-password-description" class="text-sm text-on-surface-variant"></p>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="reset-password-field">Password Baru</label>
                <input id="reset-password-field" required minlength="6" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="password">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="reset-password-confirm-field">Konfirmasi Password Baru</label>
                <input id="reset-password-confirm-field" required minlength="6" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="password">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('reset-password-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Reset Password</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const ENDPOINT = '/admin/school-admins';
    let currentPage = 1;
    let searchQuery = '';
    let searchTimeout = null;

    function getPerPage() {
        return document.getElementById('per-page-select').value || 15;
    }

    async function loadData(page = 1) {
        currentPage = page;

        try {
            const params = new URLSearchParams({ page, per_page: getPerPage() });
            if (searchQuery) params.set('search', searchQuery);

            const res = await Auth.apiFetch(`${ENDPOINT}?${params}`);
            if (res.status === 403) {
                document.getElementById('data-table').innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">Hanya superadmin yang dapat mengakses halaman ini.</td></tr>';
                return;
            }

            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;

            AdminUtils.renderTable('data-table', items, [
                { key: 'name' },
                { key: 'email' },
                { key: 'school', render: (row) => row.school?.name || '—' },
            ], (row) => `
                <div class="flex justify-end gap-1">
                    <button onclick="resetPassword(${row.id}, '${(row.name || '').replace(/'/g, "\\'")}')" class="px-2 py-1 text-xs font-semibold rounded bg-surface-container-high text-on-surface hover:bg-surface-container transition-colors">Reset Password</button>
                    ${AdminUtils.editBtn(row.id)}
                    ${AdminUtils.deleteBtn(row.id, row.name)}
                </div>
            `);

            AdminUtils.renderPagination('pagination', meta, loadData);
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Gagal memuat admin sekolah', 'error');
        }
    }

    async function openCreateModal() {
        document.getElementById('item-id').value = '';
        document.getElementById('crud-form').reset();
        document.getElementById('field-password').required = true;
        document.getElementById('password-field').style.display = '';
        document.getElementById('crud-modal-title').textContent = 'Tambah Admin Sekolah';
        await AdminUtils.populateSelect('field-school', '/schools');
        AdminUtils.showModal('crud-modal');
    }

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
            document.getElementById('password-field').style.display = 'none';
            await AdminUtils.populateSelect('field-school', '/schools');
            document.getElementById('field-school').value = item.school_id || '';
            document.getElementById('crud-modal-title').textContent = 'Edit Admin Sekolah';
            AdminUtils.showModal('crud-modal');
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Gagal memuat detail admin sekolah', 'error');
        }
    }

    async function saveItem() {
        const id = document.getElementById('item-id').value;
        const payload = {
            name: document.getElementById('field-name').value,
            email: document.getElementById('field-email').value,
            school_id: parseInt(document.getElementById('field-school').value),
        };
        const password = document.getElementById('field-password').value;
        if (password) payload.password = password;

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
                throw new Error(err.error || err.message || 'Validation error');
            }

            AdminUtils.hideModal('crud-modal');
            AdminUtils.showToast(id ? 'Admin sekolah berhasil diperbarui' : 'Admin sekolah berhasil dibuat');
            loadData(id ? currentPage : 1);
        } catch (e) {
            console.error(e);
            AdminUtils.showToast(e.message || 'Gagal menyimpan admin sekolah', 'error');
        }
    }

    function resetPassword(id, name) {
        document.getElementById('reset-admin-id').value = id;
        document.getElementById('reset-password-form').reset();
        document.getElementById('reset-admin-id').value = id;
        document.getElementById('reset-password-description').textContent = `Atur password baru untuk ${name}.`;
        AdminUtils.showModal('reset-password-modal');
    }

    async function submitResetPassword() {
        const userId = document.getElementById('reset-admin-id').value;
        const password = document.getElementById('reset-password-field').value;
        const confirmation = document.getElementById('reset-password-confirm-field').value;

        if (password !== confirmation) {
            AdminUtils.showToast('Passwords do not match', 'error');
            return;
        }

        try {
            const res = await Auth.apiFetch('/admin/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: parseInt(userId),
                    new_password: password,
                    new_password_confirmation: confirmation,
                }),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.error || err.message || 'Failed to reset password');
            }

            AdminUtils.hideModal('reset-password-modal');
            AdminUtils.showToast('Password admin sekolah berhasil direset');
        } catch (e) {
            console.error(e);
            AdminUtils.showToast(e.message || 'Gagal reset password', 'error');
        }
    }

    async function deleteItem(id, name) {
        if (!AdminUtils.confirmDelete(name)) return;

        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${id}`, { method: 'DELETE' });
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.error || err.message || 'Delete failed');
            }

            AdminUtils.showToast('Admin sekolah berhasil dihapus');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            AdminUtils.showToast(e.message || 'Gagal menghapus admin sekolah', 'error');
        }
    }

    document.getElementById('search-input').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = e.target.value.trim();
            loadData(1);
        }, 300);
    });

    document.getElementById('per-page-select').addEventListener('change', () => loadData(1));

    window.loadData = loadData;
    window.openCreateModal = openCreateModal;
    window.editItem = editItem;
    window.saveItem = saveItem;
    window.resetPassword = resetPassword;
    window.submitResetPassword = submitResetPassword;
    window.deleteItem = deleteItem;

    // ── Import/Export/Template ──────────────────────────────
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/school-admins', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'admin-sekolah.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Gagal export admin sekolah', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        try {
            const res = await fetch('/api/v1/import/school-admins', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
                body: formData,
            });
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Import failed');
            }
            AdminUtils.showToast('Admin sekolah berhasil diimport');
            loadData();
        } catch (e) {
            console.error(e);
            AdminUtils.showToast(e.message || 'Gagal import admin sekolah', 'error');
        }
        input.value = '';
    }

    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/school-admins/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-admin-sekolah.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Gagal download template', 'error');
        }
    }

    loadData();
</script>
@endpush
