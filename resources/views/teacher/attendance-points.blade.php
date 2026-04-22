@extends('layouts.app')

@section('title', 'Titik Absensi')

@section('content')
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Titik Absensi</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Kelola titik absensi untuk perusahaan yang Anda tangani. Titik yang Anda buat perlu disetujui admin sebelum bisa digunakan siswa.
        </p>
    </div>
    <x-help-button title="Panduan Titik Absensi">
        <p>Di halaman ini kamu bisa menambah titik absensi perusahaan.</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Pilih perusahaan dari daftar yang ditugaskan ke Anda</li>
            <li>Klik tombol "Tambah Titik" dan tentukan lokasi di peta</li>
            <li>Titik yang Anda buat akan berstatus <strong>Menunggu</strong> sampai disetujui admin</li>
            <li>Setelah disetujui, siswa dapat absen di titik tersebut</li>
        </ul>
    </x-help-button>
</header>

<!-- Company selector -->
<div class="mb-6">
    <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Pilih Perusahaan</label>
    <select id="company-select" onchange="onCompanyChange()" class="px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary w-full max-w-sm">
        <option value="">— Pilih perusahaan —</option>
    </select>
</div>

<!-- Points table (hidden until company selected) -->
<div id="points-section" class="hidden bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex flex-wrap justify-between items-center border-b border-surface-container gap-4">
        <div>
            <h2 class="text-xl font-bold tracking-tight font-headline">Titik Absensi</h2>
            <p id="company-name-label" class="text-sm text-on-surface-variant mt-0.5"></p>
        </div>
        <button onclick="openAddModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add_location</span> Tambah Titik
        </button>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Nama Titik</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Koordinat</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Radius</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="points-table" class="divide-y divide-surface-container">
            <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Pilih perusahaan terlebih dahulu.</td></tr>
        </tbody>
    </table>
    </div>
</div>

<!-- Empty state when no companies assigned -->
<div id="no-companies-msg" class="hidden text-center py-16 text-on-surface-variant">
    <span class="material-symbols-outlined text-5xl mb-3 block text-outline">business_off</span>
    <p class="font-medium">Anda belum ditugaskan ke perusahaan manapun.</p>
    <p class="text-sm mt-1">Hubungi admin untuk mendapatkan akses ke perusahaan.</p>
</div>

<!-- Add Point Modal -->
@component('partials.modal', ['id' => 'add-modal', 'title' => 'Tambah Titik Absensi'])
<div class="space-y-4">
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Nama Titik <span class="text-red-500">*</span></label>
        <input id="point-name" type="text" placeholder="cth. Gerbang Utama, Gudang B..." class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary">
    </div>
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Radius (meter) <span class="text-red-500">*</span></label>
        <input id="point-radius" type="number" value="50" min="1" max="10000" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary">
    </div>
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Lokasi <span class="text-red-500">*</span></label>
        <p class="text-xs text-on-surface-variant mb-2">Klik pada peta untuk menentukan titik lokasi.</p>
        <div id="add-map" class="w-full h-56 rounded-lg overflow-hidden border border-outline-variant/20 mb-2"></div>
        <div class="flex gap-3">
            <div class="flex-1">
                <label class="block text-[0.65rem] text-on-surface-variant mb-1">Latitude</label>
                <input id="point-lat" type="number" step="any" placeholder="cth. -6.200000" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/20 rounded-lg text-xs focus:ring-2 focus:ring-primary" oninput="onCoordsInput()">
            </div>
            <div class="flex-1">
                <label class="block text-[0.65rem] text-on-surface-variant mb-1">Longitude</label>
                <input id="point-lng" type="number" step="any" placeholder="cth. 106.816666" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/20 rounded-lg text-xs focus:ring-2 focus:ring-primary" oninput="onCoordsInput()">
            </div>
            <div class="flex items-end pb-0.5">
                <button type="button" onclick="useMyLocation()" title="Gunakan lokasi saya" class="px-3 py-2 bg-surface-container-high text-on-surface rounded-lg text-xs flex items-center gap-1 hover:bg-surface-container-highest transition-colors">
                    <span class="material-symbols-outlined text-sm">my_location</span>
                </button>
            </div>
        </div>
    </div>

    <div id="modal-error" class="hidden text-sm text-red-600 bg-red-50 p-3 rounded-lg"></div>

    <div class="flex justify-end gap-3 pt-2">
        <button type="button" onclick="AdminUtils.hideModal('add-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
        <button type="button" onclick="submitPoint()" id="save-btn" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Simpan</button>
    </div>
