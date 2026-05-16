### Requirement: Student dashboard uses mobile-first content hierarchy
Sistem SHALL menampilkan panel dashboard siswa di `/dashboard` dengan hirarki mobile-first yang memprioritaskan informasi status utama dan aksi harian pada layar kecil.

#### Scenario: Student opens dashboard on mobile
- **WHEN** siswa membuka `/dashboard` pada layar kecil
- **THEN** sistem menampilkan ringkasan status magang dan aksi utama tanpa mengharuskan user memahami grid multi-kolom terlebih dahulu

### Requirement: Student dashboard highlights primary daily actions
Sistem SHALL menampilkan aksi utama siswa dengan target sentuh yang jelas dan label yang mudah dipahami untuk alur harian inti.

#### Scenario: Student needs a primary action
- **WHEN** siswa melihat panel dashboard
- **THEN** sistem menampilkan akses yang mudah ditemukan untuk absen, daily log, kehadiran, izin/sakit, dan penilaian

### Requirement: Student dashboard keeps critical widgets functional after layout simplification
Sistem MUST mempertahankan fungsi inti dashboard siswa setelah penyederhanaan tata letak, termasuk membuka modal absen, menampilkan status absen hari ini, menampilkan ringkasan kehadiran, dan menampilkan daily log terbaru.

#### Scenario: Student uses existing dashboard functions
- **WHEN** siswa berinteraksi dengan widget dan aksi pada dashboard yang telah disederhanakan
- **THEN** modal absen, link navigasi, ringkasan data, dan daftar log terbaru tetap berfungsi dengan data yang sama seperti sebelumnya

### Requirement: Student dashboard communicates loading and empty states clearly
Sistem SHALL menampilkan state loading, empty, dan feedback yang mudah dipahami pada area dashboard siswa agar user tahu apa yang sedang terjadi dan apa langkah berikutnya.

#### Scenario: Student data is still loading
- **WHEN** data dashboard siswa belum selesai dimuat
- **THEN** sistem menampilkan loading state yang jelas pada area terkait tanpa membuat halaman terasa rusak

#### Scenario: Student has no recent data
- **WHEN** ringkasan tertentu tidak memiliki data seperti daily log terbaru atau status magang aktif
- **THEN** sistem menampilkan empty state singkat yang mudah dipahami dan tidak mengganggu alur penggunaan
