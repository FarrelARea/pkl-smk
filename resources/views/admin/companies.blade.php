@extends('layouts.app')

@section('title', 'Manajemen Perusahaan')

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 data-help-target="page-title" class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Manajemen Perusahaan</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Kelola data perusahaan mitra dan informasi kontak mereka.
        </p>
    </div>
    <div data-help-target="header-actions" class="flex items-center gap-2">
        <x-help-button title="Panduan Manajemen Perusahaan">
            <p>Di halaman ini kamu bisa mengelola data perusahaan mitra magang.</p>
            <ul class="list-disc pl-4 mt-2 space-y-1">
                <li>Tambah perusahaan baru dengan lokasi di peta</li>
                <li>Edit dan hapus data perusahaan</li>
                <li>Reset password pembimbing lapangan perusahaan</li>
                <li>Atur batas jarak untuk validasi kehadiran siswa</li>
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
        <button onclick="openCreateModal()" data-help-target="add-button" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Perusahaan
        </button>
    </div>
</header>

<!-- Search & Table -->
<div data-help-target="data-table" class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div data-help-target="filter-bar" class="p-6 flex justify-between items-center border-b border-surface-container gap-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Perusahaan</h2>
        <div class="flex gap-3 items-end flex-wrap">
            <div data-help-target="search-field" class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Cari perusahaan..." type="text">
            </div>
            <div id="filter-school-group" class="w-64">
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Sekolah</label>
                <select id="filter-school" class="w-full px-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all" onchange="loadData(1)">
                    <option value="">Semua Sekolah</option>
                </select>
                <p id="filter-school-locked" class="hidden w-full px-4 py-2 bg-surface-container-low rounded-lg text-sm text-on-surface"></p>
            </div>
            <div data-help-target="per-page-field">
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Per Halaman</label>
                <select id="per-page-select" class="px-3 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Nama</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Sekolah</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Industri</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Alamat</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Telepon</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Aksi</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="7" class="px-6 py-12 text-center text-on-surface-variant">Memuat...</td></tr>
        </tbody>
    </table>
    </div>
    <div id="pagination"></div>
</div>

<!-- Create/Edit Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Perusahaan'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-name">Nama</label>
                <input id="field-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-industry">Industri</label>
                <input id="field-industry" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-school">Sekolah</label>
                <select id="field-school" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih sekolah...</option>
                </select>
                <p id="field-school-locked" class="hidden w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm text-on-surface"></p>
            </div>

            <div class="border-t border-outline-variant/20 pt-4">
                <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">Alamat</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Provinsi</label>
                        <select id="field-province" onchange="onProvinceChange()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Pilih Provinsi...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Kota/Kabupaten</label>
                        <select id="field-city" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent opacity-50 cursor-not-allowed" disabled>
                            <option value="">Pilih Kota/Kabupaten...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Detail Alamat</label>
                        <input type="text" id="field-address" placeholder="Jl. Nama Jalan, No. RT/RW" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-phone">Telepon</label>
                <input id="field-phone" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-email">Email</label>
                <input id="field-email" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="email">
            </div>

            <div class="border-t border-outline-variant/20 pt-4">
                <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">Lokasi</h4>
                <div class="mb-2">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                        <input type="text" id="map-search-input" onkeyup="handleMapSearch(event)" placeholder="Cari lokasi di peta..." class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        <div id="map-search-results" class="absolute z-[1000] w-full mt-1 bg-surface-container-lowest border border-outline-variant/20 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden"></div>
                    </div>
                </div>
                <div id="map-container" class="rounded-lg overflow-hidden border border-outline-variant/20 relative" style="height: 200px;">
                    <div id="company-map" class="w-full h-full"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-latitude">Latitude</label>
                        <input id="field-latitude" step="any" onchange="updateMapFromInputs()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="number" step="any">
                    </div>
                    <div>
                        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-longitude">Longitude</label>
                        <input id="field-longitude" step="any" onchange="updateMapFromInputs()" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="number" step="any">
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="button" onclick="geocodeAddress()" id="btn-geocode" class="flex-1 px-4 py-2 bg-secondary-container text-on-secondary-container rounded-lg font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">location_searching</span> Geocode
                    </button>
                    <button type="button" onclick="getCurrentLocation()" id="btn-location" class="flex-1 px-4 py-2 bg-tertiary-container text-on-tertiary-container rounded-lg font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">my_location</span> GPS
                    </button>
                </div>
                <p id="geocode-status" class="text-xs text-on-surface-variant mt-2 hidden"></p>
            </div>

            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-distance-threshold">Batas Jarak (meter)</label>
                <input id="field-distance-threshold" value="100" min="10" max="1000" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="number">
                <p class="text-xs text-on-surface-variant mt-1">Jarak maksimal untuk validasi kehadiran (10-1000 meter)</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Simpan</button>
            </div>
        </div>
    </form>
