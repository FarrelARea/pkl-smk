@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div id="dashboard-loading" class="flex items-center justify-center py-20">
    <div class="text-center">
        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="text-gray-500">Memuat dashboard...</p>
    </div>
</div>

<div id="dashboard-error" class="hidden py-20 text-center">
    <p class="text-red-600 mb-4" id="error-text">Gagal memuat data.</p>
    <button onclick="loadDashboard()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Coba Lagi</button>
</div>

<div id="dashboard-content" class="hidden">
    {{-- Admin Dashboard --}}
    <div id="dashboard-admin" class="hidden">
        @include('dashboard.admin')
    </div>

    {{-- Teacher Dashboard --}}
    <div id="dashboard-teacher" class="hidden">
        @include('dashboard.teacher')
    </div>

    {{-- Supervisor Dashboard --}}
    <div id="dashboard-supervisor" class="hidden">
        @include('dashboard.supervisor')
    </div>

    {{-- Student Dashboard --}}
    <div id="dashboard-student" class="hidden">
        @include('dashboard.student')
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    if (!Auth.requireAuth()) {
        // redirecting to login...
    } else {
        window.loadDashboard = loadDashboard;
        loadDashboard();
    }

    async function loadDashboard() {
        const loading = document.getElementById('dashboard-loading');
        const error = document.getElementById('dashboard-error');
        const content = document.getElementById('dashboard-content');

        loading.classList.remove('hidden');
        error.classList.add('hidden');
        content.classList.add('hidden');

        try {
            const res = await Auth.apiFetch('/auth/me');
            const data = await res.json();
            const user = data.data || data;

            // Store user ID globally for student features
            window.currentUserId = user.id;

            // Update user info in sidebar
            document.getElementById('user-name').textContent = user.name;
            const roleLabels = {
                school_admin: 'Admin Sekolah',
                teacher: 'Guru',
                student: 'Murid',
                company_supervisor: 'Supervisor'
            };
            document.getElementById('user-role').textContent = roleLabels[user.role] || user.role;

            // Show role-specific dashboard
            loading.classList.add('hidden');
            content.classList.remove('hidden');

            if (user.role === 'school_admin') {
                document.getElementById('dashboard-admin').classList.remove('hidden');
                await loadAdminDashboard();
            } else if (user.role === 'teacher') {
                document.getElementById('dashboard-teacher').classList.remove('hidden');
                await loadTeacherDashboard();
            } else if (user.role === 'company_supervisor') {
                document.getElementById('dashboard-supervisor').classList.remove('hidden');
                await loadSupervisorDashboard();
            } else if (user.role === 'student') {
                document.getElementById('dashboard-student').classList.remove('hidden');
                await loadStudentDashboard(user.id);
            }
        } catch (err) {
            if (err.message !== 'Unauthorized') {
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                document.getElementById('error-text').textContent = 'Gagal memuat data: ' + err.message;
            }
        }
    }

    async function loadAdminDashboard() {
        try {
            const [studentsRes, schoolsRes, companiesRes, internshipsRes] = await Promise.all([
                Auth.apiFetch('/students'),
                Auth.apiFetch('/schools'),
                Auth.apiFetch('/companies'),
                Auth.apiFetch('/internships'),
            ]);

            const students = await studentsRes.json();
            const schools = await schoolsRes.json();
            const companies = await companiesRes.json();
            const internships = await internshipsRes.json();

            const getData = (d) => Array.isArray(d.data) ? d.data : (d.data?.data || []);

            document.getElementById('stat-students').textContent = getData(students).length;
            document.getElementById('stat-schools').textContent = getData(schools).length;
            document.getElementById('stat-companies').textContent = getData(companies).length;

            const activeInternships = getData(internships).filter(i => i.status === 'active');
            document.getElementById('stat-internships').textContent = activeInternships.length;
        } catch (err) {
            console.error('Admin dashboard error:', err);
        }
    }

    async function loadTeacherDashboard() {
        try {
            const res = await Auth.apiFetch('/my-students');
            const data = await res.json();
            const students = Array.isArray(data.data) ? data.data : (data.data?.data || []);

            const tbody = document.getElementById('teacher-students-body');
            tbody.innerHTML = '';

            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada siswa bimbingan</td></tr>';
                return;
            }

            for (const student of students) {
                tbody.innerHTML += `<tr class="border-b border-gray-100">
                    <td class="px-4 py-3 text-sm">${student.name}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${student.email}</td>
                    <td class="px-4 py-3 text-sm"><span class="px-2 py-1 rounded-full text-xs ${student.internship ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">${student.internship ? 'Magang Aktif' : 'Belum Magang'}</span></td>
                </tr>`;
            }
        } catch (err) {
            console.error('Teacher dashboard error:', err);
        }
    }

    async function loadSupervisorDashboard() {
        try {
            const res = await Auth.apiFetch('/my-students');
            const data = await res.json();
            const students = Array.isArray(data.data) ? data.data : (data.data?.data || []);

            const tbody = document.getElementById('supervisor-students-body');
            tbody.innerHTML = '';

            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada siswa magang</td></tr>';
                return;
            }

            for (const student of students) {
                tbody.innerHTML += `<tr class="border-b border-gray-100">
                    <td class="px-4 py-3 text-sm">${student.name}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${student.email}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">—</td>
                </tr>`;
            }
        } catch (err) {
            console.error('Supervisor dashboard error:', err);
        }
    }

    async function loadStudentDashboard(studentId) {
        try {
            const statusRes = await Auth.apiFetch(`/students/${studentId}/internship-status`);
            const statusData = await statusRes.json();
            const status = statusData.data;

            // Store internship ID globally before making dependent calls
            window.activeInternshipId = status?.id || null;

            const [logsRes, clockRes, attendanceRes] = await Promise.all([
                Auth.apiFetch(`/daily-logs?student_id=${window.currentUserId}`),
                window.activeInternshipId ? Auth.apiFetch(`/clock-in-out/${window.activeInternshipId}/today-status`).catch(() => ({ ok: false })) : Promise.resolve({ ok: false }),
                Auth.apiFetch(`/clock-in-out?user_id=${window.currentUserId}`).catch(() => ({ ok: false })),
            ]);

            const logsData = await logsRes.json();
            const infoEl = document.getElementById('student-internship-info');

            if (status && status.company) {
                // Calculate progress bar
                const startDate = new Date(status.start_date);
                const endDate = new Date(status.end_date);
                const now = new Date();

                const totalMs = endDate - startDate;
                const elapsedMs = now - startDate;
                const progressPercent = Math.max(0, Math.min(100, (elapsedMs / totalMs) * 100));

                const daysElapsed = Math.floor((now - startDate) / (1000 * 60 * 60 * 24));
                const daysRemaining = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));
                const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

                const formatDate = (date) => new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });

                infoEl.innerHTML = `
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Perusahaan</p>
                            <p class="font-semibold text-gray-900">${status.company.name || '—'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Supervisor</p>
                            <p class="font-semibold text-gray-900">${status.supervisor?.name || '—'}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Progress Magang</span>
                            <span class="text-2xl font-bold text-blue-600">${Math.round(progressPercent)}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-blue-600 h-full rounded-full transition-all duration-300" style="width: ${progressPercent}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span>${formatDate(status.start_date)}</span>
                            <span>${daysRemaining > 0 ? daysRemaining + ' hari tersisa' : 'Selesai'}</span>
                            <span>${formatDate(status.end_date)}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Hari Berjalan</p>
                            <p class="text-xl font-bold text-gray-900">${daysElapsed}</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Total Hari</p>
                            <p class="text-xl font-bold text-gray-900">${totalDays}</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 inline-block">${status.status || 'Aktif'}</p>
                        </div>
                    </div>
                `;
            } else {
                infoEl.innerHTML = '<p class="text-gray-500">Belum ada magang aktif</p>';
            }

            // Load clock status if available
            if (clockRes.ok) {
                const clockData = await clockRes.json();
                const clockStatusDiv = document.getElementById('today-clock-status');
                const clockContent = document.getElementById('clock-status-content');

                const formatTime = (dt) => dt ? new Date(dt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '—';
                const todayRecords = clockData.today_records || [];

                if (todayRecords.length > 0 || clockData.is_currently_in) {
                    clockStatusDiv.classList.remove('hidden');

                    let html = '';

                    // Show "currently in" status if applicable
                    if (clockData.is_currently_in && clockData.last_clock_in) {
                        const inTime = formatTime(clockData.last_clock_in.created_at);
                        const inDate = new Date(clockData.last_clock_in.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                        html += `<div class="flex items-center gap-2 p-3 bg-green-50 rounded-lg border border-green-200 mb-3">
                            <span class="material-symbols-outlined text-green-600 animate-pulse">radio_button_checked</span>
                            <span class="text-green-800 font-semibold">Sedang Masuk sejak ${inDate} ${inTime}</span>
                        </div>`;
                    }

                    // Show today's records
                    if (todayRecords.length > 0) {
                        html += '<div class="space-y-2">';
                        for (const rec of todayRecords) {
                            const time = formatTime(rec.created_at);
                            const isIn = rec.type === 'clock_in';
                            const icon = isIn ? 'login' : 'logout';
                            const label = isIn ? 'Masuk' : 'Keluar';
                            const color = isIn ? 'blue' : 'green';
                            html += `<div class="flex items-center justify-between py-1">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-${color}-500 text-sm">${icon}</span>
                                    <span class="text-sm text-gray-700">${label}</span>
                                </div>
                                <span class="text-sm font-mono font-semibold text-gray-900">${time}</span>
                            </div>`;
                        }
                        html += '</div>';
                    }

                    clockContent.innerHTML = html;
                }
            }

            const logs = Array.isArray(logsData.data) ? logsData.data : (logsData.data?.data || []);
            const tbody = document.getElementById('student-logs-body');
            tbody.innerHTML = '';

            const recentLogs = logs.slice(0, 5);
            if (recentLogs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada daily log</td></tr>';
            } else {
                for (const log of recentLogs) {
                    const logDate = log.log_date || log.date || '—';
                    const logActivity = log.activities || log.context || '—';
                    const derivedStatus = log.teacher_comment ? 'Direview' : 'Menunggu';
                    const statusClass = log.teacher_comment ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
                    tbody.innerHTML += `<tr class="border-b border-gray-100">
                        <td class="px-4 py-3 text-sm">${logDate}</td>
                        <td class="px-4 py-3 text-sm">${logActivity}</td>
                        <td class="px-4 py-3 text-sm"><span class="px-2 py-1 rounded-full text-xs ${statusClass}">${derivedStatus}</span></td>
                    </tr>`;
                }
            }

            // Load attendance summary from clock-in/out records
            if (attendanceRes.ok) {
                const attendanceData = await attendanceRes.json();
                const records = Array.isArray(attendanceData.data) ? attendanceData.data : (attendanceData.data?.data || []);

                if (records.length > 0) {
                    const uniqueDays = new Set(records.map(r => new Date(r.created_at).toDateString()));
                    const inRange = records.filter(r => r.is_within_range).length;
                    const outRange = records.filter(r => !r.is_within_range).length;

                    document.getElementById('attendance-days').textContent = uniqueDays.size;
                    document.getElementById('attendance-in-range').textContent = inRange;
                    document.getElementById('attendance-out-range').textContent = outRange;
                    document.getElementById('attendance-summary-widget').classList.remove('hidden');
                }
            }
        } catch (err) {
            console.error('Student dashboard error:', err);
        }
    }

    // Clock in/out functions
    let currentLocation = null;
    let currentClockType = null;
    let cameraStream = null;
    let capturedPhotoBlob = null;

    window.showClockModal = function() {
        currentLocation = null;
        capturedPhotoBlob = null;

        // Reset modal visibility
        document.getElementById('clock-modal').classList.remove('hidden');

        // Reset location UI completely
        const locStatus = document.getElementById('location-status');
        locStatus.classList.add('hidden');
        locStatus.classList.remove('border-solid', 'border-green-200', 'bg-green-50');
        locStatus.classList.add('border-dashed', 'border-gray-200');
        const locIcon = document.getElementById('loc-icon');
        locIcon.textContent = 'my_location';
        locIcon.classList.remove('text-green-500');
        locIcon.classList.add('text-blue-500', 'animate-pulse');
        document.getElementById('location-text').textContent = 'Mendapatkan lokasi...';

        document.getElementById('location-error').classList.add('hidden');
        document.getElementById('manual-coords').classList.add('hidden');

        // Reset camera UI
        document.getElementById('photo-preview').classList.add('hidden');
        document.getElementById('retake-btn').classList.add('hidden');
        document.getElementById('capture-btn').classList.remove('hidden');
        document.getElementById('camera-overlay').classList.remove('hidden');
        document.getElementById('camera-overlay').innerHTML = `
            <span class="material-symbols-outlined text-5xl mb-2">photo_camera</span>
            <p class="text-sm">Menghubungkan kamera...</p>
        `;

        // Reset feedback and buttons
        document.getElementById('clock-feedback').classList.add('hidden');
        document.getElementById('clock-feedback').innerHTML = '';
        const inBtn = document.getElementById('clock-in-submit');
        const outBtn = document.getElementById('clock-out-submit');
        inBtn.classList.remove('hidden');
        outBtn.classList.remove('hidden');
        inBtn.disabled = false;
        outBtn.disabled = false;
        inBtn.innerHTML = '<span class="material-symbols-outlined">login</span> Masuk';
        outBtn.innerHTML = '<span class="material-symbols-outlined">logout</span> Keluar';

        // Update datetime
        const now = new Date();
        document.getElementById('clock-datetime').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' — ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        acquireLocation();
        startCamera();
    };

    window.closeClockModal = function() {
        document.getElementById('clock-modal').classList.add('hidden');
        stopCamera();
        currentLocation = null;
        currentClockType = null;
        capturedPhotoBlob = null;
    };

    async function startCamera() {
        const video = document.getElementById('camera-preview');
        const overlay = document.getElementById('camera-overlay');

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = cameraStream;
            overlay.classList.add('hidden');
        } catch (err) {
            overlay.innerHTML = `
                <span class="material-symbols-outlined text-4xl mb-2 text-red-400">no_photography</span>
                <p class="text-sm text-gray-300">Kamera tidak tersedia</p>
                <p class="text-xs text-gray-500 mt-1">${err.message}</p>
            `;
        }
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(t => t.stop());
            cameraStream = null;
        }
        const video = document.getElementById('camera-preview');
        if (video) video.srcObject = null;
    }

    window.capturePhoto = function() {
        const video = document.getElementById('camera-preview');
        const canvas = document.getElementById('camera-canvas');
        const preview = document.getElementById('photo-preview');

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        canvas.getContext('2d').drawImage(video, 0, 0);

        // Convert to blob
        canvas.toBlob((blob) => {
            capturedPhotoBlob = blob;
            preview.src = URL.createObjectURL(blob);
            preview.classList.remove('hidden');
            document.getElementById('retake-btn').classList.remove('hidden');
            document.getElementById('capture-btn').classList.add('hidden');
            stopCamera();
        }, 'image/jpeg', 0.85);
    };

    window.retakePhoto = function() {
        capturedPhotoBlob = null;
        document.getElementById('photo-preview').classList.add('hidden');
        document.getElementById('retake-btn').classList.add('hidden');
        document.getElementById('capture-btn').classList.remove('hidden');
        startCamera();
    };

    function acquireLocation() {
        const locationStatus = document.getElementById('location-status');
        const locationError = document.getElementById('location-error');
        const manualCoords = document.getElementById('manual-coords');
        const locIcon = document.getElementById('loc-icon');

        locationStatus.classList.remove('hidden');
        locationError.classList.add('hidden');
        manualCoords.classList.add('hidden');

        if (!navigator.geolocation) {
            showLocationError('Perangkat tidak mendukung geolokasi');
            manualCoords.classList.remove('hidden');
            return;
        }

        const timeoutId = setTimeout(() => {
            showLocationError('Waktu tunggu GPS habis. Masukkan koordinat manual.');
            manualCoords.classList.remove('hidden');
        }, 5000);

        navigator.geolocation.getCurrentPosition(
            (position) => {
                clearTimeout(timeoutId);
                currentLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };
                locIcon.textContent = 'check_circle';
                locIcon.classList.remove('animate-pulse', 'text-blue-500');
                locIcon.classList.add('text-green-500');
                document.getElementById('location-text').textContent = `Lokasi diperoleh (±${Math.round(position.coords.accuracy)}m)`;
                document.getElementById('location-status').classList.remove('border-dashed', 'border-gray-200');
                document.getElementById('location-status').classList.add('border-solid', 'border-green-200', 'bg-green-50');
                manualCoords.classList.add('hidden');
            },
            (error) => {
                clearTimeout(timeoutId);
                let errorMsg = 'Gagal mendapatkan lokasi';
                if (error.code === 1) errorMsg = 'Izin geolokasi ditolak';
                else if (error.code === 2) errorMsg = 'Informasi lokasi tidak tersedia';
                showLocationError(errorMsg);
                manualCoords.classList.remove('hidden');
            },
            { timeout: 5000, enableHighAccuracy: true }
        );
    }

    function showLocationError(message) {
        document.getElementById('location-error').classList.remove('hidden');
        document.getElementById('error-text').textContent = message;
        document.getElementById('location-status').classList.add('hidden');
    }

    // STUDENT DAILY LOGS FUNCTIONS
    window.loadStudentDailyLogs = async function() {
        try {
            const res = await Auth.apiFetch(`/daily-logs?student_id=${window.currentUserId}`);
            const data = await res.json();
            const logs = Array.isArray(data.data) ? data.data : (data.data?.data || []);

            const tbody = document.getElementById('daily-logs-body');
            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada daily log. Buat yang pertama sekarang!</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            logs.sort((a, b) => new Date(b.log_date || b.date) - new Date(a.log_date || a.date));

            for (const log of logs) {
                const date = new Date(log.log_date || log.date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                const activityText = log.activities || log.context || '';
                const activity = activityText.substring(0, 100) + (activityText.length > 100 ? '...' : '');
                const derivedStatus = log.teacher_comment ? 'reviewed' : 'pending';

                const statusBadges = {
                    pending: '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Menunggu</span>',
                    reviewed: '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Direview</span>'
                };

                const location = log.latitude && log.longitude ? (log.location_verified ? '✓ Di lokasi' : '⚠ Luar lokasi') : '—';
                const canEdit = !log.teacher_comment;

                tbody.innerHTML += `<tr class="border-b border-gray-100">
                    <td class="px-6 py-4 text-sm">${date}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${activity}</td>
                    <td class="px-6 py-4 text-sm">${statusBadges[derivedStatus]}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${location}</td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <button onclick="viewLogDetail(${log.id})" class="text-blue-600 hover:text-blue-700">Lihat</button>
                        ${canEdit ? `<button onclick="editLog(${log.id})" class="text-blue-600 hover:text-blue-700">Edit</button>` : ''}
                        ${canEdit ? `<button onclick="deleteLog(${log.id})" class="text-red-600 hover:text-red-700">Hapus</button>` : ''}
                    </td>
                </tr>`;
            }
        } catch (err) {
            console.error('Failed to load daily logs:', err);
            document.getElementById('daily-logs-body').innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Gagal memuat data</td></tr>';
        }
    };

    window.showCreateLogForm = function() {
        // Set date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('log-date').value = today;
        document.getElementById('log-activity').value = '';
        document.getElementById('log-file').value = '';
        document.getElementById('create-log-modal').classList.remove('hidden');

        // Request location
        requestLocationForLog();
    };

    window.closeCreateLogForm = function() {
        document.getElementById('create-log-modal').classList.add('hidden');
    };

    window.requestLocationForLog = function() {
        const locationStatus = document.getElementById('location-status');
        const manualCoords = document.getElementById('manual-coords');

        locationStatus.classList.remove('hidden');
        manualCoords.classList.add('hidden');

        if (!navigator.geolocation) {
            document.getElementById('location-text').textContent = 'Perangkat tidak mendukung geolokasi';
            manualCoords.classList.remove('hidden');
            return;
        }

        const timeoutId = setTimeout(() => {
            document.getElementById('location-text').textContent = 'Waktu tunggu GPS habis. Masukkan koordinat manual atau lanjutkan tanpa lokasi.';
            manualCoords.classList.remove('hidden');
        }, 5000);

        navigator.geolocation.getCurrentPosition(
            (position) => {
                clearTimeout(timeoutId);
                document.getElementById('location-text').textContent = `✓ Lokasi diperoleh (akurasi ±${Math.round(position.coords.accuracy)}m)`;
                document.getElementById('log-lat').value = position.coords.latitude;
                document.getElementById('log-lng').value = position.coords.longitude;
            },
            (error) => {
                clearTimeout(timeoutId);
                document.getElementById('location-text').textContent = 'Gagal mendapatkan lokasi. Masukkan koordinat manual.';
                manualCoords.classList.remove('hidden');
            },
            { timeout: 5000, enableHighAccuracy: true }
        );
    };

    window.handleSubmitLog = async function(event) {
        event.preventDefault();

        const activity = document.getElementById('log-activity').value;
        const date = document.getElementById('log-date').value;
        const lat = document.getElementById('log-lat').value;
        const lng = document.getElementById('log-lng').value;
        const fileInput = document.getElementById('log-file');

        // Validate activity length
        if (activity.length < 20) {
            alert('Aktivitas harus minimal 20 karakter');
            return;
        }

        // Validate file size
        if (fileInput.files.length > 0 && fileInput.files[0].size > 5 * 1024 * 1024) {
            alert('File terlalu besar (max 5MB)');
            return;
        }

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        try {
            const formData = new FormData();
            formData.append('log_date', date);
            formData.append('activities', activity);
            formData.append('internship_id', window.activeInternshipId);
            if (lat && lng) {
                formData.append('latitude', lat);
                formData.append('longitude', lng);
            }
            if (fileInput.files.length > 0) {
                formData.append('photo', fileInput.files[0]);
            }

            const res = await Auth.apiFetch('/daily-logs', {
                method: 'POST',
                body: formData,
                headers: {} // Let browser set Content-Type for FormData
            });

            if (res.ok) {
                const responseData = await res.json();
                const log = responseData.daily_log || responseData.data;

                let message = 'Daily log berhasil disimpan';
                if (log && log.latitude && log.longitude) {
                    if (log.location_verified) {
                        message += '\n✓ Lokasi dalam range';
                    } else {
                        message += `\n⚠ Lokasi di luar range (${log.location_distance}m)`;
                    }
                }

                alert(message);
                closeCreateLogForm();
                loadStudentDailyLogs();
            } else {
                const error = await res.json();
                alert('Gagal menyimpan: ' + (error.message || 'Error tidak dikenal'));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Log';
        }
    };

    window.deleteLog = async function(logId) {
        if (!confirm('Apakah Anda yakin ingin menghapus log ini?')) return;

        try {
            const res = await Auth.apiFetch(`/daily-logs/${logId}`, { method: 'DELETE' });
            if (res.ok) {
                alert('Log berhasil dihapus');
                loadStudentDailyLogs();
            } else {
                alert('Gagal menghapus log');
            }
        } catch (err) {
            alert('Error: ' + err.message);
        }
    };

    window.viewLogDetail = async function(logId) {
        try {
            const res = await Auth.apiFetch(`/daily-logs/${logId}`);
            if (!res.ok) {
                alert('Gagal memuat detail log');
                return;
            }

            const data = await res.json();
            const log = data.data || data.daily_log || data;

            // Populate modal
            const date = new Date(log.log_date || log.date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('detail-log-date').textContent = date;
            document.getElementById('detail-log-activity').textContent = log.activities || log.context || '—';

            const derivedStatus = log.teacher_comment ? 'reviewed' : 'pending';
            const statusBadges = {
                pending: '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Menunggu</span>',
                reviewed: '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Direview</span>'
            };
            document.getElementById('detail-log-status').innerHTML = statusBadges[derivedStatus];

            // Location
            if (log.latitude && log.longitude) {
                const location = log.location_verified ? `✓ Di lokasi (${log.latitude}, ${log.longitude})` : `⚠ Luar lokasi - ${log.location_distance}m (${log.latitude}, ${log.longitude})`;
                document.getElementById('detail-log-location').textContent = location;
                document.getElementById('detail-log-location-section').classList.remove('hidden');
            } else {
                document.getElementById('detail-log-location-section').classList.add('hidden');
            }

            // Photo
            if (log.photo) {
                document.getElementById('detail-log-photo').src = `/storage/${log.photo}`;
                document.getElementById('detail-log-photo-section').classList.remove('hidden');
            } else {
                document.getElementById('detail-log-photo-section').classList.add('hidden');
            }

            // Comments
            if (log.teacher_comment) {
                document.getElementById('detail-log-comments').textContent = log.teacher_comment;
                document.getElementById('detail-log-comments-section').classList.remove('hidden');
            } else {
                document.getElementById('detail-log-comments-section').classList.add('hidden');
            }

            // Edit/Delete buttons (allow if no teacher comment yet)
            const editBtn = document.getElementById('detail-log-edit-btn');
            const deleteBtn = document.getElementById('detail-log-delete-btn');
            const canEdit = !log.teacher_comment;

            if (canEdit) {
                editBtn.classList.remove('hidden');
                deleteBtn.classList.remove('hidden');
                editBtn.onclick = () => editLogFromDetail(logId);
                deleteBtn.onclick = () => deleteLogFromDetail(logId);
            } else {
                editBtn.classList.add('hidden');
                deleteBtn.classList.add('hidden');
            }

            // Store log ID for edit/delete
            window.currentLogId = logId;

            // Show modal
            document.getElementById('view-log-modal').classList.remove('hidden');
        } catch (err) {
            console.error('Failed to load log detail:', err);
            alert('Gagal memuat detail log: ' + err.message);
        }
    };

    window.closeViewLogModal = function() {
        document.getElementById('view-log-modal').classList.add('hidden');
    };

    window.editLogFromDetail = function(logId) {
        closeViewLogModal();
        editLog(logId || window.currentLogId);
    };

    window.deleteLogFromDetail = function(logId) {
        closeViewLogModal();
        deleteLog(logId);
    };

    window.editLog = async function(logId) {
        try {
            const res = await Auth.apiFetch(`/daily-logs/${logId}`);
            if (!res.ok) {
                alert('Gagal memuat detail log');
                return;
            }

            const data = await res.json();
            const log = data.data || data.daily_log || data;

            // Populate edit form
            document.getElementById('edit-log-date').value = log.log_date || log.date || '';
            document.getElementById('edit-log-activity').value = log.activities || log.context || '';
            document.getElementById('edit-log-lat').value = log.latitude || '';
            document.getElementById('edit-log-lng').value = log.longitude || '';
            document.getElementById('edit-log-file').value = '';

            // Show location status
            const editLocationStatus = document.getElementById('edit-location-status');
            if (log.latitude && log.longitude) {
                const location = log.location_verified ? `✓ Di lokasi` : `⚠ Luar lokasi (${log.location_distance}m)`;
                document.getElementById('edit-location-text').textContent = location;
                editLocationStatus.classList.remove('hidden');
            } else {
                editLocationStatus.classList.add('hidden');
            }

            // Store log ID
            window.currentEditLogId = logId;

            // Show modal
            document.getElementById('edit-log-modal').classList.remove('hidden');
        } catch (err) {
            console.error('Failed to load log for edit:', err);
            alert('Gagal memuat log untuk edit: ' + err.message);
        }
    };

    window.closeEditLogForm = function() {
        document.getElementById('edit-log-modal').classList.add('hidden');
    };

    window.handleUpdateLog = async function(event) {
        event.preventDefault();

        const activity = document.getElementById('edit-log-activity').value;
        const date = document.getElementById('edit-log-date').value;
        const lat = document.getElementById('edit-log-lat').value;
        const lng = document.getElementById('edit-log-lng').value;
        const fileInput = document.getElementById('edit-log-file');
        const logId = window.currentEditLogId;

        // Validate activity length
        if (activity.length < 20) {
            alert('Aktivitas harus minimal 20 karakter');
            return;
        }

        // Validate file size
        if (fileInput.files.length > 0 && fileInput.files[0].size > 5 * 1024 * 1024) {
            alert('File terlalu besar (max 5MB)');
            return;
        }

        const submitBtn = document.getElementById('edit-submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        try {
            const formData = new FormData();
            formData.append('log_date', date);
            formData.append('activities', activity);
            formData.append('internship_id', window.activeInternshipId);
            if (lat && lng) {
                formData.append('latitude', lat);
                formData.append('longitude', lng);
            }
            if (fileInput.files.length > 0) {
                formData.append('photo', fileInput.files[0]);
            }
            formData.append('_method', 'PUT');

            const res = await Auth.apiFetch(`/daily-logs/${logId}`, {
                method: 'POST',
                body: formData,
                headers: {}
            });

            if (res.ok) {
                const responseData = await res.json();
                const log = responseData.daily_log || responseData.data;

                let message = 'Daily log berhasil diperbarui';
                if (log && log.latitude && log.longitude) {
                    if (log.location_verified) {
                        message += '\n✓ Lokasi dalam range';
                    } else {
                        message += `\n⚠ Lokasi di luar range (${log.location_distance}m)`;
                    }
                }

                alert(message);
                closeEditLogForm();
                loadStudentDailyLogs();
            } else {
                const error = await res.json();
                alert('Gagal memperbarui: ' + (error.message || 'Error tidak dikenal'));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Perubahan';
        }
    };

    window.submitClock = async function(type) {
        currentClockType = type;
        let lat, lng;

        if (currentLocation) {
            lat = currentLocation.latitude;
            lng = currentLocation.longitude;
        } else {
            const latInput = parseFloat(document.getElementById('lat-input').value);
            const lngInput = parseFloat(document.getElementById('lng-input').value);

            if (isNaN(latInput) || isNaN(lngInput)) {
                alert('Masukkan koordinat yang valid');
                return;
            }
            lat = latInput;
            lng = lngInput;
        }

        if (!capturedPhotoBlob) {
            alert('Ambil foto terlebih dahulu sebagai bukti kehadiran');
            return;
        }

        const buttons = document.querySelectorAll('#clock-in-submit, #clock-out-submit');
        buttons.forEach(b => { b.disabled = true; b.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span> Mengirim...'; });

        try {
            const formData = new FormData();
            formData.append('type', type === 'in' ? 'clock_in' : 'clock_out');
            formData.append('latitude', lat);
            formData.append('longitude', lng);
            formData.append('internship_id', window.activeInternshipId);
            formData.append('photo', capturedPhotoBlob, 'clock-photo.jpg');

            const res = await Auth.apiFetch('/clock-in-out', {
                method: 'POST',
                body: formData,
                headers: {}
            });

            const data = await res.json();
            const feedback = document.getElementById('clock-feedback');

            if (res.ok) {
                const result = data.clock_record || data.data;
                const isIn = type === 'in';
                const rangeOk = data.is_within_range;
                const dist = data.distance_meters ? Math.round(data.distance_meters) : null;

                feedback.innerHTML = `
                    <div class="p-4 rounded-xl ${rangeOk ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200'}">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-2xl ${rangeOk ? 'text-green-600' : 'text-yellow-600'}">${rangeOk ? 'check_circle' : 'warning'}</span>
                            <span class="font-bold ${rangeOk ? 'text-green-800' : 'text-yellow-800'}">${isIn ? 'Absen Masuk' : 'Absen Keluar'} Berhasil</span>
                        </div>
                        <p class="text-sm ${rangeOk ? 'text-green-700' : 'text-yellow-700'}">
                            ${rangeOk ? '✓ Dalam radius lokasi perusahaan' : `⚠ Di luar radius lokasi (${dist}m dari perusahaan)`}
                        </p>
                    </div>
                `;
                feedback.classList.remove('hidden');
                buttons.forEach(b => b.classList.add('hidden'));

                setTimeout(() => {
                    stopCamera();
                    document.getElementById('clock-modal').classList.add('hidden');
                    capturedPhotoBlob = null;
                    currentLocation = null;
                    // Reset modal UI for next use
                    feedback.classList.add('hidden');
                    feedback.innerHTML = '';
                    buttons.forEach(b => b.classList.remove('hidden'));
                    // Reload only the student dashboard data, not the whole page
                    loadStudentDashboard(window.currentUserId);
                }, 2000);
            } else {
                feedback.innerHTML = `
                    <div class="p-4 rounded-xl bg-red-50 border border-red-200">
                        <p class="text-sm text-red-700 font-medium">${data.error || data.message || 'Gagal melakukan absen'}</p>
                    </div>
                `;
                feedback.classList.remove('hidden');
            }
        } catch (error) {
            alert('Gagal melakukan absen: ' + error.message);
        } finally {
            buttons.forEach(b => {
                b.disabled = false;
                if (b.id === 'clock-in-submit') b.innerHTML = '<span class="material-symbols-outlined">login</span> Masuk';
                if (b.id === 'clock-out-submit') b.innerHTML = '<span class="material-symbols-outlined">logout</span> Keluar';
            });
        }
    };

    // Character counters
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('log-activity')?.addEventListener('input', (e) => {
            const counter = document.getElementById('char-count');
            if (counter) counter.textContent = e.target.value.length;
        });
        document.getElementById('edit-log-activity')?.addEventListener('input', (e) => {
            const counter = document.getElementById('edit-char-count');
            if (counter) counter.textContent = e.target.value.length;
        });
    });

    // Logout handler
    document.getElementById('logout-btn')?.addEventListener('click', async () => {
        try {
            await Auth.apiFetch('/auth/logout', { method: 'POST' });
        } catch (e) {
            // ignore
        }
        Auth.removeToken();
        window.location.href = '/login';
    });
</script>
@endpush
