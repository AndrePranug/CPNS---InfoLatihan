# Design System: Web Latihan CPNS

Panduan desain untuk antarmuka web latihan CPNS (Laravel 13, Vue, Inertia, Tailwind CSS). Gaya visual mengacu pada referensi dasbor belajar: biru lembut, kartu membulat, sidebar kiri, dan panel ringkasan di kanan. Maskot, nama, dan ilustrasi referensi tidak dipakai; semua aset dibuat sendiri.

---

## 1. Prinsip Desain

1. **Tenang dan fokus.** Latar biru sangat muda, kartu putih, satu warna utama. Peserta sedang belajar, bukan dihibur berlebihan.
2. **Progres selalu terlihat.** Target harian, streak, skor, dan peringkat tampil di dasbor tanpa perlu membuka halaman lain.
3. **Satu aksi utama per area.** Setiap kartu punya satu tombol utama (misalnya Lanjutkan Latihan).
4. **Halaman ujian bebas gangguan.** Tanpa sidebar, tanpa notifikasi, timer selalu terlihat.
5. **Mobile first.** Dirancang dari lebar 360 px, lalu diperluas ke tablet dan desktop.

---

## 2. Design Tokens

Nilai warna di bawah adalah titik awal yang diambil dari kesan visual referensi; sesuaikan saat desain final.

### 2.1 Warna

| Token | Hex | Pemakaian |
| --- | --- | --- |
| `brand-600` | `#1D6FF2` | Tombol utama, link, progress bar, item nav aktif (teks) |
| `brand-700` | `#1458C9` | Hover tombol utama |
| `brand-100` | `#DCEBFF` | Item nav aktif (latar), chip, hover ringan |
| `brand-50` | `#EEF5FF` | Latar kartu sekunder |
| `page` | `#EAF3FF` | Latar halaman (bisa gradien ke `#F6FAFF`) |
| `surface` | `#FFFFFF` | Kartu, sidebar, input |
| `ink-900` | `#0B1B3F` | Judul dan teks utama |
| `ink-600` | `#4B5B7A` | Teks sekunder |
| `ink-400` | `#8A97B0` | Placeholder, label kecil |
| `line` | `#DDE7F5` | Garis pemisah dan border kartu |
| `success` | `#22A559` | Benar, lulus passing grade, hari tercapai |
| `warning` | `#F59E0B` | Ragu-ragu, streak, peringatan waktu |
| `danger` | `#E5484D` | Salah, waktu hampir habis, gagal |

**Warna subtes** (latar kartu pastel + ikon solid):

| Subtes | Latar | Ikon / aksen |
| --- | --- | --- |
| TWK | `#FFE6E6` | `#EF4444` |
| TIU | `#EDE7FF` | `#7C5CF0` |
| TKP | `#E1F8E5` | `#22A559` |
| SKB (fase 3) | `#FFEBD6` | `#F97316` |

Aturan: jangan hanya mengandalkan warna untuk makna. Status soal selalu disertai ikon atau teks.

### 2.2 Tipografi

- Font: **Plus Jakarta Sans** atau **Inter** (pilih satu), fallback `system-ui`.
- Angka tabel dan timer memakai `tabular-nums`.

| Peran | Ukuran / berat | Kelas Tailwind |
| --- | --- | --- |
| Judul hero | 36 / 800 | `text-4xl font-extrabold` |
| Judul halaman | 28 / 700 | `text-3xl font-bold` |
| Judul kartu | 18 / 700 | `text-lg font-bold` |
| Isi | 15 / 400 | `text-[15px]` |
| Teks sekunder | 14 / 500 | `text-sm font-medium text-ink-600` |
| Label kecil | 12 / 500 | `text-xs text-ink-400` |
| Timer ujian | 20 / 700 | `text-xl font-bold tabular-nums` |

### 2.3 Jarak, radius, bayangan

