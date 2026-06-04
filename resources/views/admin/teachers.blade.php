@extends('layouts.app')

@section('title', 'Manajemen Guru')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Manajemen Guru</h1>
        <x-help-button title="Panduan Manajemen Guru">
            <p>Di halaman ini kamu bisa mengelola data guru pembimbing.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Tambah guru baru</li>
                <li>Edit dan hapus data guru</li>
                <li>Tetapkan guru ke kelas tertentu</li>
            </ul>
        </x-help-button>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Kelola data guru dan penugasan kelas mereka.
        </p>
    </div>
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-surface-container-high rounded-md">
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
            <span class="material-symbols-outlined text-sm">add</span> Tambah Guru
        </button>
    </div>
</header>

<!-- Search & Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex justify-between items-center border-b border-surface-container gap-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Guru</h2>
        <div class="flex gap-3 items-end">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Cari guru..." type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Per Halaman</label>
                <select id="per-page-select" class="px-3 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
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
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Kelas yang Ditugaskan</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
        </tbody>
    </table>
    </div>
    <div id="pagination"></div>
</div>

<!-- CRUD Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Guru'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-name">Nama</label>
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
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-school">Sekolah</label>
                <select id="field-school" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih sekolah...</option>
                </select>
                <p id="field-school-locked" class="hidden w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm text-on-surface"></p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Simpan</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- Reset Password Modal -->
