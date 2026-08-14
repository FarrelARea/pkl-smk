## ADDED Requirements

### Requirement: Superadmin can manage school admin accounts
Sistem SHALL menyediakan halaman admin khusus superadmin untuk membuat, melihat, mengubah, dan menghapus akun admin sekolah, dan setiap akun admin sekolah MUST terhubung ke tepat satu sekolah.

#### Scenario: Superadmin membuka halaman manajemen admin sekolah
- **WHEN** superadmin mengakses halaman manajemen admin sekolah di area `/admin`
- **THEN** sistem menampilkan daftar admin sekolah beserta sekolah yang terhubung untuk setiap akun

#### Scenario: Superadmin membuat admin sekolah baru
- **WHEN** superadmin mengisi data admin sekolah baru dan memilih satu sekolah
- **THEN** sistem membuat akun admin dengan role admin sekolah dan menyimpan relasi ke sekolah yang dipilih

#### Scenario: Superadmin mengubah assignment sekolah admin
- **WHEN** superadmin mengubah sekolah yang terhubung ke akun admin sekolah
- **THEN** sistem memperbarui assignment sekolah admin tersebut dan assignment lama tidak lagi dipakai untuk pembatasan akses baru

#### Scenario: Non-superadmin mencoba membuka halaman manajemen admin sekolah
- **WHEN** admin sekolah atau role lain mengakses halaman manajemen admin sekolah
- **THEN** sistem MUST menolak akses

### Requirement: School admin access is limited to assigned school data
Sistem SHALL membatasi seluruh operasi admin sekolah ke data milik sekolah yang ditetapkan pada akun mereka, sementara superadmin MUST tetap dapat mengakses seluruh data lintas sekolah.

#### Scenario: Admin sekolah membuka daftar guru
- **WHEN** admin sekolah mengakses data guru
- **THEN** sistem hanya menampilkan guru yang terkait dengan sekolah admin tersebut

#### Scenario: Admin sekolah membuka daftar siswa
- **WHEN** admin sekolah mengakses data siswa
- **THEN** sistem hanya menampilkan siswa yang terkait dengan sekolah admin tersebut

#### Scenario: Admin sekolah mengakses data dari sekolah lain melalui request langsung
- **WHEN** admin sekolah mengirim request ke endpoint admin dengan identifier atau `school_id` milik sekolah lain
- **THEN** sistem MUST menolak akses atau mengembalikan hasil kosong sesuai kontrak endpoint

#### Scenario: Superadmin membuka data lintas sekolah
- **WHEN** superadmin mengakses halaman atau endpoint admin yang sama
- **THEN** sistem menampilkan data dari semua sekolah tanpa pembatasan school scope

### Requirement: School admin cannot manage school entities
Sistem MUST melarang admin sekolah membuat sekolah baru atau mengubah data sekolah di luar kemampuan yang secara eksplisit diizinkan untuk superadmin.

#### Scenario: Admin sekolah mencoba membuat sekolah baru
- **WHEN** admin sekolah mengirim aksi create school dari UI atau langsung ke endpoint
- **THEN** sistem MUST menolak aksi tersebut

#### Scenario: Admin sekolah mencoba mengubah sekolah lain
- **WHEN** admin sekolah mengirim aksi update atau delete pada data sekolah yang tidak menjadi assignment mereka
- **THEN** sistem MUST menolak aksi tersebut

#### Scenario: Superadmin mengelola data sekolah
- **WHEN** superadmin membuat, mengubah, atau menghapus data sekolah
- **THEN** sistem mengizinkan aksi sesuai aturan superadmin

### Requirement: School-scoped records carry school context where needed
Sistem SHALL menyimpan atau menurunkan konteks sekolah secara konsisten untuk record yang dikelola admin agar pembatasan akses dapat diterapkan aman pada operasi baca dan tulis.

#### Scenario: Entitas admin dibuat untuk sekolah tertentu
- **WHEN** sistem membuat record domain yang berada di bawah cakupan sekolah melalui admin sekolah
- **THEN** record tersebut MUST menyimpan atau mereferensikan konteks sekolah yang sama dengan admin yang membuatnya

#### Scenario: Data lama membutuhkan school scope
- **WHEN** migrasi atau backfill dijalankan untuk entitas yang memerlukan `school_id`
- **THEN** sistem mengisi konteks sekolah berdasarkan relasi yang sudah ada sebelum pembatasan akses diberlakukan penuh
