@extends('layouts.app')

@section('title', 'Companies Management')

@push('styles')
<style>
    #company-map {
        background: #f0f0f0;
        transition: opacity 0.3s ease;
    }
    #company-map .leaflet-container {
        height: 100%;
        width: 100%;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<header class="mb-10 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-2 font-headline">Companies Management</h1>
        <p class="text-on-surface-variant max-w-2xl font-body">
            Manage partner companies and their contact information.
        </p>
    </div>
    <div>
        <button onclick="openCreateModal()" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Add Company
        </button>
    </div>
</header>

<!-- Search & Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-[0px_12px_32px_rgba(25,28,30,0.04)] overflow-hidden border border-outline-variant/10">
    <div class="p-6 flex justify-between items-center border-b border-surface-container gap-4">
        <h2 class="text-xl font-bold tracking-tight font-headline">Companies</h2>
        <div class="flex gap-3 items-end">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input id="search-input" class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64 transition-all" placeholder="Search companies..." type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Per Page</label>
                <select id="per-page-select" class="px-3 py-2 bg-surface-container-low border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low">
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Name</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Industry</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Address</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Phone</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant">Email</th>
                <th class="px-6 py-4 text-[0.75rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Actions</th>
            </tr>
        </thead>
        <tbody id="data-table" class="divide-y divide-surface-container">
            <tr><td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">Loading...</td></tr>
        </tbody>
    </table>
    <div id="pagination"></div>
</div>

<!-- Modal -->
@component('partials.modal', ['id' => 'crud-modal', 'title' => 'Company'])
    <form id="crud-form" onsubmit="event.preventDefault(); saveItem();">
        <input type="hidden" id="item-id">
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-name">Name</label>
                <input id="field-name" required class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-industry">Industry</label>
                <input id="field-industry" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            
            <div class="border-t border-outline-variant/20 pt-4 mt-4">
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
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-phone">Phone</label>
                <input id="field-phone" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="text">
            </div>
            <div>
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-email">Email</label>
                <input id="field-email" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="email">
            </div>

            <div class="border-t border-outline-variant/20 pt-4 mt-4">
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
                <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5" for="field-distance-threshold">Distance Threshold (meters)</label>
                <input id="field-distance-threshold" value="100" min="10" max="1000" class="w-full px-4 py-2.5 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" type="number" min="10" max="1000">
                <p class="text-xs text-on-surface-variant mt-1">Max distance for attendance validation (10-1000 meters)</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="AdminUtils.hideModal('crud-modal')" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 primary-gradient text-white rounded-md font-bold text-xs uppercase tracking-wider active:scale-95 transition-all">Save</button>
            </div>
        </div>
    </form>
@endcomponent
@endsection

