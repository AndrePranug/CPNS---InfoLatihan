# PROGRESS

Status proyek **Latihan CPNS — Simulasi SKD**. Dokumen ini sumber kebenaran progres: apa yang sudah jadi, apa yang belum, dan langkah berikutnya.

Terakhir diperbarui: **9 November 2026**

---

## 1. Ringkasan

- Stack: **Laravel 13.40 · Inertia 3.7 · Vue 3 · Tailwind v4 · Vite 8 · MySQL 8.3**
- Database: `cpns` (dev) dan `cpns_testing` (test), user `root` tanpa password (Laragon)
- Test: **58 test, 296 assertion — semua lulus** di MySQL
- Sumber spesifikasi: `PRD Web Latihan CPNS.md` dan `design (3).md`

---

## 2. Cara Menjalankan

```bash
php artisan serve                 # http://127.0.0.1:8000
npm run dev                       # atau: npm run build
php artisan migrate               # setelah pull
php artisan migrate:fresh --seed  # reset bersih
php artisan test --compact        # test suite (MySQL cpns_testing)
vendor/bin/pint --format agent    # format PHP
```

Akun seed:

| Email | Password | Peran |
| --- | --- | --- |
| `admin@example.com` | `password` | admin |
| `test@example.com` | `password` | peserta |

Catatan lingkungan:

- `php artisan serve --host=127.0.0.1` (jangan `localhost`, dia bind IPv6 saja).
- Windows PowerShell 5.1: `php artisan tinker --execute` merusak kutip; pakai file skrip + `php artisan tinker skrip.php`.
- Proyek **bukan** repo git, jadi `pint --dirty` tidak tersedia — pakai `pint --format agent`.
- Kalau frontend tidak berubah di browser: `npm run build`.
- Reset sandi memakai mailer; default `MAIL_MAILER=log` → tautan masuk `storage/logs/laravel.log`.

---

## 3. Status Requirement PRD

| Kode | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-01 | Registrasi & login email + sandi, **verifikasi email** | P0 | Sebagian — registrasi/login ada, verifikasi email belum |
| FR-02 | Login Google (Socialite) | P1 | **Selesai** |
| FR-03 | Reset sandi & edit profil | P0 | **Selesai** |
| FR-04 | Peran peserta dan admin | P0 | **Selesai** |
| FR-10 | Daftar paket tryout + filter status | P0 | **Selesai** |
| FR-11 | Halaman ujian: satu soal/layar, timer server, grid nomor | P0 | **Selesai** |
| FR-12 | Autosave jawaban | P0 | **Selesai** |
| FR-13 | Submit manual + otomatis saat waktu habis | P0 | **Selesai** |
| FR-14 | Penilaian TWK/TIU 0\|5, TKP bobot 1–5 | P0 | **Selesai** |
| FR-15 | Hasil: skor subtes, total, status PG, pembahasan | P0 | **Selesai** |
| FR-16 | Riwayat nilai + grafik perkembangan | P1 | **Selesai** |
| FR-17 | Analisis kelemahan per topik | P1 | **Selesai** |
| FR-18 | Peringkat per paket tryout | P1 | **Selesai** |
| FR-19 | Latihan per subtes/topik tanpa timer ketat | P2 | **Selesai** |
| FR-30 | CRUD bank soal lengkap | P0 | **Selesai** |
| FR-31 | Editor teks kaya (gambar, rumus) | P1 | Sebagian — gambar via `image_path`, editor kaya belum |
| FR-32 | Impor soal CSV/Excel + validasi per baris | P1 | **Selesai** (CSV) |
| FR-33 | CRUD paket: pilih soal, durasi, PG, jadwal, harga | P0 | **Selesai** |
| FR-34 | Manajemen pengguna | P0 | **Selesai** |
| FR-35 | Dasbor statistik admin | P1 | **Selesai** |
| FR-36 | Pembayaran & voucher | P2 | **Belum** (Fase 2) |

Aturan bisnis PRD yang sudah diterapkan:

- Timer dihitung server (`ends_at`), klien hanya menampilkan.
- Satu attempt aktif per user per paket.
- Soal & pilihan diacak per attempt, deterministik (`ExamOrderService`), kunci tidak dikirim sebelum submit.

---

## 4. Yang Sudah Dibangun

### Fondasi
- Desain token di `resources/css/app.css` (brand blue, ink, pastel TWK/TIU/TKP, font Plus Jakarta Sans).
- Layout peserta: `AppLayout.vue` + `AppSidebar.vue` (sidebar desktop, bottom nav mobile).
- Layout admin: `AdminLayout.vue` (flash sukses + error).

### Autentikasi
- `Auth/AuthenticatedSessionController` — login, tolak user nonaktif.
- `Auth/RegisteredUserController` — registrasi.
- `Auth/GoogleController` — OAuth + linking akun via `google_id`.
- `Auth/PasswordResetLinkController`, `Auth/NewPasswordController` — lupa/reset sandi.
- `ProfileController` — edit nama/email + ganti sandi.

### Inti ujian
- `ExamService` — mulai/lanjutkan attempt, payload soal (acak), submit.
- `ScoringService` — skor TWK/TIU/TKP + passing grade dari DB.
- `ExamOrderService` — acak deterministik per attempt (hash SHA-256).
- `ReviewService` — pembahasan per soal setelah submit.
- Halaman: `Exam/Attempt.vue` (timer, grid nomor, keyboard nav, dialog submit), `Results/Show.vue` (skor, topik lemah, peringkat paket, pembahasan + filter).

