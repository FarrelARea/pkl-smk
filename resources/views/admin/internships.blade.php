@extends('layouts.app')

@section('title', 'Manajemen Magang')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-[26px] font-extrabold text-on-surface tracking-tight mb-2 font-headline">Manajemen Magang</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Kelola penugasan magang antara siswa, perusahaan, dan pembimbing.
        </p>
    </div>
    <x-help-button title="Panduan Manajemen Magang">
        <p>Di halaman ini kamu bisa mengelola penugasan magang.</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Tambah penugasan magang baru (siswa → perusahaan → pembimbing)</li>
            <li>Tetapkan siswa secara massal ke perusahaan</li>
            <li>Akhiri magang yang sudah selesai</li>
            <li>Filter berdasarkan siswa, perusahaan, atau status</li>
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
        <button onclick="openBatchModal()" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">group_add</span> Tetapkan Massal
        </button>
        <button onclick="openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Magang
        </button>
    </div>
</header>

<!-- Filter Bar -->
<div class="mb-6 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Cari</label>
        <input type="text" id="filter-search" placeholder="Nama siswa..." class="px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onkeyup="debounceSearch()">
    </div>
    <div class="w-56">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="filter-student">Siswa</label>
        <select id="filter-student" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">Semua Siswa</option>
        </select>
    </div>
    <div class="w-56">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="filter-company">Perusahaan</label>
        <select id="filter-company" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">Semua Perusahaan</option>
        </select>
    </div>
    <div class="w-48">
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="filter-status">Status</label>
        <select id="filter-status" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="completed">Selesai</option>
            <option value="cancelled">Dibatalkan</option>
        </select>
    </div>
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Per Page</label>
        <select id="filter-per-page" onchange="loadData(1)" class="px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
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
        <h2 class="text-xl font-bold tracking-tight font-headline">Magang</h2>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Siswa</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Perusahaan</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Pembimbing</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Tanggal Mulai</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Tanggal Selesai</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
        </tbody>
    </table>
    </div>
    <div id="pagination"></div>
</div>

<!-- Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Magang'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Cari Siswa</label>
                <input type="text" id="field-student-search" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent mb-2" placeholder="Ketik nama siswa..." oninput="filterStudentDropdown()">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-student_id">Siswa</label>
                <select id="field-student_id" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Siswa</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-company_id">Perusahaan</label>
                <select id="field-company_id" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Perusahaan</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-supervisor_id">Pembimbing</label>
                <select id="field-supervisor_id" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Pembimbing</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-start_date">Tanggal Mulai</label>
                <input id="field-start_date" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-end_date">Tanggal Selesai</label>
                <input id="field-end_date" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-status">Status</label>
                <select id="field-status" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="active">Aktif</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Simpan</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- Batch Assignment Modal -->
@component('partials.modal', ['id' => 'batch-modal', 'title' => 'Tetapkan Siswa Massal'])
    <form id="batch-form" onsubmit="event.preventDefault(); submitBatch();">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-company_id">Perusahaan</label>
                <select id="batch-company_id" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" onchange="loadCompanySupervisors()">
                    <option value="">Pilih Perusahaan</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-supervisor_id">Pembimbing</label>
                <select id="batch-supervisor_id" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Pembimbing</option>
                </select>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-start_date">Tanggal Mulai</label>
                <input id="batch-start_date" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-end_date">Tanggal Selesai</label>
                <input id="batch-end_date" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="date">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="batch-student-search">Cari Siswa</label>
                <input type="text" id="batch-student-search" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Ketik untuk mencari siswa..." oninput="filterStudents()">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">
                    Siswa
                    <span class="normal-case font-normal text-xs" id="selected-count">(0 dipilih, maks 50)</span>
                </label>
                <div id="batch-students-list" class="w-full h-96 overflow-y-auto px-3 py-2 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm space-y-1">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('batch-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Tetapkan</button>
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

            populateSelect('filter-student', studentsList, 'Semua Siswa');
            populateSelect('filter-company', companiesList, 'Semua Perusahaan');
            populateSelect('field-student_id', studentsList, 'Pilih Siswa');
            populateSelect('field-company_id', companiesList, 'Pilih Perusahaan');
            populateSelect('field-supervisor_id', supervisorsList, 'Pilih Pembimbing');
            populateSelect('batch-company_id', companiesList, 'Pilih Perusahaan');
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
                    <button onclick="endInternship(${row.id})" class="p-2 text-on-surface-variant hover:text-error transition-colors" title="Akhiri Magang"><span class="material-symbols-outlined text-sm">stop_circle</span></button>
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
        document.getElementById('crud-modal-title').textContent = 'Tambah Magang';
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
            document.getElementById('crud-modal-title').textContent = 'Edit Magang';
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
        if (!confirm('Yakin ingin mengakhiri magang ini?')) return;
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
        populateSelect('batch-company_id', companiesList, 'Pilih Perusahaan');
        document.getElementById('batch-supervisor_id').innerHTML = '<option value="">Pilih Pembimbing</option>';
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
            supervisorSelect.innerHTML = '<option value="">Pilih Pembimbing</option>';
            return;
        }

        try {
            const res = await Auth.apiFetch(`/supervisors?company_id=${companyId}&per_page=1000`);
            const json = await res.json();
            const supervisors = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            
            supervisorSelect.innerHTML = '<option value="">Pilih Pembimbing</option>';
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
        document.getElementById('selected-count').textContent = `(${selectedStudentIds.size} dipilih, maks 50)`;
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

    // ── Import/Export/Template ──────────────────────────────
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/internships', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'magang.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal export magang', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        try {
            const res = await fetch('/api/v1/import/internships', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
                body: formData,
            });
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Import failed');
            }
            showToast('Magang berhasil diimport');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Gagal import magang', 'error');
        }
        input.value = '';
    }

    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/internships/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-magang.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal download template', 'error');
        }
    }

    // ── Init ─────────────────────────────────────────────
    await loadDropdowns();
    loadData();
</script>
@endpush
