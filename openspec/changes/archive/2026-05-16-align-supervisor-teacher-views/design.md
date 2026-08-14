## Context

Aplikasi ini sudah memiliki workspace guru yang dipakai untuk memantau siswa, meninjau aktivitas, dan menjalankan tindak lanjut berbasis role. Workspace pembimbing perusahaan perlu mengejar parity pada segmen inti tersebut, tetapi tetap mempertahankan identitas role melalui label, copy, dan fokus data yang lebih relevan ke konteks perusahaan. Implementasi harus mengikuti arsitektur saat ini: Blade pages role-specific, navigasi client-side berbasis `/api/v1/auth/me`, dan data utama yang datang dari API `/api/v1`.

## Goals / Non-Goals

**Goals:**
- Memberikan workspace pembimbing dengan cakupan segmen inti setara workspace guru.
- Meminimalkan duplikasi perilaku dengan meniru alur guru yang sudah terbukti, sambil membedakan presentasi visual dan copy role pembimbing.
- Menjaga redirect login, sidebar gating, dan pemuatan data tetap konsisten dengan pola role-based yang ada.
- Mengidentifikasi kebutuhan API pembimbing yang belum lengkap dan menutup gap yang menghalangi parity UI.

**Non-Goals:**
- Mendesain ulang total seluruh dashboard guru atau pembimbing.
- Menyatukan kedua role menjadi satu view generik tunggal jika hal itu mempersulit copy, label, atau fokus data role-specific.
- Mengubah model otorisasi dasar atau menambah role baru.
- Menambah kapabilitas bisnis baru di luar segmen yang sudah tersedia pada workflow guru.

## Decisions

- Gunakan workspace guru sebagai baseline perilaku, lalu buat versi pembimbing yang mengikuti struktur segmen, aksi, dan urutan informasi yang sama.
  - Rationale: kebutuhan user menyebut fiturnya pada dasarnya sama, sehingga parity tercepat dan paling aman datang dari menyamakan flow yang sudah ada.
  - Alternative considered: membuat dashboard pembimbing baru dari nol. Ditolak karena berisiko menghasilkan gap fitur baru dan membutuhkan validasi UX lebih besar.

- Pertahankan view dan entry point role-specific, bukan mengganti keduanya dengan satu template bersama sepenuhnya.
  - Rationale: user meminta ada improvisasi agar guru dan pembimbing tetap terasa berbeda. Variasi copy, ikon, heading, warna aksen, dan penyusunan konteks bisa dilakukan tanpa mengorbankan parity fitur.
  - Alternative considered: satu Blade/template generik untuk guru dan pembimbing. Ditunda kecuali reuse-nya benar-benar jelas saat implementasi, agar tidak memaksakan abstraksi terlalu dini.

- Samakan segment/navigation model pembimbing dengan guru pada lapisan sidebar dan routing view, lalu isi tiap segmen dengan endpoint pembimbing yang ekuivalen atau adaptasi endpoint yang ada.
  - Rationale: parity yang terlihat oleh user terutama ditentukan oleh ketersediaan segmen dan transisi antarhalaman, bukan hanya komponen visual.
  - Alternative considered: hanya menambah beberapa widget di dashboard pembimbing. Ditolak karena tidak memenuhi permintaan parity "from all of the segment".

- Jika ada gap data pembimbing, perluas controller/API pembimbing yang ada agar mengembalikan bentuk data yang dibutuhkan UI, daripada meminjam endpoint guru dengan pengecualian role.
  - Rationale: menjaga batas otorisasi dan domain tetap jelas untuk role pembimbing.
  - Alternative considered: membuka endpoint guru untuk pembimbing. Ditolak karena memperbesar coupling dan risiko authorization drift.

## Risks / Trade-offs

- [Ada perbedaan halus antara tanggung jawab guru dan pembimbing] → Mitigation: parity difokuskan pada segmen inti, sementara label, copy, dan subset aksi disesuaikan dengan konteks pembimbing.
- [UI parity mendorong duplikasi Blade/JS] → Mitigation: reuse hanya pada potongan yang benar-benar sama, terutama utilitas dan pola rendering yang sudah ada, tanpa abstraksi besar yang prematur.
- [Endpoint pembimbing mungkin belum mengembalikan data selengkap workflow guru] → Mitigation: audit tiap segmen terhadap kebutuhan data guru dan tambahkan field/endpoint pembimbing yang ekuivalen sebelum merapikan UI.
- [Perubahan redirect/gating role dapat memengaruhi pengalaman login pembimbing] → Mitigation: validasi alur login supervisor, sidebar visibility, dan tiap segmen setelah implementasi.
