@extends('layouts.app')

@section('title', 'Pengajuan Izin & Sakit')

@section('content')
<div class="space-y-6">
    {{-- Judul --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pengajuan Izin & Sakit</h1>
            <x-help-button title="Panduan Pengajuan Izin & Sakit">
                <p>Di halaman ini kamu bisa mengajukan izin atau sakit.</p>
                <ul class="list-disc pl-4 mt-2 space-y-1">
                    <li>Pilih jenis pengajuan (sakit, izin, atau lainnya)</li>
                    <li>Isi tanggal dan alasan pengajuan</li>
                    <li>Lampirkan dokumen pendukung jika ada</li>
                    <li>Pantau status persetujuan dari guru</li>
                </ul>
            </x-help-button>
        <a href="/dashboard" class="text-blue-600 hover:text-blue-700">← Kembali ke Dashboard</a>
    </div>

    {{-- Form Pengajuan --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 text-lg mb-4">Ajukan Izin / Sakit</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pengajuan <span class="text-red-500">*</span></label>
                <select id="req-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                    <option value="sick">Sakit</option>
                    <option value="permit">Izin</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" id="req-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-gray-400 font-normal">(opsional, isi jika lebih dari 1 hari)</span></label>
                <input type="date" id="req-end-date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                <textarea id="req-reason" rows="3" placeholder="Jelaskan alasan pengajuan..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen Pendukung <span class="text-gray-400 font-normal">(opsional, PDF/JPG/PNG maks 5MB)</span></label>
                <input type="file" id="req-doc" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>
        <div id="form-error" class="hidden mt-3 text-sm text-red-600 bg-red-50 p-3 rounded-lg"></div>
        <div id="form-success" class="hidden mt-3 text-sm text-green-700 bg-green-50 p-3 rounded-lg"></div>
        <div class="mt-4">
            <button onclick="submitRequest()" id="submit-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg text-sm transition-colors">
                Ajukan
            </button>
        </div>
    </div>

    {{-- Riwayat Pengajuan --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Riwayat Pengajuan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Durasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Alasan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Catatan Guru</th>
                    </tr>
                </thead>
                <tbody id="requests-body">
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    let activeInternshipId = null;

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;

        // Set tanggal minimum ke hari ini
        document.getElementById('req-date').min = new Date().toISOString().split('T')[0];

        try {
            const meRes = await Auth.apiFetch('/auth/me');
            const meData = await meRes.json();
            const user = meData.data || meData;

            const statusRes = await Auth.apiFetch(`/students/${user.id}/internship-status`);
            const statusData = await statusRes.json();
            activeInternshipId = statusData.data?.id || null;
        } catch (e) {
            console.error('Gagal memuat info user:', e);
        }

        await loadRequests();
    });

    async function loadRequests() {
        try {
            const res = await Auth.apiFetch('/permission-requests');
            const data = await res.json();
            const records = data.data || [];
            renderTable(records);
        } catch (e) {
            console.error('Gagal memuat pengajuan:', e);
        }
    }

    function renderTable(records) {
        const tbody = document.getElementById('requests-body');
        if (records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada pengajuan.</td></tr>';
            return;
        }

        const typeBadge = {
            sick:   '<span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Sakit</span>',
            permit: '<span class="px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">Izin</span>',
            other:  '<span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">Lainnya</span>',
        };
        const statusBadge = {
            pending:  '<span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Menunggu</span>',
            approved: '<span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Disetujui</span>',
            rejected: '<span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Ditolak</span>',
        };

        tbody.innerHTML = records.map(r => {
            const fmt = d => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const dateStr = r.end_date && r.end_date !== r.request_date
                ? `${fmt(r.request_date)} – ${fmt(r.end_date)}`
                : fmt(r.request_date);

            const start = new Date(r.request_date);
            const end = r.end_date ? new Date(r.end_date) : start;
            const days = Math.round((end - start) / 86400000) + 1;
            const durasi = days > 1 ? `${days} hari` : '1 hari';

            return `<tr class="border-b border-gray-100">
                <td class="px-6 py-4 text-sm">${dateStr}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${durasi}</td>
                <td class="px-6 py-4 text-sm">${typeBadge[r.type] || r.type}</td>
                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">${r.reason || '—'}</td>
                <td class="px-6 py-4 text-sm">${statusBadge[r.status] || r.status}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${r.handler_note || '—'}</td>
            </tr>`;
        }).join('');
    }

    window.submitRequest = async () => {
        const type = document.getElementById('req-type').value;
        const date = document.getElementById('req-date').value;
        const endDate = document.getElementById('req-end-date').value;
        const reason = document.getElementById('req-reason').value.trim();
        const doc = document.getElementById('req-doc').files[0];
        const errorEl = document.getElementById('form-error');
        const successEl = document.getElementById('form-success');
        const btn = document.getElementById('submit-btn');

        errorEl.classList.add('hidden');
        successEl.classList.add('hidden');

        if (!date) { errorEl.textContent = 'Tanggal harus diisi.'; errorEl.classList.remove('hidden'); return; }
        if (!reason) { errorEl.textContent = 'Alasan harus diisi.'; errorEl.classList.remove('hidden'); return; }
        if (!activeInternshipId) { errorEl.textContent = 'Kamu tidak memiliki magang aktif.'; errorEl.classList.remove('hidden'); return; }

        const formData = new FormData();
        formData.append('internship_id', activeInternshipId);
        formData.append('request_date', date);
        if (endDate) formData.append('end_date', endDate);
        formData.append('type', type);
        formData.append('reason', reason);
        if (doc) formData.append('document', doc);

        btn.textContent = 'Mengajukan...';
        btn.disabled = true;

        try {
            const token = Auth.getToken();
            const res = await fetch('/api/v1/permission-requests', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}` },
                body: formData,
            });
            const data = await res.json();

            if (!res.ok) {
                const msg = data.error || data.message || Object.values(data.errors || {})[0]?.[0] || 'Gagal mengajukan.';
                errorEl.textContent = msg;
                errorEl.classList.remove('hidden');
                return;
            }

            successEl.textContent = 'Pengajuan berhasil dikirim. Menunggu persetujuan guru.';
            successEl.classList.remove('hidden');
            document.getElementById('req-date').value = '';
            document.getElementById('req-end-date').value = '';
            document.getElementById('req-reason').value = '';
            document.getElementById('req-doc').value = '';
            await loadRequests();
        } catch (e) {
            errorEl.textContent = 'Terjadi kesalahan. Coba lagi.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.textContent = 'Ajukan';
            btn.disabled = false;
        }
    };
</script>
@endpush
