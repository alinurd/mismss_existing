# Perencanaan Setup Lokal — mismass_existing

## Konteks
- Project: Laravel 10 (PHP ^8.1), aplikasi "Mismass Logistic".
- `.env` saat ini masih berisi kredensial **production** (host `app-mismass.com`, DB `u464450487_mismass`).
- Ada dump database di root project: `db_existinig.sql` (~359MB, format Adminer/MySQL, berisi
  `CREATE DATABASE u464450487_mismass` + `USE u464450487_mismass`).
- Target: jalankan project ini secara lokal via Docker, dengan nama database lokal **`db_existing`**.
- Infra Docker bersama sudah ada & jalan di `/data/project/docker/infra` (compose `name: infra`):
  - `mysql8` (MySQL 8.4, root pass `Admin123`, port 3306)
  - `redis` (port 6379)
  - `adminer` (port 8002), `portainer` (8001), `jenkins` (8003)
  - Network bersama: `infra_default`
- Pola per-app (dicontoh dari `apps/missmess` & `apps/ptdap`, dijalankan via `setup-docker.sh`):
  - Tiap app punya `compose.yml` sendiri (nginx + php-fpm + node/tools), join network `infra_net` (external, = `infra_default`) + network privat sendiri.
  - PHP container terhubung ke `mysql8`/`redis` milik infra, bukan container DB terpisah.
  - Port nginx per-app: 8004 (ptdap), 8005 (missmess), 8006 (gopump) → port berikutnya yang bebas untuk `mismass_existing` perlu dicek ulang saat eksekusi.

## Rencana Langkah
1. **Cek port bebas** untuk nginx `mismass_existing` (mulai dari 8007/8008, cross-check `docker ps` & `ss -tlnp`).
2. **Buat struktur docker app-level**: `docker/php/Dockerfile`, `docker/php/php.ini`, `docker/nginx/default.conf`, `compose.yml` di root `mismass_existing`, mengikuti pola `missmess/compose.yml` (join `infra_default` sebagai network eksternal).
3. **Siapkan database lokal**:
   - Pastikan `mysql8` hidup.
   - Import `db_existinig.sql` ke database baru bernama `db_existing` (bukan `u464450487_mismass`) — strip statement `CREATE DATABASE` & `USE` lama saat streaming import agar tidak membuat DB dengan nama production.
4. **Update `.env` lokal**:
   - `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:<port>`
   - `DB_HOST=mysql8`, `DB_PORT=3306`, `DB_DATABASE=db_existing`, `DB_USERNAME=root`, `DB_PASSWORD=Admin123`
   - `REDIS_HOST=redis`, `REDIS_PORT=6379`
   - Kredensial pihak ketiga (mail, WA gateway, Doku, Qontak, FCA) dibiarkan apa adanya kecuali user minta diubah — hanya dicatat sebagai potensi side-effect kalau fitur terkait dites.
5. **Build & start container**: `docker compose up -d --build`.
6. **Dependency**: composer install di dalam container (vendor host mungkin tidak kompatibel binary ext), permission `storage`/`bootstrap/cache`, `storage:link`, `optimize:clear`. NPM install + build kalau perlu asset lokal.
7. **Verifikasi**: akses `http://localhost:<port>`, cek halaman login, cek koneksi DB (query sample), cek log error.
8. Catat semua keputusan & port final di `progress.md`.

## Hal yang perlu konfirmasi ke user (jika muncul saat eksekusi)
- Port final yang dipakai untuk nginx.
- Apakah perlu jalankan `npm run build` (ada asset di `resources/`?) atau cukup pakai asset yang sudah ada di `public/`.
- Apakah data production di `.env` (WA gateway, mail, payment gateway) perlu dinonaktifkan/sandbox-kan untuk mencegah side-effect saat testing lokal (mis. auto-kirim WA/email ke customer asli).
