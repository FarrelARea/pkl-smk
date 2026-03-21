@extends('layouts.app')

@section('title', 'Riwayat Kehadiran')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Kehadiran</h1>
        <a href="/dashboard" class="text-blue-600 hover:text-blue-700">← Kembali ke Dashboard</a>
    </div>

    {{-- Summary Stats --}}
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

    {{-- Clock History Table --}}
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
</div>
@endsection

@push('scripts')
<script type="module">
    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await loadAttendance();
    });

    async function loadAttendance() {
        try {
            const res = await Auth.apiFetch('/student/attendance');
            const data = await res.json();
            const clocks = data.clock_records || [];
            const attendanceRecords = data.attendance_records || [];

            // Stats from clock records
            const uniqueDays = new Set(clocks.filter(r => r.type === 'clock_in').map(r => new Date(r.created_at).toDateString()));
            document.getElementById('stat-total').textContent = uniqueDays.size;
            document.getElementById('stat-sick').textContent = attendanceRecords.filter(r => r.status === 'sick').length;
            document.getElementById('stat-permission').textContent = attendanceRecords.filter(r => r.status === 'permission').length;
            document.getElementById('stat-absent').textContent = attendanceRecords.filter(r => r.status === 'absent').length;

            console.log('attendance_records:', attendanceRecords);
            console.log('clock_records:', clocks);
            renderStatusTable(attendanceRecords);
            renderClockTable(clocks);
        } catch (err) {
            console.error('Failed to load attendance:', err);
        }
    }

    function renderStatusTable(records) {
        const tbody = document.getElementById('status-records-body');
        const filtered = records;

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada data kehadiran.</td></tr>';
            return;
        }

        const badgeMap = {
            sick:       '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Sakit</span>',
            permission: '<span class="px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Izin</span>',
            absent:     '<span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">Tidak Hadir</span>',
            present:    '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Hadir</span>',
        };

        tbody.innerHTML = filtered.map(r => {
            const date = new Date(r.attendance_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            return `<tr class="border-b border-gray-100">
                <td class="px-6 py-4 text-sm">${date}</td>
                <td class="px-6 py-4 text-sm">${badgeMap[r.status] || r.status}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${r.notes || '—'}</td>
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
</script>
@endpush
