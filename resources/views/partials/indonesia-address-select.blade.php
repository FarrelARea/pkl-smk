@props([
    'provinceField' => 'province_name',
    'cityField' => 'city_name',
    'districtField' => 'district_name',
    'villageField' => 'village_name',
    'detailField' => 'address',
])

<div x-data="IndonesiaAddressSelect()" class="space-y-4">
    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">
            Provinsi
        </label>
        <select 
            x-model="selectedProvince"
            @change="onProvinceChange()"
            class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
        >
            <option value="">Pilih Provinsi...</option>
            <template x-for="province in provinces" :key="province.code">
                <option :value="province.code" x-text="province.name"></option>
            </template>
        </select>
    </div>

    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">
            Kota/Kabupaten
        </label>
        <select 
            x-model="selectedCity"
            @change="onCityChange()"
            :disabled="!selectedProvince || loadingCities"
            class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <option value="">Pilih Kota/Kabupaten...</option>
            <template x-if="loadingCities">
                <option disabled>Memuat...</option>
            </template>
            <template x-for="city in cities" :key="city.code">
                <option :value="city.code" x-text="city.name"></option>
            </template>
        </select>
        <input 
            type="text" 
            x-show="selectedCity === 'other'"
            x-model="manualCity"
            placeholder="Masukkan nama Kota/Kabupaten"
            class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent mt-2"
        >
    </div>

    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">
            Kecamatan
        </label>
        <input 
            type="text" 
            x-model="manualDistrict"
            placeholder="Masukkan nama Kecamatan"
            class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
        >
    </div>

    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">
            Kelurahan/Desa
        </label>
        <input 
            type="text" 
            x-model="manualVillage"
            placeholder="Masukkan nama Kelurahan/Desa"
            class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
        >
    </div>

    <div>
        <label class="block text-[0.7rem] font-bold text-on-surface-variant uppercase tracking-widest mb-1.5">
            Detail Alamat
        </label>
        <input 
            type="text" 
            x-model="addressDetail"
            placeholder="Jl. Nama Jalan, No. RT/RW, dsb"
            class="w-full px-4 py-1.5 h-8 bg-surface-container-low border border-outline-variant/20 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
        >
    </div>

    <input type="hidden" :name="'{{ $provinceField }}'" :value="provinceName">
    <input type="hidden" :name="'{{ $cityField }}'" :value="cityName">
    <input type="hidden" :name="'{{ $districtField }}'" :value="manualDistrict">
    <input type="hidden" :name="'{{ $villageField }}'" :value="manualVillage">
    <input type="hidden" :name="'{{ $detailField }}'" :value="fullAddress">
</div>

<script>
function IndonesiaAddressSelect() {
    return {
        provinces: [],
        cities: [],
        selectedProvince: '',
        selectedCity: '',
        manualDistrict: '',
        manualVillage: '',
        addressDetail: '',
        loadingCities: false,

        init() {
            this.loadProvinces();
        },

        async loadProvinces() {
            try {
                const res = await fetch('/api/v1/address/provinces');
                const json = await res.json();
                this.provinces = json.data || [];
            } catch (e) {
                console.error('Failed to load provinces:', e);
            }
        },

        async onProvinceChange() {
            this.selectedCity = '';
            this.cities = [];
            this.manualDistrict = '';
            this.manualVillage = '';
            
            if (!this.selectedProvince) return;

            this.loadingCities = true;
            try {
                const res = await fetch(`/api/v1/address/cities?province_code=${this.selectedProvince}`);
                const json = await res.json();
                this.cities = json.data || [];
            } catch (e) {
                console.error('Failed to load cities:', e);
            } finally {
                this.loadingCities = false;
            }
        },

        onCityChange() {
            this.manualDistrict = '';
            this.manualVillage = '';
        },

        get provinceName() {
            const province = this.provinces.find(p => p.code === this.selectedProvince);
            return province ? province.name : '';
        },

        get cityName() {
            if (this.selectedCity === 'other') {
                return this.manualCity;
            }
            const city = this.cities.find(c => c.code === this.selectedCity);
            return city ? city.name : '';
        },

        get fullAddress() {
            const parts = [
                this.addressDetail,
                this.manualVillage,
                this.manualDistrict,
                this.cityName,
                this.provinceName,
                'Indonesia'
            ].filter(Boolean);
            return parts.join(', ');
        }
    }
}
</script>
