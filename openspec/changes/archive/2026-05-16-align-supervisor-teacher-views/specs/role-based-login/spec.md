## MODIFIED Requirements

### Requirement: Login submission dengan auto role
Ketika user submit form login di halaman role-specific, sistem SHALL mengirim request ke API `/api/v1/auth/login` dengan role yang sudah di-set otomatis dari URL.

#### Scenario: Submit login dari halaman role
- **WHEN** user mengisi email dan password lalu submit di `/login/student`
- **THEN** sistem mengirim POST ke `/api/v1/auth/login` dengan body `{ email, password }` dan role `student` sudah ter-set otomatis

#### Scenario: Login admin sekolah berhasil
- **WHEN** admin sekolah mengirim login dari `/login/admin` dan API mengembalikan `access_token`
- **THEN** token disimpan dan user di-redirect ke `/dashboard` dengan hak akses yang nanti dibatasi oleh assignment sekolah akun tersebut

#### Scenario: Login superadmin berhasil
- **WHEN** superadmin mengirim login dari `/login/admin` dan API mengembalikan `access_token`
- **THEN** token disimpan dan user di-redirect ke `/dashboard` dengan akses global sesuai aturan superadmin

#### Scenario: Login pembimbing berhasil
- **WHEN** pembimbing mengirim login dari `/login/supervisor` dan API mengembalikan `access_token`
- **THEN** token disimpan dan user di-redirect ke workspace pembimbing yang menyediakan segmen inti setara workspace guru

#### Scenario: Login gagal
- **WHEN** API mengembalikan error
- **THEN** pesan error ditampilkan di halaman login tanpa redirect
