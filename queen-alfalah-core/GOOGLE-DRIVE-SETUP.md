# Penyiapan Google Drive untuk Pusat Media

Folder induk telah dibuat pada Google Drive sekolah:

- Nama: `SMK Queen Al-Falah - Pusat Media`
- ID: `1N0w6Y9e2p5IYn_ipLLcR7hG2ApDT2KK9`
- URL: <https://drive.google.com/drive/folders/1N0w6Y9e2p5IYn_ipLLcR7hG2ApDT2KK9>
- Kategori: `Waka`, `Guru`, dan `Tendik`

Plugin tidak dapat memakai sesi Google Drive milik Codex secara langsung. WordPress memerlukan kredensial Google API sendiri agar koneksi tetap berjalan ketika Codex dan administrator sedang tidak membuka browser.

## Pilihan yang direkomendasikan: OAuth pemilik My Drive

1. Buka Google Cloud Console dan pilih/buat project sekolah.
2. Aktifkan Google Drive API.
3. Konfigurasikan OAuth consent screen untuk penggunaan internal/sekolah.
4. Buat OAuth Client ID untuk aplikasi web.
5. Lakukan authorization-code flow dengan scope `https://www.googleapis.com/auth/drive` dan `access_type=offline`.
6. Simpan client ID, client secret, dan refresh token hanya di `wp-config.php`.

Tambahkan sebelum baris `/* That's all, stop editing! */`:

```php
define( 'QAF_GOOGLE_DRIVE_ROOT_FOLDER_ID', '1N0w6Y9e2p5IYn_ipLLcR7hG2ApDT2KK9' );
define( 'QAF_GOOGLE_DRIVE_OAUTH_CLIENT_ID', 'CLIENT_ID.apps.googleusercontent.com' );
define( 'QAF_GOOGLE_DRIVE_OAUTH_CLIENT_SECRET', 'CLIENT_SECRET' );
define( 'QAF_GOOGLE_DRIVE_OAUTH_REFRESH_TOKEN', 'REFRESH_TOKEN' );
```

Dokumentasi resmi:

- OAuth aplikasi server: <https://developers.google.com/identity/protocols/oauth2/web-server>
- Upload Google Drive: <https://developers.google.com/workspace/drive/api/guides/manage-uploads>

## Alternatif: service account pada Shared Drive

Service account tidak memiliki kuota penyimpanan dan tidak dapat memiliki file. Karena itu, mode ini hanya disarankan untuk Google Workspace Shared Drive.

1. Buat service account dan aktifkan Google Drive API.
2. Tambahkan email service account sebagai `Content manager` pada Shared Drive.
3. Buat folder induk pada Shared Drive.
4. Simpan JSON key di luar web root dan repository.
5. Gunakan konfigurasi:

```php
define( 'QAF_GOOGLE_DRIVE_ROOT_FOLDER_ID', 'ID_FOLDER_INDUK_SHARED_DRIVE' );
define( 'QAF_GOOGLE_DRIVE_CREDENTIALS_PATH', 'C:/lokasi-privat/queen-drive-service-account.json' );
```

Dokumentasi resmi Shared Drive:

<https://developers.google.com/workspace/drive/api/guides/about-shareddrives>

## Membuat akun Pusat Media

1. Buka **Pengguna > Tambah Baru**.
2. Buat username unik dan password kuat untuk setiap personel.
3. Pilih salah satu role:
   - Waka Sekolah
   - Guru
   - Tenaga Kependidikan
4. Isi **Unit / Jabatan**.
5. Biarkan **ID Folder Google Drive** kosong.

Saat login pertama ke `/pusat-media/`, plugin membuat folder:

```text
SMK Queen Al-Falah - Pusat Media/
├── Waka/
│   └── Nama Pengguna (@username)/
├── Guru/
│   └── Nama Pengguna (@username)/
└── Tendik/
    └── Nama Pengguna (@username)/
```

Setiap akun hanya dapat menelusuri, mengunggah, dan mengunduh file pada folder pribadinya beserta subfolder turunannya. Jangan menyimpan password WordPress, client secret, refresh token, atau JSON service account di Git.
