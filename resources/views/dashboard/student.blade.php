<div class="space-y-6">
    <x-help-button title="Panduan Dashboard Siswa">
        <p>Ini adalah halaman utama kamu selama magang. Di sini kamu bisa:</p>
        <ul class="list-disc pl-4 mt-2 space-y-1">
            <li>Absen masuk dan keluar dengan foto dan lokasi</li>
            <li>Kirim daily log harian</li>
            <li>Lihat riwayat kehadiran</li>
            <li>Ajukan izin atau sakit</li>
            <li>Upload dokumen penilaian</li>
        </ul>
    </x-help-button>

    {{-- Status & Progress Magang --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Status Magang</h3>
        <div id="student-internship-info" class="space-y-4">
            <p class="text-gray-500">Memuat...</p>
        </div>
    </div>

    {{-- Widget Ringkasan Kehadiran --}}
    <div id="attendance-summary-widget" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Ringkasan Kehadiran</h3>
            <a href="/student/attendance" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</a>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600" id="attendance-days">0</p>
                <p class="text-xs text-gray-500 mt-1">Hari Absen</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600" id="attendance-in-range">0</p>
                <p class="text-xs text-gray-500 mt-1">Di Lokasi</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-yellow-600" id="attendance-out-range">0</p>
                <p class="text-xs text-gray-500 mt-1">Luar Lokasi</p>
            </div>
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button id="clock-in-btn" onclick="showClockModal()" class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6 hover:shadow-lg transition-all text-left">
            <div class="text-3xl mb-2">📍</div>
            <h4 class="font-semibold text-gray-900">Absen Lokasi</h4>
            <p class="text-sm text-gray-600">Absen masuk/keluar menggunakan lokasi</p>
        </button>

        <a href="/student/daily-logs" class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6 hover:shadow-lg transition-all text-left">
            <div class="text-3xl mb-2">📝</div>
            <h4 class="font-semibold text-gray-900">Daily Log</h4>
            <p class="text-sm text-gray-600">Kirim laporan harian kerja</p>
        </a>

        <a href="/student/attendance" class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-6 hover:shadow-lg transition-all text-left">
            <div class="text-3xl mb-2">✓</div>
            <h4 class="font-semibold text-gray-900">Kehadiran</h4>
            <p class="text-sm text-gray-600">Lihat riwayat kehadiran</p>
        </a>

        <a href="/student/permission-requests" class="bg-gradient-to-br from-orange-50 to-orange-100 border-2 border-orange-200 rounded-xl p-6 hover:shadow-lg transition-all text-left">
            <div class="text-3xl mb-2">📋</div>
            <h4 class="font-semibold text-gray-900">Izin & Sakit</h4>
            <p class="text-sm text-gray-600">Ajukan izin atau sakit</p>
        </a>

        <a href="/student/evaluations" class="bg-gradient-to-br from-indigo-50 to-indigo-100 border-2 border-indigo-200 rounded-xl p-6 hover:shadow-lg transition-all text-left">
            <div class="text-3xl mb-2">📁</div>
            <h4 class="font-semibold text-gray-900">Penilaian</h4>
            <p class="text-sm text-gray-600">Kalender absensi & dokumen</p>
        </a>
    </div>

    {{-- Status Absen Hari Ini --}}
    <div id="today-clock-status" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
        <h3 class="font-semibold text-gray-900 mb-4">Status Absen Hari Ini</h3>
        <div id="clock-status-content" class="space-y-2">
            <p class="text-gray-500">Memuat...</p>
        </div>
    </div>

    {{-- Modal Absen Masuk/Keluar --}}
    <div id="clock-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl">
            {{-- Judul --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold">Absen Kehadiran</h2>
                        <p class="text-blue-100 text-sm mt-1" id="clock-datetime"></p>
                    </div>
                    <button onclick="closeClockModal()" class="text-white/70 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-5">
                {{-- Langkah 1: Lokasi --}}
                <div id="clock-step-location">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                        <span class="text-sm font-semibold text-gray-700">Lokasi</span>
                    </div>
                    <div id="location-status" class="p-4 rounded-xl border-2 border-dashed border-gray-200 hidden">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-blue-500 animate-pulse" id="loc-icon">my_location</span>
                            <p id="location-text" class="text-sm text-gray-600">Mendapatkan lokasi...</p>
                        </div>
                    </div>
                    <div id="location-error" class="p-4 bg-red-50 rounded-xl border border-red-200 hidden">
                        <p id="error-text" class="text-sm text-red-700"></p>
                    </div>
                    <div id="manual-coords" class="space-y-3 mt-3 hidden">
                        <p class="text-xs text-gray-500">Masukkan koordinat manual:</p>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="number" id="lat-input" placeholder="Latitude" step="0.000001" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <input type="number" id="lng-input" placeholder="Longitude" step="0.000001" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Langkah 2: Kamera --}}
                <div id="clock-step-camera">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                        <span class="text-sm font-semibold text-gray-700">Foto Bukti Kehadiran</span>
                    </div>

                    {{-- Preview kamera --}}
                    <div id="camera-container" class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                        <video id="camera-preview" class="w-full h-full object-cover" autoplay playsinline muted></video>
                        <canvas id="camera-canvas" class="hidden"></canvas>

                        {{-- Overlay kamera --}}
                        <div id="camera-overlay" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-gray-900/60">
                            <span class="material-symbols-outlined text-5xl mb-2">photo_camera</span>
                            <p class="text-sm">Menghubungkan kamera...</p>
                        </div>

                        {{-- Preview foto (menggantikan video) --}}
                        <img id="photo-preview" class="absolute inset-0 w-full h-full object-cover hidden" alt="Preview">

                        {{-- Tombol ulangi --}}
                        <button id="retake-btn" onclick="retakePhoto()" class="absolute top-3 right-3 bg-black/50 hover:bg-black/70 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur hidden transition-colors">
                            <span class="material-symbols-outlined text-sm align-middle mr-1">refresh</span>Ulangi
                        </button>
                    </div>

                    {{-- Tombol ambil foto --}}
                    <div class="flex justify-center mt-3">
                        <button id="capture-btn" onclick="capturePhoto()" class="w-16 h-16 rounded-full border-4 border-blue-600 bg-white hover:bg-blue-50 transition-colors flex items-center justify-center shadow-lg">
                            <span class="w-12 h-12 bg-blue-600 rounded-full"></span>
                        </button>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button onclick="submitClock('in')" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-xl transition-all disabled:bg-gray-300 disabled:cursor-not-allowed shadow-lg shadow-blue-600/20" id="clock-in-submit">
                        <span class="material-symbols-outlined">login</span>
                        Masuk
                    </button>
                    <button onclick="submitClock('out')" class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-4 rounded-xl transition-all disabled:bg-gray-300 disabled:cursor-not-allowed shadow-lg shadow-emerald-600/20" id="clock-out-submit">
                        <span class="material-symbols-outlined">logout</span>
                        Keluar
                    </button>
                </div>

                {{-- Feedback submit --}}
                <div id="clock-feedback" class="hidden"></div>
            </div>
        </div>
    </div>

    {{-- Daily Log Terbaru --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Daily Log Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktivitas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody id="student-logs-body">
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
