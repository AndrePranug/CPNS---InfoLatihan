# Latihan CPNS — Simulasi SKD

Aplikasi web tryout CPNS **Seleksi Kompetensi Dasar (TWK / TIU / TKP)** dengan penilaian otomatis, pembahasan, progres belajar, dan panel admin. Dibangun dengan Laravel + Inertia + Vue.

## Fitur yang Sudah Selesai

### Peserta

- **Akun** — registrasi & login email + sandi, **verifikasi email** (tautan bertanda tangan, kirim ulang), **login Google** (OAuth), lupa/reset sandi, edit profil, ganti sandi. Ganti email otomatis mereset verifikasi.
- **Dasbor** — sapaan, kartu lanjutkan tryout, ringkasan subtes, target belajar harian (cincin progres), **streak**, **misi mingguan**, dan **peringkat** dengan tab minggu ini / bulan ini.
- **Daftar tryout** — status pengerjaan, jadwal buka/tutup, harga, dan tombol mulai/lanjutkan.
- **Pembayaran & voucher** — checkout paket berbayar dengan kode voucher; default gateway dimatikan (`PAYMENT_ENABLED=false`) sampai akun Midtrans/Xendit dan adapter tersedia.
- **Peringkat nasional** — skor terbaik semua peserta, tab minggu ini / bulan ini / semua waktu, posisi pengguna sendiri.
- **Halaman ujian** — satu soal per layar, **timer dihitung server**, grid nomor soal, navigasi keyboard, tandai ragu-ragu, **autosave jawaban**, submit manual + otomatis saat waktu habis. Satu attempt aktif per paket, soal & pilihan **diacak deterministik** per attempt, kunci tidak dikirim sebelum submit.
- **Hasil** — skor per subtes dan total, status passing grade, peringkat paket, analisis topik terlemah, pembahasan tiap soal (termasuk gambar), filter hasil, plus **email hasil** setelah penilaian.
- **Riwayat & progres** — riwayat nilai, grafik perkembangan 20 attempt, topik terlemah, menit belajar 14 hari.
- **Latihan bebas** — filter subtes/topik, tanpa timer, kunci + pembahasan instan setelah menjawab.
- **Halaman beranda** publik.

### Admin

- **Dasbor statistik** — jumlah peserta/soal/attempt dan **soal paling sering salah**.
- **Bank soal** — CRUD, revisi berversi yang aman (ditolak bila soal dipakai attempt aktif), **editor teks kaya** (bold, miring, garis bawah, superskrip/subskrip, daftar) dengan **sanitasi HTML di server**, unggah gambar soal, **impor CSV & Excel** (`.xlsx`/`.xls`) dengan validasi per baris dan laporan baris gagal.
- **Paket tryout** — pilih & urutkan soal, durasi, passing grade, jadwal buka/tutup, harga, simpan sebagai draf atau terbitkan.
- **Subtes & passing grade**, **manajemen pengguna** (aktif/nonaktif), **voucher** (kode, potongan persen/nominal, kuota, kedaluwarsa).
- **Peran** admin/peserta dengan middleware dan otorisasi server-side.

## Teknologi

| Lapisan | Dipakai |
| --- | --- |
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Inertia 3, Vue 3, Tailwind CSS 4, Vite 8 |
| Database | MySQL 8 |
| Pendukung | Laravel Socialite, Quill 2, PhpSpreadsheet, Symfony HtmlSanitizer |

## Persyaratan

- PHP **8.3+** dengan ekstensi **`zip`** aktif (wajib untuk impor Excel)
- Composer
- Node.js 20+
- MySQL 8

## Instalasi

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpns
DB_USERNAME=root
DB_PASSWORD=
```

Lalu:

```bash
php artisan migrate --seed
npm run build        # atau: npm run dev
php artisan serve --host=127.0.0.1
```

Buka `http://127.0.0.1:8000`.

### Akun seed

| Peran | Email | Sandi |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Peserta | `test@example.com` | `password` |

Ganti kredensial ini sebelum dipakai di produksi.

### Konfigurasi opsional

- **Google login** — isi `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET`. Redirect URI: `http://127.0.0.1:8000/auth/google/callback`. Bila kosong, route login Google mengembalikan 404.
- **Email** — default `MAIL_MAILER=log`. Tautan verifikasi, reset sandi, dan email hasil tryout masuk ke `storage/logs/laravel.log`. Email hasil memakai antrean `database`, jadi jalankan `php artisan queue:work`. Untuk produksi isi SMTP.
- **Pembayaran** — biarkan `PAYMENT_ENABLED=false` dan `PAYMENT_GATEWAY=none` sampai adapter Midtrans/Xendit tersedia.

## Test

```bash
php artisan test --compact    # 88 test, 437 assertion
vendor/bin/pint --format agent
```

## Status Pengembangan

Seluruh kebutuhan **P0/P1** PRD (FR-01 – FR-35) sudah selesai. Yang belum:

- **FR-36 / Fase 2** — inti pembayaran, voucher, peringkat nasional, dan email hasil selesai. Yang tersisa hanya adapter gateway Midtrans/Xendit; saat ini `PAYMENT_ENABLED=false`.
- **Fase 3** — latihan SKB, forum, PWA, laporan mitra bimbel.

Rincian progres, arsitektur, dan catatan teknis ada di [`PROGRESS.md`](PROGRESS.md).
