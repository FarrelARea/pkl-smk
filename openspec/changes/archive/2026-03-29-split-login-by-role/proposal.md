## Why

Saat ini semua role (siswa, guru, pembimbing, admin) login melalui satu halaman `/login` dan harus memilih role secara manual. Ini membingungkan dan tidak efisien. Memisahkan halaman login per role memberikan UX yang lebih bersih dan URL yang bisa di-bookmark langsung.

## What Changes

- Hapus halaman login tunggal di `/login` yang memiliki role selector
- Buat halaman login terpisah per role:
  - `/login/student` — login khusus siswa
  - `/login/teacher` — login khusus guru
  - `/login/supervisor` — login khusus pembimbing
  - `/login/admin` — login khusus admin
- Ubah `/login` menjadi halaman landing/pilihan role yang mengarahkan ke masing-masing halaman login
- Setiap halaman login role memiliki style/branding yang sesuai dengan role tersebut
- Role otomatis di-set berdasarkan URL, tidak perlu pilih manual lagi

## Capabilities

### New Capabilities
- `role-based-login`: Halaman login terpisah per role dengan auto-set role dari URL, dan landing page pemilihan role di `/login`

### Modified Capabilities

## Impact

- **Routes**: Tambah route `/login/{role}` untuk student, teacher, supervisor, admin
- **Views**: Buat blade view baru per role atau satu view dengan parameter role
- **Existing login**: `/login` berubah jadi halaman pilihan role (bukan form login langsung)
- **JS Auth logic**: Login form tidak perlu role selector lagi, role dikirim dari URL parameter
