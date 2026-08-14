## MODIFIED Requirements

### Requirement: School admin access is limited to assigned school data
Sistem SHALL membatasi seluruh operasi admin sekolah ke data milik sekolah yang ditetapkan pada akun mereka, sementara superadmin MUST tetap dapat mengakses seluruh data lintas sekolah. Pada flow create dan update yang membutuhkan konteks sekolah, nilai sekolah efektif untuk admin sekolah MUST berasal dari assignment akun login, bukan dari pilihan manual atau payload klien. Untuk entitas yang sudah menyimpan `school_id` secara langsung, pembatasan akses dan query MUST memakai `school_id` pada record tersebut sebagai sumber kebenaran utama. Untuk entitas operasional admin seperti daily log dan attendance yang belum menyimpan `school_id` langsung pada record utama, sistem MUST menurunkan school scope dari relasi otoritatif yang sudah ada dan menerapkannya secara konsisten pada halaman dan endpoint admin terkait.

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

#### Scenario: School-scoped records already have direct ownership
- **WHEN** admin sekolah mengakses atau mengubah entitas yang sudah memiliki `school_id` langsung pada record
- **THEN** sistem MUST memvalidasi dan memfilter akses menggunakan `school_id` pada record tersebut

#### Scenario: Admin sekolah membuka daily logs di halaman admin
- **WHEN** admin sekolah mengakses halaman atau endpoint admin daily logs
- **THEN** sistem hanya menampilkan daily log yang school ownership-nya berasal dari sekolah admin melalui relasi otoritatif yang berlaku

#### Scenario: Admin sekolah membuka attendance di halaman admin
- **WHEN** admin sekolah mengakses halaman atau endpoint admin attendance
- **THEN** sistem hanya menampilkan attendance yang school ownership-nya berasal dari sekolah admin melalui relasi otoritatif yang berlaku

#### Scenario: Superadmin membuka data lintas sekolah
- **WHEN** superadmin mengakses halaman atau endpoint admin yang sama
- **THEN** sistem menampilkan data dari semua sekolah tanpa pembatasan school scope