- Skala jarak kelipatan 4 px; jarak antar kartu 24 px (`gap-6`), padding kartu 20 sampai 24 px.
- Radius: kartu `rounded-2xl` (16 px), hero `rounded-3xl` (24 px), tombol dan input `rounded-xl` (12 px), chip `rounded-full`.
- Bayangan lembut, tanpa gradien tebal: `0 8px 24px -12px rgb(29 111 242 / 0.18)`.
- Border kartu 1 px `line`.

### 2.4 Konfigurasi Tailwind (v4, `resources/css/app.css`)

```css
@import "tailwindcss";

@theme {
  --font-sans: "Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif;

  --color-brand-50: #eef5ff;
  --color-brand-100: #dcebff;
  --color-brand-600: #1d6ff2;
  --color-brand-700: #1458c9;

  --color-page: #eaf3ff;
  --color-ink-900: #0b1b3f;
  --color-ink-600: #4b5b7a;
  --color-ink-400: #8a97b0;
  --color-line: #dde7f5;

  --color-success: #22a559;
  --color-warning: #f59e0b;
  --color-danger: #e5484d;

  --shadow-card: 0 8px 24px -12px rgb(29 111 242 / 0.18);
}
```

Jika proyek memakai Tailwind v3, pindahkan nilai yang sama ke `theme.extend` di `tailwind.config.js`.

---

## 3. Layout

### 3.1 Kerangka dasbor (desktop, 1024 px ke atas)

```
+-----------+-----------------------------------------+-------------+
| Sidebar   | Topbar: pencarian | streak | poin       |             |
| 240 px    +-----------------------------------------+ Panel kanan |
|           | Hero sapaan + tombol Lanjutkan          | 320 px      |
| Nav       | Lanjutkan Latihan (kartu progres)       | Target      |
| Kartu     | Latih Kemampuan (TWK, TIU, TKP)         | harian      |
| Premium   | Paket Populer                           | Peringkat   |
| Profil    |                                         | Misi        |
+-----------+-----------------------------------------+-------------+
```

- Sidebar tetap (sticky), latar `surface`, border kanan `line`.
- Konten tengah maksimal 880 px; panel kanan 320 px.
- Grid: `grid grid-cols-[240px_minmax(0,1fr)_320px] gap-6`.

### 3.2 Breakpoint

| Lebar | Perilaku |
| --- | --- |
| di bawah 768 px | Sidebar jadi bottom navigation (5 ikon); panel kanan pindah ke bawah konten |
| 768 sampai 1023 px | Sidebar hanya ikon (72 px); panel kanan di bawah konten |
| 1024 px ke atas | Tiga kolom penuh |

### 3.3 Layout ujian (terpisah)

- Tanpa sidebar. Header tipis: nama paket, timer, tombol Selesai.
- Kiri: nomor soal, teks soal, 5 pilihan jawaban (kartu besar yang bisa diklik).
- Kanan (desktop) atau laci dari bawah (mobile): grid nomor soal.
- Bawah: tombol Sebelumnya, Ragu-ragu, Berikutnya.

---

## 4. Komponen

### 4.1 Sidebar

- Logo teks + tagline pendek di atas, dipisah garis.
- Menu: **Beranda, Tryout, Latihan, Tantangan, Progres, Peringkat, Materi**. Ikon garis 20 px (Lucide), jarak antar item 4 px.
- Item aktif: latar `brand-100`, teks dan ikon `brand-600`, `rounded-xl`, tinggi 44 px.
- Kartu **Paket Premium** di bawah menu (aktif di fase 2): ikon mahkota, deskripsi 2 baris, tombol Upgrade.
- Paling bawah: avatar, nama, level, ikon pengaturan.

### 4.2 Topbar

- Kolom pencarian lebar penuh, `rounded-full`, ikon kaca pembesar, placeholder "Cari paket, materi, topik".
- Kanan: chip **streak** (ikon api + angka) dan chip **poin** (ikon permata + angka).

### 4.3 Hero sapaan

