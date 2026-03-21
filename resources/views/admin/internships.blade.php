@extends('layouts.app')

@section('title', 'Internships Management')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Internships Management</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Manage internship assignments between students, companies and supervisors.
        </p>
    </div>
    <div>
        <button onclick="openBatchModal()" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2 mr-3">
            <span class="material-symbols-outlined text-sm">group_add</span> Batch Assign
        </button>
        <button onclick="openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Add Internship
        </button>
    </div>
</header>

<!-- Filter Bar -->
<div class="mb-6 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Search</label>
        <input type="text" id="filter-search" placeholder="Student name..." class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onkeyup="debounceSearch()">
    </div>
    <div class="w-56">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="filter-student">Student</label>
        <select id="filter-student" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">All Students</option>
        </select>
    </div>
    <div class="w-56">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="filter-company">Company</label>
        <select id="filter-company" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">All Companies</option>
        </select>
    </div>
    <div class="w-48">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="filter-status">Status</label>
        <select id="filter-status" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Per Page</label>
        <select id="filter-per-page" onchange="loadData(1)" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
</div>

<!-- Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex justify-between items-center border-b border-surface-container">
        <h2 class="text-xl font-bold tracking-tight font-headline">Internships</h2>
    </div>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Student</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Company</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Supervisor</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Start Date</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">End Date</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td></tr>
        </tbody>
    </table>
    <div id="pagination"></div>
</div>

<!-- Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Internship'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Search Student</label>
                <input type="text" id="field-student-search" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent mb-2" placeholder="Type student name..." oninput="filterStudentDropdown()">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-student_id">Student</label>
                <select id="field-student_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select Student</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-company_id">Company</label>
                <select id="field-company_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select Company</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-supervisor_id">Supervisor</label>
                <select id="field-supervisor_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select Supervisor</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-start_date">Start Date</label>
                <input id="field-start_date" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-end_date">End Date</label>
                <input id="field-end_date" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-status">Status</label>
                <select id="field-status" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Save</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- Batch Assignment Modal -->
