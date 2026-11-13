# PRD Web Latihan CPNS

Nov 9, 2026 · @andre

## 1. Ringkasan dan Latar Belakang

Produk ini adalah platform tryout berbasis web yang membantu calon peserta seleksi CPNS berlatih Seleksi Kompetensi Dasar (SKD) dengan simulasi mirip CAT BKN, lengkap dengan skor otomatis dan pembahasan soal.

**Masalah yang diselesaikan**

- Peserta sulit mengukur kesiapan karena latihan tersebar di PDF, grup chat, dan aplikasi yang tampilannya berbeda dari ujian asli.
- Skor TKP memakai bobot 1 sampai 5 per jawaban, sehingga tidak bisa dikoreksi manual dengan cepat.
- Peserta tidak tahu subtes dan topik mana yang paling lemah, sehingga belajar tanpa prioritas.

**Gambaran SKD yang disimulasikan**

| Subtes | Jumlah soal | Skor per soal | Passing grade |
| --- | --- | --- | --- |
| TWK (Tes Wawasan Kebangsaan) | 30 | 0 atau 5 | 65 |
| TIU (Tes Intelegensia Umum) | 35 | 0 atau 5 | 80 |
| TKP (Tes Karakteristik Pribadi) | 45 | 1 sampai 5 | 166 |

Total 110 soal dengan durasi 100 menit. Angka di atas adalah asumsi awal; passing grade dan jumlah soal dibuat dapat dikonfigurasi admin karena bisa berubah mengikuti regulasi seleksi tahun berjalan.

## 2. Tujuan dan Metrik Keberhasilan

Tujuan utama: peserta bisa mengerjakan tryout SKD penuh, melihat skor per subtes, dan tahu topik yang harus dipelajari ulang, dalam satu platform.

**Tujuan produk**

1. Menyediakan simulasi ujian dengan timer, navigasi soal, dan penilaian yang konsisten dengan format SKD.
2. Memberi analisis kelemahan per subtes dan per topik setelah setiap tryout.
3. Memberi admin alat yang cepat untuk mengelola bank soal, paket tryout, dan pengguna.

**Metrik keberhasilan (target 3 bulan setelah rilis, dapat disesuaikan)**

| Metrik | Target |
| --- | --- |
| Pengguna terdaftar | 1.000 |
| Tryout selesai per pengguna aktif per bulan | minimal 3 |
| Tryout yang diselesaikan hingga submit | minimal 80% dari yang dimulai |
| Waktu muat halaman soal (p95) | di bawah 2 detik |
| Kenaikan rata-rata skor total antara tryout pertama dan ketiga | minimal 10% |

## 3. Persona dan User Stories

Ada dua peran utama: peserta yang berlatih, dan admin yang mengelola konten.

| Persona | Profil | Kebutuhan utama |
| --- | --- | --- |
| Peserta (calon CPNS) | Lulusan S1/D3, 22 sampai 35 tahun, belajar dari ponsel dan laptop | Latihan realistis, skor jelas, tahu topik lemah |
| Admin | Pengelola bimbel atau pemilik platform | Input soal cepat, atur paket, pantau penggunaan |

**User stories peserta**

- Sebagai peserta, saya ingin mendaftar dan masuk dengan email atau akun Google agar progres saya tersimpan.
- Sebagai peserta, saya ingin memilih paket tryout dan mengerjakannya dengan timer 100 menit agar terbiasa dengan tekanan waktu.
- Sebagai peserta, saya ingin menandai soal ragu-ragu dan melompat antar nomor agar bisa mengatur strategi.
- Sebagai peserta, saya ingin melihat skor TWK, TIU, TKP, status lulus passing grade, dan pembahasan setelah submit.
- Sebagai peserta, saya ingin melihat grafik perkembangan skor dan peringkat agar tahu posisi saya.
- Sebagai peserta, saya ingin melanjutkan tryout yang terputus karena koneksi hilang agar jawaban tidak hilang.

**User stories admin**

- Sebagai admin, saya ingin menambah soal beserta pilihan, kunci, bobot TKP, dan pembahasan agar bank soal terus bertambah.
- Sebagai admin, saya ingin impor soal dari file Excel atau CSV agar tidak mengetik satu per satu.
- Sebagai admin, saya ingin menyusun paket tryout dari bank soal, mengatur jadwal buka, dan harga agar bisa dijual atau digratiskan.
- Sebagai admin, saya ingin melihat statistik per soal dan per paket agar bisa memperbaiki soal yang bermasalah.

## 4. Ruang Lingkup

Rilis pertama (MVP) fokus pada tryout SKD penuh dan pembahasan; pembayaran, kelas video, dan aplikasi mobile native ditunda.

