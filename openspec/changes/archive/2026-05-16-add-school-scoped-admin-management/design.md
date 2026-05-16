## Context

Aplikasi saat ini membedakan akses global superadmin dan role sekolah lain, tetapi belum memiliki admin sekolah yang dapat mengelola data operasional hanya untuk satu sekolah. Sebagian besar halaman admin memakai pola Blade + JavaScript yang memanggil API `/api/v1`, sehingga pembatasan akses tidak cukup di UI dan harus diterapkan konsisten pada autentikasi, middleware/otorisasi, dan query backend.

Perubahan ini juga berpotensi menyentuh banyak entitas domain karena data seperti guru, siswa, kelas, penugasan, dan aktivitas internship secara praktis berada dalam konteks sekolah. Karena itu desain perlu menetapkan satu sumber kebenaran untuk school scoping dan aturan fallback untuk data lama yang belum punya `school_id`.

## Goals / Non-Goals

**Goals:**
- Memungkinkan superadmin membuat dan mengelola akun admin sekolah dari area admin.
- Menetapkan satu relasi sekolah untuk setiap admin sekolah dan memakai relasi itu sebagai batas akses utama.
- Menerapkan school scoping secara server-side untuk endpoint dan query admin agar data lintas sekolah tidak bocor.
- Memastikan admin sekolah tidak bisa membuat, mengubah, atau menghapus entitas sekolah di luar penugasan mereka, termasuk menambah sekolah baru.
- Menjaga pengalaman login admin tetap sederhana dengan entry point `/login/admin` yang sama.

**Non-Goals:**
- Mengubah pengalaman login role non-admin.
- Mendesain ulang seluruh UI admin di luar kebutuhan halaman pengelolaan admin sekolah.
- Memperkenalkan multi-school assignment untuk satu admin.
- Menyelesaikan data governance historis di luar kebutuhan minimum agar school scoping bisa diterapkan aman.

## Decisions

### Gunakan role yang sudah ada dengan school assignment eksplisit
Admin sekolah tetap menggunakan role `school_admin` yang sudah ada, tetapi setiap akun harus memiliki `school_id` terisi. Ini menghindari ledakan role baru, menjaga kompatibilitas dengan login admin yang ada, dan memusatkan perbedaan perilaku pada scope data, bukan pada nama role.

Alternatif yang dipertimbangkan:
- Membuat role baru terpisah untuk admin sekolah. Ditolak karena akan menggandakan alur login, middleware, dan logika menu untuk kemampuan yang secara fungsional masih turunan dari admin.

### Terapkan pembatasan akses di lapisan query dan otorisasi backend
Setiap endpoint admin yang membaca atau menulis data sekolah harus menentukan school scope dari user yang sedang login. Superadmin melewati pembatasan ini, sementara admin sekolah wajib difilter berdasarkan `school_id` mereka. Validasi write operations juga harus menolak payload yang mencoba menyetel `school_id` lain atau membuat entitas sekolah baru.

Alternatif yang dipertimbangkan:
- Hanya menyembunyikan data di Blade/JavaScript. Ditolak karena API tetap bisa diakses langsung dan berisiko membocorkan data.
- Menambahkan global scope ke semua model. Ditunda karena terlalu invasif untuk perubahan awal dan berisiko memengaruhi area non-admin yang belum siap.

### Tambahkan halaman manajemen admin sekolah yang hanya terlihat untuk superadmin
UI baru di area `/admin` dipakai superadmin untuk membuat, melihat, mengubah, dan menonaktifkan admin sekolah serta mengaitkannya ke sekolah tertentu. Halaman ini menjadi titik kontrol tunggal untuk assignment admin-ke-sekolah.

Alternatif yang dipertimbangkan:
- Mengelola admin sekolah dari halaman schools yang ada. Ditolak karena mencampur lifecycle sekolah dan lifecycle user admin, serta menyulitkan pembatasan hak akses.

### Tambahkan `school_id` pada entitas yang perlu scoping eksplisit, lalu backfill dari relasi yang sudah ada
Untuk tabel yang query admin-nya tidak selalu bisa diturunkan aman dari join yang ada, tambahkan `school_id` dan isi data lama melalui migrasi/backfill. Untuk entitas yang sudah punya jalur relasi tegas ke sekolah, scoping bisa memakai join/query tanpa duplikasi kolom tambahan.

Alternatif yang dipertimbangkan:
- Menambahkan `school_id` ke semua tabel terkait. Ditolak untuk perubahan awal karena mahal, rawan inkonsistensi, dan belum tentu perlu.
- Mengandalkan inferensi relasi di semua query tanpa kolom tambahan. Ditolak bila inferensi terlalu kompleks atau ambigu untuk operasi tulis dan filter tabel besar.

### Bedakan kemampuan superadmin vs admin sekolah di navigasi dan endpoint
Navigasi client-side boleh menyembunyikan menu yang tidak relevan untuk admin sekolah, tetapi pembeda utama harus berada di route protection dan controller/service layer. Endpoint create/update/delete school wajib tetap khusus superadmin.

Alternatif yang dipertimbangkan:
- Membiarkan admin sekolah melihat menu yang nanti gagal di API. Ditolak karena membingungkan dan meningkatkan noise error.

## Risks / Trade-offs

- [Data lama belum konsisten punya relasi sekolah] → Audit tabel yang disentuh halaman admin dan siapkan migrasi/backfill minimum sebelum enforcement ketat diaktifkan.
- [Beberapa query admin saat ini mungkin implicit/global] → Identifikasi endpoint admin utama dan tambahkan filter `school_id` secara eksplisit, lalu uji akses superadmin vs admin sekolah.
- [Reuse role `school_admin` dapat membingungkan antara admin global dan scoped] → Perlakukan superadmin sebagai satu-satunya admin global dan dokumentasikan bahwa `school_admin` selalu scoped ke satu sekolah.
- [Perubahan lintas banyak modul meningkatkan peluang celah otorisasi] → Mulai dari daftar endpoint admin yang dipakai Blade pages dan verifikasi satu per satu terhadap scoped access.
- [Penambahan `school_id` di beberapa tabel dapat memperumit migrasi produksi] → Buat migrasi additive dan backfill idempotent, lalu tahan constraint ketat sampai data lama aman.

## Migration Plan

1. Tambahkan dukungan data untuk admin sekolah dan `school_id` pada entitas yang memang perlu scoping eksplisit.
2. Backfill data lama berdasarkan relasi sekolah yang sudah ada atau tandai record yang perlu penanganan manual.
3. Tambahkan halaman dan endpoint manajemen admin sekolah untuk superadmin.
4. Terapkan school scoping pada endpoint admin dan sembunyikan menu/aksi yang tidak diizinkan untuk admin sekolah.
5. Verifikasi login admin, akses superadmin, akses admin sekolah, dan pembatasan create school sebelum rilis.

Rollback strategy:
- Rollback kode aplikasi untuk menonaktifkan enforcement scoped access jika ditemukan regressions besar.
- Pertahankan migrasi additive bila sudah terlanjur dipakai data produksi; rollback schema hanya aman bila belum ada data baru bergantung pada kolom tersebut.

## Open Questions

- Tabel domain mana saja yang benar-benar perlu kolom `school_id` baru versus cukup difilter lewat relasi saat query?
- Apakah admin sekolah boleh mengubah profil sekolahnya sendiri (misalnya nama/alamat) atau seluruh CRUD school harus tetap superadmin-only?
- Apakah akun admin sekolah perlu status aktif/nonaktif terpisah dari mekanisme user yang ada?
