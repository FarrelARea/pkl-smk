## Context

Aplikasi PKL saat ini memiliki satu halaman login di `/login` dengan role selector (4 tombol: Siswa, Guru, Pembimbing, Admin). User harus memilih role sebelum login. Ini adalah SPA-like approach menggunakan Blade + vanilla JS yang memanggil API `/api/v1/auth/login`.

## Goals / Non-Goals

**Goals:**
- Pisahkan login per role ke URL terpisah (`/login/student`, `/login/teacher`, `/login/supervisor`, `/login/admin`)
- Ubah `/login` menjadi halaman pemilihan role (landing page dengan 4 card/link)
- Setiap halaman login role memiliki branding/icon yang sesuai
- Role otomatis di-set dari URL, tidak perlu selector manual

**Non-Goals:**
- Mengubah logic autentikasi backend (API tetap sama)
- Mengubah flow setelah login (redirect ke `/dashboard` tetap sama)
- Menambah registrasi atau forgot password

## Decisions

### 1. Satu Blade view dengan parameter role vs view terpisah per role
**Keputusan**: Gunakan satu Blade view `auth.login-role` yang menerima parameter `$role` dari route. Landing page tetap di view `auth.login` yang di-refactor.

**Alasan**: Menghindari duplikasi 4 view yang hampir identik. Perbedaan hanya di judul, icon, dan warna — bisa di-handle dengan config array di view.

### 2. Route structure
**Keputusan**:
- `GET /login` → landing page pemilihan role
- `GET /login/student` → form login siswa
- `GET /login/teacher` → form login guru
- `GET /login/supervisor` → form login pembimbing
- `GET /login/admin` → form login admin

**Alasan**: URL yang bersih dan bisa di-bookmark. Menggunakan slug yang jelas per role.

### 3. Mapping URL slug ke role value API
**Keputusan**: Mapping di route/controller level:
- `student` → `student`
- `teacher` → `teacher`
- `supervisor` → `company_supervisor`
- `admin` → `school_admin`

**Alasan**: URL menggunakan nama pendek yang user-friendly, sementara API tetap menerima role value yang sudah ada.

## Risks / Trade-offs

- **[Bookmarked old URL]** → `/login` sekarang jadi landing page, bukan form. User yang bookmark `/login` akan melihat halaman pilihan role dulu — ini acceptable karena UX tetap jelas.
- **[4 URL baru]** → Sedikit lebih banyak route, tapi sangat minimal dan terorganisir.