### Progres & gamifikasi ringan
- Tabel `daily_activities` + kolom `attempts.daily_activity_recorded`.
- `ProgressService` — `record`, `recordAttempt`, `dailyGoal`, `streak`, `leaderboard`, `tryoutRanking`, `resumeAttempt`.
- `Dashboard.vue` — hero sapaan, kartu lanjutkan, kartu subtes, target harian (cincin SVG), streak, peringkat.
- `Progress/Index.vue` — ringkasan, grafik skor bertumpuk 20 attempt, topik terlemah, menit belajar 14 hari.
- `History/Index.vue` — riwayat + tren skor.

### Latihan (FR-19)
- `PracticeService` — soal acak stateless + `check()` yang mengembalikan kunci hanya setelah dijawab.
- `Practice/Index.vue` — tanpa timer, filter subtes/topik, feedback instan + pembahasan, hitung benar/salah, "Soal baru".

### Admin
- `Admin/Dashboard.vue` — statistik + **soal paling sering salah**.
- Bank soal: index, form, edit/versi (`QuestionVersionService`), impor CSV (`QuestionImportService`).
- Paket tryout: index, editor isi paket (`Admin/Tryouts/Edit.vue` — pilih/urutkan soal), pengaturan paket (judul, deskripsi, durasi, harga, jadwal buka/tutup, terbit).
- Subtes & passing grade, manajemen pengguna (aktif/nonaktif).

### Database
Tabel: `users` (+`role`, `google_id`, `is_active`), `subtests`, `topics`, `questions`, `options`, `tryouts`, `tryout_question`, `attempts`, `attempt_answers`, `daily_activities`, plus tabel bawaan Laravel.

---

## 5. Struktur File Penting

```
app/Services/
  ExamService.php          # orkestrasi attempt
  ScoringService.php       # penilaian
  ReviewService.php        # pembahasan hasil
  ExamOrderService.php     # pengacakan deterministik
  ProgressService.php      # target harian, streak, peringkat
  PracticeService.php      # latihan tanpa timer (stateless)
  QuestionImportService.php
  QuestionVersionService.php

app/Http/Controllers/
  DashboardController.php  ProgressController.php  HistoryController.php
  ExamController.php       ResultController.php    ProfileController.php
  PracticeController.php   TryoutController.php
  Admin/{Dashboard,Question,Tryout,Subtest,User}Controller.php
  Auth/{AuthenticatedSession,RegisteredUser,Google,
        PasswordResetLink,NewPassword}Controller.php

resources/js/Pages/
  Dashboard.vue  Progress/Index.vue  History/Index.vue  Practice/Index.vue
  Tryouts/{Index,Show}.vue  Exam/Attempt.vue  Results/Show.vue
  Profile/Edit.vue
  Auth/{Login,Register,ForgotPassword,ResetPassword}.vue
  Admin/{Dashboard,Questions/*,Tryouts/*,Subtests/*,Users/*}.vue

routes/web.php             # 46 route
tests/Feature/             # 58 test
```

---

## 6. Belum Dikerjakan (Backlog)

Urutan usulan, dari yang paling berdampak:

1. **FR-01 — Verifikasi email** (P0 yang masih tertunda; butuh konfigurasi mailer produksi dulu).
2. **Misi/hadiah harian** — desain dashboard menyebut misi & hadiah; belum ada modelnya.
3. **Peringkat periode** — filter mingguan/bulanan pada `ProgressService::leaderboard`.
4. **FR-31 — Editor teks kaya** untuk soal/pembahasan (gambar inline, rumus sederhana).
5. **FR-32 — Impor Excel** (`.xlsx`), bukan hanya CSV.
7. **Fase 2** — pembayaran (Midtrans/Xendit), kode voucher, peringkat nasional, notifikasi email.
8. **Fase 3** — latihan SKB, forum, PWA, laporan mitra bimbel.
Perbaikan kecil yang tertunda:

- Filter soal per topik di editor paket.
- Ilustrasi hero dashboard (saat ini gradien polos).
- Ubah `SESSION_DRIVER`/`CACHE_STORE`/`QUEUE_CONNECTION` ke Redis untuk produksi (sekarang `database`).
- `app/Http/Controllers` belum semua punya Form Request; validasi masih inline di controller.

---

## 7. Catatan Teknis / Jebakan

- **Default kolom DB tidak terpantul di model** setelah `create()` — deklarasikan di `protected $attributes` (contoh: `Attempt::$attributes['daily_activity_recorded']`).
- Pivot paket = `tryout_question`; relasi harus menyebut nama tabel eksplisit.
- Factory `Subtest` memakai `randomElement` (bukan `unique()`) — enum hanya 3 nilai.
- Factory `Question` dan `Topic` otomatis membuat `Subtest` baru; di test override `subtest_id` **dan** `topic_id` agar tidak bentrok unique `subtests.code`.
- `ProgressService::leaderboard` hanya menghitung attempt terkirim (`submitted_at` bukan null).
- `ExamOrderService::shuffle` butuh `Attempt` yang sudah tersimpan (pakai `id`), jangan dipanggil sebelum insert.

---

## 8. Cara Melanjutkan dengan Agen

Tempel ke agen:

> Baca `PROGRESS.md`. Lanjutkan item backlog nomor N. Ikuti konvensi yang ada: Inertia + Vue SFC, teks UI bahasa Indonesia, test PHPUnit feature, jalankan `php artisan test --compact` dan `vendor/bin/pint --format agent` sebelum selesai.

Langkah verifikasi minimal sebelum menutup tugas:

1. `php artisan test --compact` → semua lulus.
2. `npm run build` → sukses.
3. `vendor/bin/pint --format agent` → bersih.
4. Smoke test halaman yang berubah di `php artisan serve`.
5. Perbarui tabel Status Requirement dan backlog di file ini.