@endcomponent

<!-- Reset Password Modal -->
@component('partials.modal', ['id' => 'reset-password-modal', 'title' => 'Reset Password Pembimbing'])
    <form id="reset-password-form" onsubmit="event.preventDefault(); submitResetPassword();">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">Pembimbing <span class="text-red-500">*</span></label>
                <select id="reset-supervisor-id" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih pembimbing...</option>
                </select>
                <p id="no-supervisor-msg" class="hidden mt-1 text-xs text-amber-600">Perusahaan ini belum memiliki pembimbing lapangan.</p>
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="reset-password-field">Password Baru <span class="text-red-500">*</span></label>
                <input id="reset-password-field" type="password" required minlength="6" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="reset-password-confirm-field">Konfirmasi Password <span class="text-red-500">*</span></label>
                <input id="reset-password-confirm-field" type="password" required minlength="6" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div id="reset-error" class="hidden text-sm text-red-600 bg-red-50 p-3 rounded-lg"></div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('reset-password-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Batal</button>
                <button type="submit" id="reset-submit-btn" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Reset Password</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete, renderTable, renderPagination } = AdminUtils;

    const ENDPOINT = '/companies';
    let currentPage = 1;
    let searchQuery = '';
    let searchTimeout = null;
    let provinces = [];
    let companyMap = null;
    let marker = null;
    let currentUser = null;

    function setLockedSchoolField(selectId, textId, schoolId, schoolName) {
        const select = document.getElementById(selectId);
        const locked = document.getElementById(textId);
        if (!select || !locked) return;

        if (currentUser?.role === 'superadmin') {
            select.classList.remove('hidden');
            locked.classList.add('hidden');
            locked.textContent = '';
            return;
        }

        select.classList.add('hidden');
        locked.classList.remove('hidden');
        locked.textContent = schoolName || 'Sekolah tidak ditemukan';
        if (schoolId) {
            select.value = schoolId;
        }
    }

    async function resolveSchoolName(schoolId) {
        if (!schoolId) return '';
        const existing = document.querySelector(`#field-school option[value="${schoolId}"]`) || document.querySelector(`#filter-school option[value="${schoolId}"]`);
        if (existing?.textContent) {
            return existing.textContent;
        }

        try {
            const res = await Auth.apiFetch(`/schools/${schoolId}`);
            const json = await res.json();
            const school = json.data || json;
            return school.name || '';
        } catch {
            return '';
        }
    }

    async function applySchoolScopeUi() {
        if (currentUser?.role === 'superadmin') {
            setLockedSchoolField('filter-school', 'filter-school-locked', '', '');
            setLockedSchoolField('field-school', 'field-school-locked', '', '');
            return;
        }

        const schoolId = currentUser?.school_id || '';
        const schoolName = await resolveSchoolName(schoolId);
        document.getElementById('filter-school').value = schoolId;
        document.getElementById('field-school').value = schoolId;
        setLockedSchoolField('filter-school', 'filter-school-locked', schoolId, schoolName);
        setLockedSchoolField('field-school', 'field-school-locked', schoolId, schoolName);
    }

    function getEffectiveSchoolId(selectId) {
        if (currentUser?.role === 'superadmin') {
            return document.getElementById(selectId).value;
        }

        return currentUser?.school_id || document.getElementById(selectId).value;
    }

    async function initSchoolSelects() {
        await AdminUtils.populateSelect('filter-school', '/schools');
        await AdminUtils.populateSelect('field-school', '/schools');
        await applySchoolScopeUi();
    }

    async function initializePageContext() {
        const res = await Auth.apiFetch('/auth/me');
        const json = await res.json();
        currentUser = json.data || json;
        await initSchoolSelects();
    }

    function getCurrentModalSchoolId() {
        return currentUser?.role === 'superadmin'
            ? document.getElementById('field-school').value
            : (currentUser?.school_id || document.getElementById('field-school').value);
    }

    function syncModalSchoolValue(schoolId = '') {
        const effectiveSchoolId = currentUser?.role === 'superadmin' ? schoolId : (currentUser?.school_id || schoolId);
        document.getElementById('field-school').value = effectiveSchoolId || '';
    }

    function updateModalSchoolRequirement() {
        document.getElementById('field-school').required = true;
    }

    function getFilterSchoolId() {
        return getEffectiveSchoolId('filter-school');
    }

    function getRequestSchoolId() {
        return getEffectiveSchoolId('field-school');
    }

    window.loadData = loadData;

    function waitForAuth(cb) {
        const check = () => window.Auth ? cb() : setTimeout(check, 50);
        check();
    }

    // ── Map Functions ─────────────────────────────────────────────────

    function initMap() {
        if (companyMap) { companyMap.invalidateSize(); return; }
        companyMap = L.map('company-map', { center: [-2.5489, 118.0149], zoom: 5 });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors', maxZoom: 19
        }).addTo(companyMap);
    }

    function updateMap(lat, lng) {
        if (!companyMap) initMap();
        setTimeout(() => companyMap.invalidateSize(), 50);
        const latNum = parseFloat(lat), lngNum = parseFloat(lng);
        if (isNaN(latNum) || isNaN(lngNum)) {
            companyMap.setView([-2.5489, 118.0149], 5);
            if (marker) { companyMap.removeLayer(marker); marker = null; }
            return;
        }
        if (marker) {
            marker.setLatLng([latNum, lngNum]);
        } else {
            marker = L.marker([latNum, lngNum], { draggable: true }).addTo(companyMap);
            marker.on('dragend', e => {
                const pos = e.target.getLatLng();
                document.getElementById('field-latitude').value = pos.lat.toFixed(7);
                document.getElementById('field-longitude').value = pos.lng.toFixed(7);
            });
        }
        companyMap.setView([latNum, lngNum], 16);
    }

    function clearMap() {
        if (marker && companyMap) { companyMap.removeLayer(marker); marker = null; }
        if (companyMap) companyMap.setView([-2.5489, 118.0149], 5);
    }

    window.updateMapFromInputs = () => updateMap(
        document.getElementById('field-latitude').value,
        document.getElementById('field-longitude').value
    );

    // ── Map Search ────────────────────────────────────────────────────

    window.handleMapSearch = (event) => {
        const query = event.target.value.trim();
        clearTimeout(searchTimeout);
        if (query.length < 3) { document.getElementById('map-search-results').classList.add('hidden'); return; }
        searchTimeout = setTimeout(() => searchLocation(query), 300);
    };

    async function searchLocation(query) {
        const resultsDiv = document.getElementById('map-search-results');
        resultsDiv.innerHTML = '<div class="px-4 py-3 text-sm text-on-surface-variant">Mencari...</div>';
        resultsDiv.classList.remove('hidden');
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + ', Indonesia')}&format=json&limit=5`);
            const results = await res.json();
            if (results.length === 0) { resultsDiv.innerHTML = '<div class="px-4 py-3 text-sm text-on-surface-variant">Tidak ada hasil</div>'; return; }
            resultsDiv.innerHTML = results.map(r => `
                <div class="px-4 py-3 text-sm cursor-pointer hover:bg-surface-container-high border-b border-outline-variant/10 last:border-0"
                    onclick="selectMapResult(${r.lat}, ${r.lon}, '${r.display_name.replace(/'/g, "\\'")}')">
                    <div class="font-medium text-on-surface line-clamp-1">${r.display_name.split(',')[0]}</div>
                    <div class="text-xs text-on-surface-variant line-clamp-1">${r.display_name}</div>
                </div>`).join('');
        } catch (e) {
            resultsDiv.innerHTML = '<div class="px-4 py-3 text-sm text-on-surface-variant">Gagal mencari</div>';
        }
    }

    window.selectMapResult = (lat, lon, displayName) => {
        document.getElementById('map-search-input').value = displayName.split(',')[0];
        document.getElementById('map-search-results').classList.add('hidden');
        document.getElementById('field-latitude').value = parseFloat(lat).toFixed(7);
        document.getElementById('field-longitude').value = parseFloat(lon).toFixed(7);
        updateMap(lat, lon);
        showToast('Lokasi ditemukan!');
    };

    document.addEventListener('click', e => {
        if (!e.target.closest('#map-search-input') && !e.target.closest('#map-search-results'))
            document.getElementById('map-search-results').classList.add('hidden');
    });

    // ── Address (Province/City) ───────────────────────────────────────

    async function loadProvinces() {
        try {
            const res = await fetch('/api/v1/address/provinces');
            const json = await res.json();
            provinces = json.data || [];
            const sel = document.getElementById('field-province');
            sel.innerHTML = '<option value="">Pilih Provinsi...</option>';
            provinces.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.code; opt.textContent = p.name;
                sel.appendChild(opt);
            });
        } catch (e) { console.error('Failed to load provinces:', e); }
    }

    window.onProvinceChange = async () => {
        const code = document.getElementById('field-province').value;
        const citySelect = document.getElementById('field-city');
        citySelect.innerHTML = '<option value="">Memuat...</option>';
        citySelect.disabled = true;
        citySelect.classList.add('opacity-50', 'cursor-not-allowed');
        if (!code) { citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>'; return; }
        try {
            const res = await fetch(`/api/v1/address/cities?province_code=${code}`);
            const json = await res.json();
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
            (json.data || []).forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.code; opt.textContent = c.name;
                citySelect.appendChild(opt);
            });
            citySelect.disabled = false;
            citySelect.classList.remove('opacity-50', 'cursor-not-allowed');
        } catch (e) {
            citySelect.innerHTML = '<option value="">Error loading</option>';
        }
    };

    function resetAddressSelects() {
        const citySelect = document.getElementById('field-city');
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
        citySelect.disabled = true;
        citySelect.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // ── Geocode ───────────────────────────────────────────────────────

    window.geocodeAddress = async () => {
        const provinceSelect = document.getElementById('field-province');
        const citySelect = document.getElementById('field-city');
        const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const cityName = citySelect.options[citySelect.selectedIndex]?.text || '';
        const addressDetail = document.getElementById('field-address').value.trim();
        const parts = [addressDetail, cityName, provinceName, 'Indonesia'].filter(p => p && !p.startsWith('Pilih'));
        const fullAddress = parts.join(', ');

        if (!fullAddress || fullAddress === 'Indonesia') { showToast('Isi alamat terlebih dahulu', 'error'); return; }

        const btn = document.getElementById('btn-geocode');
        const status = document.getElementById('geocode-status');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Processing...';
        status.classList.remove('hidden');
        status.textContent = 'Geocoding...';

        try {
            const res = await Auth.apiFetch('/address/geocode', { method: 'POST', body: JSON.stringify({ address: fullAddress }) });
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || 'Geocoding failed');
            document.getElementById('field-latitude').value = json.data.latitude.toFixed(6);
            document.getElementById('field-longitude').value = json.data.longitude.toFixed(6);
            updateMap(json.data.latitude, json.data.longitude);
            status.textContent = '✓ ' + json.data.latitude.toFixed(6) + ', ' + json.data.longitude.toFixed(6);
            showToast('Lokasi ditemukan!');
        } catch (e) {
            status.textContent = '✗ ' + (e.message || 'Gagal');
            showToast(e.message || 'Geocoding gagal', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm">location_searching</span> Geocode';
        }
    };

    window.getCurrentLocation = () => {
        if (!navigator.geolocation) { showToast('Geolocation tidak didukung', 'error'); return; }
        const btn = document.getElementById('btn-location');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Getting...';
        navigator.geolocation.getCurrentPosition(pos => {
            document.getElementById('field-latitude').value = pos.coords.latitude.toFixed(6);
            document.getElementById('field-longitude').value = pos.coords.longitude.toFixed(6);
            updateMap(pos.coords.latitude, pos.coords.longitude);
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm">my_location</span> GPS';
            showToast('Lokasi berhasil didapat!');
        }, () => {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm">my_location</span> GPS';
            showToast('Gagal mendapatkan lokasi', 'error');
        }, { enableHighAccuracy: true, timeout: 10000 });
    };

    // ── Load Data ─────────────────────────────────────────────────────

    async function loadData(page = 1) {
        currentPage = page;
        try {
            const perPage = document.getElementById('per-page-select').value || 15;
            const schoolId = getFilterSchoolId();
            const params = new URLSearchParams({ page, per_page: perPage });
            if (schoolId) params.set('school_id', schoolId);
            if (searchQuery) params.set('search', searchQuery);

            const res = await Auth.apiFetch(`${ENDPOINT}?${params}`);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;

            renderTable('data-table', items, [
                { key: 'name' },
                { key: 'school', render: (row) => row.school?.name || '—' },
                { key: 'industry' },
                { key: 'address' },
                { key: 'phone' },
                { key: 'email' },
            ], row => `
                <div class="flex items-center justify-end gap-1">
                    <button onclick="editItem(${row.id})" class="p-2 text-on-surface-variant hover:text-primary transition-colors" title="Edit"><span class="material-symbols-outlined text-sm">edit</span></button>
                    <button onclick="openResetPassword(${row.id}, '${row.name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-amber-500 transition-colors" title="Reset Password Pembimbing"><span class="material-symbols-outlined text-sm">lock_reset</span></button>
                    <button onclick="deleteItem(${row.id}, '${row.name.replace(/'/g, "\\'")}')" class="p-2 text-on-surface-variant hover:text-error transition-colors" title="Hapus"><span class="material-symbols-outlined text-sm">delete</span></button>
                </div>`);

            renderPagination('pagination', meta, loadData);
        } catch (e) {
            console.error(e);
            showToast('Gagal memuat data perusahaan', 'error');
        }
    }

    // ── Create Modal ──────────────────────────────────────────────────

    window.openCreateModal = async () => {
        document.getElementById('item-id').value = '';
        document.getElementById('crud-form').reset();
        document.getElementById('field-distance-threshold').value = '100';
        document.getElementById('field-latitude').value = '';
        document.getElementById('field-longitude').value = '';
        document.getElementById('geocode-status').classList.add('hidden');
        document.getElementById('map-search-input').value = '';
        document.getElementById('crud-modal-title').textContent = 'Tambah Perusahaan';
        resetAddressSelects();
        await AdminUtils.populateSelect('field-school', '/schools');
        syncModalSchoolValue();
        await applySchoolScopeUi();
        updateModalSchoolRequirement();
        await loadProvinces();
        clearMap();
        showModal('crud-modal');
        setTimeout(() => companyMap?.invalidateSize(), 150);
    };

    // ── Edit Item ─────────────────────────────────────────────────────

    window.editItem = async (id) => {
        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${id}`);
            if (!res.ok) { showToast('Gagal memuat data perusahaan', 'error'); return; }
            const json = await res.json();
            const item = json.data || json;

            document.getElementById('item-id').value = item.id;
            document.getElementById('field-name').value = item.name || '';
            document.getElementById('field-industry').value = item.industry || '';
            document.getElementById('field-address').value = item.address || '';
            document.getElementById('field-phone').value = item.phone || '';
            document.getElementById('field-email').value = item.email || '';
            document.getElementById('field-latitude').value = item.latitude || '';
            document.getElementById('field-longitude').value = item.longitude || '';
            document.getElementById('field-distance-threshold').value = item.distance_threshold || 100;
            document.getElementById('geocode-status').classList.add('hidden');
            document.getElementById('map-search-input').value = '';
            document.getElementById('crud-modal-title').textContent = 'Edit Perusahaan';

            resetAddressSelects();
            await AdminUtils.populateSelect('field-school', '/schools');
            syncModalSchoolValue(item.school_id || item.school?.id || '');
            await applySchoolScopeUi();
            updateModalSchoolRequirement();
            await loadProvinces();

            if (item.province_name) {
                const provinceSelect = document.getElementById('field-province');
                for (const opt of provinceSelect.options) {
                    if (opt.textContent === item.province_name) {
                        provinceSelect.value = opt.value;
                        await onProvinceChange();
                        await new Promise(r => setTimeout(r, 150));
                        if (item.city_name) {
                            const citySelect = document.getElementById('field-city');
                            for (const cityOpt of citySelect.options) {
                                if (cityOpt.textContent === item.city_name) {
                                    citySelect.value = cityOpt.value;
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }
            }

            showModal('crud-modal');
            setTimeout(() => {
                if (item.latitude && item.longitude) {
                    updateMap(item.latitude, item.longitude);
                } else {
                    clearMap();
                }
                companyMap?.invalidateSize();
            }, 150);
        } catch (e) {
            console.error(e);
            showToast('Gagal memuat detail perusahaan', 'error');
        }
    };

    // ── Delete Item ───────────────────────────────────────────────────

    window.deleteItem = async (id, name) => {
        if (!confirmDelete(name)) return;
        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${id}`, { method: 'DELETE' });
            if (res.ok) {
                showToast('Perusahaan berhasil dihapus');
                loadData(currentPage);
            } else {
                showToast('Gagal menghapus perusahaan', 'error');
            }
        } catch (e) {
            showToast('Gagal menghapus perusahaan', 'error');
        }
    };

    // ── Save Item ─────────────────────────────────────────────────────

    window.saveItem = async () => {
        const id = document.getElementById('item-id').value;
        const provinceSelect = document.getElementById('field-province');
        const citySelect = document.getElementById('field-city');
        const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const cityName = citySelect.options[citySelect.selectedIndex]?.text || '';

        const payload = {
            name: document.getElementById('field-name').value,
            industry: document.getElementById('field-industry').value,
            address: document.getElementById('field-address').value,
            phone: document.getElementById('field-phone').value,
            email: document.getElementById('field-email').value,
            province_name: provinceName.startsWith('Pilih') ? '' : provinceName,
            city_name: cityName.startsWith('Pilih') ? '' : cityName,
            distance_threshold: parseInt(document.getElementById('field-distance-threshold').value) || 100,
        };
        const schoolId = getRequestSchoolId();
        if (schoolId) payload.school_id = parseInt(schoolId);
        const lat = document.getElementById('field-latitude').value;
        const lng = document.getElementById('field-longitude').value;
        if (lat) payload.latitude = parseFloat(lat);
        if (lng) payload.longitude = parseFloat(lng);

        try {
            const res = await Auth.apiFetch(id ? `${ENDPOINT}/${id}` : ENDPOINT, {
                method: id ? 'PUT' : 'POST',
                body: JSON.stringify(payload),
            });
            if (!res.ok) {
                const err = await res.json();
                showToast(err.message || 'Validasi gagal', 'error');
                return;
            }
            hideModal('crud-modal');
            showToast(id ? 'Perusahaan berhasil diperbarui' : 'Perusahaan berhasil ditambahkan');
            loadData(id ? currentPage : 1);
        } catch (e) {
            showToast('Gagal menyimpan perusahaan', 'error');
        }
    };

    // ── Reset Password ────────────────────────────────────────────────

    window.openResetPassword = async (companyId, companyName) => {
        document.getElementById('reset-password-modal-title').textContent = `Reset Password — ${companyName}`;
        document.getElementById('reset-password-field').value = '';
        document.getElementById('reset-password-confirm-field').value = '';
        document.getElementById('reset-error').classList.add('hidden');

        const supervisorSelect = document.getElementById('reset-supervisor-id');
        const noMsg = document.getElementById('no-supervisor-msg');
        supervisorSelect.innerHTML = '<option value="">Memuat...</option>';
        supervisorSelect.disabled = true;
        noMsg.classList.add('hidden');

        showModal('reset-password-modal');

        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${companyId}`);
            const json = await res.json();
            const supervisors = (json.data || json).supervisors || [];

            supervisorSelect.innerHTML = '<option value="">Pilih pembimbing...</option>';
            if (supervisors.length === 0) {
                noMsg.classList.remove('hidden');
            } else {
                supervisors.forEach(s => {
                    supervisorSelect.innerHTML += `<option value="${s.id}">${s.name} (${s.email})</option>`;
                });
                if (supervisors.length === 1) supervisorSelect.value = supervisors[0].id;
            }
            supervisorSelect.disabled = false;
        } catch (e) {
            supervisorSelect.innerHTML = '<option value="">Gagal memuat</option>';
            supervisorSelect.disabled = false;
        }
    };

    window.submitResetPassword = async () => {
        const supervisorId = document.getElementById('reset-supervisor-id').value;
        const password = document.getElementById('reset-password-field').value;
        const confirm = document.getElementById('reset-password-confirm-field').value;
        const errorEl = document.getElementById('reset-error');
        const btn = document.getElementById('reset-submit-btn');
        errorEl.classList.add('hidden');

        if (!supervisorId) { errorEl.textContent = 'Pilih pembimbing terlebih dahulu.'; errorEl.classList.remove('hidden'); return; }
        if (password !== confirm) { errorEl.textContent = 'Password dan konfirmasi tidak cocok.'; errorEl.classList.remove('hidden'); return; }
        if (password.length < 6) { errorEl.textContent = 'Password minimal 6 karakter.'; errorEl.classList.remove('hidden'); return; }

        btn.textContent = 'Menyimpan...';
        btn.disabled = true;

        try {
            const res = await Auth.apiFetch('/admin/reset-password', {
                method: 'POST',
                body: JSON.stringify({ user_id: parseInt(supervisorId), new_password: password, new_password_confirmation: confirm }),
            });
            const json = await res.json();
            if (!res.ok) { errorEl.textContent = json.error || json.message || 'Gagal reset password.'; errorEl.classList.remove('hidden'); return; }
            hideModal('reset-password-modal');
            showToast('Password berhasil direset', 'success');
        } catch (e) {
            errorEl.textContent = 'Terjadi kesalahan.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.textContent = 'Reset Password';
            btn.disabled = false;
        }
    };

    // ── Search & Per-page ─────────────────────────────────────────────

    document.getElementById('search-input').addEventListener('input', e => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { searchQuery = e.target.value.trim(); loadData(1); }, 300);
    });

    document.getElementById('per-page-select').addEventListener('change', () => { currentPage = 1; loadData(1); });

    // ── Init ──────────────────────────────────────────────────────────

    // ── Import/Export/Template ──────────────────────────────
    window.exportData = exportData;
    window.importData = importData;
    window.downloadTemplate = downloadTemplate;

    async function exportData() {
        try {
            const res = await fetch('/api/v1/export/companies', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'perusahaan.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal export perusahaan', 'error');
        }
    }

    async function importData(input) {
        const file = input.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        try {
            const res = await fetch('/api/v1/import/companies', {
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
                body: formData,
            });
            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Import failed');
            }
            showToast('Perusahaan berhasil diimport');
            loadData();
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Gagal import perusahaan', 'error');
        }
        input.value = '';
    }

    async function downloadTemplate() {
        try {
            const res = await fetch('/api/v1/import/companies/template', {
                headers: { 'Authorization': 'Bearer ' + Auth.getToken() },
            });
            if (!res.ok) throw new Error('Download template failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'template-import-perusahaan.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        } catch (e) {
            console.error(e);
            showToast('Gagal download template', 'error');
        }
    }

    waitForAuth(async () => {
        if (!Auth.requireAuth()) return;
        await initializePageContext();
        loadData();
    });
</script>
@endpush
