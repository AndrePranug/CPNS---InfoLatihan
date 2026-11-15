# Progress Report - Perombakan UI/UX Latihan CPNS

**Status:** Selesai Penuh (100%)  
**Build Status:** Vite build sukses (`npm run build`), PHP Pint lulus (`vendor/bin/pint --format agent`), PHPUnit 97 tests passed (518 assertions).

---

## 1. Landing Page Redesign (Sesuai Referensi Eduma Theme)
File: `resources/js/Pages/Welcome.vue`

1. **Top Contact Bar (Navy):**
   - Info kontak resmi: Email (`bantuan@latihancpns.id`), Telp/WhatsApp (`+62 812-3456-7890`), jam operasional, dan ikon media sosial.
2. **Main Navigation Bar:**
   - Logo edukasi modern dengan lencana kredensial BKN & KemenPAN RB.
   - Menu navigasi: Beranda, Paket Tryout, Kategori Soal, Jadwal Akbar, Mentoring, FAQ.
   - Tombol CTA oranye energik (*Eduma signature accent* `#ff782d`).
3. **Hero Section:**
   - Headline tegas: *"Wujudkan Impian Lolos CPNS Bersama Platform Simulasi No. 1 di Indonesia"*.
   - Tombol ganda: *"Mulai Tryout Gratis"* & *"Pelajari Sistem CAT"*.
   - Visual model peserta didik dengan latar belakang geometri biru bulat, dilengkapi *floating badges*:
     - Kartu *"25.000+ Peserta Aktif"* dengan avatar realistik.
     - Lencana *"95% Lolos PG"* dengan checkmark akreditasi.
4. **Paket Tryout Unggulan (Featured Packages - 4 Cards Grid):**
   - Format kartu kursus dengan foto sampul kurasi Unsplash, indikator level, jumlah butir soal, estimasi durasi, rating bintang, dan harga coret/diskon.
   - Tombol tengah: *"Lihat Semua Paket"*.
5. **Split Section (Kategori Subtes + Mengapa Memilih Kami):**
   - **Kiri (Kategori):** 6 kotak kategori berpola grid (TWK, TIU, TKP, SKB Formasi Khusus, Penalaran Logis, Pembahasan Video).
   - **Kanan (Why Choose Us):** 4 poin keunggulan utama (Mentor Ahli ASN, Sistem CAT Real-Time BKN, Kunci & Pembahasan Teruji, Analisis Passing Grade) + Foto pembina dengan lencana pengalaman *"10+ Tahun Membimbing CPNS"*.
6. **Pita Statistik Navy (Eduma Navy Bar):**
   - 4 angka pencapaian: 25.000+ Peserta Aktif, 50+ Mentor Berpengalaman, 1.200+ Bank Soal Teruji, 95% Tingkat Kepuasan.
7. **Mentor Terbaik & Jadwal Kegiatan (Events):**
   - **Meet Our Top Mentors:** 4 profil instruktur ASN & Master Penalaran lengkap dengan foto, nama, spesialisasi, dan tautan sosial.
   - **Upcoming Events:** 3 agenda tryout akbar nasional & webinar bedah kisi-kisi dengan kotak penanggalan tanggal/bulan yang kontras.
8. **3-Kolom Sinergis (Testimoni + Artikel/Tips + FAQ Accordion):**
   - **Testimoni Peserta:** Kartu testimoni otentik dari peraih NIP 2024.
   - **Tips & Info CASN:** 3 artikel persiapan ujian dan strategi pengerjaan soal.
   - **FAQ Interaktif:** Accordion expand/collapse untuk pertanyaan umum seputar CAT, passing grade, dan jaminan pembahasan.
9. **Newsletter Banner & Quick Contact Cards:**
   - Kotak berlangganan tips & update formasi CPNS 2026.
   - 3 kartu kontak cepat (Telepon, Email, Lokasi Kantor).
10. **Footer Navy 5-Kolom:**
    - Identitas platform, tautan cepat, navigasi subtes, legalitas/bantuan, galeri dokumentasi kegiatan belajar, serta hak cipta resmi.

---

## 2. Navigasi Admin: Sidebar Kiri (Admin Left Sidebar)
File: `resources/js/Layouts/AdminLayout.vue`

- Mengganti total navigasi bar atas (horizontal header) menjadi **Sidebar Kiri Menetap** (`w-64`, sticky full height).
- Pengelompokan menu rapi:
  - **Menu Utama:** Dasbor, Bank Soal, Paket Tryout, Voucher & Promo.
  - **Struktur Ujian:** Formasi SKB, Subtes & PG.
  - **Data & Akun:** Laporan Mitra, Pengguna.
- Dilengkapi ikon SVG presisi di setiap menu, indikator rute aktif yang kontras, tombol cepat menuju situs publik, dan profil admin terintegrasi.
- Responsif penuh untuk mobile via hamburger drawer.

---

## 3. Konsistensi Desain Seluruh Modul Peserta & Admin
- **Peserta:** Dasbor, Ujian (CAT Simulator BKN), Hasil & Pembahasan, Statistik & Perkembangan, Riwayat, Peringkat Nasional, Latihan Mandiri, Forum Diskusi, Checkout, dan Pengaturan Profil.
- **Admin:** Dasbor (dengan KPI berwarna & Quick Management Hub), Bank Soal, Form Pembuatan Soal, Impor Soal, Paket Tryout, Subtes & PG, Formasi SKB, Voucher, Pengguna, dan Laporan Mitra.
- **Auth:** Login, Register, Forgot Password, Reset Password, Verify Email dengan formulir modern dan tombol Google OAuth.
