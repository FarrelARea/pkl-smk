## Context

Panel siswa pada `/dashboard` sudah memiliki fungsi utama yang diperlukan, tetapi susunan konten dan aksi masih terasa padat untuk penggunaan mobile. Implementasi saat ini membagi markup ke `resources/views/dashboard/student.blade.php`, sementara logika data dan action handler siswa masih hidup di `resources/views/dashboard.blade.php`, jadi perubahan UI perlu menjaga integrasi dengan struktur itu tanpa mengubah kontrak API yang sudah dipakai.

## Goals / Non-Goals

**Goals:**
- Menjadikan panel dashboard siswa mobile-first dengan urutan konten yang lebih mudah dipindai di layar kecil.
- Menonjolkan alur harian utama siswa: melihat status magang, cek status absen hari ini, dan menjalankan aksi utama.
- Menyederhanakan quick actions dan ringkasan log agar target tap lebih nyaman dan navigasi lebih jelas.
- Mempertahankan flow JavaScript dan endpoint yang sudah ada agar perubahan tetap ringan dan cepat diimplementasikan.

**Non-Goals:**
- Mengubah endpoint API, payload, atau aturan backend untuk dashboard siswa.
- Mendesain ulang dashboard role lain selain siswa.
- Menambah fitur bisnis baru di luar penyusunan ulang UI, copy, dan state presentasi.
- Memecah seluruh JavaScript dashboard ke modul frontend baru.

## Decisions

### Gunakan penyusunan ulang markup student partial sebagai pusat perubahan
Perubahan utama akan difokuskan pada `resources/views/dashboard/student.blade.php`, karena partial itu sudah menjadi boundary UI untuk panel siswa. Ini menjaga scope tetap kecil dan menghindari efek samping ke role lain.

Alternatif yang dipertimbangkan:
- Mendesain ulang seluruh `resources/views/dashboard.blade.php`: ditolak karena terlalu luas dan berisiko mempengaruhi admin, guru, dan supervisor.
- Membuat view baru khusus mobile: ditolak karena menambah duplikasi markup dan maintenance.

### Pertahankan data flow JavaScript yang ada, hanya selaraskan dengan struktur DOM baru
Function loader dan action handler yang ada akan tetap dipakai, tetapi selector DOM dan target rendering akan disesuaikan bila struktur kartu, section, atau daftar berubah. Ini menjaga perubahan tetap fungsional tanpa menyentuh kontrak API.

Alternatif yang dipertimbangkan:
- Rewrite logic dashboard siswa ke file JS terpisah: ditolak untuk perubahan ini karena menambah scope dan memperlambat delivery.
- Menyisakan DOM lama lalu menumpuk styling baru di atasnya: ditolak karena berpotensi mempertahankan kerumitan lama.

### Prioritaskan hirarki satu kolom di mobile lalu expand di breakpoint lebih besar
Urutan default mobile akan menampilkan status utama, CTA penting, status absen hari ini, lalu informasi pendukung seperti ringkasan kehadiran dan log terbaru. Di layar lebih besar, layout dapat melebar menjadi grid ringan tanpa mengubah prioritas konten.

Alternatif yang dipertimbangkan:
- Mempertahankan grid multi-kolom dari awal: ditolak karena membuat prioritas konten di mobile kurang jelas.
- Menggunakan pola card carousel: ditolak karena menyembunyikan aksi penting di balik gesture tambahan.

### Sederhanakan action surface dan state feedback
Quick actions akan tetap sederhana dengan label yang jelas, deskripsi singkat, dan target tap besar. Loading, empty state, dan status feedback akan dibuat lebih mudah dipahami supaya siswa tidak perlu menebak langkah berikutnya.

Alternatif yang dipertimbangkan:
- Menambah lebih banyak dekorasi visual untuk tiap kartu: ditolak karena bertentangan dengan tujuan sederhana dan mudah dipakai.
- Menyembunyikan secondary actions ke dropdown: ditolak karena memperlambat akses di mobile.

## Risks / Trade-offs

- [Perubahan struktur DOM memutus selector JavaScript lama] → Cocokkan ulang ID/selector yang dipakai loader dan action handler setelah markup disederhanakan.
- [UI lebih sederhana tetapi informasi terasa berkurang] → Pertahankan semua fungsi inti dan tampilkan ringkasan penting lebih dahulu, bukan menghapus capability utama.
- [Modal absen masih cukup padat di perangkat kecil] → Fokuskan perubahan utama pada dashboard landing surface, lalu rapikan spacing dan button sizing modal seperlunya agar tetap usable.
- [Perubahan visual pada partial siswa tidak selaras dengan layout aplikasi umum] → Gunakan utilitas Tailwind dan pola card yang sudah ada di repo agar tetap terasa konsisten.