@component('partials.modal', ['id' => 'reset-password-modal', 'title' => 'Reset Password'])
    <form id="reset-password-form" onsubmit="event.preventDefault(); submitResetPassword();">
        <input type="hidden" id="reset-teacher-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Password Baru</label>
                <input id="reset-password-field" type="password" required minlength="6" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Konfirmasi Password</label>
                <input id="reset-password-confirm-field" type="password" required minlength="6" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('reset-password-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Reset Password</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- Assign to Classes Modal (multi-select checkboxes) -->
@component('partials.modal', ['id' => 'assign-modal', 'title' => 'Tetapkan ke Kelas'])
    <form id="assign-form" onsubmit="event.preventDefault(); submitAssign();">
        <input type="hidden" id="assign-teacher-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Pilih Kelas</label>
                <div id="class-checkboxes" class="max-h-60 overflow-y-auto space-y-2 p-3 bg-surface-container-low rounded-lg border border-outline-variant/20">
                    <p class="text-xs text-outline">Memuat kelas...</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('assign-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Tetapkan Terpilih</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete, renderTable, renderPagination, editBtn, deleteBtn } = AdminUtils;

    const ENDPOINT = '/teachers';
    let currentPage = 1;
    let searchQuery = '';
    let searchTimeout = null;
    let currentUser = null;

    function setLockedSchoolField(schoolId, schoolName) {
        const select = document.getElementById('field-school');
        const locked = document.getElementById('field-school-locked');

        if (currentUser?.role === 'superadmin') {
            select.classList.remove('hidden');
            locked.classList.add('hidden');
            locked.textContent = '';
            return;
        }

        select.classList.add('hidden');
        locked.classList.remove('hidden');
        locked.textContent = schoolName || 'Sekolah tidak ditemukan';
        if (schoolId) {
            select.value = schoolId;
        }
    }

    async function resolveSchoolName(schoolId) {
        if (!schoolId) return '';
        const existing = document.querySelector(`#field-school option[value="${schoolId}"]`);
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
                { key: 'teacher_classes', render: (row) => {
                    const classes = row.teacher_classes || [];
                    return classes.length ? classes.map(c => `<span class="px-2 py-0.5 bg-secondary-fixed text-on-secondary-fixed-variant rounded-full text-[0.6rem] font-bold">${c.name}</span>`).join(' ') : '<span class="text-outline text-xs">Tidak ada</span>';
                }},
            ], (row) => `
                <div class="flex justify-end gap-1">
                    ${editBtn(row.id)}
                    <button onclick="resetPassword(${row.id}, '${row.name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-warning transition-colors" title="Reset Password"><span class="material-symbols-outlined text-sm">lock_reset</span></button>
                    <button onclick="assignClass(${row.id})" class="p-2 text-on-surface-variant hover:text-tertiary transition-colors"><span class="material-symbols-outlined text-sm">assignment_ind</span></button>
                    ${deleteBtn(row.id, row.name)}
                </div>
            `);

            renderPagination('pagination', meta, loadData);
        } catch (e) {
            console.error(e);
            showToast('Gagal memuat data guru', 'error');
        }
    }

    // ── Open Create Modal ────────────────────────────────
    async function openCreateModal() {
        document.getElementById('item-id').value = '';
        document.getElementById('crud-form').reset();
        document.getElementById('field-password').required = true;
        document.getElementById('field-school').required = true;
        document.getElementById('password-field').style.display = '';
        document.getElementById('crud-modal-title').textContent = 'Tambah Guru';
        await AdminUtils.populateSelect('field-school', '/schools');
        if (currentUser?.role !== 'superadmin') {
            const schoolId = currentUser.school_id || '';
            document.getElementById('field-school').value = schoolId;
            setLockedSchoolField(schoolId, await resolveSchoolName(schoolId));
        } else {
            setLockedSchoolField('', '');
        }
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
            document.getElementById('field-school').required = false;
            document.getElementById('password-field').style.display = 'none';
            await AdminUtils.populateSelect('field-school', '/schools');
            const schoolId = item.school_id || '';
            document.getElementById('field-school').value = schoolId;
            setLockedSchoolField(schoolId, item.school?.name || await resolveSchoolName(schoolId));
            document.getElementById('crud-modal-title').textContent = 'Edit Guru';
            showModal('crud-modal');
        } catch (e) {
            console.error(e);
            showToast('Failed to load teacher details', 'error');
        }
    }

    // ── Delete Item ──────────────────────────────────────
    async function deleteItem(id, name) {
        if (!confirmDelete(name)) return;
        try {
            await Auth.apiFetch(`${ENDPOINT}/${id}`, { method: 'DELETE' });
            showToast('Teacher deleted successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast('Gagal menghapus guru', 'error');
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
        const schoolId = document.getElementById('field-school').value;
        if (schoolId) payload.school_id = parseInt(schoolId);

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
            showToast(id ? 'Teacher updated successfully' : 'Teacher created successfully');
            loadData(id ? currentPage : 1);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to save teacher', 'error');
        }
    }

    // ── Assign to Classes (multi-select) ──────────────────
    async function assignClass(id) {
        document.getElementById('assign-teacher-id').value = id;
        const container = document.getElementById('class-checkboxes');
        container.innerHTML = '<p class="text-xs text-outline">Memuat...</p>';

        try {
            // Load all classes and current teacher's assigned classes
            const [classesRes, teacherRes] = await Promise.all([
                Auth.apiFetch('/classes?per_page=1000'),
                Auth.apiFetch(`${ENDPOINT}/${id}`),
            ]);
            const classesJson = await classesRes.json();
            const teacherJson = await teacherRes.json();
            const classes = Array.isArray(classesJson.data) ? classesJson.data : (classesJson.data?.data || []);
            const teacher = teacherJson.data || teacherJson;
            const assignedIds = (teacher.teacher_classes || []).map(c => c.id);

            container.innerHTML = classes.length ? classes.map(cls => `
                <label class="flex items-center gap-3 px-2 py-1.5 rounded hover:bg-surface-container-high cursor-pointer">
                    <input type="checkbox" class="assign-class-cb w-4 h-4 rounded-sm border-outline-variant text-primary focus:ring-primary" value="${cls.id}" ${assignedIds.includes(cls.id) ? 'checked' : ''}>
                    <span class="text-sm">${cls.name}${cls.school ? ' <span class="text-outline text-xs">(' + cls.school.name + ')</span>' : ''}</span>
                </label>
            `).join('') : '<p class="text-xs text-outline">No classes available</p>';

            showModal('assign-modal');
        } catch (e) {
            console.error(e);
            showToast('Failed to load classes', 'error');
        }
    }

    async function submitAssign() {
        const teacherId = document.getElementById('assign-teacher-id').value;
        const checked = document.querySelectorAll('.assign-class-cb:checked');
        const classIds = Array.from(checked).map(cb => parseInt(cb.value));

        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${teacherId}/assign-class`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ class_ids: classIds }),
            });
            if (!res.ok) throw new Error('Failed');

            hideModal('assign-modal');
            showToast('Class assignments updated');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast('Failed to update assignments', 'error');
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

    // ── Reset Password ───────────────────────────────────
    async function resetPassword(id, name) {
        document.getElementById('reset-teacher-id').value = id;
        document.getElementById('reset-password-field').value = '';
        document.getElementById('reset-password-confirm-field').value = '';
        document.getElementById('reset-password-modal-title').textContent = `Reset Password for ${name}`;
        showModal('reset-password-modal');
    }

    async function submitResetPassword() {
        const teacherId = document.getElementById('reset-teacher-id').value;
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
                    user_id: parseInt(teacherId),
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

    // ── Expose to window ─────────────────────────────────
    window.loadData = loadData;
    window.editItem = editItem;
    window.deleteItem = deleteItem;
    window.saveItem = saveItem;
    window.assignClass = assignClass;
    window.submitAssign = submitAssign;
    window.openCreateModal = openCreateModal;
    window.resetPassword = resetPassword;
    window.submitResetPassword = submitResetPassword;
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/teachers', {
                headers: {
                    'Authorization': 'Bearer ' + Auth.getToken(),
                },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'teachers.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Failed to export teachers', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('file', file);
        
        try {
            const res = await fetch('/api/v1/import/teachers', {
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
            
            showToast('Teachers imported successfully');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to import teachers', 'error');
        }
        
        input.value = '';
    }

    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/teachers/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-guru.xlsx';
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
        const res = await Auth.apiFetch('/auth/me');
        currentUser = await res.json();
        currentUser = currentUser.data || currentUser;
        loadData();
    }

    // ── Init ─────────────────────────────────────────────
    initPage();
</script>
@endpush
