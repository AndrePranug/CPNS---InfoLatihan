# PROGRESS

Status proyek **Latihan CPNS — Simulasi SKD**. Dokumen ini sumber kebenaran progres: apa yang sudah jadi, apa yang belum, dan langkah berikutnya.

Terakhir diperbarui: **14 November 2026**

---

## 1. Ringkasan

- Stack: **Laravel 13.40 · Inertia 3.7 · Vue 3 · Tailwind v4 · Vite 8 · MySQL 8.3**
- Database: `cpns` (dev) dan `cpns_testing` (test), user `root` tanpa password (Laragon)
- Test: **97 test, 518 assertion — semua lulus** di MySQL
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
- Proyek sudah repo git; `pint --dirty` bisa dipakai, atau `pint --format agent`.
- Kalau frontend tidak berubah di browser: `npm run build`.
- Reset sandi memakai mailer; default `MAIL_MAILER=log` → tautan masuk `storage/logs/laravel.log`.

---

## 3. Status Requirement PRD

| Kode | Kebutuhan | Prioritas | Status |
| --- | --- | --- | --- |
| FR-01 | Registrasi & login email + sandi, verifikasi email | P0 | **Selesai** |
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
| FR-31 | Editor teks kaya (gambar, rumus) | P1 | **Selesai** — Quill (bold/miring/garis bawah/sup/sub/daftar) + sanitasi server; gambar via unggahan terpisah |
| FR-32 | Impor soal CSV/Excel + validasi per baris | P1 | **Selesai** — CSV & `.xlsx`/`.xls` |
| FR-33 | CRUD paket: pilih soal, durasi, PG, jadwal, harga | P0 | **Selesai** |
| FR-34 | Manajemen pengguna | P0 | **Selesai** |
| FR-35 | Dasbor statistik admin | P1 | **Selesai** |
| FR-36 | Pembayaran & voucher | P2 | **Sebagian** — order, voucher, peringkat nasional, email hasil siap; adapter gateway mati (`PAYMENT_ENABLED=false`) |

Aturan bisnis PRD yang sudah diterapkan:

- Timer dihitung server (`ends_at`), klien hanya menampilkan.
- Satu attempt aktif per user per paket.
- Soal & pilihan diacak per attempt, deterministik (`ExamOrderService`), kunci tidak dikirim sebelum submit.

Fitur Fase 3 (roadmap PRD):

- **Latihan SKB per formasi** — tabel `formations`; `questions.exam_type` (`skd`/`skb`) + `formation_id`; `subtest_id` nullable untuk soal SKB. Latihan bebas `/practice?mode=skb&formation=ID`, admin `/admin/formations`.
- **Forum diskusi** — thread + balasan; pemilik/admin boleh hapus, admin boleh kunci & sematkan.
- **PWA** — `public/manifest.webmanifest`, `public/sw.js`, `public/offline.html`, ikon 192/512; service worker didaftarkan hanya di build produksi.
- **Laporan mitra bimbel** — `/admin/reports` (ringkasan, rekap peserta, rekap paket) + ekspor CSV per peserta/periode.

---

## 4. Yang Sudah Dibangun

### Fondasi
- Desain token di `resources/css/app.css` (brand blue, ink, pastel TWK/TIU/TKP, font Plus Jakarta Sans).
- Layout peserta: `AppLayout.vue` + `AppSidebar.vue` (sidebar desktop, bottom nav mobile).
- Layout admin: `AdminLayout.vue` (flash sukses + error).

### Autentikasi
- `Auth/AuthenticatedSessionController` — login, tolak user nonaktif.
- `Auth/RegisteredUserController` — registrasi.
- `Auth/EmailVerificationController` — verifikasi email (notice, tautan bertanda tangan, kirim ulang).
- `Auth/GoogleController` — OAuth + linking akun via `google_id`.
- `Auth/PasswordResetLinkController`, `Auth/NewPasswordController` — lupa/reset sandi.
- `ProfileController` — edit nama/email + ganti sandi; ganti email mereset verifikasi dan mengirim tautan baru.

### Inti ujian
- `ExamService` — mulai/lanjutkan attempt, payload soal (acak), submit.
- `ScoringService` — skor TWK/TIU/TKP + passing grade dari DB.
- `ExamOrderService` — acak deterministik per attempt (hash SHA-256).
- `ReviewService` — pembahasan per soal setelah submit.
- Halaman: `Exam/Attempt.vue` (timer, grid nomor, keyboard nav, dialog submit), `Results/Show.vue` (skor, topik lemah, peringkat paket, pembahasan + filter).

