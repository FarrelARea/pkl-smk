@extends('layouts.app')

@section('title', 'Daily Logs')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Daily Logs</h1>

    {{-- Filter Bar --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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
                <button onclick="loadData()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                    Apply
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <table class="w-full">
            <thead class="bg-surface-container-low">
                <tr>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Student</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Date</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Activities</th>
                    <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="logs-table-body">
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant text-sm">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Detail Modal --}}
@component('partials.modal', ['id' => 'detail-modal', 'title' => 'Daily Log Detail'])
    <div class="space-y-4">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Activities</label>
            <p id="detail-activities" class="text-sm text-on-surface"></p>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Reflection</label>
            <p id="detail-reflection" class="text-sm text-on-surface"></p>
        </div>
        <hr class="border-outline-variant/20">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Add Comment</label>
            <textarea id="comment-text" rows="3" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Write a comment..."></textarea>
            <div class="mt-3 flex justify-end">
                <button onclick="addComment()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                    Submit Comment
                </button>
            </div>
        </div>
    </div>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    let currentLogId = null;

    async function loadStudents() {
        try {
            const json = await Auth.apiFetch('/students');
            const students = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const select = document.getElementById('filter-student');
            students.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.name || student.user?.name || 'Unknown';
                select.appendChild(option);
            });
        } catch (e) {
            console.error('Failed to load students:', e);
        }
    }

    async function loadData() {
        const studentId = document.getElementById('filter-student').value;
        const startDate = document.getElementById('filter-start-date').value;
        const endDate = document.getElementById('filter-end-date').value;

        const params = new URLSearchParams();
        if (studentId) params.append('student_id', studentId);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        const tbody = document.getElementById('logs-table-body');
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-on-surface-variant text-sm">Loading...</td></tr>';

        try {
            const json = await Auth.apiFetch(`/daily-logs?${params.toString()}`);
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-on-surface-variant text-sm">No daily logs found.</td></tr>';
                return;
            }

            tbody.innerHTML = items.map(row => {
                const id = row.id;
                const studentName = row.student?.name || row.user?.name || 'Unknown';
                const date = row.date || '-';
                const activities = row.activities || '';
                const truncated = activities.length > 50 ? activities.substring(0, 50) + '...' : activities;

                return `<tr class="border-t border-outline-variant/10">
                    <td class="px-6 py-4 text-sm text-on-surface">${AdminUtils.escapeHtml(studentName)}</td>
                    <td class="px-6 py-4 text-sm text-on-surface">${AdminUtils.escapeHtml(date)}</td>
                    <td class="px-6 py-4 text-sm text-on-surface">${AdminUtils.escapeHtml(truncated)}</td>
                    <td class="px-6 py-4">
                        <button onclick="viewDetail(${id})" class="p-2 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-sm">visibility</span></button>
                    </td>
                </tr>`;
            }).join('');
        } catch (e) {
            console.error('Failed to load daily logs:', e);
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-red-500 text-sm">Failed to load data.</td></tr>';
        }
    }

    async function viewDetail(id) {
        currentLogId = id;
        try {
            const json = await Auth.apiFetch(`/daily-logs/${id}`);
            const log = json.data || json;

            document.getElementById('detail-activities').textContent = log.activities || '-';
            document.getElementById('detail-reflection').textContent = log.reflection || '-';
            document.getElementById('comment-text').value = '';

            AdminUtils.openModal('detail-modal');
        } catch (e) {
            console.error('Failed to load daily log detail:', e);
        }
    }

    async function addComment() {
        if (!currentLogId) return;

        const comment = document.getElementById('comment-text').value.trim();
        if (!comment) return;

        try {
            await Auth.apiFetch(`/daily-logs/${currentLogId}/comment`, {
                method: 'POST',
                body: JSON.stringify({ comment }),
            });

            document.getElementById('comment-text').value = '';
            AdminUtils.closeModal('detail-modal');
            loadData();
        } catch (e) {
            console.error('Failed to add comment:', e);
        }
    }

    window.loadData = loadData;
    window.viewDetail = viewDetail;
    window.addComment = addComment;

    loadStudents();
    loadData();
</script>
@endpush