</div>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete } = AdminUtils;

    let myCompanies = [];
    let currentCompanyId = null;
    let currentPoints = [];
    let addMap = null;
    let addMarker = null;
    let addCircle = null;

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await loadMyCompanies();
    });

    async function loadMyCompanies() {
        try {
            const res = await Auth.apiFetch('/teacher/my-companies');
            myCompanies = await res.json();

            const sel = document.getElementById('company-select');
            if (myCompanies.length === 0) {
                document.getElementById('no-companies-msg').classList.remove('hidden');
                sel.parentElement.classList.add('hidden');
                return;
            }

            sel.innerHTML = '<option value="">— Pilih perusahaan —</option>';
            myCompanies.forEach(c => {
                sel.innerHTML += `<option value="${c.id}">${c.name}${c.industry ? ' · ' + c.industry : ''}</option>`;
            });
        } catch (e) {
            console.error(e);
            showToast('Gagal memuat daftar perusahaan', 'error');
        }
    }

    window.onCompanyChange = async () => {
        const val = document.getElementById('company-select').value;
        if (!val) {
            document.getElementById('points-section').classList.add('hidden');
            return;
        }
        currentCompanyId = parseInt(val);
        const company = myCompanies.find(c => c.id === currentCompanyId);
        document.getElementById('company-name-label').textContent = company?.name || '';
        document.getElementById('points-section').classList.remove('hidden');
        await loadPoints();
    };

    async function loadPoints() {
        if (!currentCompanyId) return;
        try {
            const res = await Auth.apiFetch(`/companies/${currentCompanyId}/attendance-points`);
            currentPoints = await res.json();
            renderTable();
        } catch (e) {
            showToast('Gagal memuat titik absensi', 'error');
        }
    }

    function statusBadge(status) {
        const map = { pending: 'bg-amber-100 text-amber-800', approved: 'bg-green-100 text-green-800', rejected: 'bg-red-100 text-red-800' };
        const label = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' };
        return `<span class="px-2.5 py-0.5 rounded-full text-xs font-semibold ${map[status] || ''}">${label[status] || status}</span>`;
    }

    function renderTable() {
        const tbody = document.getElementById('points-table');
        if (currentPoints.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Belum ada titik absensi. Klik "Tambah Titik" untuk menambahkan.</td></tr>';
            return;
        }
        tbody.innerHTML = currentPoints.map(p => {
            const canDelete = p.status === 'pending';
            return `<tr class="hover:bg-surface-container/50 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-on-surface">${p.name}</td>
                <td class="px-6 py-4 text-sm text-on-surface-variant font-mono text-xs">${parseFloat(p.latitude).toFixed(6)}, ${parseFloat(p.longitude).toFixed(6)}</td>
                <td class="px-6 py-4 text-sm text-on-surface-variant">${p.distance_threshold} m</td>
                <td class="px-6 py-4">${statusBadge(p.status)}</td>
                <td class="px-6 py-4 text-right">
                    ${canDelete
                        ? `<button onclick="deletePoint(${p.id}, '${p.name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-error transition-colors" title="Hapus"><span class="material-symbols-outlined text-sm">delete</span></button>`
                        : '<span class="text-xs text-on-surface-variant/40">—</span>'
                    }
                </td>
            </tr>`;
        }).join('');
    }

    // ── Add Modal ──────────────────────────────────────────────────────

    window.openAddModal = () => {
        document.getElementById('point-name').value = '';
        document.getElementById('point-radius').value = '50';
        document.getElementById('point-lat').value = '';
        document.getElementById('point-lng').value = '';
        document.getElementById('modal-error').classList.add('hidden');
        showModal('add-modal');

        setTimeout(() => {
            const defaultLat = -6.2;
            const defaultLng = 106.816;

            if (!addMap) {
                addMap = L.map('add-map').setView([defaultLat, defaultLng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(addMap);
                addMap.on('click', onMapClick);
            } else {
                addMap.setView([defaultLat, defaultLng], 14);
                clearMapPin();
            }
            addMap.invalidateSize();
        }, 150);
    };

    function onMapClick(e) {
        const { lat, lng } = e.latlng;
        document.getElementById('point-lat').value = lat.toFixed(7);
        document.getElementById('point-lng').value = lng.toFixed(7);
        placeMapPin(lat, lng);
    }

    window.onCoordsInput = () => {
        const lat = parseFloat(document.getElementById('point-lat').value);
        const lng = parseFloat(document.getElementById('point-lng').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            placeMapPin(lat, lng);
            addMap?.setView([lat, lng], addMap.getZoom());
        }
    };

    function placeMapPin(lat, lng) {
        const radius = parseInt(document.getElementById('point-radius').value) || 50;
        clearMapPin();
        addMarker = L.marker([lat, lng]).addTo(addMap);
        addCircle = L.circle([lat, lng], { radius, color: '#3b82f6', fillOpacity: 0.15 }).addTo(addMap);
    }

    function clearMapPin() {
        if (addMarker) { addMap.removeLayer(addMarker); addMarker = null; }
        if (addCircle) { addMap.removeLayer(addCircle); addCircle = null; }
    }

    window.useMyLocation = () => {
        if (!navigator.geolocation) {
            showToast('Geolocation tidak didukung', 'error');
            return;
        }
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            document.getElementById('point-lat').value = lat.toFixed(7);
            document.getElementById('point-lng').value = lng.toFixed(7);
            placeMapPin(lat, lng);
            addMap?.setView([lat, lng], 17);
        }, () => showToast('Gagal mendapatkan lokasi', 'error'));
    };

    window.submitPoint = async () => {
        const name = document.getElementById('point-name').value.trim();
        const radius = parseInt(document.getElementById('point-radius').value);
        const lat = parseFloat(document.getElementById('point-lat').value);
        const lng = parseFloat(document.getElementById('point-lng').value);
        const errorEl = document.getElementById('modal-error');
        const btn = document.getElementById('save-btn');
        errorEl.classList.add('hidden');

        if (!name) { errorEl.textContent = 'Nama titik wajib diisi.'; errorEl.classList.remove('hidden'); return; }
        if (isNaN(lat) || isNaN(lng)) { errorEl.textContent = 'Klik peta atau masukkan koordinat terlebih dahulu.'; errorEl.classList.remove('hidden'); return; }
        if (!radius || radius < 1) { errorEl.textContent = 'Radius harus lebih dari 0.'; errorEl.classList.remove('hidden'); return; }

        btn.textContent = 'Menyimpan...';
        btn.disabled = true;

        try {
            const res = await Auth.apiFetch(`/companies/${currentCompanyId}/attendance-points`, {
                method: 'POST',
                body: JSON.stringify({ name, latitude: lat, longitude: lng, distance_threshold: radius }),
            });
            const json = await res.json();
            if (!res.ok) {
                errorEl.textContent = json.message || JSON.stringify(json);
                errorEl.classList.remove('hidden');
                return;
            }
            hideModal('add-modal');
            showToast('Titik absensi berhasil diajukan. Menunggu persetujuan admin.', 'success');
            await loadPoints();
        } catch (e) {
            errorEl.textContent = 'Terjadi kesalahan.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.textContent = 'Simpan';
            btn.disabled = false;
        }
    };

    window.deletePoint = async (id, name) => {
        if (!await confirmDelete(name)) return;
        try {
            const res = await Auth.apiFetch(`/companies/${currentCompanyId}/attendance-points/${id}`, { method: 'DELETE' });
            if (res.ok) {
                showToast('Titik absensi dihapus', 'success');
                await loadPoints();
            } else {
                const json = await res.json();
                showToast(json.error || 'Gagal menghapus', 'error');
            }
        } catch (e) {
            showToast('Gagal menghapus', 'error');
        }
    };
</script>
@endpush
