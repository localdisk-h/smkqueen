# Queen Al-Falah Core

Companion plugin resmi untuk tema **Queen Al-Falah**. Plugin memisahkan model konten dan data sekolah dari lapisan tampilan, sehingga program keahlian, pengumuman, agenda, dan data kelembagaan tetap tersedia ketika tema diganti.

Versi: **1.0.0**  
WordPress minimum: **6.2**  
PHP minimum: **7.4**  
Lisensi: **GPL-2.0-or-later**

## Fitur

- 12 custom post type (CPT) sekolah dan 14 taksonomi publik.
- Meta terstruktur yang terdaftar pada Metadata API dan REST API.
- Meta box klasik/Gutenberg dengan sanitasi per tipe, nonce, dan pemeriksaan kemampuan pengguna.
- Pengaturan identitas, visi-misi, kontak, lokasi, pendaftaran, dan media sosial.
- Kolom admin dan pengurutan data terstruktur.
- Arsip agenda terurut berdasarkan tanggal mulai.
- Arsip pengumuman publik menyembunyikan item kedaluwarsa tanpa mengubah daftar admin.
- Importer demo satu klik yang aman, idempoten, dan tidak menimpa konten pengguna.
- Data dipertahankan saat plugin dihapus.

## Instalasi

1. Unggah folder `queen-alfalah-core` ke `wp-content/plugins/` atau unggah ZIP melalui **Plugin > Tambah Plugin**.
2. Aktifkan **Queen Al-Falah Core**.
3. Aktifkan tema **Queen Al-Falah** atau child theme-nya.
4. Buka **Sekolah > Pengaturan** dan periksa seluruh identitas resmi.
5. Bila memerlukan kerangka awal, buka **Sekolah > Penyiapan Demo** lalu klik **Siapkan Situs Demo**.
6. Tinjau draf dan data awal sebelum situs dipublikasikan.
7. Simpan ulang **Pengaturan > Permalink** hanya bila rute masih menampilkan 404 setelah perubahan infrastruktur.

Aktivasi plugin mendaftarkan CPT/taksonomi, menambahkan default secara non-destruktif, dan menyegarkan rewrite rules. Deaktivasi melepas model konten untuk request berikutnya dan menyegarkan rewrite rules. Request biasa dan importer tidak melakukan flush rewrite.

## Model konten

| CPT | Slug publik | Kegunaan |
|---|---|---|
| `qaf_program` | `program-keahlian` | Program/kompetensi keahlian |
| `qaf_teacher` | `guru-tendik` | Guru dan tenaga kependidikan |
| `qaf_notice` | `pengumuman` | Pengumuman resmi |
| `qaf_agenda` | `agenda` | Agenda bertanggal |
| `qaf_achievement` | `prestasi` | Prestasi sekolah/warga sekolah |
| `qaf_extra` | `ekstrakurikuler` | Kegiatan ekstrakurikuler |
| `qaf_service` | `layanan` | Akses layanan digital |
| `qaf_gallery` | `galeri` | Album foto/video |
| `qaf_partner` | `mitra-industri` | Mitra sekolah/industri |
| `qaf_vacancy` | `lowongan-kerja` | Lowongan terverifikasi BKK |
| `qaf_alumni` | `alumni` | Kisah alumni dengan persetujuan |
| `qaf_facility` | `sarana-prasarana` | Sarana dan prasarana |

Semua CPT mendukung judul, editor, ringkasan, gambar unggulan, urutan halaman, revisi, custom fields, menu, arsip, dan REST API.

## Kontrak meta tema

Seluruh key menggunakan awalan privat `_qaf_` dan satu nilai per post:

- Program: `_qaf_program_code`, `_qaf_program_head`, `_qaf_program_gender`, `_qaf_competencies`, `_qaf_careers`.
- Guru/tendik: `_qaf_role`, `_qaf_subject`, `_qaf_order`.
- Pengumuman: `_qaf_priority`, `_qaf_expiry`, `_qaf_file_url`.
- Agenda: `_qaf_start_date`, `_qaf_end_date`, `_qaf_location`.
- Prestasi: `_qaf_level`, `_qaf_achievement_date`, `_qaf_recipient`.
- Ekstrakurikuler: `_qaf_schedule`, `_qaf_coach`.
- Layanan: `_qaf_external_url`, `_qaf_icon_name`, `_qaf_open_new`.
- Galeri: `_qaf_video_url`, `_qaf_album_date`.
- Mitra: `_qaf_partner_url`, `_qaf_partner_sector`.
- Lowongan: `_qaf_deadline`, `_qaf_company`, `_qaf_apply_url`.
- Alumni: `_qaf_graduation_year`, `_qaf_current_role`.
- Sarana: `_qaf_capacity`, `_qaf_facility_status`.

Tanggal memakai `YYYY-MM-DD`; waktu lokal memakai `YYYY-MM-DDTHH:MM`. URL dibatasi ke HTTP/HTTPS. Nilai pilihan divalidasi terhadap daftar yang disediakan plugin.

Contoh penggunaan dari tema:

```php
$start = get_post_meta( get_the_ID(), '_qaf_start_date', true );
$name  = function_exists( 'qaf_core_get_setting' )
	? qaf_core_get_setting( 'school_name', get_bloginfo( 'name' ) )
	: get_bloginfo( 'name' );
```