- Latar gradien biru muda (`brand-100` ke `brand-50`), `rounded-3xl`, tinggi sekitar 300 px.
- Isi: sapaan ("Selamat pagi, Alex!"), satu kalimat motivasi, tombol utama **Lanjutkan Latihan** dengan ikon panah.
- Chip di pojok kanan atas: target seleksi aktif (misalnya "Target: CPNS 2026").
- Sisi kanan: ilustrasi orisinal bertema belajar (buku, tameng, bendera, tanda centang). Jangan memakai maskot atau karakter pihak lain.

### 4.4 Kartu Lanjutkan Latihan

- Thumbnail kiri 150 x 100 px `rounded-xl`, judul paket, sub-judul ("Paket 3, 42 dari 110 soal"), progress bar horizontal, persentase.
- Tombol **Mulai** atau **Lanjutkan** di kanan, `brand-600`, ikon play.
- Progress bar: tinggi 8 px, track `brand-100`, isi `brand-600`, `rounded-full`.

### 4.5 Kartu subtes

Empat kartu sejajar (2 kolom di mobile): ikon dalam kotak `rounded-xl` berwarna solid, nama subtes, jumlah paket atau soal, panah kecil. Latar pastel sesuai tabel warna subtes. Hover: naik 2 px dan bayangan sedikit lebih kuat.

### 4.6 Kartu Target Harian (panel kanan)

- Cincin progres (donut) 120 px: menit belajar hari ini dibanding target (misalnya 15 / 20 menit).
- Di bawahnya 7 lingkaran hari (Sen sampai Min): tercapai = lingkaran biru dengan centang, belum = lingkaran kosong, hari ini = outline tebal.
- Teks dorongan singkat di samping cincin.

### 4.7 Kartu Peringkat

- Daftar 5 baris: medali (1 sampai 3 berwarna, 4 dan seterusnya angka), avatar 32 px, nama, skor.
- Baris milik pengguna ditandai latar `brand-100` dan label "Kamu".
- Tab periode: Minggu ini, Bulan ini.

### 4.8 Kartu Misi

Hadiah kecil dengan progress (misalnya "Selesaikan 5 latihan TIU minggu ini", 3 / 5). Ikon kado orisinal, progress bar tipis.

### 4.9 Tombol

| Varian | Gaya |
| --- | --- |
| Primer | `bg-brand-600 text-white rounded-xl h-11 px-5 font-semibold hover:bg-brand-700` |
| Sekunder | `bg-white border border-line text-ink-900 hover:bg-brand-50` |
| Ghost | teks `brand-600`, tanpa latar, hover `bg-brand-50` |
| Bahaya | `bg-danger text-white` |

Fokus: ring 2 px `brand-600` dengan offset 2 px. Disabled: opasitas 50 persen dan kursor `not-allowed`.

### 4.10 Komponen ujian

- **Pilihan jawaban:** kartu `rounded-xl` border `line`; terpilih = border `brand-600` + latar `brand-50`; huruf pilihan (A sampai E) dalam lingkaran di kiri.
- **Grid nomor soal:** kotak 40 px; terjawab = `brand-600` putih; ragu-ragu = `warning` dengan ikon bendera; kosong = putih border `line`; soal aktif = ring `brand-600`.
- **Timer:** pil di header; normal `ink-900`; di bawah 10 menit `warning`; di bawah 2 menit `danger` dan berdenyut halus.
- **Dialog Selesai:** menampilkan jumlah terjawab, ragu-ragu, kosong; tombol Kembali dan Selesai dan Nilai.

### 4.11 Halaman hasil

- Kartu skor total besar dengan status **Lulus** atau **Belum lulus** passing grade.
- Tiga kartu TWK, TIU, TKP: skor, passing grade, bar pencapaian.
- Daftar kelemahan per topik diurutkan dari terendah.
- Pembahasan per soal: soal, jawaban pengguna, kunci, penjelasan; filter Semua, Salah, Ragu-ragu.

### 4.12 Panel admin