### Progres & gamifikasi ringan
- Tabel `daily_activities` + kolom `attempts.daily_activity_recorded`.
- `ProgressService` — `record`, `recordAttempt`, `dailyGoal`, `streak`, `leaderboard` (semua/minggu/bulan), `nationalRanking`, `missions`, `tryoutRanking`, `resumeAttempt`.
- `Dashboard.vue` — hero sapaan, kartu lanjutkan, kartu subtes, target harian (cincin SVG), streak, peringkat dengan tab **Minggu ini / Bulan ini**, kartu **Misi minggu ini** (3 tryout, 50 soal, 100 menit).
- `Progress/Index.vue` — ringkasan, grafik skor bertumpuk 20 attempt, topik terlemah, menit belajar 14 hari.
- `History/Index.vue` — riwayat + tren skor.
- `Rankings/Index.vue` — peringkat nasional semua peserta dengan tab minggu ini / bulan ini / semua waktu, posisi pengguna, dan sorotan baris sendiri.
- `AttemptResultNotification` — email hasil tryout (skor, status PG, tautan pembahasan) dikirim sekali saat attempt selesai dinilai; antrean `database`, jalankan `php artisan queue:work`.

### Latihan (FR-19)
- `PracticeService` — soal acak stateless + `check()` yang mengembalikan kunci hanya setelah dijawab.
- `Practice/Index.vue` — tanpa timer, filter subtes/topik, feedback instan + pembahasan, hitung benar/salah, "Soal baru".
- Mode **SKB**: filter per formasi, tanpa subtes/topik, label soal memakai nama formasi.

### Forum diskusi
- `ForumController` + `Forum/Index.vue`, `Forum/Show.vue` — buat diskusi, balas, hapus (pemilik/admin), kunci & sematkan (admin), paginasi.
- Tabel `forum_threads`, `forum_replies`; thread terkunci menolak balasan baru.

### PWA
- `manifest.webmanifest` (nama, tema `#1d6ff2`, ikon 192/512 `any maskable`), `theme-color` + `apple-touch-icon` di `app.blade.php`.
- `sw.js` — network-first untuk navigasi dengan fallback `offline.html`, cache-first untuk `/build/` dan `/icons/`, melewati request Inertia/XHR; versi cache `latihan-cpns-v1`.
- Registrasi service worker hanya `import.meta.env.PROD` di `app.js`.

### Laporan mitra
- `Admin/ReportController` + `Admin/Reports/Index.vue` — rentang tanggal (default 30 hari), ringkasan peserta/percobaan/rata-rata/lulus, rekap per peserta (maks. 100 di layar) dan per paket.
- Ekspor CSV `laporan-mitra-{from}-{to}.csv` berisi seluruh peserta pada rentang tersebut.

### Admin
- `Admin/Dashboard.vue` — statistik + **soal paling sering salah**.
- Bank soal: index, form dengan editor teks kaya (Quill), edit/versi (`QuestionVersionService`), impor CSV/Excel (`QuestionImportService`), unggah gambar soal (`POST /admin/questions/{question}/image`, disk `public`, maks 2 MB).
- Paket tryout: index, editor isi paket (`Admin/Tryouts/Edit.vue` — pilih/urutkan soal), pengaturan paket (judul, deskripsi, durasi, harga, jadwal buka/tutup, terbit).
- Subtes & passing grade, manajemen pengguna (aktif/nonaktif).
- Voucher: `Admin/Vouchers/Index.vue` — kode, potongan persen/nominal, maks. pemakaian, kedaluwarsa, aktif/nonaktif.

### Pembayaran & voucher (FR-36, flag mati)
- `PaymentService` — cek akses (gratis atau order `paid`), validasi voucher, kuotasi diskon, pembuatan order `pending`, `markPaid` idempoten + increment pemakaian voucher.
- `CheckoutController` + `Checkout.vue` — ringkasan harga, input voucher, pesanan menunggu pembayaran. Saat `PAYMENT_ENABLED=false` halaman menampilkan pesan bahwa pembayaran dinonaktifkan dan tombol mulai untuk paket berbayar ditolak.
- `ExamController::start` memblokir paket berbayar tanpa order lunas; pengguna dengan order `paid` tetap bisa mulai meski gateway mati.
- Adapter gateway (redirect + webhook) **belum dibuat** — ditandai komentar `ponytail:` di `PaymentService::createOrder`.