## Pengaturan sekolah

Semua pengaturan disimpan sebagai satu option `qaf_core_settings`. Default dipasang dengan `add_option`, sehingga aktivasi ulang tidak menimpa nilai yang sudah disunting. Kelompok data mencakup:

- nama resmi, nama singkat, moto, NPSN, tanggal berdiri, akreditasi, dan yayasan;
- visi dan misi;
- alamat, telepon, email, koordinat, dan tautan peta;
- URL pendaftaran;
- Facebook, Instagram, YouTube, dan TikTok.

Tautan atau akun yang belum resmi sebaiknya dikosongkan. Hak akses halaman pengaturan dapat disesuaikan melalui filter `qaf_core_manage_settings_capability`; default-nya `manage_options`.

## Importer demo

Importer tersedia di **Sekolah > Penyiapan Demo** dan hanya dapat dijalankan melalui request POST oleh pengguna dengan kemampuan pengelolaan plugin. Request dilindungi nonce. Importer:

1. membuat Beranda, Berita, Profil, Sambutan, Visi-Misi, Sejarah, Struktur Organisasi, Kesiswaan, Informasi, PPDB, BKK, dan Kontak;
2. mengatur Beranda statis dan halaman Berita;
3. membuat empat program yang namanya telah menjadi data awal tema: TJKT, MPLB, DKV, dan Layanan Kesehatan;
4. membuat tujuh kerangka ekstrakurikuler sebagai **draf** untuk verifikasi;
5. menerbitkan enam entri akses layanan yang hanya mengarah ke halaman/arsip lokal atau URL pendaftaran yang dikonfigurasi;
6. membuat draf berita, pengumuman, dan agenda dengan label jelas;
7. membuat empat menu dan hanya mengisi lokasi tema yang masih kosong.

Idempotensi dijaga dengan meta privat `_qaf_demo_key` serta slug stabil. Import ulang memakai kembali item yang ditemukan, termasuk item pengguna dengan slug/objek yang sama. Konten yang sudah ada tidak diperbarui, status item di Sampah tidak dipulihkan, menu yang sudah ditetapkan tidak diganti, dan tidak ada data yang dihapus.

Data operasional, nama personal, jadwal, pembina, mitra, lowongan, statistik, atau klaim prestasi tidak diterbitkan otomatis. Administrator tetap wajib memeriksa nomenklatur, kurikulum, ketentuan peserta, tautan, periode, izin foto, dan semua fakta bertanggal.

## Perilaku arsip

- Main query arsip `qaf_agenda` di front-end memakai `_qaf_start_date` urut naik.
- Main query arsip `qaf_notice` di front-end menampilkan item tanpa tanggal kedaluwarsa, tanggal kosong, atau tanggal kedaluwarsa yang masih hari ini/masa depan.
- Admin, REST API, singular, pencarian, dan secondary query tidak diubah oleh aturan tersebut.

## Keamanan dan privasi

- Meta box menyimpan data hanya setelah nonce valid, bukan autosave/revisi, dan pengguna dapat `edit_post`.
- REST meta memerlukan kemampuan menyunting post terkait.
- Settings API melakukan sanitasi sesuai tipe data.
- Importer memerlukan kemampuan pengaturan, nonce, dan request POST.
- URL dibatasi ke protokol HTTP/HTTPS.
- Jangan memasukkan NIK, NISN, alamat rumah, nomor pribadi, data kesehatan, atau foto tanpa dasar izin yang sesuai.

## Penghapusan plugin

`uninstall.php` sengaja tidak menghapus post, term, menu, option, maupun meta. Data sekolah adalah catatan institusional dan harus tetap tersedia setelah plugin dinonaktifkan atau dihapus. Untuk penghapusan permanen, ekspor cadangan lebih dahulu lalu hapus data secara eksplisit melalui alat administrasi WordPress.

## Pemecahan masalah

**Menu CPT tidak terlihat**  
Pastikan plugin aktif. CPT ditempatkan di bawah menu admin **Sekolah**.

**Arsip menampilkan 404**  
Pastikan plugin aktif, lalu simpan ulang struktur permalink satu kali.

**Agenda tidak muncul di arsip**  
Pastikan statusnya Terbit dan field Mulai berisi format tanggal-waktu yang valid.

**Pengumuman hilang dari arsip publik**  
Periksa field Berlaku Sampai. Item kedaluwarsa tetap tersedia di admin dan URL singularnya, tetapi tidak dimasukkan ke arsip aktif.

**Importer tidak menetapkan menu**  
Aktifkan tema Queen Al-Falah dan jalankan importer lagi. Lokasi yang sudah berisi menu sengaja tidak ditimpa.

## Pengembangan

Tidak ada dependency build atau library eksternal. Jalankan pemeriksaan sintaks untuk semua berkas PHP sebelum rilis:

```sh
find queen-alfalah-core -name '*.php' -exec php -l {} \;
```

## Changelog

### 1.0.0 — 2026-07-13

- Rilis awal model konten, meta, taksonomi, pengaturan, admin list, filter arsip, dan importer idempoten.
- Menambahkan dokumentasi keamanan, privasi, serta kebijakan retensi data.

