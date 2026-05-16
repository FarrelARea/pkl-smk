## Why

Dashboard siswa saat ini memuat terlalu banyak elemen visual dan interaksi padat sekaligus, sehingga kurang nyaman dipakai di layar ponsel yang menjadi perangkat utama siswa. Perubahan ini dibutuhkan sekarang agar halaman `/dashboard` untuk siswa menjadi lebih sederhana, mudah dipahami, dan tetap mendukung alur penting seperti cek status magang, absen, dan akses fitur harian.

## What Changes

- Menyederhanakan tata letak dashboard siswa menjadi mobile-first dengan hirarki informasi yang jelas sejak layar kecil.
- Menata ulang konten dashboard siswa agar status magang, status absen hari ini, dan aksi utama tampil lebih cepat ditemukan.
- Mengubah daftar aksi cepat menjadi pola navigasi yang lebih sederhana dan mudah disentuh di perangkat mobile.
- Memastikan komponen penting tetap fungsional, termasuk membuka modal absen, melihat ringkasan kehadiran, dan melihat daily log terbaru.
- Memperjelas state loading, empty, dan feedback pada area dashboard siswa agar lebih mudah digunakan.

## Capabilities

### New Capabilities
- `student-dashboard-mobile-experience`: Pengalaman dashboard siswa yang mobile-first, sederhana, dan mudah dipakai untuk alur harian utama.

### Modified Capabilities
- `role-based-login`: Setelah login sebagai siswa berhasil, dashboard tujuan tetap `/dashboard` tetapi pengalaman yang ditampilkan MUST diprioritaskan untuk panel siswa mobile-first.

## Impact

- Affected code: `resources/views/dashboard.blade.php`, `resources/views/dashboard/student.blade.php`, dan JavaScript inline terkait dashboard siswa.
- Affected UX: tata letak panel siswa, quick actions, status magang, status absen, dan daftar log terbaru.
- No API contract change expected; existing student dashboard endpoints dan action flow tetap dipakai.
