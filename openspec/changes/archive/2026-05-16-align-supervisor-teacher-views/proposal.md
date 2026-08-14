## Why

Pembimbing perusahaan saat ini belum memiliki pengalaman halaman yang selengkap alur guru, padahal kebutuhan operasional keduanya pada banyak segmen serupa. Menyamakan cakupan fitur ini akan mengurangi gap peran, mempercepat adopsi, dan menurunkan kebutuhan pelatihan tambahan.

## What Changes

- Menambahkan capability dashboard dan halaman kerja pembimbing yang mencakup segmen-segmen utama yang sudah tersedia untuk guru.
- Menyamakan alur inti pembimbing dengan guru untuk daftar siswa binaan, monitoring kegiatan, penilaian, dan tindak lanjut yang relevan.
- Memberikan pembedaan presentasi yang wajar antara guru dan pembimbing agar peran tetap terasa berbeda tanpa mengubah kapabilitas inti.
- Menyesuaikan navigasi, data loading, dan gating berbasis role agar pembimbing dapat mengakses seluruh segmen yang dibutuhkan.

## Capabilities

### New Capabilities
- `supervisor-workspace-parity`: Pengalaman pembimbing menyediakan cakupan segmen inti yang setara dengan workspace guru, dengan presentasi yang tetap sesuai konteks pembimbing.

### Modified Capabilities
- `role-based-login`: Setelah login sebagai pembimbing, pengguna diarahkan ke pengalaman kerja pembimbing yang memiliki cakupan segmen inti setara guru.

## Impact

- Affected code: role-specific Blade views, sidebar/navigation gating, frontend auth helpers, inline page scripts, dan controller/API endpoints yang dipakai halaman pembimbing.
- APIs: kemungkinan perlu memperluas atau menyesuaikan endpoint pembimbing agar menyediakan data yang sudah tersedia pada workflow guru.
- Systems: pengalaman role pembimbing di web app dan perilaku redirect/gating pasca-login.
