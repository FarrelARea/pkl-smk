<div class="space-y-4 sm:space-y-6">
    <x-help-button title="Panduan Dashboard Siswa">
        <p>Halaman ini membantu kamu menjalani aktivitas magang harian dengan lebih cepat.</p>
        <ul class="mt-2 list-disc space-y-1 pl-4">
            <li>Absen masuk dan keluar dengan foto dan lokasi</li>
            <li>Kirim daily log harian</li>
            <li>Lihat riwayat kehadiran</li>
            <li>Ajukan izin atau sakit</li>
            <li>Lihat penilaian dan dokumen terkait</li>
        </ul>
    </x-help-button>

    <section class="overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-600 via-blue-600 to-indigo-600 text-white shadow-sm">
        <div class="space-y-4 p-5 sm:p-6">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">Dashboard Siswa</p>
                <div>
                    <h2 class="text-xl font-semibold sm:text-2xl">Fokus pada aktivitas magang hari ini</h2>
                    <p class="mt-1 text-sm text-blue-100 sm:text-base">Cek status magang, lakukan absensi, dan buka fitur penting tanpa perlu banyak langkah.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <button id="clock-in-btn" onclick="showClockModal()" class="flex w-full items-start gap-3 rounded-2xl bg-white/14 px-4 py-4 text-left transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/60">
                    <span class="material-symbols-outlined rounded-xl bg-white/15 p-2 text-[22px]">location_on</span>
                    <span>
                        <span class="block text-base font-semibold">Absen sekarang</span>
                        <span class="mt-1 block text-sm text-blue-100">Masuk atau keluar dengan foto dan lokasi.</span>
                    </span>
                </button>

                <a href="/student/daily-logs" class="flex items-start gap-3 rounded-2xl bg-white px-4 py-4 text-slate-900 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white/60">
                    <span class="material-symbols-outlined rounded-xl bg-blue-100 p-2 text-[22px] text-blue-700">edit_note</span>
                    <span>
                        <span class="block text-base font-semibold">Isi daily log</span>
                        <span class="mt-1 block text-sm text-slate-600">Catat kegiatan harian magang dengan cepat.</span>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-slate-900">Status magang</p>
                <p class="mt-1 text-sm text-slate-500">Ringkasan perusahaan, supervisor, dan progres magang aktif kamu.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">Utama</span>
        </div>
        <div id="student-internship-info" class="space-y-4">
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                Memuat status magang...
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <a href="/student/attendance" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-200 hover:bg-blue-50/60">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                <span class="material-symbols-outlined">calendar_month</span>
            </div>
            <h3 class="text-sm font-semibold text-slate-900">Kehadiran</h3>
            <p class="mt-1 text-sm text-slate-500">Lihat riwayat absensi dan detail kehadiran.</p>
        </a>

        <a href="/student/permission-requests" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-amber-200 hover:bg-amber-50/60">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                <span class="material-symbols-outlined">assignment_late</span>
            </div>
            <h3 class="text-sm font-semibold text-slate-900">Izin & sakit</h3>
            <p class="mt-1 text-sm text-slate-500">Ajukan izin ketika tidak bisa hadir magang.</p>
        </a>

        <a href="/student/evaluations" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50/60">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                <span class="material-symbols-outlined">grade</span>
            </div>
            <h3 class="text-sm font-semibold text-slate-900">Penilaian</h3>
            <p class="mt-1 text-sm text-slate-500">Cek penilaian dan dokumen pendukung magang.</p>
        </a>

        <a href="/student/daily-logs" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/60">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                <span class="material-symbols-outlined">menu_book</span>
            </div>
            <h3 class="text-sm font-semibold text-slate-900">Riwayat log</h3>
            <p class="mt-1 text-sm text-slate-500">Buka log harian lengkap dan kelola catatanmu.</p>
        </a>
    </section>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
        <section class="space-y-4">
            <div id="today-clock-status" class="hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Status absen hari ini</h3>
                        <p class="mt-1 text-sm text-slate-500">Pantau absensi yang sudah tercatat hari ini.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Hari ini</span>
                </div>
                <div id="clock-status-content" class="space-y-3">
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                        Memuat status absensi...
                    </div>
                </div>
            </div>

            <div id="attendance-summary-widget" class="hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Ringkasan kehadiran</h3>
                        <p class="mt-1 text-sm text-slate-500">Lihat jumlah hari hadir dan kecocokan lokasi absensi.</p>
                    </div>
                    <a href="/student/attendance" class="text-sm font-medium text-blue-600 hover:text-blue-700">Lihat semua</a>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-blue-50 px-4 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-blue-700">Hari absen</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900" id="attendance-days">0</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 px-4 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-emerald-700">Di lokasi</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900" id="attendance-in-range">0</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50 px-4 py-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-amber-700">Luar lokasi</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900" id="attendance-out-range">0</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-6">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Daily log terbaru</h3>
                    <p class="mt-1 text-sm text-slate-500">Lihat catatan terakhir tanpa membuka halaman lain.</p>
                </div>
                <a href="/student/daily-logs" class="text-sm font-medium text-blue-600 hover:text-blue-700">Buka semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 sm:px-6">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 sm:px-6">Aktivitas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 sm:px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody id="student-logs-body" class="divide-y divide-slate-100 bg-white">
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500 sm:px-6">Memuat daily log terbaru...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="clock-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="max-h-[calc(100vh-2rem)] w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4 text-white sm:px-6 sm:py-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold sm:text-xl">Absen kehadiran</h2>
                        <p class="mt-1 text-sm text-blue-100" id="clock-datetime"></p>
                    </div>
                    <button onclick="closeClockModal()" class="text-white/70 transition-colors hover:text-white">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>
            </div>

            <div class="max-h-[calc(100vh-8rem)] space-y-5 overflow-y-auto p-4 sm:p-6">
                <div id="clock-step-location">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">1</span>
                        <span class="text-sm font-semibold text-slate-700">Lokasi</span>
                    </div>
                    <div id="location-status" class="hidden rounded-xl border-2 border-dashed border-slate-200 p-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined animate-pulse text-blue-500" id="loc-icon">my_location</span>
                            <p id="location-text" class="text-sm text-slate-600">Mendapatkan lokasi...</p>
                        </div>
                    </div>
                    <div id="location-error" class="hidden rounded-xl border border-red-200 bg-red-50 p-4">
                        <p id="error-text" class="text-sm text-red-700"></p>
                    </div>
                    <div id="manual-coords" class="mt-3 hidden space-y-3">
                        <p class="text-xs text-slate-500">Masukkan koordinat manual:</p>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input type="number" id="lat-input" placeholder="Latitude" step="0.000001" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                            <input type="number" id="lng-input" placeholder="Longitude" step="0.000001" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div id="clock-step-camera">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">2</span>
                        <span class="text-sm font-semibold text-slate-700">Foto bukti kehadiran</span>
                    </div>

                    <div id="camera-container" class="relative aspect-[4/3] overflow-hidden rounded-xl bg-slate-900">
                        <video id="camera-preview" class="h-full w-full object-cover" autoplay playsinline muted></video>
                        <canvas id="camera-canvas" class="hidden"></canvas>

                        <div id="camera-overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/60 text-white">
                            <span class="material-symbols-outlined mb-2 text-5xl">photo_camera</span>
                            <p class="text-sm">Menghubungkan kamera...</p>
                        </div>

                        <img id="photo-preview" class="absolute inset-0 hidden h-full w-full object-cover" alt="Preview">

                        <button id="retake-btn" onclick="retakePhoto()" class="absolute right-3 top-3 hidden rounded-full bg-black/50 px-3 py-1.5 text-xs text-white backdrop-blur transition-colors hover:bg-black/70">
                            <span class="material-symbols-outlined mr-1 align-middle text-sm">refresh</span>Ulangi
                        </button>
                    </div>

                    <div class="mt-3 flex justify-center">
                        <button id="capture-btn" onclick="capturePhoto()" class="flex h-16 w-16 items-center justify-center rounded-full border-4 border-blue-600 bg-white shadow-lg transition-colors hover:bg-blue-50">
                            <span class="h-12 w-12 rounded-full bg-blue-600"></span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 pt-2 sm:grid-cols-2">
                    <button onclick="submitClock('in')" class="flex items-center justify-center gap-2 rounded-xl bg-blue-600 py-4 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300" id="clock-in-submit">
                        <span class="material-symbols-outlined">login</span>
                        Masuk
                    </button>
                    <button onclick="submitClock('out')" class="flex items-center justify-center gap-2 rounded-xl bg-emerald-600 py-4 font-semibold text-white shadow-lg shadow-emerald-600/20 transition-all hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300" id="clock-out-submit">
                        <span class="material-symbols-outlined">logout</span>
                        Keluar
                    </button>
                </div>

                <div id="clock-feedback" class="hidden"></div>
            </div>
        </div>
    </div>
</div>
