@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 data-help-target="page-title" class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Manajemen Siswa</h1>
        <x-help-button title="Panduan Manajemen Siswa">
            <p>Di halaman ini kamu bisa mengelola semua data siswa.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Tambah siswa baru atau import dari file</li>
                <li>Edit dan hapus data siswa</li>
                <li>Tetapkan siswa ke kelas</li>
                <li>Reset password siswa</li>
                <li>Lihat status magang siswa</li>
                <li>Export data siswa</li>
            </ul>
        </x-help-button>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Kelola data siswa, atur kelas, dan pantau status magang.
        </p>
    </div>
    <div data-help-target="header-actions" class="flex items-center gap-2">
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
        <button onclick="openCreateModal()" data-help-target="add-button" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Siswa
        </button>
    </div>
</header>

<!-- Filter Bar -->
<div data-help-target="filter-bar" class="flex gap-4 mb-8 flex-wrap items-end">
    <div data-help-target="search-field">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Cari</label>
        <input type="text" id="filter-search" placeholder="Nama atau Email..." class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onkeyup="debounceSearch()">
    </div>
    <div id="filter-school-group" class="w-64">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Sekolah</label>
        <select id="filter-school" onchange="onSchoolFilterChange()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">Semua Sekolah</option>
        </select>
        <p id="filter-school-locked" class="hidden w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm text-on-surface"></p>
    </div>
    <div class="w-64">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Kelas</label>
        <select id="filter-class" onchange="loadStudents()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">Semua Kelas</option>
        </select>
    </div>
    <div data-help-target="per-page-field">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Per Halaman</label>
        <select id="filter-per-page" onchange="loadStudents()" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
</div>

<!-- Students Table -->
<div data-help-target="data-table" class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
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
        <tbody id="students-table" class="divide-y divide-surface-container">
            <tr>
                <td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td>
            </tr>
        </tbody>
    </table>
    </div>
    <div id="students-pagination"></div>
</div>

<!-- Create/Edit Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Tambah Siswa'])
    <form id="crud-form" onsubmit="saveStudent(event)" class="space-y-4">
        <input type="hidden" id="edit-id" value="">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Nama</label>
            <input type="text" id="field-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Email</label>
            <input type="email" id="field-email" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div id="password-field">
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Password</label>
            <input type="password" id="field-password" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" minlength="6">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Sekolah</label>
            <select id="field-school" onchange="onModalSchoolChange()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Pilih sekolah...</option>
            </select>
            <p id="field-school-locked" class="hidden w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm text-on-surface"></p>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Kelas <span class="text-error">*</span></label>
            <input type="hidden" id="field-class" value="">
            <div class="relative">
                <input type="text" id="class-search" placeholder="Cari kelas..." class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" oninput="filterClassDropdown()">
                <div id="class-dropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-surface-container-low border border-outline-variant/20 rounded-lg shadow-lg z-10 max-h-60 overflow-y-auto">
                    <p class="text-xs text-outline p-3">Pilih kelas...</p>
                </div>
            </div>
            <p id="class-error" class="text-error text-xs mt-1 hidden"></p>
        </div>
        <button type="submit" class="w-full py-3 primary-gradient text-white rounded-lg font-bold text-sm shadow-md active:scale-95 transition-transform">
            Simpan
        </button>
    </form>
@endcomponent

<!-- Reset Password Modal -->
@component('partials.modal', ['id' => 'reset-password-modal', 'title' => 'Reset Password'])
    <form id="reset-password-form" onsubmit="event.preventDefault(); submitResetPassword();">
        <input type="hidden" id="reset-student-id">
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

<!-- Assign to Classes Modal (single-select radio buttons) -->
@component('partials.modal', ['id' => 'assign-modal', 'title' => 'Tetapkan ke Kelas'])
    <form id="assign-form" onsubmit="submitAssignClass(event)" class="space-y-4">
        <input type="hidden" id="assign-student-id" value="">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Cari Kelas</label>
            <input type="text" id="assign-class-search" placeholder="Cari kelas..." class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent mb-3" oninput="filterAssignClassList()">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Pilih Kelas</label>
            <div id="assign-class-list" class="max-h-60 overflow-y-auto space-y-0 bg-surface-container-low rounded-lg border border-outline-variant/20">
                <p class="text-xs text-outline p-3">Memuat kelas...</p>
            </div>
        </div>
        <button type="submit" class="w-full py-3 primary-gradient text-white rounded-lg font-bold text-sm shadow-md active:scale-95 transition-transform">
            Tetapkan Kelas
        </button>
    </form>
