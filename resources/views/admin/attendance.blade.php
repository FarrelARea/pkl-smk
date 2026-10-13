@extends('layouts.app')

@section('title', 'Manajemen Kehadiran')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-[26px] font-extrabold text-on-surface tracking-tight mb-2 font-headline">Manajemen Kehadiran</h1>
        <x-help-button title="Panduan Manajemen Kehadiran">
            <p>Di halaman ini kamu bisa melihat dan mengelola kehadiran siswa.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Lihat kehadiran siswa berdasarkan tanggal (termasuk clock-in via aplikasi)</li>
                <li>Catat kehadiran manual satu per satu atau massal</li>
                <li>Gunakan tombol panah untuk navigasi tanggal</li>
            </ul>
        </x-help-button>
        <div class="flex items-center gap-3">
            <button onclick="window.openBulkModal()" class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider">
                Catat Massal
            </button>
            <button onclick="window.openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                Catat Kehadiran
            </button>
        </div>
    </div>

    {{-- Date Navigation --}}
    <div class="flex items-center gap-4">
        <button onclick="changeDate(-1)" class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-on-surface">chevron_left</span>
        </button>
        <input type="date" id="attendance-date" class="px-4 py-1.5 h-9 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent font-medium">
        <button onclick="changeDate(1)" class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-on-surface">chevron_right</span>
        </button>
        <button onclick="goToToday()" class="px-4 py-1.5 h-9 bg-primary-container text-on-primary-container rounded-lg text-xs font-bold uppercase tracking-wider hover:opacity-90 transition-opacity">
            Hari Ini
        </button>
    </div>

    {{-- Summary Badge --}}
    <div id="summary-badge" class="rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-3 text-sm text-emerald-800 hidden">
        <span id="summary-text"></span>
    </div>

    {{-- Attendance List --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Siswa</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Status</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Clock In</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Clock Out</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-left">Keterangan</th>
                        <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="attendance-table-body">
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-on-surface-variant text-sm">Memuat...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Record Attendance Modal --}}
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Kehadiran'])
    <form id="crud-form" onsubmit="window.saveAttendance(event)" class="space-y-4">
        <input type="hidden" id="crud-id">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Siswa</label>
            <select id="crud-student-id" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Pilih Siswa</option>
            </select>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Tanggal Kehadiran</label>
            <input type="date" id="crud-attendance-date" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Magang</label>
            <select id="crud-internship-id" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Pilih Magang</option>
            </select>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Status</label>
            <select id="crud-status" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Pilih Status</option>
                <option value="present">Hadir</option>
                <option value="absent">Tidak Hadir</option>
                <option value="sick">Sakit</option>
                <option value="permission">Izin</option>
            </select>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="window.closeModal('crud-modal')" class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider">
                Batal
            </button>
            <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                Simpan
            </button>
        </div>
    </form>
@endcomponent

{{-- Edit Clock Times Modal --}}
@component('partials.modal', ['id' => 'clock-modal', 'title' => 'Detail Kehadiran'])
    <form id="clock-form" onsubmit="window.saveClockAttendance(event)" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <p id="clock-student-name" class="font-bold text-on-surface"></p>
                <p id="clock-attendance-date-label" class="text-xs text-on-surface-variant"></p>
            </div>
            <div>
                <label for="clock-attendance-status" class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Status</label>
                <select id="clock-attendance-status" class="px-3 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="present">Hadir</option>
                    <option value="absent">Alpa</option>
                    <option value="sick">Sakit</option>
                    <option value="permission">Izin</option>
                </select>
            </div>
        </div>
        <p id="clock-status-note" class="hidden rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800"></p>
        <div id="clock-records" class="space-y-3 max-h-80 overflow-y-auto"></div>
        <p id="clock-empty" class="hidden text-sm text-on-surface-variant">Belum ada catatan clock untuk tanggal ini.</p>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="window.closeModal('clock-modal')" class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider">Batal</button>
            <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">Simpan Perubahan</button>
        </div>
    </form>
@endcomponent

{{-- Bulk Record Modal --}}
@component('partials.modal', ['id' => 'bulk-modal', 'title' => 'Catat Kehadiran Massal'])
    <form id="bulk-form" onsubmit="window.saveBulkAttendance(event)" class="space-y-4">
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Siswa</label>
            <div id="bulk-students-list" class="max-h-48 overflow-y-auto space-y-2 p-3 bg-surface-container-low border border-outline-variant/20 rounded-lg">
                <p class="text-sm text-on-surface-variant">Memuat data siswa...</p>
            </div>
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Tanggal Kehadiran</label>
            <input type="date" id="bulk-attendance-date" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>
        <div>
            <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Status</label>
            <select id="bulk-status" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">Pilih Status</option>
                <option value="present">Hadir</option>
                <option value="absent">Tidak Hadir</option>
                <option value="sick">Sakit</option>
                <option value="permission">Izin</option>
            </select>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="window.closeModal('bulk-modal')" class="px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-md font-bold text-xs uppercase tracking-wider">
                Batal
            </button>
            <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider">
                Simpan
            </button>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { apiFetch } = Auth;
    const { showToast } = AdminUtils;
    let students = [];
    let currentDate = new Date().toISOString().slice(0, 10);

    function currentDateInput() {
        return currentDate;
    }

    async function loadStudents() {
        const res = await apiFetch('/students');
        const json = await res.json();
        students = Array.isArray(json.data) ? json.data : (json.data?.data || []);

        const crudSelect = document.getElementById('crud-student-id');
        const options = students.map(s => `<option value="${s.id}">${s.name || s.user?.name || ''}</option>`).join('');
        crudSelect.innerHTML = `<option value="">Pilih Siswa</option>` + options;

        crudSelect.addEventListener('change', async function () {
            const internshipSelect = document.getElementById('crud-internship-id');
            internshipSelect.innerHTML = '<option value="">Memuat magang...</option>';
            if (!this.value) {
                internshipSelect.innerHTML = '<option value="">Pilih Magang</option>';
                return;
            }
            const response = await apiFetch(`/students/${this.value}/internship-history`);
            const internships = await response.json();
            internshipSelect.innerHTML = '<option value="">Pilih Magang</option>' + internships.map(item =>
                `<option value="${item.id}">${item.company?.name || 'Magang'} (${item.start_date} - ${item.end_date})</option>`
            ).join('');
        });

        const list = document.getElementById('bulk-students-list');
        list.innerHTML = students.map(s => `
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" value="${s.id}" class="bulk-student-checkbox rounded border-outline-variant">
                <span class="text-sm text-on-surface">${s.name || s.user?.name || ''}</span>
            </label>
        `).join('');
    }

    async function loadAttendance() {
        const res = await apiFetch(`/admin/panel/attendance-calendar?date=${encodeURIComponent(currentDate)}`);
        const data = await res.json();
        const attendance = data.attendance || [];

        document.getElementById('summary-badge').classList.remove('hidden');
        document.getElementById('summary-text').textContent = `${data.present_count} siswa hadir dari ${data.tracked_count} data kehadiran yang tercatat.`;

        const tbody = document.getElementById('attendance-table-body');

        if (attendance.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-on-surface-variant text-sm">Belum ada data kehadiran pada tanggal ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = attendance.map(item => {
            const clocks = item.all_clocks || [];
            const clockIn = item.clock_in ? new Date(item.clock_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '—';
            const clockOut = item.clock_out ? new Date(item.clock_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '—';
            const workHours = item.clock_in && item.clock_out ? calcWorkHours(item.clock_in, item.clock_out) : '';
            const clockInId = item.clock_in_id;
            const clockOutId = item.clock_out_id;

            let clockInfo = '';
            if (item.clock_in) {
                const withinRange = clocks.find(clock => clock.type === 'clock_in')?.within_range;
                clockInfo = `<span class="text-xs ${withinRange ? 'text-emerald-600' : 'text-amber-600'}">${withinRange ? 'di area' : 'luar area'}</span>`;
            }

            return `
                <tr class="border-t border-outline-variant/10 hover:bg-surface-container/30 transition-colors">
                    <td class="px-6 py-4 text-sm text-on-surface font-medium">${item.student_name || '-'}</td>
                    <td class="px-6 py-4">${attendanceBadge(item.status)}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">
                        ${clockIn !== '—' ? `<div>${clockIn}</div>${clockInfo}` : '—'}
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">${clockOut}${workHours ? `<div class="text-xs text-on-surface-variant/60">${workHours}</div>` : ''}</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">${item.notes || '—'}</td>
                    <td class="px-6 py-4 text-right">
                        <button type="button" onclick='window.openClockModal(${JSON.stringify(item)})' class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-bold text-primary hover:bg-primary-container/30 transition-colors" title="Lihat dan edit detail kehadiran">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                            Edit
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function attendanceBadge(status) {
        const map = {
            present: 'bg-emerald-100 text-emerald-700',
            sick: 'bg-red-100 text-red-700',
            permission: 'bg-amber-100 text-amber-700',
            absent: 'bg-gray-200 text-gray-700',
        };
        const label = {
            present: 'Hadir',
            sick: 'Sakit',
            permission: 'Izin',
            absent: 'Alpa',
        };
        return `<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold ${map[status] || 'bg-gray-100 text-gray-600'}">${label[status] || status}</span>`;
    }

    function calcWorkHours(clockIn, clockOut) {
        const diff = new Date(clockOut) - new Date(clockIn);
        const hours = Math.floor(diff / 3600000);
        const mins = Math.round((diff % 3600000) / 60000);
        return `${hours}j ${mins}m`;
    }

    window.changeDate = function (offset) {
        const d = new Date(currentDate);
        d.setDate(d.getDate() + offset);
        currentDate = d.toISOString().slice(0, 10);
        document.getElementById('attendance-date').value = currentDate;
        loadAttendance();
    };

    window.goToToday = function () {
        currentDate = new Date().toISOString().slice(0, 10);
        document.getElementById('attendance-date').value = currentDate;
        loadAttendance();
    };

    document.getElementById('attendance-date').addEventListener('change', function () {
        currentDate = this.value;
        loadAttendance();
    });

    window.openCreateModal = function () {
        document.getElementById('crud-id').value = '';
        document.getElementById('crud-student-id').value = '';
        document.getElementById('crud-attendance-date').value = currentDate;
        document.getElementById('crud-internship-id').innerHTML = '<option value="">Pilih Magang</option>';
        document.getElementById('crud-status').value = '';
        window.openModal('crud-modal');
    };

    function timeInputValue(value) {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return String(value).slice(0, 5);
        return date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    }

    function clockRecordHtml(clock, index) {
        const type = clock.type === 'clock_out' ? 'clock_out' : 'clock_in';
        const label = type === 'clock_in' ? 'Clock In' : 'Clock Out';
        const photo = clock.photo_url || clock.photo || clock.image_url;
        return `<div class="rounded-lg border border-outline-variant/20 p-3" data-clock-record data-clock-id="${clock.id}" data-original-time="${timeInputValue(clock.time)}" data-original-range="${clock.within_range}" data-original-type="${type}">
            <div class="flex items-center justify-between gap-3 mb-2"><span data-clock-label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">${label} #${index + 1}</span>${photo ? `<a href="${photo}" target="_blank" rel="noopener" class="text-xs font-semibold text-primary hover:underline">Lihat foto</a>` : ''}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3"><input type="time" data-clock-time value="${timeInputValue(clock.time)}" required class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"><select data-clock-type aria-label="Jenis clock" class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"><option value="clock_in" ${type === 'clock_in' ? 'selected' : ''}>Clock In</option><option value="clock_out" ${type === 'clock_out' ? 'selected' : ''}>Clock Out</option></select><select data-clock-range class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"><option value="true" ${clock.within_range ? 'selected' : ''}>Di area</option><option value="false" ${!clock.within_range ? 'selected' : ''}>Di luar area</option></select></div>
        </div>`;
    }

    window.openClockModal = function (attendance) {
        const clocks = attendance?.all_clocks || [];
        document.getElementById('clock-student-name').textContent = attendance?.student_name || 'Siswa';
        document.getElementById('clock-attendance-date-label').textContent = attendance?.attendance_date || currentDate;
        document.getElementById('clock-attendance-status').value = attendance?.status || 'present';
        document.getElementById('clock-records').innerHTML = clocks.map(clockRecordHtml).join('');
        document.getElementById('clock-empty').classList.toggle('hidden', clocks.length > 0);
        const status = document.getElementById('clock-attendance-status');
        status.disabled = !attendance?.internship_id;
        const note = document.getElementById('clock-status-note');
        note.classList.toggle('hidden', Boolean(attendance?.internship_id));
        note.textContent = 'Status tidak dapat disimpan: internship_id tidak tersedia.';
        document.getElementById('clock-form').dataset.studentId = attendance?.student_id || '';
        document.getElementById('clock-form').dataset.internshipId = attendance?.internship_id || '';
        document.getElementById('clock-form').dataset.attendanceDate = attendance?.attendance_date || currentDate;
        document.getElementById('clock-form').dataset.originalStatus = attendance?.status || 'present';
        document.getElementById('clock-form').dataset.attendanceId = attendance?.attendance_id || '';
        window.openModal('clock-modal');
    };

    document.getElementById('clock-records').addEventListener('change', function (event) {
        if (!event.target.matches('[data-clock-type]')) return;
        const record = event.target.closest('[data-clock-record]');
        record.querySelector('[data-clock-label]').textContent = `${event.target.value === 'clock_in' ? 'Clock In' : 'Clock Out'} #${Array.from(document.querySelectorAll('[data-clock-record]')).indexOf(record) + 1}`;
    });

    window.saveClockAttendance = async function (e) {
        e.preventDefault();
        const updates = Array.from(document.querySelectorAll('[data-clock-record]')).filter(record => {
            return record.querySelector('[data-clock-time]').value !== record.dataset.originalTime
                || record.querySelector('[data-clock-range]').value !== record.dataset.originalRange
                || record.querySelector('[data-clock-type]').value !== record.dataset.originalType;
        });

        try {
            for (const record of updates) {
                const time = record.querySelector('[data-clock-time]').value;
                const response = await apiFetch(`/admin/clock-in-out/${encodeURIComponent(record.dataset.clockId)}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        created_at: `${currentDate}T${time}:00`,
                        is_within_range: record.querySelector('[data-clock-range]').value === 'true',
                        type: record.querySelector('[data-clock-type]').value,
                    }),
                });
                if (!response.ok) {
                    const body = await response.json().catch(() => ({}));
                    throw new Error(body.error || body.message || 'Gagal memperbarui waktu kehadiran');
                }
            }
            const form = document.getElementById('clock-form');
            const status = document.getElementById('clock-attendance-status');
            if (status.value !== form.dataset.originalStatus) {
                if (!form.dataset.internshipId) throw new Error('Status tidak dapat disimpan: internship_id tidak tersedia');
                const response = await apiFetch('/attendance/status', { method: 'PUT', body: JSON.stringify({
                    student_id: form.dataset.studentId,
                    internship_id: form.dataset.internshipId,
                    attendance_date: form.dataset.attendanceDate,
                    status: status.value,
                }) });
                if (!response.ok) throw new Error('Gagal memperbarui status kehadiran');
            }
            window.closeModal('clock-modal');
            loadAttendance();
        } catch (error) {
            showToast(error.message || 'Gagal memperbarui waktu kehadiran', 'error');
        }
    };

    window.saveAttendance = async function (e) {
        e.preventDefault();
        const id = document.getElementById('crud-id').value;
        const payload = {
            student_id: document.getElementById('crud-student-id').value,
            internship_id: document.getElementById('crud-internship-id').value,
            attendance_date: document.getElementById('crud-attendance-date').value,
            status: document.getElementById('crud-status').value,
        };

        if (id) {
            await apiFetch(`/attendance/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
        } else {
            await apiFetch('/attendance', { method: 'POST', body: JSON.stringify(payload) });
        }

        window.closeModal('crud-modal');
        loadAttendance();
    };

    window.openBulkModal = function () {
        document.getElementById('bulk-attendance-date').value = currentDate;
        document.getElementById('bulk-status').value = '';
        window.openModal('bulk-modal');
    };

    window.saveBulkAttendance = async function (e) {
        e.preventDefault();
        const checkboxes = document.querySelectorAll('.bulk-student-checkbox:checked');
        const studentIds = Array.from(checkboxes).map(cb => cb.value);

        if (studentIds.length === 0) {
            alert('Pilih minimal satu siswa.');
            return;
        }

        const payload = {
            student_ids: studentIds,
            attendance_date: document.getElementById('bulk-attendance-date').value,
            status: document.getElementById('bulk-status').value,
        };

        await apiFetch('/attendance/bulk', { method: 'POST', body: JSON.stringify(payload) });
        window.closeModal('bulk-modal');
        loadAttendance();
    };

    // Initialize
    document.getElementById('attendance-date').value = currentDate;
    await loadStudents();
    loadAttendance();
</script>
@endpush
