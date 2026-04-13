## 1. Routes

- [x] 1.1 Ubah route `/login` di `routes/web.php` menjadi landing page pemilihan role
- [x] 1.2 Tambah route `/login/{role}` dengan validasi role (student, teacher, supervisor, admin) dan redirect ke `/login` jika role tidak valid

## 2. Landing Page (Pilihan Role)

- [x] 2.1 Refactor `resources/views/auth/login.blade.php` menjadi halaman pemilihan role dengan 4 card yang link ke `/login/student`, `/login/teacher`, `/login/supervisor`, `/login/admin`

## 3. Role-Specific Login Page

- [x] 3.1 Buat view `resources/views/auth/login-role.blade.php` dengan form login (email + password) tanpa role selector, menerima variabel `$role`, `$roleLabel`, `$roleIcon`
- [x] 3.2 Implementasi config array mapping di route/view: slug → role API value, label, icon
- [x] 3.3 Implementasi JS login submission yang mengirim role otomatis dari variabel Blade ke API `/api/v1/auth/login`

## 4. Testing

- [x] 4.1 Verifikasi semua 4 halaman login role bisa diakses dan menampilkan branding yang benar
- [x] 4.2 Verifikasi `/login/invalid` redirect ke `/login`
- [x] 4.3 Verifikasi login berhasil dari setiap halaman role
