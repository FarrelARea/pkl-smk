@extends('layouts.app')

@section('title', 'Riwayat Kehadiran')

@section('content')
<div class="space-y-6">
    {{-- Judul --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Kehadiran</h1>
            <x-help-button title="Panduan Riwayat Kehadiran">
                <p>Di halaman ini kamu bisa melihat riwayat kehadiran selama magang.</p>
                <ul class="list-disc pl-4 mt-2 space-y-1">
                    <li>Lihat ringkasan total hadir, sakit, izin, dan tidak hadir</li>
                    <li>Cek riwayat clock in/out lengkap dengan foto dan lokasi</li>
                    <li>Pantau kalender kehadiran bulanan dengan status per hari</li>
                </ul>
            </x-help-button>
        <a href="/dashboard" class="text-blue-600 hover:text-blue-700">← Kembali ke Dashboard</a>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
            <p class="text-xs text-gray-500 uppercase">Total Hari Hadir</p>
            <p class="text-3xl font-bold text-blue-600" id="stat-total">0</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
            <p class="text-xs text-gray-500 uppercase">Sakit</p>
            <p class="text-3xl font-bold text-yellow-500" id="stat-sick">0</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
            <p class="text-xs text-gray-500 uppercase">Izin</p>
            <p class="text-3xl font-bold text-blue-400" id="stat-permission">0</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
            <p class="text-xs text-gray-500 uppercase">Tidak Hadir</p>
            <p class="text-3xl font-bold text-red-500" id="stat-absent">0</p>
        </div>
    </div>

    {{-- Status Absensi (Sakit/Izin/Tidak Hadir) --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Status Kehadiran</h3>
            <p class="mt-1 text-sm text-gray-500">Menampilkan catatan status yang dicatat untuk hari tertentu seperti sakit, izin, tidak hadir, atau hadir.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody id="status-records-body">
                    <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel Riwayat Clock In/Out --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Riwayat Clock In/Out</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Foto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Jarak</th>
                    </tr>
                </thead>
                <tbody id="attendance-records-body">
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-lg">Kalender Kehadiran</h2>
                <p class="mt-1 text-sm text-gray-500">Preview kehadiran bulanan dari clock in dan status kehadiran yang tercatat.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="changeMonth(-1)" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-gray-600">chevron_left</span>
                </button>
                <span id="calendar-label" class="font-medium text-gray-700 w-36 text-center"></span>
                <button onclick="changeMonth(1)" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-gray-600">chevron_right</span>
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 mb-4 text-xs text-gray-600">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span> Tidak Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Sakit</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span> Izin</span>
        </div>

        <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-2">
            <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
        </div>
        <div id="calendar-grid" class="grid grid-cols-7 gap-1">
            <div class="col-span-7 py-8 text-center text-gray-400">Memuat...</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    let currentMonth;
    let currentYear;
    let attendanceData = [];
    let clockRecords = [];
    let attendanceRecords = [];

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;

        const now = new Date();
        currentMonth = now.getMonth() + 1;
        currentYear = now.getFullYear();

        await loadAttendance();
    });

    async function loadAttendance() {
        try {
            const res = await Auth.apiFetch('/student/attendance');
            const data = await res.json();
            clockRecords = data.clock_records || [];
            attendanceRecords = data.attendance_records || [];
            attendanceData = buildCalendarAttendance(attendanceRecords);

            document.getElementById('stat-total').textContent = attendanceRecords.filter(r => r.status === 'present').length;
            document.getElementById('stat-sick').textContent = attendanceRecords.filter(r => r.status === 'sick').length;
            document.getElementById('stat-permission').textContent = attendanceRecords.filter(r => r.status === 'permission').length;
            document.getElementById('stat-absent').textContent = attendanceRecords.filter(r => r.status === 'absent').length;

            renderStatusTable(attendanceRecords);
            renderClockTable(clockRecords);
            renderCalendar();
        } catch (err) {
            console.error('Gagal memuat kehadiran:', err);
            document.getElementById('status-records-body').innerHTML = '<tr><td colspan="3" class="px-6 py-8 text-center text-red-600">Gagal memuat status kehadiran.</td></tr>';
            document.getElementById('attendance-records-body').innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-red-600">Gagal memuat riwayat clock in/out.</td></tr>';
            document.getElementById('calendar-grid').innerHTML = '<div class="col-span-7 py-8 text-center text-red-600">Gagal memuat kalender kehadiran.</div>';
        }
    }

    function buildCalendarAttendance(attendanceRecords) {
        return attendanceRecords
            .filter(record => {
                const [year, month] = record.attendance_date.split('-').map(Number);
                return month === currentMonth && year === currentYear;
            })
            .map(record => ({
                attendance_date: record.attendance_date,
                status: record.status,
                notes: record.notes || null,
            }))
            .sort((a, b) => a.attendance_date.localeCompare(b.attendance_date));
    }

    function renderStatusTable(records) {
        const tbody = document.getElementById('status-records-body');

        if (records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada data kehadiran yang tercatat.</td></tr>';
            return;
        }

        const badgeMap = {
            sick: '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Sakit</span>',
            permission: '<span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Izin</span>',
            absent: '<span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">Tidak Hadir</span>',
            present: '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Hadir</span>',
        };

        tbody.innerHTML = records.map(record => {
            const date = new Date(record.attendance_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            return `<tr class="border-b border-gray-100">
                <td class="px-6 py-4 text-sm">${date}</td>
                <td class="px-6 py-4 text-sm">${badgeMap[record.status] || record.status}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${record.notes || '—'}</td>
            </tr>`;
        }).join('');
    }

    function renderClockTable(records) {
        const tbody = document.getElementById('attendance-records-body');
        if (records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat clock in/out.</td></tr>';
            return;
        }

        tbody.innerHTML = records.map(record => {
            const date = new Date(record.created_at);
            const dateStr = date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
            const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            const typeBadge = record.type === 'clock_in'
                ? '<span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Masuk</span>'
                : '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Keluar</span>';

            const locationBadge = record.is_within_range
                ? '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Di Lokasi</span>'
                : '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Luar Lokasi</span>';

            const distance = record.distance_meters ? `${Math.round(record.distance_meters)} m` : '—';
            const photo = record.photo
                ? `<img src="/storage/${record.photo}" alt="Bukti" class="w-12 h-12 rounded-lg object-cover cursor-pointer hover:opacity-80 border border-gray-200" onclick="window.open('/storage/${record.photo}', '_blank')">`
                : '<span class="text-gray-300 text-xs">—</span>';

            return `<tr class="border-b border-gray-100">
                <td class="px-6 py-4 text-sm">${dateStr}</td>
                <td class="px-6 py-4 text-sm">${typeBadge}</td>
                <td class="px-6 py-4 text-sm font-mono text-gray-700">${timeStr}</td>
                <td class="px-6 py-4 text-sm">${photo}</td>
                <td class="px-6 py-4 text-sm">${locationBadge}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${distance}</td>
            </tr>`;
        }).join('');
    }

    function renderCalendar() {
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const statusLabelMap = {
            present: 'Hadir',
            absent: 'Tidak Hadir',
            sick: 'Sakit',
            permission: 'Izin',
        };

        document.getElementById('calendar-label').textContent = `${months[currentMonth - 1]} ${currentYear}`;

        const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

        const statusMap = {};
        for (const entry of attendanceData) {
            const day = parseInt(entry.attendance_date.substring(8, 10), 10);
            statusMap[day] = entry;
        }

        const colorMap = {
            present: 'bg-green-500 text-white',
            absent: 'bg-red-400 text-white',
            sick: 'bg-yellow-400 text-white',
            permission: 'bg-blue-400 text-white',
        };

        let html = '';
        for (let i = 0; i < firstDay; i++) {
            html += '<div></div>';
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const entry = statusMap[day];
            const color = entry ? colorMap[entry.status] : 'bg-gray-100 text-gray-400';
            const label = entry ? statusLabelMap[entry.status] || entry.status : 'Belum ada data';
            const note = entry?.notes ? ` — ${entry.notes}` : '';
            html += `<div class="aspect-square flex flex-col items-center justify-center rounded-lg text-[11px] font-medium ${color}" title="${label}${note}">
                <span>${day}</span>
                ${entry ? `<span class="mt-1 text-[9px] leading-none">${label}</span>` : ''}
            </div>`;
        }

        document.getElementById('calendar-grid').innerHTML = html;
    }

    window.changeMonth = async (delta) => {
        currentMonth += delta;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }

        attendanceData = buildCalendarAttendance(clockRecords, attendanceRecords);
        renderCalendar();
    };
</script>
@endpush
