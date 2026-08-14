## Why

Saat ini peran admin di aplikasi masih berpusat pada superadmin, sehingga pengelolaan data sekolah tidak bisa didelegasikan ke admin sekolah yang hanya bertanggung jawab atas satu sekolah. Fitur ini dibutuhkan sekarang agar superadmin bisa menyiapkan admin per sekolah tanpa memberi akses lintas sekolah atau izin membuat sekolah baru.

## What Changes

- Menambahkan halaman `/admin` khusus untuk superadmin agar dapat membuat, melihat, dan mengelola akun admin sekolah beserta relasi sekolahnya.
- Menambahkan konsep admin sekolah yang dibatasi ke satu `school_id` dan hanya dapat mengakses data milik sekolah tersebut.
- Membatasi seluruh area admin agar admin sekolah hanya melihat dan mengelola data sekolahnya sendiri, termasuk data turunan seperti guru, siswa, kelas, dan data operasional terkait.
- Melarang admin sekolah membuat atau mengelola data sekolah di luar sekolah yang ditetapkan, termasuk menambah sekolah baru.
- Menambahkan `school_id` pada tabel/domain yang diperlukan agar pembatasan data per sekolah bisa diterapkan secara konsisten.

## Capabilities

### New Capabilities
- `school-scoped-admin-management`: Manajemen admin sekolah oleh superadmin dan pembatasan akses admin ke satu sekolah.

### Modified Capabilities
- `role-based-login`: Login admin tetap memakai entry `/login/admin`, tetapi sistem harus mengizinkan admin sekolah masuk dengan kredensial role admin yang sama dan mengarahkan otorisasi berdasarkan school assignment.

## Impact

- Affected code: route web admin, login/auth flow, middleware/authorization, controller API admin, Blade admin pages, dan query/filter data berbasis sekolah.
- Affected data: tabel `users` dan tabel domain lain yang belum memiliki `school_id` namun perlu dibatasi per sekolah.
- Affected behavior: superadmin tetap punya akses global, sedangkan admin sekolah hanya punya akses scoped dan tidak bisa membuat sekolah baru.
- Dependencies: migrasi database tambahan dan penyesuaian seeder untuk akun admin sekolah.