@component('partials.modal', ['id' => 'batch-modal', 'title' => 'Batch Assign Students'])
    <form id="batch-form" onsubmit="event.preventDefault(); submitBatch();">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-company_id">Company</label>
                <select id="batch-company_id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onchange="loadCompanySupervisors()">
                    <option value="">Select Company</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-supervisor_id">Supervisor</label>
                <select id="batch-supervisor_id" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select Supervisor</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-start_date">Start Date</label>
                <input id="batch-start_date" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-end_date">End Date</label>
                <input id="batch-end_date" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-student-search">Search Students</label>
                <input type="text" id="batch-student-search" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Type to search students..." oninput="filterStudents()">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">
                    Students
                    <span class="normal-case font-normal text-xs" id="selected-count">(0 selected, max 50)</span>
                </label>
                <div id="batch-students-list" class="w-full h-96 overflow-y-auto px-3 py-2 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm space-y-1">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('batch-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Assign</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete, renderTable, renderPagination, editBtn, deleteBtn, statusBadge } = AdminUtils;

    const ENDPOINT = '/internships';
    let currentPage = 1;
    let searchTimeout = null;

    function getPerPage() {
        return document.getElementById('filter-per-page').value || 15;
    }

    function debounceSearch() {
        clearTimeout(searchTimeout);
        currentPage = 1;
        searchTimeout = setTimeout(() => loadData(), 500);
    }

    // Indonesian month names
    const indonesianMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function formatDateIndonesian(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        const day = date.getDate();
        const month = indonesianMonths[date.getMonth()];
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }

    let studentsList = [];
    let companiesList = [];
    let supervisorsList = [];

    // ── Load Dropdowns ───────────────────────────────────
    async function loadDropdowns() {
        try {
            const [studentsRes, companiesRes, supervisorsRes] = await Promise.all([
                Auth.apiFetch('/students?per_page=1000'),
                Auth.apiFetch('/companies?per_page=1000'),
                Auth.apiFetch('/supervisors?per_page=1000'),
            ]);

            const studentsJson = await studentsRes.json();
            const companiesJson = await companiesRes.json();
            const supervisorsJson = await supervisorsRes.json();

            studentsList = Array.isArray(studentsJson.data) ? studentsJson.data : (studentsJson.data?.data || []);
            companiesList = Array.isArray(companiesJson.data) ? companiesJson.data : (companiesJson.data?.data || []);
            supervisorsList = Array.isArray(supervisorsJson.data) ? supervisorsJson.data : (supervisorsJson.data?.data || []);

            populateSelect('filter-student', studentsList, 'All Students');
            populateSelect('filter-company', companiesList, 'All Companies');
            populateSelect('field-student_id', studentsList, 'Select Student');
            populateSelect('field-company_id', companiesList, 'Select Company');
            populateSelect('field-supervisor_id', supervisorsList, 'Select Supervisor');
            populateSelect('batch-company_id', companiesList, 'Select Company');
        } catch (e) {
            console.error(e);
            showToast('Failed to load dropdown data', 'error');
        }
    }

    function populateSelect(selectId, items, placeholder) {
        const select = document.getElementById(selectId);
        select.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
        });
    }

    // ── Load Data ────────────────────────────────────────
    async function loadData(page = 1) {
        currentPage = page;
        try {
            const perPage = getPerPage();
            const params = new URLSearchParams({ page, per_page: perPage });
            const studentId = document.getElementById('filter-student').value;
            const companyId = document.getElementById('filter-company').value;
            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('filter-search').value;
            if (studentId) params.set('student_id', studentId);
            if (companyId) params.set('company_id', companyId);
            if (status) params.set('status', status);
            if (search) params.set('search', encodeURIComponent(search));

            const res = await Auth.apiFetch(`${ENDPOINT}?${params}`);
            
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Failed to load internships');
            }
            
            const json = await res.json();
            
            // Handle both paginated and non-paginated responses
            let items = [];
            let meta = null;
            
            if (json.data) {
                if (Array.isArray(json.data)) {
                    items = json.data;
                    meta = json;
                } else if (json.data.data && Array.isArray(json.data.data)) {
                    items = json.data.data;
                    meta = json.data;
                }
            }

            if (!Array.isArray(items) || items.length === 0) {
                renderTable('data-table', [], [
                    { key: 'student', render: () => '-' },
                    { key: 'company', render: () => '-' },
                    { key: 'supervisor', render: () => '-' },
                    { key: 'start_date', render: () => '-' },
                    { key: 'end_date', render: () => '-' },
                    { key: 'status', render: () => '-' },
                ], () => '<td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">No internships found</td>');
                return;
            }

            renderTable('data-table', items, [
                { key: 'student', render: (row) => row?.student?.name || '-' },
                { key: 'company', render: (row) => row?.company?.name || '-' },
                { key: 'supervisor', render: (row) => row?.supervisor?.name || '-' },
                { key: 'start_date', render: (row) => formatDateIndonesian(row?.start_date) },
                { key: 'end_date', render: (row) => formatDateIndonesian(row?.end_date) },
                { key: 'status', render: (row) => AdminUtils.statusBadge(row?.status) },
            ], (row) => `
                <div class="flex justify-end gap-1">
                    ${editBtn(row.id)}
                    <button onclick="endInternship(${row.id})" class="p-2 text-on-surface-variant hover:text-error transition-colors" title="End Internship"><span class="material-symbols-outlined text-sm">stop_circle</span></button>
                    ${deleteBtn(row.id, row?.student?.name || 'this internship')}
                </div>
            `);

            if (meta) {
                renderPagination('pagination', meta, loadData);
            }
        } catch (e) {
            console.error(e);
            showToast('Failed to load internships', 'error');
        }
    }

    // ── Open Create Modal ────────────────────────────────
    function openCreateModal() {
        document.getElementById('item-id').value = '';
        document.getElementById('crud-form').reset();
        document.getElementById('field-student-search').value = '';
        document.getElementById('crud-modal-title').textContent = 'Add Internship';
        showModal('crud-modal');
    }

    // ── Filter Student Dropdown ───────────────────────────
    function filterStudentDropdown() {
        const searchValue = document.getElementById('field-student-search').value.toLowerCase();
        const selectEl = document.getElementById('field-student_id');
        const options = selectEl.querySelectorAll('option');

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const matches = option.textContent.toLowerCase().includes(searchValue);
                option.style.display = matches ? 'block' : 'none';
            }
        });
    }

    // ── Edit Item ────────────────────────────────────────
    async function editItem(id) {
        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${id}`);
            const json = await res.json();
            const item = json.data || json;

            document.getElementById('item-id').value = item.id;
            document.getElementById('field-student_id').value = item.student_id || '';
            document.getElementById('field-company_id').value = item.company_id || '';
            document.getElementById('field-supervisor_id').value = item.supervisor_id || '';
            document.getElementById('field-start_date').value = item.start_date || '';
            document.getElementById('field-end_date').value = item.end_date || '';
            document.getElementById('field-status').value = item.status || 'active';
            document.getElementById('crud-modal-title').textContent = 'Edit Internship';
            showModal('crud-modal');
        } catch (e) {
            console.error(e);
            showToast('Failed to load internship details', 'error');
        }
    }

    // ── Delete Item ──────────────────────────────────────
    async function deleteItem(id, name) {
        if (!confirmDelete(name)) return;
        try {
            await Auth.apiFetch(`${ENDPOINT}/${id}`, { method: 'DELETE' });
            showToast('Internship deleted successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast('Failed to delete internship', 'error');
        }
    }

    // ── End Internship ───────────────────────────────────
    async function endInternship(id) {
        if (!confirm('Are you sure you want to end this internship?')) return;
        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${id}/end`, { method: 'POST' });
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Failed to end internship');
            }
            showToast('Internship ended successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to end internship', 'error');
        }
    }

    // ── Save Item ────────────────────────────────────────
    async function saveItem() {
        const id = document.getElementById('item-id').value;
        const payload = {
            student_id: document.getElementById('field-student_id').value,
            company_id: document.getElementById('field-company_id').value,
            supervisor_id: document.getElementById('field-supervisor_id').value,
            start_date: document.getElementById('field-start_date').value,
            end_date: document.getElementById('field-end_date').value || null,
            status: document.getElementById('field-status').value,
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
            showToast(id ? 'Internship updated successfully' : 'Internship created successfully');
            loadData(id ? currentPage : 1);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to save internship', 'error');
        }
    }

    // ── Filter on change ─────────────────────────────────
    document.getElementById('filter-student').addEventListener('change', () => loadData(1));
    document.getElementById('filter-company').addEventListener('change', () => loadData(1));
    document.getElementById('filter-status').addEventListener('change', () => loadData(1));

    // ── Batch Assignment ─────────────────────────────────
    function openBatchModal() {
        document.getElementById('batch-form').reset();
        populateSelect('batch-company_id', companiesList, 'Select Company');
        document.getElementById('batch-supervisor_id').innerHTML = '<option value="">Select Supervisor</option>';
        document.getElementById('batch-student-search').value = '';
        document.getElementById('batch-students-list').innerHTML = '<p class="text-on-surface-variant text-sm py-4">Select a company to see available students</p>';
        selectedStudentIds = new Set();
        updateSelectedCount();
        showModal('batch-modal');
    }

    async function loadCompanySupervisors() {
        const companyId = document.getElementById('batch-company_id').value;
        const supervisorSelect = document.getElementById('batch-supervisor_id');

        if (!companyId) {
            supervisorSelect.innerHTML = '<option value="">Select Supervisor</option>';
            return;
        }

        try {
            const res = await Auth.apiFetch(`/supervisors?company_id=${companyId}&per_page=1000`);
            const json = await res.json();
            const supervisors = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            
            supervisorSelect.innerHTML = '<option value="">Select Supervisor</option>';
            supervisors.forEach(sup => {
                const option = document.createElement('option');
                option.value = sup.id;
                option.textContent = sup.name;
                supervisorSelect.appendChild(option);
            });

            await loadAvailableStudents(companyId);
        } catch (e) {
            console.error(e);
            showToast('Failed to load supervisors', 'error');
        }
    }

    let availableStudents = [];
    let selectedStudentIds = new Set();

    async function loadAvailableStudents(companyId) {
        const studentsListEl = document.getElementById('batch-students-list');

        try {
            const res = await Auth.apiFetch('/students?per_page=1000');
            const json = await res.json();
            const allStudents = Array.isArray(json.data) ? json.data : (json.data?.data || []);

            const activeRes = await Auth.apiFetch('/internships?per_page=1000&status=active');
            const activeJson = await activeRes.json();
            const activeInternships = Array.isArray(activeJson.data) ? activeJson.data : (activeJson.data?.data || []);
            const activeStudentIds = activeInternships.map(i => i.student_id);

            availableStudents = allStudents.filter(s => !activeStudentIds.includes(s.id));
            selectedStudentIds = new Set();

            renderStudentsList();
        } catch (e) {
            console.error(e);
            showToast('Failed to load students', 'error');
        }
    }

    function renderStudentsList(filter = '') {
        const studentsListEl = document.getElementById('batch-students-list');
        const filtered = availableStudents.filter(s => 
            s.name.toLowerCase().includes(filter.toLowerCase())
        );
        
        studentsListEl.innerHTML = filtered.map(student => `
            <label class="flex items-center gap-2 py-1 cursor-pointer hover:bg-surface-container-high rounded px-2">
                <input type="checkbox" 
                    value="${student.id}" 
                    ${selectedStudentIds.has(student.id) ? 'checked' : ''}
                    onchange="toggleStudent(${student.id})"
                    class="rounded border-outline-variant text-primary focus:ring-primary">
                <span class="text-sm">${student.name}</span>
            </label>
        `).join('');
        
        updateSelectedCount();
    }

    function toggleStudent(studentId) {
        if (selectedStudentIds.has(studentId)) {
            selectedStudentIds.delete(studentId);
        } else {
            if (selectedStudentIds.size >= 50) {
                showToast('Maximum 50 students allowed', 'error');
                return;
            }
            selectedStudentIds.add(studentId);
        }
        renderStudentsList(document.getElementById('batch-student-search').value);
    }

    function filterStudents() {
        const searchValue = document.getElementById('batch-student-search').value;
        renderStudentsList(searchValue);
    }

    function updateSelectedCount() {
        document.getElementById('selected-count').textContent = `(${selectedStudentIds.size} selected, max 50)`;
    }

    async function submitBatch() {
        const studentIds = Array.from(selectedStudentIds);
        
        if (studentIds.length === 0) {
            showToast('Please select at least one student', 'error');
            return;
        }

        const payload = {
            student_ids: studentIds,
            company_id: document.getElementById('batch-company_id').value,
            supervisor_id: document.getElementById('batch-supervisor_id').value || null,
            start_date: document.getElementById('batch-start_date').value,
            end_date: document.getElementById('batch-end_date').value,
        };

        try {
            const res = await Auth.apiFetch('/internships/batch', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.error || 'Failed to create internships');
            }

            hideModal('batch-modal');
            showToast(`Successfully assigned ${studentIds.length} students`);
            loadData(1);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to create internships', 'error');
        }
    }

    // ── Expose to window ─────────────────────────────────
    window.loadData = loadData;
    window.editItem = editItem;
    window.deleteItem = deleteItem;
    window.saveItem = saveItem;
    window.openCreateModal = openCreateModal;
    window.openBatchModal = openBatchModal;
    window.debounceSearch = debounceSearch;
    window.filterStudentDropdown = filterStudentDropdown;
    window.loadCompanySupervisors = loadCompanySupervisors;
    window.filterStudents = filterStudents;
    window.toggleStudent = toggleStudent;
    window.submitBatch = submitBatch;
    window.endInternship = endInternship;

    // ── Init ─────────────────────────────────────────────
    await loadDropdowns();
    loadData();
</script>
@endpush