| Fase | Isi |
| --- | --- |
| MVP (in scope) | Registrasi dan login, bank soal, paket tryout, simulasi CAT dengan timer, penilaian otomatis, pembahasan, riwayat nilai, panel admin |
| Fase 2 | Pembayaran (Midtrans atau Xendit), kode voucher, peringkat nasional, latihan per topik, notifikasi email |
| Fase 3 | Latihan SKB per formasi, forum diskusi, PWA, laporan bagi mitra bimbel |
| Di luar lingkup | Aplikasi mobile native, kelas video langsung, pendaftaran resmi CPNS ke SSCASN |

Produk ini bersifat latihan dan tidak berafiliasi dengan BKN atau instansi pemerintah; hal ini perlu dinyatakan jelas di footer dan syarat penggunaan.

## 5. Kebutuhan Fungsional

Setiap kebutuhan diberi kode agar mudah dilacak ke tiket dan pengujian. Prioritas: P0 wajib untuk MVP, P1 sebaiknya ada, P2 fase berikutnya.

**Autentikasi dan akun**

| Kode | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-01 | Registrasi dan login dengan email dan kata sandi, verifikasi email | P0 |
| FR-02 | Login dengan Google (Socialite) | P1 |
| FR-03 | Reset kata sandi dan edit profil | P0 |
| FR-04 | Peran pengguna: peserta dan admin | P0 |

**Tryout dan simulasi (peserta)**

| Kode | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-10 | Daftar paket tryout dengan filter gratis atau berbayar dan status (belum, berjalan, selesai) | P0 |
| FR-11 | Halaman ujian: satu soal per layar, timer mundur server-side, panel nomor soal berwarna (terjawab, ragu, kosong) | P0 |
| FR-12 | Jawaban tersimpan otomatis setiap pilihan diubah, sehingga bisa dilanjutkan setelah refresh atau koneksi putus | P0 |
| FR-13 | Submit manual atau otomatis saat waktu habis | P0 |
| FR-14 | Penilaian otomatis: TWK dan TIU 0 atau 5 per soal, TKP bobot 1 sampai 5 sesuai pilihan | P0 |
| FR-15 | Hasil: skor per subtes, total, status lulus passing grade, dan pembahasan per soal | P0 |
| FR-16 | Riwayat nilai dan grafik perkembangan | P1 |
| FR-17 | Analisis kelemahan per topik (misalnya Pancasila, silogisme, numerik) | P1 |
| FR-18 | Peringkat per paket tryout | P1 |
| FR-19 | Latihan per subtes atau topik tanpa timer ketat | P2 |

**Panel admin**

| Kode | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-30 | CRUD bank soal: teks, gambar, 5 pilihan, kunci atau bobot, pembahasan, subtes, topik, tingkat kesulitan | P0 |
| FR-31 | Editor teks kaya untuk soal dan pembahasan, termasuk gambar dan rumus sederhana | P1 |
| FR-32 | Impor soal dari Excel atau CSV dengan validasi per baris | P1 |
| FR-33 | CRUD paket tryout: pilih soal, atur durasi, passing grade, jadwal buka dan tutup, harga | P0 |
| FR-34 | Manajemen pengguna: lihat, nonaktifkan, reset | P0 |
| FR-35 | Dasbor statistik: jumlah pengguna, tryout selesai, rata-rata skor, soal dengan tingkat benar terendah | P1 |
| FR-36 | Pembayaran dan voucher | P2 |

**Aturan bisnis penting**

- Timer dihitung di server dari waktu mulai; klien hanya menampilkan sisa waktu agar tidak bisa dimanipulasi.
- Satu pengguna hanya boleh punya satu percobaan aktif per paket pada satu waktu.
- Urutan soal dan pilihan jawaban dapat diacak per percobaan; kunci jawaban tidak pernah dikirim ke klien sebelum submit.
- Soal yang sudah dipakai dalam percobaan peserta tidak boleh diubah kuncinya; perubahan dibuat sebagai versi baru.

## 6. Alur Pengguna Utama

Alur inti peserta berjalan lurus dari memilih paket sampai membaca pembahasan.

1. Peserta membuka landing page, mendaftar, dan memverifikasi email.
2. Di dasbor, peserta memilih paket tryout dan menekan Mulai; sistem membuat percobaan baru dan mencatat waktu mulai di server.
3. Peserta mengerjakan soal satu per satu; setiap jawaban disimpan otomatis dan soal bisa ditandai ragu-ragu.
4. Peserta menekan Selesai, atau sistem submit otomatis saat 100 menit habis.
5. Sistem menghitung skor TWK, TIU, TKP, total, dan status lulus.
6. Peserta melihat halaman hasil, lalu membuka pembahasan per soal dan analisis topik lemah.

**Kasus tepi**

