# Deploy Laravel ke Hostinger (Stabil & Siap Pakai)

Panduan ini untuk mencegah kasus: lokal jalan, hosting error (POS/halaman lain tidak bisa diakses).

## 1) Struktur Hosting
- Arahkan **Document Root** domain/subdomain ke folder `public/` project Laravel.
- Pastikan file `public/.htaccess` aktif.

## 2) Upload File yang Benar
- Upload source project **tanpa** folder `vendor` dan `node_modules` (akan di-install di server).
- Pastikan file berikut **tidak ada** di server:
  - `public/hot`
  - `bootstrap/cache/routes-v7.php`
  - file cache lain di `bootstrap/cache/*.php` (kecuali nanti di-generate ulang lewat artisan).

## 3) Konfigurasi `.env` Production
Minimal:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
```

## 4) Jalankan Perintah Wajib Setelah Deploy
Dari root project:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5) Build Frontend Production
Jika pakai Vite:

```bash
npm install
npm run build
```

Pastikan folder `public/build` ter-generate.

## 6) Permission Folder Penting
Pastikan writable:
- `storage/`
- `bootstrap/cache/`

## 7) Cek Route POS
Verifikasi route tersedia:

```bash
php artisan route:list | grep pos
```

## 8) Cek Kesiapan Production (Otomatis)
Gunakan command berikut untuk validasi cepat sebelum go-live:

```bash
php artisan deploy:hostinger-ready
```

Command ini akan mengecek hal-hal umum yang sering bikin error di Hostinger:
- `APP_DEBUG` harus `false`
- artifact dev `public/hot` tidak boleh ada
- cache route lama (`bootstrap/cache/routes-v7.php`) tidak boleh ikut deploy
- `public/build/manifest.json` harus ada
- permission `storage/` dan `bootstrap/cache` writable
- casing file controller POS aman untuk Linux

## 9) Deploy Sekali Jalan (Operator Friendly)
Jika server menyediakan SSH, jalankan:

```bash
bash scripts/hostinger_deploy.sh
```

Script ini menjalankan urutan deploy production end-to-end dan menutup langkah dengan `deploy:hostinger-ready`.

## 10) Jika Masih Tidak Bisa Akses
- Cek log Laravel: `storage/logs/laravel.log`
- Cek apakah user sudah login dan role sesuai.
- Middleware role sekarang redirect ke dashboard dengan flash error (bukan hard 403), jadi lebih mudah diagnosis.

---
Jika ingin paling aman, gunakan langkah deploy yang sama setiap kali update (clear cache -> cache ulang).
