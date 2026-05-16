## Why

Admin sekolah sudah dibatasi ke satu sekolah, tetapi UI admin masih menampilkan menu Sekolah dan form tertentu masih meminta input sekolah secara manual. Perubahan ini dibutuhkan sekarang agar pengalaman admin sekolah konsisten dengan scope aksesnya dan tidak membuka peluang memilih atau mengirim `school_id` yang tidak sesuai dengan assignment akun.

## What Changes

- Menghapus atau menyembunyikan menu Sekolah untuk admin sekolah, sambil mempertahankan akses penuh menu tersebut untuk superadmin.
- Mengubah form admin yang membutuhkan konteks sekolah agar admin sekolah tidak perlu memilih sekolah secara manual.
- Memastikan input sekolah pada create/update flow admin sekolah otomatis memakai sekolah yang terhubung ke akun yang sedang login.
- Menolak atau mengabaikan payload `school_id` yang tidak sesuai ketika request dikirim oleh admin sekolah, sehingga scope sekolah tetap ditentukan server-side.

## Capabilities

### New Capabilities
- None.

### Modified Capabilities
- `school-scoped-admin-management`: Perilaku navigasi dan form admin sekolah harus mengikuti assignment sekolah akun login, termasuk menyembunyikan manajemen sekolah dan otomatis mengikat input sekolah.

## Impact

- Affected code: navigasi/layout Blade admin, halaman admin yang memiliki field sekolah, helper JavaScript admin, controller/API admin yang menerima `school_id`, dan logika auth/me yang dipakai untuk gating UI.
- Affected behavior: superadmin tetap dapat melihat menu Sekolah dan memilih sekolah secara manual bila diperlukan, sedangkan admin sekolah hanya bekerja dalam konteks sekolah assignment mereka.
- Security impact: server harus tetap menjadi sumber kebenaran untuk `school_id` agar admin sekolah tidak bisa menyisipkan scope sekolah lain lewat request langsung.
