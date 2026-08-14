## ADDED Requirements

### Requirement: Supervisor workspace provides teacher-level segment coverage
Sistem SHALL menyediakan workspace pembimbing yang mencakup segmen inti yang setara dengan workspace guru untuk alur pemantauan siswa, aktivitas harian, evaluasi, dan tindak lanjut yang relevan.

#### Scenario: Supervisor membuka workspace utama
- **WHEN** pembimbing yang sudah login membuka halaman dashboard atau navigasi utama pembimbing
- **THEN** sistem menampilkan segmen inti yang ekuivalen dengan workspace guru dan hanya menyembunyikan aksi yang memang tidak relevan untuk peran pembimbing

### Requirement: Supervisor navigation mirrors teacher workflow structure
Sistem SHALL menyediakan struktur navigasi pembimbing yang mengikuti pengelompokan workflow guru agar perpindahan antar segmen terasa konsisten.

#### Scenario: Supervisor melihat sidebar atau menu kerja
- **WHEN** pembimbing memuat layout aplikasi setelah login
- **THEN** sistem menampilkan entri navigasi pembimbing untuk tiap segmen inti yang tersedia pada workflow guru dengan label dan konteks yang sesuai untuk pembimbing

### Requirement: Supervisor pages keep role-specific presentation
Sistem SHALL membedakan presentasi workspace pembimbing dari guru melalui heading, copy, ikon, dan konteks data tanpa mengurangi cakupan fitur inti.

#### Scenario: Supervisor membuka halaman yang ekuivalen dengan halaman guru
- **WHEN** pembimbing membuka salah satu segmen yang juga dimiliki guru
- **THEN** sistem menampilkan identitas visual dan teks yang menunjukkan konteks pembimbing perusahaan, bukan menyalin branding guru secara mentah

### Requirement: Supervisor data endpoints support workspace parity
Sistem MUST menyediakan data pembimbing yang cukup untuk mengisi segmen-segmen inti yang diparikan dengan workflow guru.

#### Scenario: Supervisor membuka segmen yang membutuhkan data ringkasan dan daftar kerja
- **WHEN** halaman pembimbing memuat segmen yang membutuhkan daftar siswa, ringkasan status, evaluasi, log, pengajuan izin, atau dokumen
- **THEN** API pembimbing mengembalikan data yang cukup untuk mengisi segmen tersebut tanpa memakai jalur otorisasi guru
