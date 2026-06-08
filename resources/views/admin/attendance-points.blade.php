@extends('layouts.app')

@section('title', 'Titik Absensi')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-[26px] font-extrabold text-on-surface tracking-tight mb-2 font-headline">Titik Absensi</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Tinjau dan setujui titik absensi yang diajukan oleh guru atau pembimbing lapangan.
        </p>
    </div>
    <x-help-button title="Panduan Titik Absensi">
        <p>Di halaman ini kamu bisa mengelola titik absensi perusahaan.</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Setujui titik absensi agar bisa digunakan siswa</li>
            <li>Tolak titik yang tidak sesuai</li>
            <li>Setiap titik memiliki radius tersendiri (dalam meter)</li>
            <li>Siswa akan otomatis dicocokan ke titik terdekat yang sudah disetujui</li>
        </ul>
    </x-help-button>
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
</header>

<!-- Table card -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <!-- Filters -->
    <div class="p-6 border-b border-surface-container space-y-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Daftar Titik Absensi</h2>
        <div class="flex flex-wrap gap-3 items-end">
            <!-- Search -->
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-11 pr-4 py-1.5 h-8 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-52 transition-all" placeholder="Cari nama atau perusahaan..." type="text">
            </div>
            <!-- Status filter -->
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Status</label>
                <select id="filter-status" class="px-3 py-1.5 h-8 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="">Semua</option>
                    <option value="pending">Menunggu</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <!-- Company filter -->
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Perusahaan</label>
                <select id="filter-company" class="px-3 py-1.5 h-8 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="">Semua</option>
                </select>
            </div>
            <!-- Date from -->
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Dari Tanggal</label>
                <input id="filter-date-from" type="date" class="px-3 py-1.5 h-8 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <!-- Date to -->
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Sampai Tanggal</label>
                <input id="filter-date-to" type="date" class="px-3 py-1.5 h-8 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <!-- Reset -->
            <button onclick="resetFilters()" class="px-3 py-2 bg-surface-container-high text-on-surface-variant rounded-lg text-sm hover:bg-surface-container-highest transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">filter_alt_off</span> Reset
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Nama Titik</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Perusahaan</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Radius</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Dibuat Oleh</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Tanggal</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
        </tbody>
    </table>
    </div>
</div>

<!-- Detail / Map Modal -->
@component('partials.modal', ['id' => 'detail-modal', 'title' => 'Detail Titik Absensi'])
<div class="space-y-4">
    <div id="detail-map" class="w-full h-56 rounded-lg overflow-hidden border border-outline-variant/20"></div>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5">Nama</p>
            <p id="detail-name" class="font-medium text-on-surface">—</p>
        </div>
        <div>
            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5">Perusahaan</p>
            <p id="detail-company" class="font-medium text-on-surface">—</p>
        </div>
        <div>
            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5">Koordinat</p>
            <p id="detail-coords" class="font-medium text-on-surface font-mono text-xs">—</p>
        </div>
        <div>
            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5">Radius</p>
            <p id="detail-radius" class="font-medium text-on-surface">—</p>
        </div>
        <div>
            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5">Dibuat Oleh</p>
            <p id="detail-creator" class="font-medium text-on-surface">—</p>
        </div>
        <div>
            <p class="text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-0.5">Diajukan</p>
            <p id="detail-date" class="font-medium text-on-surface">—</p>
        </div>
    </div>
    <div id="detail-actions" class="flex items-center gap-3 pt-2 border-t border-surface-container"></div>
