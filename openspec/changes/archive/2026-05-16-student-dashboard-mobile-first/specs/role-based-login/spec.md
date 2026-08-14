## MODIFIED Requirements

### Requirement: Login submission dengan auto role
Ketika user submit form login di halaman role-specific, sistem SHALL mengirim request ke API `/api/v1/auth/login` dengan role yang sudah di-set otomatis dari URL.

#### Scenario: Submit login dari halaman role
- **WHEN** user mengisi email dan password lalu submit di `/login/student`
- **THEN** sistem mengirim POST ke `/api/v1/auth/login` dengan body `{ email, password }` dan role `student` sudah ter-set otomatis

#### Scenario: Login berhasil
- **WHEN** API mengembalikan `access_token`
- **THEN** token disimpan dan user di-redirect ke `/dashboard`

#### Scenario: Login siswa berhasil
- **WHEN** login siswa berhasil dan user diarahkan ke `/dashboard`
- **THEN** sistem MUST menampilkan pengalaman dashboard siswa yang mobile-first, sederhana, dan mudah dipakai untuk alur harian utama

#### Scenario: Login gagal
- **WHEN** API mengembalikan error
- **THEN** pesan error ditampilkan di halaman login tanpa redirect
