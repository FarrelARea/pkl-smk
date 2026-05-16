## MODIFIED Requirements

### Requirement: School admin access is limited to assigned school data
Sistem SHALL membatasi seluruh operasi admin sekolah ke data milik sekolah yang ditetapkan pada akun mereka, sementara superadmin MUST tetap dapat mengakses seluruh data lintas sekolah. Pada flow create dan update yang membutuhkan konteks sekolah, nilai sekolah efektif untuk admin sekolah MUST berasal dari assignment akun login, bukan dari pilihan manual atau payload klien. Untuk entitas yang sudah menyimpan `school_id` secara langsung, pembatasan akses dan query MUST memakai `school_id` pada record tersebut sebagai sumber kebenaran utama.

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

#### Scenario: Superadmin membuka data lintas sekolah
- **WHEN** superadmin mengakses halaman atau endpoint admin yang sama
- **THEN** sistem menampilkan data dari semua sekolah tanpa pembatasan school scope

### Requirement: School-scoped records carry school context where needed
Sistem SHALL menyimpan atau menurunkan konteks sekolah secara konsisten untuk record yang dikelola admin agar pembatasan akses dapat diterapkan aman pada operasi baca dan tulis. Untuk entitas yang belum memiliki `school_id`, sistem MUST menambahkan kepemilikan sekolah langsung jika entitas tersebut dimiliki oleh tepat satu sekolah.

#### Scenario: Entitas admin dibuat untuk sekolah tertentu
- **WHEN** sistem membuat record domain yang berada di bawah cakupan sekolah melalui admin sekolah
- **THEN** record tersebut MUST menyimpan atau mereferensikan konteks sekolah yang sama dengan admin yang membuatnya

#### Scenario: Data lama membutuhkan school scope
- **WHEN** migrasi atau backfill dijalankan untuk entitas yang memerlukan `school_id`
- **THEN** sistem mengisi konteks sekolah berdasarkan relasi yang sudah ada sebelum pembatasan akses diberlakukan penuh

#### Scenario: Entitas tanpa direct ownership diaudit
- **WHEN** sistem atau pengembang meninjau entitas admin yang masih mengandalkan relasi tidak langsung untuk school scope
- **THEN** entitas yang dimiliki oleh satu sekolah MUST dijadwalkan untuk menerima `school_id` langsung
