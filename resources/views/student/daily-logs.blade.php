@extends('layouts.app')

@section('title', 'Daily Log')

@section('content')
<div class="space-y-6">
    {{-- Header dengan Tombol Buat --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-[26px] font-bold text-gray-900">Daily Log</h1>
            <x-help-button title="Panduan Daily Log">
                <p>Di halaman ini kamu bisa mencatat aktivitas harian selama magang.</p>
                <ul class="list-disc pl-4 mt-2 space-y-1">
                    <li>Buat daily log baru setiap hari kerja</li>
                    <li>Deskripsikan aktivitas minimal 20 karakter</li>
                    <li>Lampirkan foto jika diperlukan</li>
                    <li>Lokasi akan otomatis terdeteksi lewat GPS</li>
                    <li>Edit atau hapus log yang belum disetujui</li>
                </ul>
            </x-help-button>
        <div class="flex gap-4">
            <button onclick="showCreateLogForm()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                + Buat Log Baru
            </button>
            <a href="/dashboard" class="text-blue-600 hover:text-blue-700">← Kembali</a>
        </div>
    </div>

    {{-- Modal Edit Log --}}
    <div id="edit-log-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full p-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Edit Daily Log</h2>
                <button onclick="closeEditLogForm()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="edit-log-form" onsubmit="handleUpdateLog(event)" novalidate class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Tanggal</label>
                    <input type="date" id="edit-log-date" required class="w-full px-4 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Aktivitas (min 20 karakter)</label>
                    <textarea id="edit-log-activity" minlength="20" maxlength="2000" rows="6" placeholder="Deskripsikan aktivitas yang Anda lakukan hari ini..." required class="w-full px-4 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1"><span id="edit-char-count">0</span>/2000 karakter</p>
                </div>

                <div id="edit-location-status" class="p-4 bg-blue-50 rounded-lg border border-blue-200 hidden">
                    <p id="edit-location-text" class="text-sm text-blue-700">Lokasi: —</p>
                </div>

                <div id="edit-manual-coords" class="space-y-3 hidden">
                    <p class="text-sm text-gray-600">Masukkan koordinat secara manual:</p>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" id="edit-log-lat" placeholder="Latitude" step="0.000001" class="px-3 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <input type="number" id="edit-log-lng" placeholder="Longitude" step="0.000001" class="px-3 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Foto (Opsional, max 2MB)</label>
                    <input type="file" id="edit-log-file" accept="image/*" class="w-full px-4 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF</p>
                </div>

                <div class="flex gap-4 justify-end pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeEditLogForm()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors disabled:bg-gray-400" id="edit-submit-btn">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Buat Log --}}
    <div id="create-log-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full p-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Buat Daily Log Baru</h2>
                <button onclick="closeCreateLogForm()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="create-log-form" onsubmit="handleSubmitLog(event)" novalidate class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Tanggal</label>
                    <input type="date" id="log-date" required class="w-full px-4 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Aktivitas (min 20 karakter)</label>
                    <textarea id="log-activity" minlength="20" maxlength="2000" rows="6" placeholder="Deskripsikan aktivitas yang Anda lakukan hari ini..." required class="w-full px-4 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1"><span id="char-count">0</span>/2000 karakter</p>
                </div>

                <div id="location-status" class="p-4 bg-blue-50 rounded-lg border border-blue-200 hidden">
                    <p id="location-text" class="text-sm text-blue-700">Mendapatkan lokasi...</p>
                </div>

                <div id="manual-coords" class="space-y-3 hidden">
                    <p class="text-sm text-gray-600">Masukkan koordinat secara manual:</p>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" id="log-lat" placeholder="Latitude" step="0.000001" class="px-3 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <input type="number" id="log-lng" placeholder="Longitude" step="0.000001" class="px-3 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Foto (Opsional, max 2MB)</label>
                    <input type="file" id="log-file" accept="image/*" class="w-full px-4 py-1.5 h-8 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF</p>
                </div>

                <div class="flex gap-4 justify-end pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeCreateLogForm()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors disabled:bg-gray-400" id="submit-btn">
                        Simpan Log
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Detail Log --}}
    <div id="view-log-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-2xl w-full p-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Detail Log</h2>
                <button onclick="closeViewLogModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div id="log-detail-content" class="space-y-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Tanggal</p>
                    <p id="detail-log-date" class="font-semibold text-gray-900"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Aktivitas</p>
                    <p id="detail-log-activity" class="text-gray-700 leading-relaxed"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Status</p>
                    <p id="detail-log-status"></p>
                </div>
                <div id="detail-log-location-section" class="hidden">
                    <p class="text-xs text-gray-500 uppercase mb-1">Lokasi</p>
                    <p id="detail-log-location" class="text-gray-700"></p>
                </div>
                <div id="detail-log-photo-section" class="hidden">
                    <p class="text-xs text-gray-500 uppercase mb-1">Foto/File</p>
                    <img id="detail-log-photo" src="" alt="Log photo" class="max-w-full rounded-lg border border-gray-200">
                </div>
                <div id="detail-log-comments-section" class="hidden">
                    <p class="text-xs text-gray-500 uppercase mb-1">Percakapan</p>
                    <div id="detail-log-comments" class="space-y-2"></div>
                    <div class="mt-3 flex gap-2">
                        <input type="text" id="detail-log-comment-input" placeholder="Tulis balasan..."
                            class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        <button type="button" onclick="submitStudentComment()" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg">Kirim</button>
                    </div>
                </div>
                <div class="flex gap-4 justify-end pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeViewLogModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                    <button id="detail-log-edit-btn" type="button" onclick="editLogFromDetail()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors hidden">
                        Edit
                    </button>
                    <button id="detail-log-delete-btn" type="button" onclick="deleteLogFromDetail()" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors hidden">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Daily Log --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Aktivitas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="daily-logs-body">
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat...</td></tr>
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

        // Ambil info user dan magang untuk halaman ini
        try {
            const meRes = await Auth.apiFetch('/auth/me');
            const meData = await meRes.json();
            const user = meData.data || meData;
            window.currentUserId = user.id;

            // Ambil ID magang
            const statusRes = await Auth.apiFetch(`/students/${user.id}/internship-status`);
            const statusData = await statusRes.json();
            window.activeInternshipId = statusData.data?.id || null;
        } catch (e) {
            console.error('Gagal memuat info user/magang:', e);
        }

        loadStudentDailyLogs();
    });

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
                const reviewBadge = log.review_status === 'approved'
                    ? '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">✓ Disetujui</span>'
                    : log.review_status === 'needs_revision'
                    ? '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⚠ Perlu Revisi</span>'
                    : '<span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-500">—</span>';
                const location = log.latitude && log.longitude ? (log.location_verified ? '✓ Di lokasi' : '⚠ Luar lokasi') : '—';
                const logDateStr = new Date(log.log_date || log.date).toDateString();
                const isTodayLog = logDateStr === new Date().toDateString();
                const canEdit = isTodayLog && (
                    log.review_status === 'needs_revision' ||
                    (log.review_status !== 'approved' && !log.teacher_comment)
                );

                tbody.innerHTML += `<tr class="border-b border-gray-100">
                    <td class="px-6 py-4 text-sm">${date}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${activity}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex flex-wrap items-center gap-1">
                            ${reviewBadge}
                            ${log.comments?.length > 0 ? `<span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">💬 ${log.comments.length}</span>` : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">${location}</td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <button onclick="viewLogDetail(${log.id})" class="text-blue-600 hover:text-blue-700">Lihat</button>
                        ${canEdit ? `<button onclick="editLog(${log.id})" class="text-blue-600 hover:text-blue-700">Edit</button>` : ''}
                        ${canEdit ? `<button onclick="deleteLog(${log.id})" class="text-red-600 hover:text-red-700">Hapus</button>` : ''}
                    </td>
                </tr>`;
            }
        } catch (err) {
            console.error('Gagal memuat daily log:', err);
            document.getElementById('daily-logs-body').innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-600">Gagal memuat data</td></tr>';
        }
    };

    window.showCreateLogForm = function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('log-date').value = today;
        document.getElementById('log-activity').value = '';
        document.getElementById('log-file').value = '';
        document.getElementById('char-count').textContent = '0';
        document.getElementById('create-log-modal').classList.remove('hidden');
        requestLocationForLog();
    };

    window.closeCreateLogForm = function() {
        document.getElementById('create-log-modal').classList.add('hidden');
    };

    function requestLocationForLog() {
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
            () => {
                clearTimeout(timeoutId);
                document.getElementById('location-text').textContent = 'Gagal mendapatkan lokasi. Masukkan koordinat manual.';
                manualCoords.classList.remove('hidden');
            },
            { timeout: 5000, enableHighAccuracy: true }
        );
    }

    window.handleSubmitLog = async function(event) {
        event.preventDefault();

        if (!window.activeInternshipId) {
            alert('Tidak dapat mengirim log: tidak ada magang aktif. Silakan refresh halaman.');
            return;
        }

        const activity = document.getElementById('log-activity').value;
        const date = document.getElementById('log-date').value;
        const lat = document.getElementById('log-lat').value;
        const lng = document.getElementById('log-lng').value;
        const fileInput = document.getElementById('log-file');

        if (!date) { alert('Tanggal harus diisi'); return; }
        if (activity.length < 20) { alert('Aktivitas harus minimal 20 karakter'); return; }
        if (fileInput.files.length > 0 && fileInput.files[0].size > 2 * 1024 * 1024) { alert('File terlalu besar (max 2MB)'); return; }

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        try {
            const formData = new FormData();
            formData.append('log_date', date);
            formData.append('activities', activity);
            formData.append('internship_id', window.activeInternshipId);
            if (lat && lng) { formData.append('latitude', lat); formData.append('longitude', lng); }
            if (fileInput.files.length > 0) formData.append('photo', fileInput.files[0]);

            const res = await Auth.apiFetch('/daily-logs', { method: 'POST', body: formData, headers: {} });

            if (res.ok) {
                const responseData = await res.json();
                const log = responseData.daily_log || responseData.data;
                let message = 'Daily log berhasil disimpan';
                if (log && log.latitude && log.longitude) {
                    message += log.location_verified ? '\n✓ Lokasi dalam range' : `\n⚠ Lokasi di luar range (${log.location_distance}m)`;
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

    window.viewLogDetail = async function(logId) {
        try {
            const res = await Auth.apiFetch(`/daily-logs/${logId}`);
            if (!res.ok) { alert('Gagal memuat detail log'); return; }
            const data = await res.json();
            const log = data.data || data.daily_log || data;

            document.getElementById('detail-log-date').textContent = new Date(log.log_date || log.date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('detail-log-activity').textContent = log.activities || log.context || '—';

            const reviewBadge = log.review_status === 'approved'
                ? '<span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">✓ Disetujui</span>'
                : log.review_status === 'needs_revision'
                ? '<span class="px-3 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⚠ Perlu Revisi</span>'
                : '<span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-500">Belum Direview</span>';
            document.getElementById('detail-log-status').innerHTML = reviewBadge;
            if (log.review_note) {
                document.getElementById('detail-log-status').innerHTML += `<p class="text-xs text-gray-500 mt-1">Catatan: ${log.review_note}</p>`;
            }

            if (log.latitude && log.longitude) {
                document.getElementById('detail-log-location').textContent = log.location_verified ? `✓ Di lokasi (${log.latitude}, ${log.longitude})` : `⚠ Luar lokasi - ${log.location_distance}m (${log.latitude}, ${log.longitude})`;
                document.getElementById('detail-log-location-section').classList.remove('hidden');
            } else { document.getElementById('detail-log-location-section').classList.add('hidden'); }

            if (log.photo) { document.getElementById('detail-log-photo').src = `/storage/${log.photo}`; document.getElementById('detail-log-photo-section').classList.remove('hidden'); }
            else { document.getElementById('detail-log-photo-section').classList.add('hidden'); }

            const comments = log.comments || [];
            window.renderComments = (list) => {
                document.getElementById('detail-log-comments').innerHTML = list.map(c => {
                    const isStudent = c.author_role === 'student';
                    const bgClass = isStudent ? 'bg-green-50' : 'bg-blue-50';
                    const nameClass = isStudent ? 'text-green-800' : 'text-blue-800';
                    const name = c.author?.name || (isStudent ? 'Kamu' : 'Guru');
                    return `<div class="${bgClass} rounded-lg px-3 py-2">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs font-medium ${nameClass}">${name}</span>
                            <span class="text-xs text-gray-400">${new Date(c.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</span>
                        </div>
                        <p class="text-sm text-gray-700">${c.comment}</p>
                    </div>`;
                }).join('');
                document.getElementById('detail-log-comments-section').classList.remove('hidden');
            };
            renderComments(comments);

            const logDateStr = new Date(log.log_date || log.date).toDateString();
            const isTodayLog = logDateStr === new Date().toDateString();
            const canEdit = isTodayLog && (
                log.review_status === 'needs_revision' ||
                (log.review_status !== 'approved' && !log.teacher_comment)
            );
            document.getElementById('detail-log-edit-btn').classList.toggle('hidden', !canEdit);
            document.getElementById('detail-log-delete-btn').classList.toggle('hidden', !canEdit);
            window.currentLogId = logId;
            document.getElementById('view-log-modal').classList.remove('hidden');
        } catch (err) { alert('Gagal memuat detail log: ' + err.message); }
    };

    window.closeViewLogModal = function() { document.getElementById('view-log-modal').classList.add('hidden'); };

    window.submitStudentComment = async function() {
        const input = document.getElementById('detail-log-comment-input');
        const comment = input.value.trim();
        if (!comment || !window.currentLogId) return;
        try {
            const res = await Auth.apiFetch(`/student/daily-logs/${window.currentLogId}/comments`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ comment }),
            });
            if (!res.ok) { const err = await res.json(); alert(err.error || 'Gagal mengirim komentar'); return; }
            input.value = '';
            // Muat ulang komentar
            const logRes = await Auth.apiFetch(`/daily-logs/${window.currentLogId}`);
            if (logRes.ok) {
                const data = await logRes.json();
                const log = data.data || data.daily_log || data;
                window.renderComments(log.comments || []);
            }
        } catch (err) { alert('Gagal mengirim komentar: ' + err.message); }
    };
    window.editLogFromDetail = function() { closeViewLogModal(); editLog(window.currentLogId); };
    window.deleteLogFromDetail = function() { closeViewLogModal(); deleteLog(window.currentLogId); };

    window.editLog = async function(logId) {
        try {
            const res = await Auth.apiFetch(`/daily-logs/${logId}`);
            if (!res.ok) { alert('Gagal memuat detail log'); return; }
            const data = await res.json();
            const log = data.data || data.daily_log || data;

            document.getElementById('edit-log-date').value = log.log_date || log.date || '';
            document.getElementById('edit-log-activity').value = log.activities || log.context || '';
            document.getElementById('edit-char-count').textContent = (log.activities || log.context || '').length;
            document.getElementById('edit-log-lat').value = log.latitude || '';
            document.getElementById('edit-log-lng').value = log.longitude || '';

            if (log.latitude && log.longitude) {
                document.getElementById('edit-location-text').textContent = log.location_verified ? '✓ Di lokasi' : `⚠ Luar lokasi (${log.location_distance}m)`;
                document.getElementById('edit-location-status').classList.remove('hidden');
            } else { document.getElementById('edit-location-status').classList.add('hidden'); }

            window.currentEditLogId = logId;
            document.getElementById('edit-log-modal').classList.remove('hidden');
        } catch (err) { alert('Gagal memuat log untuk edit: ' + err.message); }
    };

    window.closeEditLogForm = function() { document.getElementById('edit-log-modal').classList.add('hidden'); };

    window.handleUpdateLog = async function(event) {
        event.preventDefault();
        const activity = document.getElementById('edit-log-activity').value;
        const date = document.getElementById('edit-log-date').value;
        const lat = document.getElementById('edit-log-lat').value;
        const lng = document.getElementById('edit-log-lng').value;
        const fileInput = document.getElementById('edit-log-file');
        const logId = window.currentEditLogId;

        if (activity.length < 20) { alert('Aktivitas harus minimal 20 karakter'); return; }
        if (fileInput.files.length > 0 && fileInput.files[0].size > 2 * 1024 * 1024) { alert('File terlalu besar (max 2MB)'); return; }

        const submitBtn = document.getElementById('edit-submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        try {
            const formData = new FormData();
            formData.append('log_date', date);
            formData.append('activities', activity);
            formData.append('internship_id', window.activeInternshipId);
            if (lat && lng) { formData.append('latitude', lat); formData.append('longitude', lng); }
            if (fileInput.files.length > 0) formData.append('photo', fileInput.files[0]);
            formData.append('_method', 'PUT');

            const res = await Auth.apiFetch(`/daily-logs/${logId}`, { method: 'POST', body: formData, headers: {} });

            if (res.ok) {
                const responseData = await res.json();
                const log = responseData.daily_log || responseData.data;
                let message = 'Daily log berhasil diperbarui';
                if (log && log.latitude && log.longitude) {
                    message += log.location_verified ? '\n✓ Lokasi dalam range' : `\n⚠ Lokasi di luar range (${log.location_distance}m)`;
                }
                alert(message);
                closeEditLogForm();
                loadStudentDailyLogs();
            } else {
                const error = await res.json();
                alert('Gagal memperbarui: ' + (error.message || 'Error tidak dikenal'));
            }
        } catch (err) { alert('Error: ' + err.message); }
        finally { submitBtn.disabled = false; submitBtn.textContent = 'Simpan Perubahan'; }
    };

    window.deleteLog = async function(logId) {
        if (!confirm('Apakah Anda yakin ingin menghapus log ini?')) return;
        try {
            const res = await Auth.apiFetch(`/daily-logs/${logId}`, { method: 'DELETE' });
            if (res.ok) { alert('Log berhasil dihapus'); loadStudentDailyLogs(); }
            else { alert('Gagal menghapus log'); }
        } catch (err) { alert('Error: ' + err.message); }
    };

    // Penghitung karakter
    document.getElementById('log-activity')?.addEventListener('input', (e) => {
        document.getElementById('char-count').textContent = e.target.value.length;
    });
    document.getElementById('edit-log-activity')?.addEventListener('input', (e) => {
        document.getElementById('edit-char-count').textContent = e.target.value.length;
    });
</script>
@endpush