@push('scripts')
<script type="module">
    const { showModal, hideModal, showToast, confirmDelete, renderTable, renderPagination, editBtn, deleteBtn } = AdminUtils;

    const ENDPOINT = '/companies';
    let currentPage = 1;
    let searchQuery = '';
    let searchTimeout = null;
    let provinces = [];
    let cities = [];
    let companyMap = null;
    let marker = null;

    function getPerPage() {
        return document.getElementById('per-page-select').value || 15;
    }

    // ── Map Functions ────────────────────────────────────────
    function initMap() {
        if (companyMap) {
            companyMap.invalidateSize();
            return;
        }
        
        const mapContainer = document.getElementById('company-map');
        if (!mapContainer) return;
        
        companyMap = L.map('company-map', {
            center: [-2.5489, 118.0149],
            zoom: 5,
            zoomControl: true,
            attributionControl: true
        });
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(companyMap);
        
        companyMap.on('load', () => {
            document.getElementById('company-map').classList.add('loaded');
        });
    }

    function refreshMap() {
        if (companyMap) {
            setTimeout(() => companyMap.invalidateSize(), 50);
        }
    }
    
    // ── Map Search Functions ────────────────────────────────
    function handleMapSearch(event) {
        const query = event.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 3) {
            document.getElementById('map-search-results').classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => searchLocation(query), 300);
    }
    
    async function searchLocation(query) {
        const resultsDiv = document.getElementById('map-search-results');
        resultsDiv.innerHTML = '<div class="px-4 py-3 text-sm text-on-surface-variant">Mencari...</div>';
        resultsDiv.classList.remove('hidden');
        
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + ', Indonesia')}&format=json&limit=5&addressdetails=1`);
            const results = await res.json();
            
            if (results.length === 0) {
                resultsDiv.innerHTML = '<div class="px-4 py-3 text-sm text-on-surface-variant">Tidak ada hasil</div>';
                return;
            }
            
            resultsDiv.innerHTML = results.map(result => `
                <div class="px-4 py-3 text-sm cursor-pointer hover:bg-surface-container-high border-b border-outline-variant/10 last:border-0" onclick="selectMapResult(${result.lat}, ${result.lon}, '${result.display_name.replace(/'/g, "\\'")}')">
                    <div class="font-medium text-on-surface line-clamp-1">${result.display_name.split(',')[0]}</div>
                    <div class="text-xs text-on-surface-variant line-clamp-1">${result.display_name}</div>
                </div>
            `).join('');
            
            resultsDiv.classList.remove('hidden');
        } catch (e) {
            console.error('Search failed:', e);
            resultsDiv.innerHTML = '<div class="px-4 py-3 text-sm text-on-surface-variant">Gagal mencari</div>';
        }
    }
    
    function selectMapResult(lat, lon, displayName) {
        document.getElementById('map-search-input').value = displayName.split(',')[0];
        document.getElementById('map-search-results').classList.add('hidden');
        
        document.getElementById('field-latitude').value = parseFloat(lat).toFixed(7);
        document.getElementById('field-longitude').value = parseFloat(lon).toFixed(7);
        updateMap(lat, lon);
        showToast('Lokasi ditemukan!');
    }
    
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#map-search-input') && !e.target.closest('#map-search-results')) {
            document.getElementById('map-search-results').classList.add('hidden');
        }
    });

    function updateMap(lat, lng) {
        if (!companyMap) initMap();
        if (!companyMap) return;
        
        setTimeout(() => companyMap.invalidateSize(), 50);
        
        const latNum = parseFloat(lat);
        const lngNum = parseFloat(lng);
        
        if (isNaN(latNum) || isNaN(lngNum)) {
            companyMap.setView([-2.5489, 118.0149], 5);
            if (marker) {
                companyMap.removeLayer(marker);
                marker = null;
            }
            return;
        }
        
        if (marker) {
            marker.setLatLng([latNum, lngNum]);
        } else {
            marker = L.marker([latNum, lngNum], { draggable: true }).addTo(companyMap);
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('field-latitude').value = pos.lat.toFixed(7);
                document.getElementById('field-longitude').value = pos.lng.toFixed(7);
            });
        }
        
        companyMap.setView([latNum, lngNum], 16);
    }

    function updateMapFromInputs() {
        const lat = document.getElementById('field-latitude').value;
        const lng = document.getElementById('field-longitude').value;
        updateMap(lat, lng);
    }

    function clearMap() {
        if (marker && companyMap) {
            companyMap.removeLayer(marker);
            marker = null;
        }
        if (companyMap) {
            companyMap.setView([-2.5489, 118.0149], 5);
        }
    }

    // ── Load Data ────────────────────────────────────────
    async function loadData(page = 1) {
        currentPage = page;
        try {
            const perPage = getPerPage();
            const params = new URLSearchParams({ page, per_page: perPage });
            if (searchQuery) params.set('search', searchQuery);

            const res = await Auth.apiFetch(`${ENDPOINT}?${params}`);
            const json = await res.json();

            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
            const meta = json.meta || json;

            renderTable('data-table', items, [
                { key: 'name' },
                { key: 'industry' },
                { key: 'address' },
                { key: 'phone' },
                { key: 'email' },
            ], (row) => `
                <div class="flex justify-end gap-1">
                    ${editBtn(row.id)}
                    ${deleteBtn(row.id, row.name)}
                </div>
            `);

            renderPagination('pagination', meta, loadData);
        } catch (e) {
            console.error(e);
            showToast('Failed to load companies', 'error');
        }
    }

    // ── Open Create Modal ────────────────────────────────
    function openCreateModal() {
        document.getElementById('item-id').value = '';
        document.getElementById('crud-form').reset();
        document.getElementById('field-distance-threshold').value = '100';
        document.getElementById('field-latitude').value = '';
        document.getElementById('field-longitude').value = '';
        document.getElementById('geocode-status').classList.add('hidden');
        document.getElementById('crud-modal-title').textContent = 'Add Company';
        
        resetAddressSelects();
        loadProvinces();
        clearMap();
        
        showModal('crud-modal');
        setTimeout(refreshMap, 150);
    }

    // ── Reset All Address Selects ────────────────────────
    function resetAddressSelects() {
        const citySelect = document.getElementById('field-city');
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
        citySelect.disabled = true;
        citySelect.classList.add('opacity-50', 'cursor-not-allowed');
    }

    // ── Load Provinces ────────────────────────────────────
    async function loadProvinces() {
        try {
            const res = await fetch('/api/v1/address/provinces');
            const json = await res.json();
            provinces = json.data || [];
            
            const provinceSelect = document.getElementById('field-province');
            provinceSelect.innerHTML = '<option value="">Pilih Provinsi...</option>';
            provinces.forEach(province => {
                const opt = document.createElement('option');
                opt.value = province.code;
                opt.textContent = province.name;
                provinceSelect.appendChild(opt);
            });
            
            // Enable city dropdown after provinces are loaded
            const citySelect = document.getElementById('field-city');
            citySelect.disabled = false;
            citySelect.classList.remove('opacity-50', 'cursor-not-allowed');
        } catch (e) {
            console.error('Failed to load provinces:', e);
        }
    }

    // ── On Province Change ────────────────────────────────
    async function onProvinceChange() {
        const provinceCode = document.getElementById('field-province').value;
        const citySelect = document.getElementById('field-city');
        
        citySelect.innerHTML = '<option value="">Memuat...</option>';
        citySelect.disabled = true;
        citySelect.classList.add('opacity-50', 'cursor-not-allowed');
        
        if (!provinceCode) {
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
            return;
        }
        
        try {
            const res = await fetch(`/api/v1/address/cities?province_code=${provinceCode}`);
            const json = await res.json();
            cities = json.data || [];
            
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
            cities.forEach(city => {
                const opt = document.createElement('option');
                opt.value = city.code;
                opt.textContent = city.name;
                citySelect.appendChild(opt);
            });
            citySelect.disabled = false;
            citySelect.classList.remove('opacity-50', 'cursor-not-allowed');
        } catch (e) {
            console.error('Failed to load cities:', e);
            citySelect.innerHTML = '<option value="">Error loading</option>';
        }
    }

    // ── Get Full Address ─────────────────────────────────
    function getFullAddress() {
        const provinceSelect = document.getElementById('field-province');
        const citySelect = document.getElementById('field-city');
        
        const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const cityName = citySelect.options[citySelect.selectedIndex]?.text || '';
        const addressDetail = document.getElementById('field-address')?.value?.trim() || '';
        
        const parts = [
            addressDetail,
            cityName !== 'Pilih Kota/Kabupaten...' ? cityName : '',
            provinceName !== 'Pilih Provinsi...' ? provinceName : '',
            'Indonesia'
        ].filter(Boolean);
        
        return parts.join(', ');
    }

    // ── Geocode Address ──────────────────────────────────
    async function geocodeAddress() {
        const fullAddress = getFullAddress();
        
        if (!fullAddress || fullAddress === 'Indonesia') {
            showToast('Please fill in address first', 'error');
            return;
        }
        
        const btn = document.getElementById('btn-geocode');
        const status = document.getElementById('geocode-status');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Processing...';
        status.classList.remove('hidden');
        status.textContent = 'Geocoding address...';
        
        try {
            const res = await Auth.apiFetch('/address/geocode', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ address: fullAddress }),
            });
            
            const json = await res.json();
            
            if (!res.ok) {
                throw new Error(json.error || 'Geocoding failed');
            }
            
            document.getElementById('field-latitude').value = json.data.latitude.toFixed(6);
            document.getElementById('field-longitude').value = json.data.longitude.toFixed(6);
            updateMap(json.data.latitude.toFixed(6), json.data.longitude.toFixed(6));
            
            status.textContent = '✓ Location found: ' + json.data.latitude.toFixed(6) + ', ' + json.data.longitude.toFixed(6);
            status.classList.remove('text-error');
            status.classList.add('text-success');
            showToast('Location found!');
        } catch (e) {
            console.error('Geocoding failed:', e);
            status.textContent = '✗ ' + (e.message || 'Geocoding failed');
            status.classList.remove('text-success');
            status.classList.add('text-error');
            showToast(e.message || 'Geocoding failed', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-sm">location_searching</span> Geocode';
        }
    }

    // ── Get Current Location ──────────────────────────────
    function getCurrentLocation() {
        const btn = document.getElementById('btn-location');
        const status = document.getElementById('geocode-status');
        
        if (!navigator.geolocation) {
            showToast('Geolocation not supported', 'error');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Getting...';
        status.classList.remove('hidden');
        status.textContent = 'Getting your location...';
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('field-latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('field-longitude').value = position.coords.longitude.toFixed(6);
                updateMap(position.coords.latitude.toFixed(6), position.coords.longitude.toFixed(6));
                
                status.textContent = '✓ Location captured!';
                status.classList.remove('text-error');
                status.classList.add('text-success');
                
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-sm">my_location</span> GPS';
                showToast('Location captured!');
            },
            (error) => {
                console.error('Geolocation error:', error);
                let message = 'Failed to get location';
                if (error.code === 1) message = 'Location permission denied';
                if (error.code === 2) message = 'Location unavailable';
                if (error.code === 3) message = 'Location request timeout';
                
                status.textContent = '✗ ' + message;
                status.classList.remove('text-success');
                status.classList.add('text-error');
                
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-sm">my_location</span> GPS';
                showToast(message, 'error');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // ── Edit Item ────────────────────────────────────────
    async function editItem(id) {
        try {
            const res = await Auth.apiFetch(`${ENDPOINT}/${id}`);
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
            
            document.getElementById('crud-modal-title').textContent = 'Edit Company';
            
            resetAddressSelects();
            await loadProvinces();
            
            if (item.latitude && item.longitude) {
                updateMap(item.latitude, item.longitude);
            } else {
                clearMap();
            }
            
            if (item.province_name) {
                const provinceSelect = document.getElementById('field-province');
                for (let opt of provinceSelect.options) {
                    if (opt.textContent === item.province_name) {
                        provinceSelect.value = opt.value;
                        await onProvinceChange();
                        
                        if (item.city_name) {
                            await new Promise(r => setTimeout(r, 100)); // Wait for cities to load
                            const citySelect = document.getElementById('field-city');
                            for (let cityOpt of citySelect.options) {
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
            setTimeout(refreshMap, 150);
        } catch (e) {
            console.error(e);
            showToast('Failed to load company details', 'error');
        }
    }

    // ── Delete Item ──────────────────────────────────────
    async function deleteItem(id, name) {
        if (!confirmDelete(name)) return;
        try {
            await Auth.apiFetch(`${ENDPOINT}/${id}`, { method: 'DELETE' });
            showToast('Company deleted successfully');
            loadData(currentPage);
        } catch (e) {
            console.error(e);
            showToast('Failed to delete company', 'error');
        }
    }

    // ── Save Item ────────────────────────────────────────
    async function saveItem() {
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
            province_name: provinceName !== 'Pilih Provinsi...' ? provinceName : '',
            city_name: cityName !== 'Pilih Kota/Kabupaten...' ? cityName : '',
            distance_threshold: parseInt(document.getElementById('field-distance-threshold').value) || 100,
        };
        
        const lat = document.getElementById('field-latitude').value;
        const lng = document.getElementById('field-longitude').value;
        if (lat) payload.latitude = parseFloat(lat);
        if (lng) payload.longitude = parseFloat(lng);

        try {
            const method = id ? 'PUT' : 'POST';
            const url = id ? `${ENDPOINT}/${id}` : ENDPOINT;
            const res = await Auth.apiFetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                const err = await res.json();
                throw new Error(err.message || 'Validation error');
            }

            hideModal('crud-modal');
            showToast(id ? 'Company updated successfully' : 'Company created successfully');
            loadData(id ? currentPage : 1);
        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to save company', 'error');
        }
    }

    // ── Search ───────────────────────────────────────────
    document.getElementById('search-input').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = e.target.value.trim();
            loadData(1);
        }, 300);
    });

    // ── Per Page Select ──────────────────────────────────
    document.getElementById('per-page-select').addEventListener('change', () => {
        currentPage = 1;
        loadData(1);
    });

    // ── Expose to window ─────────────────────────────────
    window.loadData = loadData;
    window.editItem = editItem;
    window.deleteItem = deleteItem;
    window.saveItem = saveItem;
    window.openCreateModal = openCreateModal;
    window.geocodeAddress = geocodeAddress;
    window.getCurrentLocation = getCurrentLocation;
    window.onProvinceChange = onProvinceChange;
    window.updateMapFromInputs = updateMapFromInputs;
    window.refreshMap = refreshMap;
    window.handleMapSearch = handleMapSearch;

    // ── Init ─────────────────────────────────────────────
    loadData();
</script>
@endpush
