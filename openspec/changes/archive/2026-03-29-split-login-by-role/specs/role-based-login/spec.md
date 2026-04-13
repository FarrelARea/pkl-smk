## ADDED Requirements

### Requirement: Role selection landing page
Sistem SHALL menampilkan halaman pemilihan role di `/login` dengan 4 pilihan: Siswa, Guru, Pembimbing, Admin. Setiap pilihan MUST berupa card/link yang mengarah ke halaman login role masing-masing.

#### Scenario: User mengakses /login
- **WHEN** user mengakses `/login`
- **THEN** sistem menampilkan 4 card pilihan role (Siswa → `/login/student`, Guru → `/login/teacher`, Pembimbing → `/login/supervisor`, Admin → `/login/admin`)

#### Scenario: User memilih role
- **WHEN** user klik salah satu card role
- **THEN** user diarahkan ke halaman login role yang dipilih

### Requirement: Role-specific login page
Sistem SHALL menyediakan halaman login di `/login/{role}` untuk setiap role (student, teacher, supervisor, admin). Halaman MUST menampilkan form email + password tanpa role selector, dan role MUST otomatis di-set berdasarkan URL.

#### Scenario: User mengakses /login/student
- **WHEN** user mengakses `/login/student`
- **THEN** sistem menampilkan form login dengan branding siswa (icon `school`, judul "Login Siswa") dan role otomatis di-set ke `student`

#### Scenario: User mengakses /login/teacher
- **WHEN** user mengakses `/login/teacher`
- **THEN** sistem menampilkan form login dengan branding guru (icon `history_edu`, judul "Login Guru") dan role otomatis di-set ke `teacher`

#### Scenario: User mengakses /login/supervisor
- **WHEN** user mengakses `/login/supervisor`
- **THEN** sistem menampilkan form login dengan branding pembimbing (icon `business`, judul "Login Pembimbing") dan role otomatis di-set ke `company_supervisor`

#### Scenario: User mengakses /login/admin
- **WHEN** user mengakses `/login/admin`
- **THEN** sistem menampilkan form login dengan branding admin (icon `admin_panel_settings`, judul "Login Admin") dan role otomatis di-set ke `school_admin`

#### Scenario: User mengakses role yang tidak valid
- **WHEN** user mengakses `/login/invalid-role`
- **THEN** sistem MUST redirect ke `/login`

### Requirement: Login submission dengan auto role
Ketika user submit form login di halaman role-specific, sistem SHALL mengirim request ke API `/api/v1/auth/login` dengan role yang sudah di-set otomatis dari URL.

#### Scenario: Submit login dari halaman role
- **WHEN** user mengisi email dan password lalu submit di `/login/student`
- **THEN** sistem mengirim POST ke `/api/v1/auth/login` dengan body `{ email, password }` dan role `student` sudah ter-set otomatis

#### Scenario: Login berhasil
- **WHEN** API mengembalikan `access_token`
- **THEN** token disimpan dan user di-redirect ke `/dashboard`

#### Scenario: Login gagal
- **WHEN** API mengembalikan error
- **THEN** pesan error ditampilkan di halaman login tanpa redirect
