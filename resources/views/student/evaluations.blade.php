@extends('layouts.app')

@section('title', 'Penilaian & Dokumen')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Penilaian & Dokumen</h1>
        <a href="/dashboard" class="text-blue-600 hover:text-blue-700">← Kembali ke Dashboard</a>
    </div>

    {{-- Kalender Absensi --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900 text-lg">Kalender Absensi</h2>
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

        {{-- Legend --}}
        <div class="flex flex-wrap gap-4 mb-4 text-xs text-gray-600">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span> Tidak Hadir</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Sakit</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span> Izin</span>
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-2">
            <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
        </div>
        <div id="calendar-grid" class="grid grid-cols-7 gap-1">
            <div class="col-span-7 py-8 text-center text-gray-400">Memuat...</div>
        </div>
    </div>

    {{-- Upload Dokumen --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900 text-lg">Dokumen Evaluasi</h2>
            <button onclick="showUploadModal()" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-base">upload_file</span>
                Upload Dokumen
            </button>
        </div>

        {{-- Documents List --}}
        <div id="documents-list" class="space-y-3">
            <p class="text-gray-400 text-sm text-center py-6">Memuat...</p>
        </div>
    </div>
</div>

{{-- Upload Modal --}}
<div id="upload-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 text-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold">Upload Dokumen</h2>
                <button onclick="closeUploadModal()" class="text-white/70 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen <span class="text-red-500">*</span></label>
                <input type="text" id="doc-title" placeholder="Contoh: Laporan Minggu 1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File (PDF, DOC, DOCX, maks. 10MB) <span class="text-red-500">*</span></label>
                <input type="file" id="doc-file" accept=".pdf,.doc,.docx" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <input type="hidden" id="doc-parent-id" value="">
            <div id="upload-error" class="hidden text-sm text-red-600 bg-red-50 p-3 rounded-lg"></div>
            <div class="flex gap-3 pt-2">
                <button onclick="closeUploadModal()" class="flex-1 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Batal</button>
                <button onclick="submitUpload()" id="upload-btn" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    let currentMonth, currentYear, attendanceData = [], documentsData = [], activeInternshipId = null;

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;

        try {
            const meRes = await Auth.apiFetch('/auth/me');
            const meData = await meRes.json();
            const user = meData.data || meData;

            const statusRes = await Auth.apiFetch(`/students/${user.id}/internship-status`);
            const statusData = await statusRes.json();
            activeInternshipId = statusData.data?.id || null;
        } catch (e) {
            console.error('Failed to get user info:', e);
        }

        const now = new Date();
        currentMonth = now.getMonth() + 1;
        currentYear = now.getFullYear();

        await loadData();
    });

    async function loadData() {
        try {
            const res = await Auth.apiFetch(`/student/evaluations?month=${currentMonth}&year=${currentYear}`);
            const data = await res.json();
            attendanceData = data.attendance || [];
            documentsData = data.documents || [];
            renderCalendar();
            renderDocuments();
        } catch (e) {
            console.error('Failed to load evaluations:', e);
        }
    }

    function renderCalendar() {
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('calendar-label').textContent = `${months[currentMonth - 1]} ${currentYear}`;

        const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

        // Build attendance map
        const statusMap = {};
        for (const a of attendanceData) {
            // attendance_date is "YYYY-MM-DD" — parse directly to avoid timezone offset
            const day = parseInt(a.attendance_date.substring(8, 10));
            statusMap[day] = a.status;
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
        for (let d = 1; d <= daysInMonth; d++) {
            const status = statusMap[d];
            const color = status ? colorMap[status] : 'bg-gray-100 text-gray-400';
            html += `<div class="aspect-square flex items-center justify-center rounded-lg text-xs font-medium ${color}" title="${status || ''}">${d}</div>`;
        }

        document.getElementById('calendar-grid').innerHTML = html;
    }

    function renderDocuments() {
        const container = document.getElementById('documents-list');
        if (documentsData.length === 0) {
            container.innerHTML = '<p class="text-gray-400 text-sm text-center py-6">Belum ada dokumen. Klik "Upload Dokumen" untuk menambahkan.</p>';
            return;
        }

        const statusBadge = {
            pending:  '<span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Menunggu</span>',
            approved: '<span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Disetujui</span>',
            rejected: '<span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Ditolak</span>',
        };

        const fileIcon = { pdf: '📄', doc: '📝', docx: '📝' };

        container.innerHTML = documentsData.map(doc => {
            const icon = fileIcon[doc.file_type] || '📎';
            const date = new Date(doc.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const note = doc.teacher_note ? `<p class="text-xs text-red-600 mt-1">Catatan: ${doc.teacher_note}</p>` : '';
            const reviseBtn = doc.status === 'rejected'
                ? `<button onclick="openRevise(${doc.id})" class="text-xs text-blue-600 hover:underline">Revisi</button>`
                : '';
            const deleteBtn = doc.status === 'pending'
                ? `<button onclick="deleteDocument(${doc.id})" class="text-xs text-red-500 hover:underline">Hapus</button>`
                : '';
            const parentBadge = doc.parent_id ? '<span class="text-xs text-gray-400">(Revisi)</span>' : '';

            return `<div class="flex items-start justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">${icon}</span>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">${doc.title} ${parentBadge}</p>
                        <p class="text-xs text-gray-400 mt-0.5">${doc.file_type.toUpperCase()} • ${date}</p>
                        ${note}
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    ${statusBadge[doc.status] || ''}
                    ${reviseBtn}
                    ${deleteBtn}
                </div>
            </div>`;
        }).join('');
    }

    window.changeMonth = async (delta) => {
        currentMonth += delta;
        if (currentMonth > 12) { currentMonth = 1; currentYear++; }
        if (currentMonth < 1) { currentMonth = 12; currentYear--; }
        await loadData();
    };

    window.showUploadModal = () => {
        document.getElementById('doc-title').value = '';
        document.getElementById('doc-file').value = '';
        document.getElementById('doc-parent-id').value = '';
        document.getElementById('upload-error').classList.add('hidden');
        document.getElementById('upload-modal').classList.remove('hidden');
    };

    window.closeUploadModal = () => {
        document.getElementById('upload-modal').classList.add('hidden');
    };

    window.openRevise = (parentId) => {
        document.getElementById('doc-title').value = '';
        document.getElementById('doc-file').value = '';
        document.getElementById('doc-parent-id').value = parentId;
        document.getElementById('upload-error').classList.add('hidden');
        document.getElementById('upload-modal').classList.remove('hidden');
    };

    window.submitUpload = async () => {
        const title = document.getElementById('doc-title').value.trim();
        const file = document.getElementById('doc-file').files[0];
        const parentId = document.getElementById('doc-parent-id').value;
        const errorEl = document.getElementById('upload-error');
        const btn = document.getElementById('upload-btn');

        errorEl.classList.add('hidden');

        if (!title) { errorEl.textContent = 'Judul dokumen harus diisi.'; errorEl.classList.remove('hidden'); return; }
        if (!file) { errorEl.textContent = 'File harus dipilih.'; errorEl.classList.remove('hidden'); return; }
        if (!activeInternshipId) { errorEl.textContent = 'Kamu tidak memiliki magang aktif.'; errorEl.classList.remove('hidden'); return; }

        const formData = new FormData();
        formData.append('title', title);
        formData.append('file', file);
        formData.append('internship_id', activeInternshipId);
        if (parentId) formData.append('parent_id', parentId);

        btn.textContent = 'Mengupload...';
        btn.disabled = true;

        try {
            const token = Auth.getToken();
            const res = await fetch('/api/v1/student/documents', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData,
            });
            const data = await res.json();

            if (!res.ok) {
                const msg = data.error || data.message || Object.values(data.errors || {})[0]?.[0] || 'Gagal upload.';
                errorEl.textContent = msg;
                errorEl.classList.remove('hidden');
                return;
            }

            closeUploadModal();
            await loadData();
        } catch (e) {
            errorEl.textContent = 'Terjadi kesalahan. Coba lagi.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.textContent = 'Upload';
            btn.disabled = false;
        }
    };

    window.deleteDocument = async (id) => {
        if (!confirm('Hapus dokumen ini?')) return;
        try {
            const res = await Auth.apiFetch(`/student/documents/${id}`, { method: 'DELETE' });
            if (res.ok) await loadData();
        } catch (e) {
            console.error('Failed to delete:', e);
        }
    };
</script>
@endpush
