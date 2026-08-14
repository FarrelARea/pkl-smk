## MODIFIED Requirements

### Requirement: School admin access is limited to assigned school data
Sistem SHALL membatasi seluruh operasi admin sekolah ke data milik sekolah yang ditetapkan pada akun mereka, sementara superadmin MUST tetap dapat mengakses seluruh data lintas sekolah. Pada flow create dan update yang membutuhkan konteks sekolah, nilai sekolah efektif untuk admin sekolah MUST berasal dari assignment akun login, bukan dari pilihan manual atau payload klien.

#### Scenario: Admin sekolah membuka daftar guru
- **WHEN** admin sekolah mengakses data guru
- **THEN** sistem hanya menampilkan guru yang terkait dengan sekolah admin tersebut

#### Scenario: Admin sekolah membuka daftar siswa
- **WHEN** admin sekolah mengakses data siswa
- **THEN** sistem hanya menampilkan siswa yang terkait dengan sekolah admin tersebut

#### Scenario: Admin sekolah mengakses data dari sekolah lain melalui request langsung
- **WHEN** admin sekolah mengirim request ke endpoint admin dengan identifier atau `school_id` milik sekolah lain
- **THEN** sistem MUST menolak akses atau mengembalikan hasil kosong sesuai kontrak endpoint

#### Scenario: Admin sekolah membuat data yang memerlukan school context
- **WHEN** admin sekolah mengirim create atau update request pada form admin yang membutuhkan `school_id`
- **THEN** sistem MUST memakai sekolah yang terhubung ke akun login sebagai nilai sekolah efektif untuk operasi tersebut

#### Scenario: Superadmin membuka data lintas sekolah
- **WHEN** superadmin mengakses halaman atau endpoint admin yang sama
- **THEN** sistem menampilkan data dari semua sekolah tanpa pembatasan school scope

### Requirement: School admin cannot manage school entities
Sistem MUST melarang admin sekolah membuat sekolah baru atau mengubah data sekolah di luar kemampuan yang secara eksplisit diizinkan untuk superadmin, dan UI admin sekolah MUST tidak menampilkan menu atau entry point untuk manajemen sekolah.

#### Scenario: Admin sekolah mencoba membuat sekolah baru
- **WHEN** admin sekolah mengirim aksi create school dari UI atau langsung ke endpoint
- **THEN** sistem MUST menolak aksi tersebut

#### Scenario: Admin sekolah mencoba mengubah sekolah lain
- **WHEN** admin sekolah mengirim aksi update atau delete pada data sekolah yang tidak menjadi assignment mereka
- **THEN** sistem MUST menolak aksi tersebut

#### Scenario: Admin sekolah membuka navigasi admin
- **WHEN** admin sekolah melihat sidebar atau menu admin setelah login
- **THEN** sistem MUST menyembunyikan menu Sekolah dan entry point manajemen sekolah lainnya

#### Scenario: Superadmin mengelola data sekolah
- **WHEN** superadmin membuat, mengubah, atau menghapus data sekolah
- **THEN** sistem mengizinkan aksi sesuai aturan superadmin