</div>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast } = AdminUtils;

    let allPoints = [];
    let detailMap = null;
    let searchTimeout = null;
    let currentDetailId = null;

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await loadData();
        setupFilters();
    });

    async function loadData() {
        try {
            const cRes = await Auth.apiFetch('/companies?per_page=1000');
            const cJson = await cRes.json();
            const companies = cJson.data?.data || cJson.data || cJson;

            populateCompanyFilter(companies);

            const pointResults = await Promise.all(
                companies.map(c =>
                    Auth.apiFetch(`/companies/${c.id}/attendance-points`)
                        .then(r => r.json())
                        .then(pts => pts.map(p => ({ ...p, company: c })))
                        .catch(() => [])
                )
            );
            allPoints = pointResults.flat();
            renderTable();
        } catch (e) {
            console.error(e);
            showToast('Gagal memuat data', 'error');
        }
    }

    function populateCompanyFilter(companies) {
        const sel = document.getElementById('filter-company');
        sel.innerHTML = '<option value="">Semua</option>';
        companies.forEach(c => {
            sel.innerHTML += `<option value="${c.id}">${c.name}</option>`;
        });
    }

    function statusBadge(status) {
        const map = { pending: 'bg-amber-100 text-amber-800', approved: 'bg-green-100 text-green-800', rejected: 'bg-red-100 text-red-800' };
        const label = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' };
        return `<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold ${map[status] || ''}">${label[status] || status}</span>`;
    }

    function renderTable() {
        const tbody = document.getElementById('data-table');
        const query = document.getElementById('search-input').value.toLowerCase();
        const statusFilter = document.getElementById('filter-status').value;
        const companyFilter = document.getElementById('filter-company').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo = document.getElementById('filter-date-to').value;

        let filtered = allPoints;
        if (statusFilter)   filtered = filtered.filter(p => p.status === statusFilter);
        if (companyFilter)  filtered = filtered.filter(p => String(p.company_id) === companyFilter);
        if (dateFrom)       filtered = filtered.filter(p => p.created_at.slice(0, 10) >= dateFrom);
        if (dateTo)         filtered = filtered.filter(p => p.created_at.slice(0, 10) <= dateTo);
        if (query)          filtered = filtered.filter(p =>
            p.name.toLowerCase().includes(query) ||
            (p.company?.name || '').toLowerCase().includes(query)
        );

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">Tidak ada titik absensi ditemukan.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(p => {
            const date = new Date(p.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            return `<tr class="hover:bg-surface-container/50 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-on-surface">
                    <button onclick="openDetail(${p.id})" class="hover:text-primary hover:underline text-left">${p.name}</button>
                </td>
                <td class="px-6 py-4 text-sm text-on-surface">${p.company?.name || '—'}</td>
                <td class="px-6 py-4 text-sm text-on-surface-variant">${p.distance_threshold} m</td>
                <td class="px-6 py-4 text-sm text-on-surface-variant">${p.created_by?.name || '—'}</td>
                <td class="px-6 py-4 text-sm text-on-surface-variant">${date}</td>
                <td class="px-6 py-4">${statusBadge(p.status)}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-1">
                        ${p.status !== 'approved'
                            ? `<button onclick="approvePoint(${p.company_id}, ${p.id})" class="p-1.5 rounded-lg text-green-600 hover:bg-green-50 transition-colors" title="Setujui"><span class="material-symbols-outlined text-base">check_circle</span></button>`
                            : ''}
                        ${p.status !== 'rejected'
                            ? `<button onclick="rejectPoint(${p.company_id}, ${p.id})" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Tolak"><span class="material-symbols-outlined text-base">cancel</span></button>`
                            : ''}
                        ${p.status === 'approved' && p.status === 'rejected' ? '<span class="text-xs text-on-surface-variant/40">—</span>' : ''}
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function setupFilters() {
        document.getElementById('search-input').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(renderTable, 200);
        });
        ['filter-status', 'filter-company', 'filter-date-from', 'filter-date-to'].forEach(id => {
            document.getElementById(id).addEventListener('change', renderTable);
        });
    }

    window.resetFilters = () => {
        document.getElementById('search-input').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-company').value = '';
        document.getElementById('filter-date-from').value = '';
        document.getElementById('filter-date-to').value = '';
        renderTable();
    };

    // ── Detail modal ───────────────────────────────────────────────────

    window.openDetail = (id) => {
        const point = allPoints.find(p => p.id === id);
        if (!point) return;
        currentDetailId = id;

        document.getElementById('detail-name').textContent = point.name;
        document.getElementById('detail-company').textContent = point.company?.name || '—';
        document.getElementById('detail-coords').textContent = `${point.latitude}, ${point.longitude}`;
        document.getElementById('detail-radius').textContent = `${point.distance_threshold} meter`;
        document.getElementById('detail-creator').textContent = point.created_by?.name || '—';
        document.getElementById('detail-date').textContent = new Date(point.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        refreshDetailActions(point);
        showModal('detail-modal');

        setTimeout(() => {
            const lat = parseFloat(point.latitude);
            const lng = parseFloat(point.longitude);

            if (!detailMap) {
                detailMap = L.map('detail-map').setView([lat, lng], 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(detailMap);
            } else {
                detailMap.setView([lat, lng], 17);
                detailMap.eachLayer(layer => {
                    if (layer instanceof L.Marker || layer instanceof L.Circle) detailMap.removeLayer(layer);
                });
            }
            L.marker([lat, lng]).addTo(detailMap).bindPopup(point.name).openPopup();
            L.circle([lat, lng], { radius: point.distance_threshold, color: '#3b82f6', fillOpacity: 0.15 }).addTo(detailMap);
            detailMap.invalidateSize();
        }, 150);
    };

    function refreshDetailActions(point) {
        const el = document.getElementById('detail-actions');
        const approveBtn = point.status !== 'approved'
            ? `<button onclick="approvePoint(${point.company_id}, ${point.id})" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Setujui</button>`
            : '<span class="text-xs text-green-700 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm">check_circle</span> Sudah disetujui</span>';
        const rejectBtn = point.status !== 'rejected'
            ? `<button onclick="rejectPoint(${point.company_id}, ${point.id})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Tolak</button>`
            : '<span class="text-xs text-red-600 font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-sm">cancel</span> Sudah ditolak</span>';
        el.innerHTML = `
            <button onclick="AdminUtils.hideModal('detail-modal')" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all mr-auto">Tutup</button>
            ${rejectBtn}
            ${approveBtn}`;
    }

    // ── Actions ────────────────────────────────────────────────────────

    window.approvePoint = async (companyId, id) => {
        try {
            const res = await Auth.apiFetch(`/companies/${companyId}/attendance-points/${id}/approve`, { method: 'POST' });
            if (res.ok) {
                showToast('Titik absensi disetujui', 'success');
                const idx = allPoints.findIndex(p => p.id === id);
                if (idx !== -1) allPoints[idx].status = 'approved';
                renderTable();
                if (currentDetailId === id) refreshDetailActions(allPoints.find(p => p.id === id));
            } else {
                showToast('Gagal menyetujui', 'error');
            }
        } catch (e) {
            showToast('Terjadi kesalahan', 'error');
        }
    };

    window.rejectPoint = async (companyId, id) => {
        try {
            const res = await Auth.apiFetch(`/companies/${companyId}/attendance-points/${id}/reject`, { method: 'POST' });
            if (res.ok) {
                showToast('Titik absensi ditolak', 'success');
                const idx = allPoints.findIndex(p => p.id === id);
                if (idx !== -1) allPoints[idx].status = 'rejected';
                renderTable();
                if (currentDetailId === id) refreshDetailActions(allPoints.find(p => p.id === id));
            } else {
                showToast('Gagal menolak', 'error');
            }
        } catch (e) {
            showToast('Terjadi kesalahan', 'error');
        }
    };

    // ── Import/Export/Template ──────────────────────────────
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/attendance-points', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'titik-absensi.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal export titik absensi', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        try {
            const res = await fetch('/api/v1/import/attendance-points', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
                body: formData,
            });
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Import failed');
            }
            showToast('Titik absensi berhasil diimport');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Gagal import titik absensi', 'error');
        }
        input.value = '';
    }

    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/attendance-points/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-titik-absensi.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal download template', 'error');
        }
    }
</script>
@endpush
