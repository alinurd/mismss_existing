# Progress Setup Lokal — mismass_existing

Status keseluruhan: **belum dimulai (baru perencanaan)**

- [x] Cek port nginx bebas → **8007**
- [x] Buat `docker/php/Dockerfile`, `docker/php/php.ini`
- [x] Buat `docker/nginx/default.conf`
- [x] Buat `compose.yml` app-level (join `infra_default`)
- [x] Import `db_existinig.sql` → database `db_existing` di `mysql8` (45 tabel, verified)
- [x] Update `.env` (APP_ENV=local, APP_URL=http://localhost:8007, DB_HOST=mysql8, DB_DATABASE=db_existing, DB_USERNAME=root, DB_PASSWORD=Admin123, REDIS_HOST=redis)
- [x] `docker compose up -d --build`
- [x] Composer install di container (sukses, 1 package baru ter-download)
- [x] Permission storage/bootstrap/cache, storage:link, optimize:clear
- [ ] NPM install & build — **belum dijalankan**, tunggu konfirmasi user (asset public/ existing sudah ada)
- [x] Verifikasi akses aplikasi via curl (redirect `/` → `/login`, HTTP 200, title "MisMass Apps")

## Catatan & Keputusan
- Port nginx final: **8007** (http://localhost:8007)
- Database lokal: `db_existing` di container `mysql8` (root/Admin123), diimport dari `db_existinig.sql`
  (statement `CREATE DATABASE`/`USE` production di-strip saat streaming import agar tidak membuat DB dgn nama production).
- `.env`: `MAIL_MAILER=log`, `SEND_EMAIL=false`, `WA_GATEWAY=false`, `SANDBOX=true` — dinonaktifkan by default
  supaya testing lokal tidak mengirim WA/email nyata atau memicu transaksi Doku production. Bisa diaktifkan lagi manual jika perlu tes fitur tsb.
- **Bug ditemukan & diperbaiki**: nginx config awal pakai `fastcgi_pass php:9000` (mengikuti template
  `apps/setup-docker.sh`), tapi alias DNS `php` di network bersama `infra_default` **bentrok** dengan
  container app lain yang juga pakai service name `php` (mis. `gopump-php`) — request sempat nyasar ke
  app lain secara acak. Sibling app (`missmess`, `ptdap`) sudah pakai `fastcgi_pass <container_name>:9000`
  sebagai workaround yang benar; diterapkan pola sama di `docker/nginx/default.conf`
  (`fastcgi_pass mismass_existing-app:9000`). Sudah diverifikasi stabil (5x request berturut-turut,
  title konsisten "MisMass Apps").
- Container aktif: `mismass_existing-nginx` (port 8007→80), `mismass_existing-app` (php-fpm 8.2).
- Belum login manual ke aplikasi (butuh kredensial user asli dari tabel users) — silakan cek tabel
  `users`/sejenis di DB kalau perlu akun test.