- Koneksi terputus atau tab tertutup: saat peserta kembali sebelum waktu habis, percobaan dilanjutkan dengan sisa waktu dari server.
- Waktu habis saat peserta offline: percobaan dinilai otomatis dari jawaban yang sudah tersimpan.
- Peserta membuka percobaan di dua perangkat: perangkat terbaru aktif, yang lama diminta memuat ulang.

## 7. Arsitektur Teknis

Aplikasi monolit Laravel 13 dengan frontend Vue lewat Inertia: satu codebase dan satu deployment, tanpa API REST terpisah untuk halaman web.

| Lapisan | Pilihan | Catatan |
| --- | --- | --- |
| Backend | Laravel 13 | Mensyaratkan PHP 8.3 atau lebih baru; perbaikan bug sampai Q3 2027, perbaikan keamanan sampai Q1 2028 |
| Frontend | Vue 3 dan Inertia.js | Halaman berupa komponen Vue; data dari controller dikirim sebagai props |
| Styling | Tailwind CSS | Utility-first dengan token warna sendiri |
| Build | Vite | Bawaan ekosistem Laravel |
| Database | MySQL&#32; | Pilih satu sejak awal |
| Cache, sesi, antrean | Redis | Antrean untuk submit otomatis dan email; bisa mulai dengan driver database |
| Autentikasi | Laravel Fortify atau starter kit Vue | Login email, Google lewat Socialite |
| Pengujian | Pest | Feature test untuk penilaian skor dan timer |