Memakai tokens yang sama, lebih padat: tabel dengan header lengket, baris 48 px, filter di atas, aksi di kolom paling kanan, form bank soal dalam dua kolom (editor soal di kiri, pilihan dan bobot di kanan).

---

## 5. Keadaan dan Mikro-interaksi

| Keadaan | Perilaku |
| --- | --- |
| Memuat | Skeleton abu kebiruan pada kartu, bukan spinner penuh halaman |
| Kosong | Ilustrasi kecil + satu kalimat + satu tombol aksi (misalnya "Belum ada riwayat. Mulai tryout pertama") |
| Error form | Pesan merah di bawah input dan ikon peringatan; fokus pindah ke input pertama yang salah |
| Sukses | Toast kanan atas, hilang otomatis setelah 4 detik |
| Simpan jawaban | Indikator kecil "Tersimpan" di header ujian; bila gagal, "Mencoba lagi" dan antre ulang |
| Transisi | 150 sampai 200 ms, `ease-out`; hormati `prefers-reduced-motion` |

---

## 6. Aksesibilitas

- Kontras teks minimal WCAG AA (4.5:1). `ink-400` hanya untuk placeholder dan label non-esensial.
- Seluruh halaman ujian bisa dioperasikan dengan keyboard: angka 1 sampai 5 atau huruf A sampai E memilih jawaban, panah kiri dan kanan berpindah soal.
- Target sentuh minimal 44 x 44 px.
- Ikon fungsional punya `aria-label`; ikon dekoratif `aria-hidden="true"`.
- Timer diumumkan ke pembaca layar hanya pada ambang tertentu (10, 5, 1 menit), bukan setiap detik.
- Ukuran font dapat diperbesar sampai 200 persen tanpa memutus layout.

---

## 7. Struktur Komponen Vue (Inertia)

```
resources/js/
  Layouts/
    AppLayout.vue        # sidebar + topbar + panel kanan
    ExamLayout.vue       # header ujian tanpa sidebar
    AdminLayout.vue
    GuestLayout.vue
  Components/
    ui/                  # Button, Card, Input, Badge, Modal, Toast, Skeleton, ProgressBar, ProgressRing
    dashboard/           # HeroGreeting, ContinueCard, SubtestCard, DailyGoal, Leaderboard, MissionCard
    exam/                # QuestionView, OptionItem, QuestionGrid, ExamTimer, SubmitDialog
    result/              # ScoreSummary, SubtestScore, WeakTopics, ReviewItem
  Pages/
    Dashboard.vue
    Tryouts/Index.vue
    Tryouts/Show.vue
    Exam/Attempt.vue
    Results/Show.vue
    Progress/Index.vue
    Leaderboard/Index.vue
    Admin/...
```

Aturan:

- Komponen `ui/` tidak tahu soal domain CPNS; komponen domain disusun dari `ui/`.
- Warna dan ukuran hanya lewat token Tailwind di atas, tidak ada hex di komponen.
- Props dikirim dari controller lewat Inertia; hindari memanggil API terpisah untuk data halaman.

---

## 8. Aset dan Konten

- Ikon: Lucide (garis, 1.75 px).
- Ilustrasi: dibuat orisinal atau berlisensi jelas; tidak menyalin maskot, karakter, atau logo dari produk lain.
- Gaya bahasa: ramah, ringkas, bahasa Indonesia baku santai ("Lanjutkan Latihan", "Belum lulus passing grade, ayo perbaiki TIU").
- Footer wajib memuat pernyataan bahwa platform ini bukan layanan resmi BKN atau instansi pemerintah.

---

## 9. Daftar Periksa Sebelum Rilis

- [ ] Semua halaman diuji di lebar 360, 768, dan 1280 px.
- [ ] Kontras warna diperiksa untuk kartu subtes dan status ujian.
- [ ] Halaman ujian diuji hanya dengan keyboard.
- [ ] Keadaan memuat, kosong, dan error ada di setiap daftar dan form.
- [ ] Mode gelap diputuskan: ditunda ke fase 2, atau dibuat sekarang dengan token terpisah.
