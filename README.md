# SMK Queen Al-Falah WordPress

Tema dan plugin pendamping WordPress untuk website SMK Queen Al-Falah. Project menyediakan landing page sekolah, profil, berita, pengumuman, agenda, program keahlian, PPDB, galeri, layanan sekolah, serta Pusat Aplikasi satu pintu.

## Komponen

- `queen-alfalah/` — tema WordPress Queen Al-Falah versi 1.1.1.
- `queen-alfalah-core/` — plugin pendamping versi 1.1.0 untuk tipe konten dan pengaturan sekolah.

## Fitur utama

- Landing page responsif dengan background gambar/GIF yang dapat diganti.
- Identitas, kontak, kepala sekolah, logo, warna, dan konten yang dapat dikelola dari dashboard.
- Berita, pengumuman, agenda, prestasi, guru/tendik, program keahlian, galeri, BKK, dan PPDB.
- Pusat Aplikasi `/aplikasi/` untuk Ujian Online, E-Rapor, E-Perpustakaan, SPMB, dan Gamifikasi Edu.
- Pusat Media privat `/pusat-media/` dengan akun WordPress per Waka/tim/bidang dan pembatasan folder Google Drive per pengguna.
- Struktur ZIP tema dan plugin yang kompatibel dengan pemasang WordPress.
- Dukungan penggunaan offline melalui WordPress dan Laragon.

## Persyaratan

- WordPress 6.2 atau lebih baru.
- PHP 7.4 atau lebih baru.

## Instalasi

1. Buat ZIP folder `queen-alfalah`, kemudian unggah melalui **Tampilan → Tema → Tambah Tema**.
2. Buat ZIP folder `queen-alfalah-core`, kemudian unggah melalui **Plugin → Tambah Plugin**.
3. Aktifkan plugin dan tema.
4. Buka **Sekolah → Pengaturan** untuk melengkapi identitas sekolah.
5. Buka **Pengaturan → Permalink**, lalu simpan ulang permalink.

## Pengembangan lokal

Salin kedua folder ke instalasi WordPress lokal:

```text
wp-content/themes/queen-alfalah
wp-content/plugins/queen-alfalah-core
```

Aktifkan melalui dashboard WordPress. Jangan menyimpan `wp-config.php`, database, kata sandi, atau berkas unggahan pengguna di repository.

## Pusat Media privat

Plugin membuat peran **Waka Sekolah**, **Tim Media**, dan **Bidang Sekolah**, serta halaman `/pusat-media/`. Password memakai autentikasi WordPress dan selalu disimpan sebagai hash oleh WordPress. Akun portal diarahkan ke Pusat Media dan tidak diberi akses ke dashboard administrasi. Administrator membuat satu akun untuk setiap personel/bidang melalui **Pengguna > Tambah Baru**, memilih peran yang sesuai, lalu mengisi nama unit dan ID folder Google Drive pada profil akun.

Koneksi Google Drive memakai service account baca-saja. Aktifkan Google Drive API di Google Cloud, buat service account, bagikan setiap folder root kepada email service account sebagai **Viewer**, lalu simpan JSON kredensial di luar folder publik website. Tambahkan hanya lokasinya ke `wp-config.php`:

```php
define( 'QAF_GOOGLE_DRIVE_CREDENTIALS_PATH', '/lokasi-privat/queen-drive-service-account.json' );
```

Sebagai alternatif, JSON dapat dimasukkan melalui secret/environment deployment ke konstanta `QAF_GOOGLE_DRIVE_CREDENTIALS_JSON`. Jangan commit JSON, private key, username, atau password ke Git. Pengguna hanya dapat menelusuri folder yang dipetakan pada profilnya beserta subfolder dan file turunannya; unduhan diproksi oleh WordPress setelah pemeriksaan login, capability, nonce, dan rantai folder.

## Sinkronisasi perkembangan project

Perubahan utama tetap dikerjakan pada folder `work/queen-alfalah` dan `work/queen-alfalah-core`. Jalankan perintah berikut dari repository untuk menyalin perubahan, membuat commit, dan push ke GitHub:

```powershell
.\sync-project.ps1 -Message "Jelaskan perubahan yang dibuat"
```

Skrip memverifikasi sumber dan target sebelum menyelaraskan folder. Database WordPress, `wp-config.php`, unggahan pengguna, arsip ZIP, serta berkas rahasia tidak ikut dikirim.

## Lisensi

GNU General Public License v2 atau versi yang lebih baru.
