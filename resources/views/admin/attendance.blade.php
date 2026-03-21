@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Attendance Management</h1>
        <div class="flex items-center gap-3">
            <button onclick="window.openBulkModal()" class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider">
                Bulk Record
            </button>
            <button onclick="window.openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                Record Attendance
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Student</label>
            <select id="filter-student" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">All Students</option>
            </select>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Start Date</label>
            <input type="date" id="filter-start-date" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">End Date</label>
            <input type="date" id="filter-end-date" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <button onclick="window.loadAttendance()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                Apply
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <table class="w-full">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Student</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Date</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Status</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="attendance-table-body">
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant text-sm">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Record Attendance Modal --}}
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Attendance'])
    <form id="crud-form" onsubmit="window.saveAttendance(event)" class="space-y-4">
        <input type="hidden" id="crud-id">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Student</label>
            <select id="crud-student-id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Select Student</option>
            </select>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Attendance Date</label>
            <input type="date" id="crud-attendance-date" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Status</label>
            <select id="crud-status" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Select Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="sick">Sick</option>
                <option value="permission">Permission</option>
            </select>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="window.closeModal('crud-modal')" class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider">
                Cancel
            </button>
            <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                Save
            </button>
        </div>
    </form>
@endcomponent

{{-- Bulk Record Modal --}}
@component('partials.modal', ['id' => 'bulk-modal', 'title' => 'Bulk Record Attendance'])
    <form id="bulk-form" onsubmit="window.saveBulkAttendance(event)" class="space-y-4">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Students</label>
            <div id="bulk-students-list" class="max-h-48 overflow-y-auto space-y-2 p-3 bg-surface-container-low border border-outline-variant/20 rounded-lg">
                <p class="text-sm text-on-surface-variant">Loading students...</p>
            </div>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Attendance Date</label>
            <input type="date" id="bulk-attendance-date" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Status</label>
            <select id="bulk-status" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Select Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="sick">Sick</option>
                <option value="permission">Permission</option>
            </select>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="window.closeModal('bulk-modal')" class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider">
                Cancel
            </button>
            <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                Save
            </button>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { apiFetch } = Auth;
    let students = [];

    async function loadStudents() {
        const json = await apiFetch('/students');
        students = Array.isArray(json.data) ? json.data : (json.data?.data || []);

        const filterSelect = document.getElementById('filter-student');
        const crudSelect = document.getElementById('crud-student-id');

        const options = students.map(s => `<option value="${s.id}">${s.name || s.user?.name || ''}</option>`).join('');
        filterSelect.innerHTML = `<option value="">All Students</option>` + options;
        crudSelect.innerHTML = `<option value="">Select Student</option>` + options;
    }

    window.loadAttendance = async function () {
        const studentId = document.getElementById('filter-student').value;
        const startDate = document.getElementById('filter-start-date').value;
        const endDate = document.getElementById('filter-end-date').value;

        const params = new URLSearchParams();
        if (studentId) params.append('student_id', studentId);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        const json = await apiFetch(`/attendance?${params.toString()}`);
        const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);

        const tbody = document.getElementById('attendance-table-body');

        if (items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-8 text-center text-on-surface-variant text-sm">No attendance records found.</td></tr>`;
            return;
        }

        tbody.innerHTML = items.map(row => {
            const studentName = row.student?.name || row.user?.name || '-';
            return `
                <tr class="border-t border-outline-variant/10">
                    <td class="px-6 py-4 text-sm text-on-surface">${studentName}</td>
                    <td class="px-6 py-4 text-sm text-on-surface">${row.attendance_date || ''}</td>
                    <td class="px-6 py-4 text-sm">${AdminUtils.statusBadge(row.status)}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <button onclick="window.openEditModal(${row.id})" class="text-primary hover:underline text-xs font-bold uppercase tracking-wider">Edit</button>
                            <button onclick="window.deleteAttendance(${row.id})" class="text-error hover:underline text-xs font-bold uppercase tracking-wider">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    };

    window.openCreateModal = function () {
        document.getElementById('crud-id').value = '';
        document.getElementById('crud-student-id').value = '';
        document.getElementById('crud-attendance-date').value = '';
        document.getElementById('crud-status').value = '';
        window.openModal('crud-modal');
    };

    window.openEditModal = async function (id) {
        const json = await apiFetch(`/attendance/${id}`);
        const item = json.data || json;

        document.getElementById('crud-id').value = item.id;
        document.getElementById('crud-student-id').value = item.student_id || '';
        document.getElementById('crud-attendance-date').value = item.attendance_date || '';
        document.getElementById('crud-status').value = item.status || '';
        window.openModal('crud-modal');
    };

    window.saveAttendance = async function (e) {
        e.preventDefault();
        const id = document.getElementById('crud-id').value;
        const payload = {
            student_id: document.getElementById('crud-student-id').value,
            attendance_date: document.getElementById('crud-attendance-date').value,
            status: document.getElementById('crud-status').value,
        };

        if (id) {
            await apiFetch(`/attendance/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
        } else {
            await apiFetch('/attendance', { method: 'POST', body: JSON.stringify(payload) });
        }

        window.closeModal('crud-modal');
        window.loadAttendance();
    };

    window.deleteAttendance = async function (id) {
        if (!confirm('Are you sure you want to delete this record?')) return;
        await apiFetch(`/attendance/${id}`, { method: 'DELETE' });
        window.loadAttendance();
    };

    window.openBulkModal = async function () {
        const list = document.getElementById('bulk-students-list');
        list.innerHTML = students.map(s => `
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" value="${s.id}" class="bulk-student-checkbox rounded border-outline-variant">
                <span class="text-sm text-on-surface">${s.name || s.user?.name || ''}</span>
            </label>
        `).join('');

        document.getElementById('bulk-attendance-date').value = '';
        document.getElementById('bulk-status').value = '';
        window.openModal('bulk-modal');
    };

    window.saveBulkAttendance = async function (e) {
        e.preventDefault();
        const checkboxes = document.querySelectorAll('.bulk-student-checkbox:checked');
        const studentIds = Array.from(checkboxes).map(cb => cb.value);

        if (studentIds.length === 0) {
            alert('Please select at least one student.');
            return;
        }

        const payload = {
            student_ids: studentIds,
            attendance_date: document.getElementById('bulk-attendance-date').value,
            status: document.getElementById('bulk-status').value,
        };

        await apiFetch('/attendance/bulk', { method: 'POST', body: JSON.stringify(payload) });
        window.closeModal('bulk-modal');
        window.loadAttendance();
    };

    // Initialize
    await loadStudents();
    window.loadAttendance();
</script>
@endpush
