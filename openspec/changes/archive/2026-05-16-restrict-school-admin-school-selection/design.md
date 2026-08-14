## Context

Aplikasi sudah memiliki konsep admin sekolah yang dibatasi ke satu sekolah, tetapi beberapa affordance UI dan payload form belum sepenuhnya mengikuti assignment tersebut. Menu Sekolah masih muncul di navigasi admin sekolah, dan flow yang memakai field sekolah masih berisiko membiarkan user memilih atau mengirim `school_id` manual walaupun scope datanya seharusnya sudah ditentukan oleh akun login.

Perubahan ini menyentuh boundary frontend dan backend: UI harus menghilangkan opsi yang tidak relevan untuk admin sekolah, tetapi enforcement utama tetap harus berada di controller/query layer agar request langsung ke API tidak bisa mengganti konteks sekolah. Karena repo ini memakai Blade + JavaScript yang mengambil profil user dari `/api/v1/auth/me`, desain harus menjaga satu sumber kebenaran untuk school scope sekaligus meminimalkan percabangan perilaku untuk superadmin.

## Goals / Non-Goals

**Goals:**
- Menyembunyikan menu Sekolah untuk admin sekolah tanpa mengubah akses superadmin.
- Membuat form admin yang bergantung pada sekolah otomatis memakai sekolah assignment admin sekolah yang sedang login.
- Menjadikan backend sebagai sumber kebenaran `school_id` untuk request dari admin sekolah.
- Menjaga flow superadmin tetap bisa memilih atau mengelola sekolah secara global bila fitur terkait memang membutuhkannya.

**Non-Goals:**
- Mendesain ulang seluruh navigasi admin.
- Mengubah mekanisme login atau assignment sekolah admin.
- Menambah capability baru di luar penyesuaian perilaku scoped admin yang sudah ada.

## Decisions

### Gunakan role/profile hasil `/auth/me` untuk gating menu sekolah
Sidebar dan menu Blade yang saat ini dibangun berdasarkan data user harus memeriksa role login, lalu tidak merender atau menyembunyikan menu Sekolah untuk `school_admin`. Ini menjaga UI tetap selaras dengan hak akses aktual tanpa memperkenalkan sumber state baru di frontend.

Alternatif yang dipertimbangkan:
- Menyembunyikan menu murni lewat hardcode route-per-page. Ditolak karena lebih mudah drift dari data auth global yang sudah dipakai komponen admin lain.

### Form school-scoped memakai assignment sekolah login sebagai nilai efektif
Untuk create/update flow yang membutuhkan `school_id`, frontend admin sekolah tidak lagi meminta pilihan sekolah secara manual. Bila form masih perlu menampilkan konteks sekolah, tampilkan sebagai nilai terkunci/read-only atau sembunyikan field sepenuhnya, tetapi nilai efektif yang dikirim harus berasal dari profil admin login.

Alternatif yang dipertimbangkan:
- Tetap menampilkan select sekolah lalu preselect nilai default. Ditolak karena masih memberi affordance seolah-olah pilihan bisa diubah dan meningkatkan risiko mismatch UI vs server.

### Backend menimpa atau memvalidasi `school_id` untuk request admin sekolah
Controller dan helper backend yang menerima `school_id` harus menentukan nilai final dari assignment akun login ketika requester adalah `school_admin`. Jika payload membawa `school_id` berbeda, sistem harus menolak atau mengabaikannya sesuai kontrak endpoint, tetapi tidak boleh memakai nilai dari klien sebagai sumber kebenaran.

Alternatif yang dipertimbangkan:
- Mengandalkan frontend untuk selalu mengirim nilai yang benar. Ditolak karena API dapat dipanggil langsung di luar UI.

### Pertahankan perilaku global untuk superadmin di endpoint dan form yang sama
Superadmin tetap melihat menu Sekolah dan tetap dapat memakai form global dengan school selector jika endpoint tersebut memang lintas sekolah. Ini menghindari duplikasi halaman/endpoint terpisah hanya untuk membedakan scoped admin dan superadmin.

Alternatif yang dipertimbangkan:
- Memecah halaman atau endpoint khusus school admin vs superadmin. Ditolak karena menambah maintenance surface untuk perubahan perilaku yang bisa dibedakan dari role saat runtime.

## Risks / Trade-offs

- [Ada lebih dari satu form admin yang memakai `school_id` dengan cara berbeda] → Audit semua halaman admin terkait dan samakan pola pemilihan nilai efektif berbasis user login.
- [UI sudah menyembunyikan field tetapi backend masih mempercayai payload] → Enforce overwrite/validation di controller sebelum persistence.
- [Superadmin dan admin sekolah memakai komponen frontend yang sama] → Branch perilaku hanya pada titik penentuan role dan pertahankan default global untuk superadmin.
- [Beberapa halaman mungkin mengambil daftar sekolah untuk keperluan lain] → Hanya hilangkan pilihan sekolah yang mengontrol scope data admin sekolah; jangan hapus data pendukung yang masih dibutuhkan superadmin.

## Migration Plan

1. Audit navigasi admin dan flow CRUD yang saat ini menampilkan menu Sekolah atau field `school_id`.
2. Update gating UI berdasarkan data auth agar menu Sekolah tidak muncul untuk admin sekolah.
3. Update form JavaScript/Blade agar admin sekolah memakai assignment sekolah login sebagai nilai efektif tanpa selector manual.
4. Tambahkan enforcement backend untuk menimpa atau menolak `school_id` asing pada request admin sekolah.
5. Verifikasi superadmin tetap bisa mengelola sekolah global dan admin sekolah hanya bekerja dalam scope assignment mereka.

Rollback strategy:
- Roll back perubahan UI dan controller bila ditemukan regresi pada flow admin.
- Pertahankan assignment sekolah yang sudah ada karena perubahan ini tidak memperkenalkan migrasi data baru.

## Open Questions

- Form mana saja yang saat ini masih meminta school selection untuk admin sekolah: siswa, guru, kelas, atau modul lain juga?
- Untuk endpoint tertentu, apakah lebih tepat menolak payload `school_id` asing dengan error eksplisit atau diam-diam menimpa dengan assignment user?