@endcomponent

<!-- View Internship Status Modal -->
@component('partials.modal', ['id' => 'status-modal', 'title' => 'Status Magang'])
    <div id="status-content" class="space-y-3">
        <p class="text-on-surface-variant text-sm">Memuat...</p>
    </div>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    if (!Auth.requireAuth()) { /* redirecting */ }

    let currentPage = 1;
    let searchTimeout = null;
    let currentUser = null;

    function setLockedSchoolField(selectId, textId, schoolId, schoolName) {
        const select = document.getElementById(selectId);
        const locked = document.getElementById(textId);
        if (!select || !locked) return;

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
        const existing = document.querySelector(`#field-school option[value="${schoolId}"]`) || document.querySelector(`#filter-school option[value="${schoolId}"]`);
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

    async function applySchoolScopeUi() {
        if (currentUser?.role === 'superadmin') {
            setLockedSchoolField('filter-school', 'filter-school-locked', '', '');
            setLockedSchoolField('field-school', 'field-school-locked', '', '');
            return;
        }

        const schoolId = currentUser?.school_id || '';
        const schoolName = await resolveSchoolName(schoolId);
        document.getElementById('filter-school').value = schoolId;
        document.getElementById('field-school').value = schoolId;
        setLockedSchoolField('filter-school', 'filter-school-locked', schoolId, schoolName);
        setLockedSchoolField('field-school', 'field-school-locked', schoolId, schoolName);
    }

    async function loadModalClasses(schoolId, selectedClassId = null, selectedClassName = '') {
        document.getElementById('field-class').value = selectedClassId || '';
        document.getElementById('class-search').value = selectedClassName || '';
        document.getElementById('class-error').classList.add('hidden');

        if (!schoolId) {
            allClasses = [];
            renderClassDropdown();
            return;
        }

        try {
            const res = await Auth.apiFetch(`/classes?school_id=${schoolId}&per_page=1000`);
            const json = await res.json();
            allClasses = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            renderClassDropdown();
        } catch (err) {
            console.error('Failed to load classes:', err);
            allClasses = [];
            renderClassDropdown();
        }
    }

    async function loadFilterClassesForSchool(schoolId) {
        const classSelect = document.getElementById('filter-class');
        classSelect.innerHTML = '<option value="">Semua Kelas</option>';

        if (schoolId) {
            await AdminUtils.populateSelect('filter-class', `/classes?school_id=${schoolId}`);
            const first = classSelect.querySelector('option[value=""]');
            if (!first) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Semua Kelas';
                classSelect.prepend(opt);
            }
        }
    }

    function getEffectiveSchoolId(selectId) {
        if (currentUser?.role === 'superadmin') {
            return document.getElementById(selectId).value;
        }

        return currentUser?.school_id || document.getElementById(selectId).value;
    }

    function getClassNameById(classId) {
        const classItem = allClasses.find(cls => String(cls.id) === String(classId));
        return classItem?.name || '';
    }

    function getSelectedAssignClassId() {
        const selectedRadio = document.querySelector('input[name="assign-class-select"]:checked');
        return selectedRadio ? parseInt(selectedRadio.value) : null;
    }

    function syncAssignCurrentClass() {
        assignCurrentClassId = getSelectedAssignClassId();
    }

    function restrictClassesToStudentSchool(student, classes) {
        if (currentUser?.role === 'superadmin') {
            return classes;
        }

        return classes.filter(cls => String(cls.school_id) === String(student.school_id));
    }

    function getStatusPayload(json) {
        return json.data ?? json;
    }

    function getInternshipPayload(json) {
        if (!json) return null;
        if (json.data && json.has_active_internship !== undefined) {
            return json.data;
        }
        return json.data || json;
    }

    function updateAssignClassSearchPlaceholder() {
        const input = document.getElementById('assign-class-search');
        if (!input) return;
        input.placeholder = currentUser?.role === 'superadmin' ? 'Cari kelas...' : 'Cari kelas di sekolah Anda...';
    }

    function updateSchoolFiltersVisibility() {
        const filterGroup = document.getElementById('filter-school-group');
        if (!filterGroup) return;
        filterGroup.classList.remove('hidden');
    }

    function updateModalSchoolRequirement() {
        document.getElementById('field-school').required = currentUser?.role === 'superadmin';
    }

    function updateModalSchoolSelection(schoolId) {
        document.getElementById('field-school').value = schoolId || '';
    }

    function updateModalTitle(title) {
        document.getElementById('crud-modal-title').textContent = title;
    }

    function updateClassSearchValue(value) {
        document.getElementById('class-search').value = value || '';
    }

    function clearModalClassSelection() {
        document.getElementById('field-class').value = '';
        updateClassSearchValue('');
    }

    function resetModalValidation() {
        document.getElementById('class-error').classList.add('hidden');
        document.getElementById('class-dropdown').classList.add('hidden');
    }

    function getCurrentSchoolIdForModal() {
        return currentUser?.role === 'superadmin'
            ? document.getElementById('field-school').value
            : (currentUser?.school_id || document.getElementById('field-school').value);
    }

    function showLockedSchoolContextForCurrentUser() {
        return applySchoolScopeUi();
    }

    function getAssignedSchoolId() {
        return currentUser?.school_id || '';
    }

    function isSuperAdmin() {
        return currentUser?.role === 'superadmin';
    }

    function normalizeStudentStatusResponse(json) {
        return getStatusPayload(json);
    }

    function normalizeInternshipStatusResponse(json) {
        return getInternshipPayload(json);
    }

    function getModalSchoolId(student = null) {
        if (isSuperAdmin()) {
            return student?.school_id || document.getElementById('field-school').value || '';
        }

        return getAssignedSchoolId();
    }

    function getFilterSchoolId() {
        return isSuperAdmin() ? document.getElementById('filter-school').value : getAssignedSchoolId();
    }

    function syncModalSchoolValue(student = null) {
        updateModalSchoolSelection(getModalSchoolId(student));
    }

    async function ensureSchoolScopedUi() {
        await showLockedSchoolContextForCurrentUser();
        updateSchoolFiltersVisibility();
        updateModalSchoolRequirement();
        updateAssignClassSearchPlaceholder();
    }

    function getFilteredAssignClasses(search) {
        if (!search.length) {
            return assignAllClasses;
        }

        return assignAllClasses.filter(cls => cls.name.toLowerCase().includes(search));
    }

    function getSelectedStudentClass(student) {
        const studentClasses = student.student_classes || [];
        return studentClasses.length > 0 ? studentClasses[0] : null;
    }

    async function syncModalClassesForStudent(student) {
        const schoolId = getModalSchoolId(student);
        const selectedClass = getSelectedStudentClass(student);
        await loadModalClasses(schoolId, selectedClass?.id || null, selectedClass?.name || '');
    }

    async function syncFilterClassesForCurrentRole() {
        await loadFilterClassesForSchool(getFilterSchoolId());
    }

    function getStudentRequestSchoolId() {
        return getEffectiveSchoolId('field-school');
    }

    function getClassFilterSchoolId() {
        return getEffectiveSchoolId('filter-school');
    }

    function getCurrentAssignSchoolId(student) {
        return isSuperAdmin() ? (student.school_id || null) : getAssignedSchoolId();
    }

    function filterAssignableClasses(student, classes) {
        const schoolId = getCurrentAssignSchoolId(student);
        if (!schoolId) return classes;
        return classes.filter(cls => String(cls.school_id) === String(schoolId));
    }

    function setAssignClasses(classes) {
        assignAllClasses = classes;
    }

    function getNormalizedStudent(json) {
        return getStatusPayload(json);
    }

    function getNormalizedClasses(json) {
        return Array.isArray(json.data) ? json.data : (json.data?.data || []);
    }

    function resetAssignClassSelection() {
        assignCurrentClassId = null;
    }

    function getStudentInternship(json) {
        return normalizeInternshipStatusResponse(json);
    }

    function getStudentEntity(json) {
        return getNormalizedStudent(json);
    }

    function getSchoolIdForStudent(student) {
        return student?.school_id || getAssignedSchoolId();
    }

    function isSchoolAdmin() {
        return currentUser?.role === 'school_admin';
    }

    function lockSchoolSelectorsForAssignedSchool() {
        if (!isSchoolAdmin()) return;
        document.getElementById('filter-school').value = getAssignedSchoolId();
        document.getElementById('field-school').value = getAssignedSchoolId();
    }

    async function refreshLockedSchoolUi() {
        lockSchoolSelectorsForAssignedSchool();
        await ensureSchoolScopedUi();
    }

    // ── Init ──────────────────────────────────────────────
    async function init() {
        const res = await Auth.apiFetch('/auth/me');
        currentUser = await res.json();
        currentUser = currentUser.data || currentUser;

        await AdminUtils.populateSelect('filter-school', '/schools');
        await ensureSchoolScopedUi();
        if (currentUser.role !== 'superadmin') {
            await onSchoolFilterChange();
            return;
        }

        await loadStudents();
    }

    // ── Debounce Search ───────────────────────────────────
    window.debounceSearch = function () {
        clearTimeout(searchTimeout);
        currentPage = 1;
        searchTimeout = setTimeout(() => loadStudents(), 500);
    };

    // ── Filter: school change ─────────────────────────────
    window.onSchoolFilterChange = async function () {
        const schoolId = getClassFilterSchoolId();
        await loadFilterClassesForSchool(schoolId);

        currentPage = 1;
        await loadStudents();
    };

    // ── Load Students ─────────────────────────────────────
    window.loadStudents = async function (page) {
        if (page) currentPage = page;
        const schoolId = getClassFilterSchoolId();
        const classId = document.getElementById('filter-class').value;
        const search = document.getElementById('filter-search').value;
        const perPage = document.getElementById('filter-per-page').value || 15;

        let url = `/students?page=${currentPage}&per_page=${perPage}`;
        if (schoolId) url += `&school_id=${schoolId}`;
        if (classId) url += `&class_id=${classId}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        try {
            const res = await Auth.apiFetch(url);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;

            AdminUtils.renderTable('students-table', items, [
                { key: 'name' },
                { key: 'email' },
                { key: 'student_classes', render: (row) => {
                    const classes = row.student_classes || [];
                    return classes.length ? classes.map(c => `<span class="px-2 py-0.5 bg-primary-fixed text-on-primary-fixed-variant rounded-full text-[0.6rem] font-bold">${c.name}</span>`).join(' ') : '<span class="text-outline text-xs">Tidak ada</span>';
                }},
            ], (row) => `
                ${AdminUtils.editBtn(row.id)}
                <button onclick="resetPassword(${row.id}, '${row.name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-warning transition-colors" title="Reset Password"><span class="material-symbols-outlined text-sm">lock_reset</span></button>
                ${AdminUtils.deleteBtn(row.id, row.name)}
                <button onclick="openAssignModal(${row.id})" class="p-2 text-on-surface-variant hover:text-secondary transition-colors" title="Tetapkan Kelas"><span class="material-symbols-outlined text-sm">school</span></button>
                <button onclick="viewStatus(${row.id})" class="p-2 text-on-surface-variant hover:text-tertiary transition-colors" title="Lihat Status"><span class="material-symbols-outlined text-sm">info</span></button>
            `);

            AdminUtils.renderPagination('students-pagination', meta, loadStudents);
        } catch (err) {
            console.error('Failed to load students:', err);
        }
    };

    // ── Create ────────────────────────────────────────────
    window.openCreateModal = async function () {
        document.getElementById('edit-id').value = '';
        document.getElementById('field-name').value = '';
        document.getElementById('field-email').value = '';
        document.getElementById('field-password').value = '';
        clearModalClassSelection();
        resetModalValidation();
        document.getElementById('field-password').required = true;
        updateModalSchoolRequirement();
        document.getElementById('password-field').style.display = '';
        allClasses = [];
        updateModalTitle('Tambah Siswa');
        await AdminUtils.populateSelect('field-school', '/schools');
        syncModalSchoolValue();
        await ensureSchoolScopedUi();
        await loadModalClasses(getCurrentSchoolIdForModal());
        AdminUtils.showModal('crud-modal');
    };

    // ── Class Dropdown Data ───────────────────────────────
    let allClasses = [];

    // ── School Change (in create/edit modal) ──────────────
    window.onModalSchoolChange = async function () {
        const schoolId = getCurrentSchoolIdForModal();
        clearModalClassSelection();
        document.getElementById('class-error').classList.add('hidden');
        await loadModalClasses(schoolId);
    };

    // ── Render Class Dropdown ──────────────────────────────
    window.renderClassDropdown = function (filtered = null) {
        const dropdown = document.getElementById('class-dropdown');
        const classes = filtered !== null ? filtered : allClasses;
        const selectedId = parseInt(document.getElementById('field-class').value);

        if (classes.length === 0) {
            dropdown.innerHTML = '<p class="text-xs text-outline p-3">No classes found</p>';
        } else {
            dropdown.innerHTML = classes.map(cls => `
                <label class="flex items-center gap-3 px-3 py-2 hover:bg-surface-container-high cursor-pointer border-b border-outline-variant/10 last:border-b-0">
                    <input type="radio" name="class-select" value="${cls.id}" ${selectedId === cls.id ? 'checked' : ''} onchange="selectClass(${cls.id}, '${cls.name.replace(/'/g, "\\'")}')">
                    <span class="text-sm">${cls.name}</span>
                </label>
            `).join('');
        }
    };

    // ── Select Class ───────────────────────────────────────
    window.selectClass = function (classId, className) {
        document.getElementById('field-class').value = classId;
        document.getElementById('class-search').value = className;
        document.getElementById('class-dropdown').classList.add('hidden');
    };

    // ── Filter Class Dropdown ──────────────────────────────
    window.filterClassDropdown = function () {
        const search = document.getElementById('class-search').value.toLowerCase();
        const dropdown = document.getElementById('class-dropdown');

        if (search.length === 0) {
            renderClassDropdown();
        } else {
            const filtered = allClasses.filter(cls => cls.name.toLowerCase().includes(search));
            renderClassDropdown(filtered);
        }

        dropdown.classList.remove('hidden');
    };

    // ── Show/Hide Class Dropdown ───────────────────────────
    document.getElementById('class-search')?.addEventListener('focus', function () {
        document.getElementById('class-dropdown').classList.remove('hidden');
        renderClassDropdown();
    });

    // ── Edit ──────────────────────────────────────────────
    window.editItem = async function (id) {
        try {
            const res = await Auth.apiFetch(`/students/${id}`);
            const json = await res.json();
            const student = json.data || json;

            document.getElementById('edit-id').value = student.id;
            document.getElementById('field-name').value = student.name || '';
            document.getElementById('field-email').value = student.email || '';
            document.getElementById('field-password').value = '';
            document.getElementById('field-password').required = false;
            document.getElementById('field-school').required = false;
            document.getElementById('password-field').style.display = 'none';
            document.getElementById('class-error').classList.add('hidden');

            await AdminUtils.populateSelect('field-school', '/schools');
            syncModalSchoolValue(student);
            await ensureSchoolScopedUi();
            await syncModalClassesForStudent(student);

            updateModalTitle('Edit Siswa');
            AdminUtils.showModal('crud-modal');
        } catch (err) {
            AdminUtils.showToast('Failed to load student', 'error');
        }
    };

    // ── Save (Create / Update) ────────────────────────────
    window.saveStudent = async function (e) {
        e.preventDefault();
        const id = document.getElementById('edit-id').value;
        const classId = document.getElementById('field-class').value;
        const classErrorEl = document.getElementById('class-error');

        // Validate class is selected for create/update
        if (!classId) {
            classErrorEl.textContent = 'Class is required';
            classErrorEl.classList.remove('hidden');
            return;
        }
        classErrorEl.classList.add('hidden');

        const payload = {
            name: document.getElementById('field-name').value,
            email: document.getElementById('field-email').value,
        };
        const password = document.getElementById('field-password').value;
        if (password) payload.password = password;
        const schoolId = getStudentRequestSchoolId();
        if (schoolId) payload.school_id = parseInt(schoolId);

        try {
            const url = id ? `/students/${id}` : '/students';
            const method = id ? 'PUT' : 'POST';
            const res = await Auth.apiFetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!res.ok) throw new Error('Request failed');

            const json = await res.json();
            const studentId = json.data?.id || id;

            // Assign class
            if (classId && studentId) {
                try {
                    await Auth.apiFetch(`/students/${studentId}/assign-class`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ class_id: parseInt(classId) }),
                    });
                } catch (err) {
                    console.error('Failed to assign class:', err);
                    AdminUtils.showToast('Student saved but class assignment failed', 'warning');
                }
            }

            AdminUtils.hideModal('crud-modal');
            AdminUtils.showToast(id ? 'Student updated' : 'Student created');
            await loadStudents();
        } catch (err) {
            AdminUtils.showToast('Failed to save student', 'error');
        }
    };

    // ── Delete ────────────────────────────────────────────
    window.deleteItem = async function (id, name) {
        if (!AdminUtils.confirmDelete(name)) return;
        try {
            const res = await Auth.apiFetch(`/students/${id}`, { method: 'DELETE' });
            if (!res.ok) throw new Error('Request failed');
            AdminUtils.showToast('Student deleted');
            await loadStudents();
        } catch (err) {
            AdminUtils.showToast('Failed to delete student', 'error');
        }
    };

    // ── Assign Class Data ─────────────────────────────────
    let assignAllClasses = [];
    let assignCurrentClassId = null;

    // ── Close dropdowns when clicking outside ───────────────
    document.addEventListener('click', function (e) {
        const classSearch = document.getElementById('class-search');
        const classDropdown = document.getElementById('class-dropdown');
        if (classSearch && classDropdown && !e.target.closest('#class-dropdown') && e.target !== classSearch) {
            classDropdown.classList.add('hidden');
        }
    });

    // ── Open Assign Modal ──────────────────────────────────
    window.openAssignModal = async function (studentId) {
        document.getElementById('assign-student-id').value = studentId;
        document.getElementById('assign-class-search').value = '';
        const container = document.getElementById('assign-class-list');
        container.innerHTML = '<p class="text-xs text-outline p-3">Loading...</p>';

        try {
            const [classesRes, studentRes] = await Promise.all([
                Auth.apiFetch('/classes?per_page=1000'),
                Auth.apiFetch(`/students/${studentId}`),
            ]);
            const classesJson = await classesRes.json();
            const studentJson = await studentRes.json();
            const classes = getNormalizedClasses(classesJson);
            const student = getStudentEntity(studentJson);
            const studentClasses = student.student_classes || [];
            assignCurrentClassId = studentClasses.length > 0 ? studentClasses[0].id : null;
            assignAllClasses = filterAssignableClasses(student, classes);

            renderAssignClassList();
            AdminUtils.showModal('assign-modal');
        } catch (err) {
            AdminUtils.showToast('Failed to load classes', 'error');
        }
    };

    // ── Render Assign Class List ───────────────────────────
    window.renderAssignClassList = function (filtered = null) {
        const container = document.getElementById('assign-class-list');
        const classes = filtered !== null ? filtered : assignAllClasses;

        if (classes.length === 0) {
            container.innerHTML = '<p class="text-xs text-outline p-3">No classes found</p>';
        } else {
            container.innerHTML = classes.map(cls => `
                <label class="flex items-center gap-3 px-3 py-2 border-b border-outline-variant/10 last:border-b-0 hover:bg-surface-container-high cursor-pointer">
                    <input type="radio" name="assign-class-select" value="${cls.id}" ${assignCurrentClassId === cls.id ? 'checked' : ''} onchange="syncAssignCurrentClass()">
                    <span class="text-sm">${cls.name}${cls.school ? ' <span class="text-outline text-xs">(' + cls.school.name + ')</span>' : ''}</span>
                </label>
            `).join('');
        }
    };

    // ── Filter Assign Class List ───────────────────────────
    window.filterAssignClassList = function () {
        const search = document.getElementById('assign-class-search').value.toLowerCase();
        if (search.length === 0) {
            renderAssignClassList();
        } else {
            const filtered = assignAllClasses.filter(cls => cls.name.toLowerCase().includes(search));
            renderAssignClassList(filtered);
        }
    };

    // ── Submit Assign Class ────────────────────────────────
    window.submitAssignClass = async function (e) {
        e.preventDefault();
        const studentId = document.getElementById('assign-student-id').value;
        const selectedRadio = document.querySelector('input[name="assign-class-select"]:checked');
        const classId = selectedRadio ? parseInt(selectedRadio.value) : null;

        if (!classId) {
            AdminUtils.showToast('Please select a class', 'error');
            return;
        }

        try {
            const res = await Auth.apiFetch(`/students/${studentId}/assign-class`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ class_id: classId }),
            });
            if (!res.ok) throw new Error('Failed');

            AdminUtils.hideModal('assign-modal');
            AdminUtils.showToast('Class assignment updated');
            await loadStudents();
        } catch (err) {
            AdminUtils.showToast('Failed to update assignment', 'error');
        }
    };

    // ── Reset Password ───────────────────────────────────
    window.resetPassword = async function (id, name) {
        document.getElementById('reset-student-id').value = id;
        document.getElementById('reset-password-field').value = '';
        document.getElementById('reset-password-confirm-field').value = '';
        document.getElementById('reset-password-modal-title').textContent = `Reset Password for ${name}`;
        AdminUtils.showModal('reset-password-modal');
    };

    window.submitResetPassword = async function () {
        const studentId = document.getElementById('reset-student-id').value;
        const password = document.getElementById('reset-password-field').value;
        const confirm = document.getElementById('reset-password-confirm-field').value;

        if (password !== confirm) {
            AdminUtils.showToast('Passwords do not match', 'error');
            return;
        }

        try {
            const res = await Auth.apiFetch('/admin/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: parseInt(studentId),
                    new_password: password,
                    new_password_confirmation: confirm,
                }),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.error || err.message || 'Failed to reset password');
            }

            AdminUtils.hideModal('reset-password-modal');
            AdminUtils.showToast('Password reset successfully');
            await loadStudents();
        } catch (e) {
            console.error(e);
            AdminUtils.showToast(e.message || 'Failed to reset password', 'error');
        }
    };

    // ── View Internship Status ────────────────────────────
    window.viewStatus = async function (studentId) {
        const content = document.getElementById('status-content');
        content.innerHTML = '<p class="text-on-surface-variant text-sm">Memuat...</p>';
        AdminUtils.showModal('status-modal');

        try {
            const res = await Auth.apiFetch(`/students/${studentId}/internship-status`);
            const json = await res.json();
            const status = getStatusPayload(json);

            if (status && status.company) {
                content.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Perusahaan</p>
                            <p class="text-sm font-semibold text-on-surface">${status.company.name || status.company || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Pembimbing</p>
                            <p class="text-sm font-semibold text-on-surface">${status.supervisor?.name || status.supervisor || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Tanggal Mulai</p>
                            <p class="text-sm font-semibold text-on-surface">${status.start_date || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Tanggal Selesai</p>
                            <p class="text-sm font-semibold text-on-surface">${status.end_date || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Status</p>
                            ${AdminUtils.statusBadge(status.status || 'pending')}
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = '<p class="text-on-surface-variant text-sm">Tidak ada magang aktif untuk siswa ini.</p>';
            }
        } catch (err) {
            content.innerHTML = '<p class="text-error text-sm">Gagal memuat status magang.</p>';
        }
    };

    // ── Template ───────────────────────────────────────────
    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/students/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-siswa.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Gagal download template', 'error');
        }
    }

    // ── Export/Import ──────────────────────────────────────
    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/students', {
                headers: {
                    'Authorization': 'Bearer ' + Auth.getToken(),
                },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'students.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            AdminUtils.showToast('Failed to export students', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('file', file);
        
        try {
            const res = await fetch('/api/v1/import/students', {
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
            
            AdminUtils.showToast('Students imported successfully');
            await loadStudents();
        } catch (e) {
            console.error(e);
            AdminUtils.showToast(e.message || 'Failed to import students', 'error');
        }
        
        input.value = '';
    }

    // ── Global exports ─────────────────────────────────────
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    // ── Boot ──────────────────────────────────────────────
    init();
</script>
@endpush