Versi Inertia dan Tailwind: pakai rilis stabil terbaru yang kompatibel dengan Laravel 13 dan cek dokumentasi resminya saat setup. Sumber persyaratan Laravel 13: [Laravel News](https://laravel-news.com/laravel-13-released), diakses 9 November 2026.

&#91;embedded content: arsitektur · 3 lapisan\]

Inertia mengirim data dari controller langsung ke komponen Vue sebagai props, sehingga halaman web tidak memerlukan API terpisah.

**Konvensi kode**

- Controller dibuat tipis; logika percobaan dan penilaian skor ada di class Service agar mudah diuji.
- Validasi di Form Request, otorisasi di Policy.
- Komponen Vue dipakai ulang dari satu folder Components; layout dipisah untuk peserta, admin, dan ujian.
- Jawaban disimpan lewat request kecil per soal, tanpa memuat ulang seluruh halaman ujian.

## 8. Model Data

Skema inti terdiri dari sembilan tabel; bobot TKP disimpan per pilihan jawaban sehingga penilaian cukup menjumlahkan bobot yang dipilih.

| Tabel | Kolom penting | Relasi |
| --- | --- | --- |
| users | name, email, password, role (peserta atau admin), google\_id | punya banyak attempts |
| subtests | code (TWK, TIU, TKP), name, question\_count, passing\_grade | punya banyak topics dan questions |
| topics | subtest\_id, name | milik subtests |
| questions | subtest\_id, topic\_id, body, image\_path, explanation, difficulty, version | punya banyak options |
| options | question\_id, label (A sampai E), body, is\_correct, weight (0 sampai 5) | milik questions |
| tryouts | title, description, duration\_minutes, price, opens\_at, closes\_at, is\_published | banyak ke banyak questions (tryout\_question) |
| tryout\_question | tryout\_id, question\_id, position | tabel pivot |
| attempts | user\_id, tryout\_id, started\_at, ends\_at, submitted\_at, score\_twk, score\_tiu, score\_tkp, total\_score, is\_passed | punya banyak attempt\_answers |
| attempt\_answers | attempt\_id, question\_id, option\_id, is\_flagged, answered\_at | milik attempts |

Catatan desain:

- Indeks unik pada (attempt\_id, question\_id) agar satu soal hanya punya satu jawaban per percobaan.
- Skor disimpan di attempts saat submit sehingga riwayat tidak berubah walau soal direvisi.
- Gunakan soft delete pada questions dan tryouts agar riwayat peserta tetap utuh.

## 9. Kebutuhan Non-Fungsional

Tiga hal paling kritis: jawaban tidak boleh hilang, timer tidak boleh bisa dicurangi, dan sistem harus tahan saat banyak peserta mengerjakan bersamaan.

| Aspek | Persyaratan |
| --- | --- |
| Performa | Halaman soal termuat di bawah 2 detik (p95); simpan jawaban di bawah 500 ms |
| Kapasitas | Minimal 500 peserta mengerjakan bersamaan pada tryout serentak |
| Keamanan | Hash kata sandi bawaan Laravel, proteksi CSRF, rate limiting login, kunci jawaban tidak dikirim ke klien sebelum submit, validasi otorisasi pada setiap aksi admin |
| Anti-curang dasar | Timer di server, deteksi pindah tab dicatat (tanpa memblokir), satu percobaan aktif per paket |
| Ketersediaan | Target 99,5% uptime; backup database harian |
| Kompatibilitas | Chrome, Firefox, Safari, Edge versi terbaru; responsif dari lebar 360 px |
| Aksesibilitas | Navigasi keyboard di halaman ujian, kontras warna memenuhi WCAG AA, ukuran font dapat diperbesar |
| Privasi | Patuhi UU Pelindungan Data Pribadi: persetujuan saat daftar, pengguna dapat meminta hapus akun |
| Observabilitas | Log error terpusat, pemantauan antrean, dan alert saat tingkat error naik |

## 10. UI/UX dan Daftar Halaman

Antarmuka dibuat bersih dan fokus, dengan halaman ujian yang meniru tata letak CAT: soal di kiri, panel nomor di kanan, timer selalu terlihat.

**Prinsip desain**

- Tailwind CSS dengan token warna sendiri (satu warna utama, satu warna aksen), mode terang sebagai default.
- Mobile first; panel nomor soal berubah menjadi laci (drawer) di layar kecil.
- Status nomor soal berwarna: terjawab, ragu-ragu, belum dijawab, dan soal aktif; jangan hanya mengandalkan warna, tambahkan ikon atau pola.
- Komponen dipakai ulang: tombol, kartu, tabel, modal konfirmasi, toast, dan skeleton loading.

**Daftar halaman**

| Area | Halaman |
| --- | --- |
| Publik | Landing page, daftar paket, login, registrasi, lupa kata sandi, syarat dan privasi |
| Peserta | Dasbor, detail paket, halaman ujian, hasil dan pembahasan, riwayat nilai, profil |
| Admin | Dasbor statistik, bank soal (daftar, form, impor), paket tryout, pengguna, pengaturan subtes dan passing grade |

Halaman ujian memakai layout terpisah tanpa navigasi utama agar peserta tidak terganggu, dengan dialog konfirmasi sebelum submit yang menampilkan jumlah soal kosong dan ragu-ragu.

## 11. Roadmap dan Milestone

MVP direncanakan selesai dalam 12 minggu dengan dua gerbang: demo ujian end-to-end di minggu ke-8 dan rilis di minggu ke-12. Durasi adalah estimasi awal untuk tim kecil dan perlu disesuaikan.

&#91;embedded content: roadmap MVP · 5 fase, 2 gerbang\]

| Fase | Keluaran | Kriteria selesai |
| --- | --- | --- |
| Persiapan | Repo dan CI, setup Laravel 13 dengan Vue, Inertia, Tailwind, desain UI halaman utama, skema database | Aplikasi kosong bisa dijalankan dan di-deploy ke staging |
| Fondasi | Autentikasi, peran peserta dan admin, CRUD bank soal | Admin bisa menambah soal lengkap dengan bobot TKP |
| Inti ujian | Paket tryout, halaman ujian, timer server-side, simpan otomatis, penilaian skor | Satu tryout 110 soal bisa dikerjakan dan dinilai dengan benar |
| Hasil dan admin | Halaman hasil, pembahasan, riwayat nilai, dasbor admin, impor Excel | Peserta melihat skor per subtes dan pembahasan |
| QA dan rilis | Uji beban, perbaikan bug, beta tertutup, dokumentasi | Lulus uji beban 500 peserta bersamaan dan tidak ada bug kritis |

Fase 2 (pembayaran, peringkat, latihan per topik) dan Fase 3 (SKB, forum, PWA) dijadwalkan setelah data penggunaan MVP terkumpul.

## 12. Risiko dan Pertanyaan Terbuka

Risiko terbesar ada pada kualitas dan hak cipta soal, serta lonjakan beban saat tryout serentak.

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| Soal tidak berkualitas atau melanggar hak cipta | Kepercayaan pengguna turun, risiko hukum | Soal dibuat sendiri atau berlisensi, ada proses review sebelum publish |
| Lonjakan akses saat tryout serentak | Halaman lambat atau error | Cache, antrean, uji beban sebelum rilis, siapkan skala server |
| Jawaban hilang karena koneksi buruk | Peserta kesal | Simpan otomatis per jawaban, antrean ulang di klien, lanjut setelah reconnect |
| Kecurangan (berbagi akun, mencatat soal) | Peringkat tidak adil | Satu sesi aktif, log aktivitas, acak soal dan pilihan |
| Perubahan format atau passing grade SKD | Simulasi tidak sesuai | Parameter dikonfigurasi admin, bukan ditulis permanen di kode |

**Pertanyaan terbuka**

- Apakah MVP gratis seluruhnya, atau sudah ada paket berbayar sejak awal?
- Siapa yang menulis dan mereview soal, dan berapa soal awal yang tersedia saat rilis?
- Apakah perlu fitur SKB per formasi pada rilis pertama?
- Di mana aplikasi akan di-host (VPS sendiri, Laravel Cloud, atau lainnya)?
