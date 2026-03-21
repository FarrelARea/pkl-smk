@extends('layouts.app')

@section('title', 'Students Management')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Students Management</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Manage student records, assign classes, and track internship statuses.
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
            <span class="material-symbols-outlined text-sm">add</span> Add Student
        </button>
    </div>
</header>

<!-- Filter Bar -->
<div class="flex gap-4 mb-8 flex-wrap items-end">
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Search</label>
        <input type="text" id="filter-search" placeholder="Name or Email..." class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onkeyup="debounceSearch()">
    </div>
    <div class="w-64">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">School</label>
        <select id="filter-school" onchange="onSchoolFilterChange()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">All Schools</option>
        </select>
    </div>
    <div class="w-64">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Class</label>
        <select id="filter-class" onchange="loadStudents()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">All Classes</option>
        </select>
    </div>
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Per Page</label>
        <select id="filter-per-page" onchange="loadStudents()" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
</div>

<!-- Students Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Name</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Assigned Classes</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="students-table" class="divide-y divide-surface-container">
            <tr>
                <td colspan="3" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td>
            </tr>
        </tbody>
    </table>
    <div id="students-pagination"></div>
</div>

<!-- Create/Edit Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Add Student'])
    <form id="crud-form" onsubmit="saveStudent(event)" class="space-y-4">
        <input type="hidden" id="edit-id" value="">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Name</label>
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
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">School</label>
            <select id="field-school" onchange="onModalSchoolChange()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Select a school...</option>
            </select>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Class <span class="text-error">*</span></label>
            <input type="hidden" id="field-class" value="">
            <div class="relative">
                <input type="text" id="class-search" placeholder="Search classes..." class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" oninput="filterClassDropdown()">
                <div id="class-dropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-surface-container-low border border-outline-variant/20 rounded-lg shadow-lg z-10 max-h-60 overflow-y-auto">
                    <p class="text-xs text-outline p-3">Select a class...</p>
                </div>
            </div>
            <p id="class-error" class="text-error text-xs mt-1 hidden"></p>
        </div>
        <button type="submit" class="w-full py-3 primary-gradient text-white rounded-lg font-bold text-sm shadow-md active:scale-95 transition-transform">
            Save Student
        </button>
    </form>
@endcomponent

<!-- Reset Password Modal -->
@component('partials.modal', ['id' => 'reset-password-modal', 'title' => 'Reset Password'])
    <form id="reset-password-form" onsubmit="event.preventDefault(); submitResetPassword();">
        <input type="hidden" id="reset-student-id">
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

<!-- Assign to Classes Modal (single-select radio buttons) -->
@component('partials.modal', ['id' => 'assign-modal', 'title' => 'Assign to Class'])
    <form id="assign-form" onsubmit="submitAssignClass(event)" class="space-y-4">
        <input type="hidden" id="assign-student-id" value="">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Search Class</label>
            <input type="text" id="assign-class-search" placeholder="Search classes..." class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent mb-3" oninput="filterAssignClassList()">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Select Class</label>
            <div id="assign-class-list" class="max-h-60 overflow-y-auto space-y-0 bg-surface-container-low rounded-lg border border-outline-variant/20">
                <p class="text-xs text-outline p-3">Loading classes...</p>
            </div>
        </div>
        <button type="submit" class="w-full py-3 primary-gradient text-white rounded-lg font-bold text-sm shadow-md active:scale-95 transition-transform">
            Assign Class
        </button>
    </form>
@endcomponent