### Database
Tabel: `users` (+`role`, `google_id`, `is_active`), `subtests`, `topics`, `formations`, `questions` (+`exam_type`, `formation_id`), `options`, `tryouts`, `tryout_question`, `attempts`, `attempt_answers`, `daily_activities`, `orders`, `vouchers`, `forum_threads`, `forum_replies`, plus tabel bawaan Laravel.

---

## 5. Struktur File Penting

```
app/Services/
  ExamService.php          # orkestrasi attempt
  ScoringService.php       # penilaian
  ReviewService.php        # pembahasan hasil
  ExamOrderService.php     # pengacakan deterministik
  ProgressService.php      # target harian, streak, peringkat
  PracticeService.php      # latihan tanpa timer (SKD/SKB, stateless)
  PaymentService.php       # akses paket, voucher, order, markPaid
  QuestionImportService.php  QuestionVersionService.php

app/Http/Controllers/
  DashboardController.php  ProgressController.php  HistoryController.php
  RankingController.php    ResultController.php    ProfileController.php
  ExamController.php       TryoutController.php    CheckoutController.php
  PracticeController.php   ForumController.php
  Admin/{Dashboard,Question,Tryout,Subtest,User,Voucher,Formation,Report}Controller.php
  Auth/{AuthenticatedSession,RegisteredUser,Google,EmailVerification,
        PasswordResetLink,NewPassword}Controller.php

app/Notifications/
  AttemptResultNotification.php

app/Models/
  Formation.php  ForumThread.php  ForumReply.php

public/
  manifest.webmanifest  sw.js  offline.html  icons/icon-{192,512}.png

resources/js/Pages/
  Dashboard.vue  Progress/Index.vue  Rankings/Index.vue  History/Index.vue
  Practice/Index.vue  Forum/{Index,Show}.vue
  Tryouts/{Index,Show}.vue  Checkout.vue  Exam/Attempt.vue  Results/Show.vue
  Profile/Edit.vue
  Auth/{Login,Register,VerifyEmail,ForgotPassword,ResetPassword}.vue
  Admin/{Dashboard,Questions/*,Tryouts/*,Vouchers/*,Subtests/*,Users/*,
        Formations/*,Reports/*}.vue

routes/web.php             # 69 route
tests/Feature/             # 97 test
```

---

## 6. Belum Dikerjakan (Backlog)

Urutan usulan, dari yang paling berdampak:

1. **Adapter gateway Midtrans/Xendit** (FR-36) — order + voucher + peringkat nasional + email hasil sudah siap; flag `PAYMENT_ENABLED=false`.

Fase 3 (SKB, forum, PWA, laporan mitra) sudah selesai.

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
- Teks kaya disanitasi di server (`App\Services\HtmlSanitizer`, allowlist tag) sebelum disimpan; halaman ujian/latihan/hasil baru boleh memakai `v-html`.
- Impor `.xlsx` butuh ekstensi PHP **`zip`** aktif (`php.ini` Laragon baris `extension=zip`). Tanpa itu PhpSpreadsheet gagal membaca Excel.
- Pembayaran default mati: `PAYMENT_ENABLED=false`, `PAYMENT_GATEWAY=none`. Jangan ubah ke `true` sebelum adapter gateway (redirect + webhook) dan kredensial tersedia; order hanya akan tertahan di `pending`.
- Voucher menyimpan pemakaian di `used_count`; increment hanya saat `PaymentService::markPaid` dan idempoten untuk order yang sudah lunas.
- Email hasil tryout memakai antrean `database` (`ShouldQueue`). Jalankan `php artisan queue:work` agar email terkirim; dengan `MAIL_MAILER=log` isinya masuk `storage/logs/laravel.log`.
- Peringkat nasional memakai skor terbaik per pengguna dan hanya menghitung pengguna aktif (`users.is_active`).
- Soal SKB: `questions.subtest_id` nullable dan `formation_id` wajib. Semua query SKD (editor paket, statistik subtes/topik) harus memfilter `exam_type = 'skd'`; `PracticeService` memfilter `exam_type` sesuai mode.
- Service worker hanya aktif pada build produksi. Naikkan versi `CACHE` di `public/sw.js` saat mengubah aset pre-cache agar cache lama dibersihkan.
- `StoreQuestionRequest` mengisi `exam_type = 'skd'` bila tidak dikirim, sehingga klien lama tetap kompatibel.

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