<!-- View Internship Status Modal -->
@component('partials.modal', ['id' => 'status-modal', 'title' => 'Internship Status'])
    <div id="status-content" class="space-y-3">
        <p class="text-on-surface-variant text-sm">Loading...</p>
    </div>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    if (!Auth.requireAuth()) { /* redirecting */ }

    let currentPage = 1;
    let searchTimeout = null;

    // ── Init ──────────────────────────────────────────────
    async function init() {
        await AdminUtils.populateSelect('filter-school', '/schools');
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
        const schoolId = document.getElementById('filter-school').value;
        const classSelect = document.getElementById('filter-class');
        classSelect.innerHTML = '<option value="">All Classes</option>';

        if (schoolId) {
            await AdminUtils.populateSelect('filter-class', `/classes?school_id=${schoolId}`);
            // Re-add the "All" placeholder if populateSelect cleared it
            const first = classSelect.querySelector('option[value=""]');
            if (!first) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'All Classes';
                classSelect.prepend(opt);
            }
        }

        currentPage = 1;
        await loadStudents();
    };

    // ── Load Students ─────────────────────────────────────
    window.loadStudents = async function (page) {
        if (page) currentPage = page;
        const schoolId = document.getElementById('filter-school').value;
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
                    return classes.length ? classes.map(c => `<span class="px-2 py-0.5 bg-primary-fixed text-on-primary-fixed-variant rounded-full text-[0.6rem] font-bold">${c.name}</span>`).join(' ') : '<span class="text-outline text-xs">None</span>';
                }},
            ], (row) => `
                ${AdminUtils.editBtn(row.id)}
                <button onclick="resetPassword(${row.id}, '${row.name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-warning transition-colors" title="Reset Password"><span class="material-symbols-outlined text-sm">lock_reset</span></button>
                ${AdminUtils.deleteBtn(row.id, row.name)}
                <button onclick="openAssignModal(${row.id})" class="p-2 text-on-surface-variant hover:text-secondary transition-colors" title="Assign Class"><span class="material-symbols-outlined text-sm">school</span></button>
                <button onclick="viewStatus(${row.id})" class="p-2 text-on-surface-variant hover:text-tertiary transition-colors" title="View Status"><span class="material-symbols-outlined text-sm">info</span></button>
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
        document.getElementById('field-class').value = '';
        document.getElementById('class-search').value = '';
        document.getElementById('class-error').classList.add('hidden');
        document.getElementById('class-dropdown').classList.add('hidden');
        document.getElementById('field-password').required = true;
        document.getElementById('field-school').required = true;
        document.getElementById('password-field').style.display = '';
        allClasses = [];
        document.getElementById('crud-modal-title').textContent = 'Add Student';
        await AdminUtils.populateSelect('field-school', '/schools');
        AdminUtils.showModal('crud-modal');
    };

    // ── Class Dropdown Data ───────────────────────────────
    let allClasses = [];

    // ── School Change (in create/edit modal) ──────────────
    window.onModalSchoolChange = async function () {
        const schoolId = document.getElementById('field-school').value;
        document.getElementById('field-class').value = '';
        document.getElementById('class-search').value = '';
        document.getElementById('class-error').classList.add('hidden');

        if (schoolId) {
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
        } else {
            allClasses = [];
            renderClassDropdown();
        }
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
            document.getElementById('field-school').value = student.school_id || '';

            // Populate classes for the student's school
            if (student.school_id) {
                await AdminUtils.populateSelect('field-class', `/classes?school_id=${student.school_id}`);
                // Pre-select the student's current class
                const studentClasses = student.student_classes || [];
                if (studentClasses.length > 0) {
                    document.getElementById('field-class').value = studentClasses[0].id;
                }
            }

            document.getElementById('crud-modal-title').textContent = 'Edit Student';
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
        const schoolId = document.getElementById('field-school').value;
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
            const classes = Array.isArray(classesJson.data) ? classesJson.data : (classesJson.data?.data || []);
            const student = studentJson.data || studentJson;
            const studentClasses = student.student_classes || [];
            assignCurrentClassId = studentClasses.length > 0 ? studentClasses[0].id : null;
            assignAllClasses = classes;

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
                    <input type="radio" name="assign-class-select" value="${cls.id}" ${assignCurrentClassId === cls.id ? 'checked' : ''} onchange="document.getElementById('assign-current-class').value = '${cls.id}'">
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
        content.innerHTML = '<p class="text-on-surface-variant text-sm">Loading...</p>';
        AdminUtils.showModal('status-modal');

        try {
            const res = await Auth.apiFetch(`/students/${studentId}/internship-status`);
            const json = await res.json();
            const status = json.data || json;

            if (status && status.company) {
                content.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Company</p>
                            <p class="text-sm font-semibold text-on-surface">${status.company.name || status.company || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Supervisor</p>
                            <p class="text-sm font-semibold text-on-surface">${status.supervisor?.name || status.supervisor || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Start Date</p>
                            <p class="text-sm font-semibold text-on-surface">${status.start_date || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">End Date</p>
                            <p class="text-sm font-semibold text-on-surface">${status.end_date || '—'}</p>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Status</p>
                            ${AdminUtils.statusBadge(status.status || 'pending')}
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = '<p class="text-on-surface-variant text-sm">No active internship found for this student.</p>';
            }
        } catch (err) {
            content.innerHTML = '<p class="text-error text-sm">Failed to load internship status.</p>';
        }
    };

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

    // ── Boot ──────────────────────────────────────────────
    init();
</script>
@endpush
