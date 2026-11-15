<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const activeFaq = ref(0);
const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

// Featured Courses / Tryouts (Eduma card layout)
const featuredTryouts = [
    {
        id: 1,
        title: 'Tryout Akbar SKD CAT Nasional 2026',
        category: 'Simulasi Lengkap',
        catBg: 'bg-blue-600',
        image: 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80',
        duration: '100 Menit',
        questions: '110 Butir Soal',
        level: 'Standar BKN',
        price: 'Gratis',
        originalPrice: 'Rp50.000',
        rating: '4.9',
        reviews: 1420,
    },
    {
        id: 2,
        title: 'Mastery TWK: Penalaran & Studi Kasus HOTS',
        category: 'TWK Intensif',
        catBg: 'bg-rose-600',
        image: 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80',
        duration: '45 Menit',
        questions: '30 Butir Soal',
        level: 'Tingkat Lanjut',
        price: 'Rp29.000',
        originalPrice: 'Rp59.000',
        rating: '4.8',
        reviews: 890,
    },
    {
        id: 3,
        title: 'Drilling TIU: Hitung Cepat & Silogisme Analitis',
        category: 'TIU Kilat',
        catBg: 'bg-indigo-600',
        image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80',
        duration: '50 Menit',
        questions: '35 Butir Soal',
        level: 'Semua Level',
        price: 'Rp35.000',
        originalPrice: 'Rp69.000',
        rating: '4.9',
        reviews: 950,
    },
    {
        id: 4,
        title: 'Bedah Trik Jawaban Bobot 5 TKP Pelayanan Publik',
        category: 'TKP Skala 1-5',
        catBg: 'bg-emerald-600',
        image: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=600&q=80',
        duration: '60 Menit',
        questions: '45 Butir Soal',
        level: 'Strategi NIP',
        price: 'Rp39.000',
        originalPrice: 'Rp79.000',
        rating: '4.9',
        reviews: 1680,
    },
];

// Categories (6 horizontal cards)
const categories = [
    {
        title: 'Nasionalisme & TWK',
        count: '18 Paket Ujian',
        icon: 'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918',
        color: 'text-blue-600 bg-blue-50',
    },
    {
        title: 'Logika & Silogisme TIU',
        count: '24 Paket Ujian',
        icon: 'M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18',
        color: 'text-indigo-600 bg-indigo-50',
    },
    {
        title: 'Numerik & Deret Angka',
        count: '20 Paket Ujian',
        icon: 'M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Z',
        color: 'text-amber-600 bg-amber-50',
    },
    {
        title: 'Karakteristik Pribadi (TKP)',
        count: '32 Paket Ujian',
        icon: 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
        color: 'text-emerald-600 bg-emerald-50',
    },
    {
        title: 'SKB Teknis Formasi',
        count: '15 Bidang Kementerian',
        icon: 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387',
        color: 'text-purple-600 bg-purple-50',
    },
    {
        title: 'Paket Bundling VIP & Pembahasan',
        count: '10 Bundel Soal',
        icon: 'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-4.5A3.75 3.75 0 0 0 12 10.5a3.75 3.75 0 0 0-4.5 3.75v4.5m9 0h.008',
        color: 'text-rose-600 bg-rose-50',
    },
];

// Why Choose Us
const whyChooseUs = [
    {
        title: 'Instruktur & Pembuat Soal Berpengalaman',
        desc: 'Disusun oleh ASN lulusan terbaik dan praktisi pengajar yang memahami pola soal BKN.',
        icon: 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342',
    },
    {
        title: 'Simulasi Fleksibel Kapan Saja',
        desc: 'Akses latihan dari laptop atau ponsel dengan tampilan responsif dan penyimpanan autosave.',
        icon: 'M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
    },
    {
        title: 'Materi Berkualitas Standar HOTS',
        desc: 'Kumpulan soal menuntut penalaran kritis dan daya analisis logika tingkat tinggi.',
        icon: 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z',
    },
    {
        title: 'Analisis Peringkat Nasional',
        desc: 'Evaluasi posisi kompetitifmu secara transparan bersama ribuan calon aparatur sipil negara.',
        icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
    },
];

// Top Mentors
const mentors = [
    {
        name: 'Dr. Hendra Saputra',
        role: 'Spesialis Penalaran TIU',
        agency: 'Dosen Matematika & Statistik',
        image: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=300&q=80',
    },
    {
        name: 'Sarah Wulandari, M.Pd.',
        role: 'Pakar TWK & Wawasan Kebangsaan',
        agency: 'Alumni ASN Kemenkeu',
        image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80',
    },
    {
        name: 'Bambang Irawan, M.Si.',
        role: 'Instruktur TKP & Psikometri',
        agency: 'Praktisi Asesmen SDM Aparatur',
        image: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80',
    },
    {
        name: 'Rina Kartika, S.Psi.',
        role: 'Konsultan Karir ASN',
        agency: 'Peraih Skor SKD 492 CPNS 2024',
        image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=300&q=80',
    },
];

// Upcoming Events
const events = [
    {
        day: '24',
        month: 'NOV',
        title: 'Tryout Akbar SKD Akbar Nasional Gelombang 3',
        time: '09:00 - 11:00 WIB',
        location: 'Ujian Online Serentak',
        image: 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=300&q=80',
    },
    {
        day: '02',
        month: 'DES',
        title: 'Bedah Trik Jawaban Bobot 5 TKP bersama Pakar BKN',
        time: '19:30 - 21:00 WIB',
        location: 'Live Webinar Zoom',
        image: 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&w=300&q=80',
    },
    {
        day: '15',
        month: 'DES',
        title: 'Bootcamp Hitung Cepat 1 Detik Soal Deret & Aljabar',
        time: '13:00 - 16:00 WIB',
        location: 'Intensif Online',
        image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=300&q=80',
    },
];

// Blog Posts
const blogs = [
    {
        title: 'Strategi Manajemen Waktu 100 Menit untuk 110 Butir Soal CAT BKN',
        date: '10 Nov 2026',
        tag: 'Tips Ujian',
        image: 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=400&q=80',
    },
    {
        title: 'Kisi-kisi Resmi SKD MenPAN-RB: Bedah Perubahan Materi TWK HOTS',
        date: '04 Nov 2026',
        tag: 'Kebijakan',
        image: 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&w=400&q=80',
    },
    {
        title: 'Cara Mengamankan Poin Maksimal di Subtes TKP Pelayanan Publik',
        date: '28 Okt 2026',
        tag: 'Materi TKP',
        image: 'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?auto=format&fit=crop&w=400&q=80',
    },
];

// FAQs
const faqs = [
    {
        q: 'Bagaimana cara mendaftar dan mengikuti tryout di Latihan CPNS?',
        a: 'Cukup buat akun gratis dengan email atau akun Google. Setelah masuk, Anda dapat langsung memilih paket tryout gratis yang tersedia dan memulai simulasi ujian seketika.',
    },
    {
        q: 'Apakah sistem penghitungan skor dan timer sama persis dengan CAT BKN?',
        a: 'Ya, simulasi menggunakan timer server 100 menit terpusat dan penilaian otomatis: TWK (0/5), TIU (0/5), dan TKP bertingkat 1 sampai 5 poin.',
    },
    {
        q: 'Bisakah saya mengerjakan latihan melalui ponsel pintar?',
        a: 'Bisa. Seluruh antarmuka simulasi dioptimasi secara responsif sehingga Anda nyaman berlatih kapan pun melalui smartphone, tablet, maupun laptop.',
    },
    {
        q: 'Apakah ada sertifikat dan analisis pembahasan setelah ujian?',
        a: 'Setiap paket ujian selesai, sistem akan menampilkan grafik analisis kelemahan per materi, ranking nasional, dan pembahasan lengkap tiap butir soal.',
    },
];

// Gallery Images
const galleryImages = [
    'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=200&q=80',
    'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=200&q=80',
    'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=200&q=80',
    'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=200&q=80',
    'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=200&q=80',
    'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&w=200&q=80',
];
</script>

<template>
    <div class="min-h-screen bg-white text-slate-800 font-sans selection:bg-brand-600 selection:text-white">
        <!-- 1. TOP SLIM BAR (Eduma top header: contact left, social links right) -->
        <div class="border-b border-slate-800/20 bg-[#0c1f40] px-4 py-2 text-[11px] text-slate-300">
            <div class="mx-auto flex max-w-6xl items-center justify-between">
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-amber-400">
                            <path d="M3 4a2 2 0 0 0-2 2v1.161l8.441 4.221a1.25 1.25 0 0 0 1.118 0L19 7.162V6a2 2 0 0 0-2-2H3Z"/>
                            <path d="m19 8.839-7.77 3.885a2.75 2.75 0 0 1-2.46 0L1 8.839V14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.839Z"/>
                        </svg>
                        info@latihancpns.id
                    </span>
                    <span class="hidden sm:flex items-center gap-1.5">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-amber-400">
                            <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h1.148a1.5 1.5 0 0 1 1.465 1.175l.716 3.223a1.5 1.5 0 0 1-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 0 0 6.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 0 1 1.767-1.052l3.223.716A1.5 1.5 0 0 1 18 15.352V16.5a1.5 1.5 0 0 1-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 0 1 2.43 8.326 13.019 13.019 0 0 1 2 5V3.5Z" clip-rule="evenodd"/>
                        </svg>
                        +62 812-3456-7890
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-slate-400 hidden sm:inline">Ikuti Kami:</span>
                    <div class="flex items-center gap-3 text-slate-300">
                        <a href="#" class="hover:text-white transition-colors" aria-label="Facebook">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="hover:text-white transition-colors" aria-label="Twitter">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="hover:text-white transition-colors" aria-label="LinkedIn">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76c-.95 0-1.72-.77-1.72-1.72s.77-1.72 1.72-1.72 1.72.77 1.72 1.72-.77 1.72-1.72 1.72m1.39 9.74v-8.37H5.07v8.37h2.78z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MAIN HEADER (Eduma logo + menu + search + orange CTA) -->
        <header class="sticky top-0 z-30 border-b border-slate-100 bg-white/95 backdrop-blur-md">
            <div class="mx-auto flex h-20 max-w-6xl items-center justify-between px-4 sm:px-6">
                <!-- Brand Logo (Eduma Style: Cap Icon + Modern Typography) -->
                <Link href="/" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#0c1f40] text-white shadow-md">
                        <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xl font-black tracking-tight text-[#0c1f40] leading-none">Eduma.</span>
                        <span class="text-[10px] font-semibold text-slate-400 tracking-wider">Simulasi Latihan CPNS</span>
                    </div>
                </Link>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-7 text-[13px] font-bold text-slate-700">
                    <Link href="/" class="text-brand-600">Beranda</Link>
                    <Link href="/tryouts" class="hover:text-brand-600 transition-colors">Paket Tryout</Link>
                    <Link href="/practice" class="hover:text-brand-600 transition-colors">Latihan Soal</Link>
                    <Link href="/rankings" class="hover:text-brand-600 transition-colors">Peringkat</Link>
                    <Link href="/forum" class="hover:text-brand-600 transition-colors">Forum Diskusi</Link>
                </nav>

                <!-- Search Icon & Orange Action Button (Eduma style) -->
                <div class="flex items-center gap-3">
                    <button type="button" class="hidden sm:flex h-10 w-10 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors" aria-label="Cari Tryout">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>

                    <template v-if="user">
                        <Link
                            href="/dashboard"
                            class="inline-flex h-11 items-center justify-center rounded-md bg-[#ff782d] px-5 text-xs font-bold text-white shadow-sm hover:bg-[#e8661d] active:scale-[0.98] transition-all"
                        >
                            Masuk Dasbor
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="inline-flex h-10 items-center justify-center px-3.5 text-xs font-bold text-slate-700 hover:text-brand-600 transition-colors"
                        >
                            Masuk
                        </Link>
                        <Link
                            href="/register"
                            class="inline-flex h-11 items-center justify-center rounded-md bg-[#ff782d] px-5 text-xs font-bold text-white shadow-sm hover:bg-[#e8661d] active:scale-[0.98] transition-all"
                        >
                            Daftar Sekarang
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- 3. HERO SECTION (Eduma layout: Headline left, Student with circular backdrop right, floating badges) -->
        <section class="relative overflow-hidden bg-gradient-to-br from-[#f8faff] via-[#eef4fd] to-[#e4eefb] pt-12 pb-20">
            <!-- Subtle Dot Matrix Pattern (Right Top & Left Bottom) -->
            <div class="pointer-events-none absolute right-12 top-8 grid grid-cols-6 gap-2 opacity-25">
                <span v-for="d in 24" :key="d" class="h-1 w-1 rounded-full bg-[#0c1f40]"></span>
            </div>

            <div class="mx-auto max-w-6xl px-4 sm:px-6 relative z-10">
                <div class="grid items-center gap-12 lg:grid-cols-12">
                    <!-- Left Hero Content -->
                    <div class="lg:col-span-7 space-y-6">
                        <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-600">
                            <span class="h-0.5 w-5 bg-brand-600"></span>
                            SIMULASI SKD CAT RESMI
                        </div>

                        <h1 class="text-4xl font-black tracking-tight text-[#0c1f40] sm:text-5xl lg:text-[54px] sm:leading-[1.12]">
                            Wujudkan Impian Kelulusan CPNS Bersama <span class="text-brand-600">Eduma</span>
                        </h1>

                        <p class="text-sm sm:text-base leading-relaxed text-slate-600 max-w-xl">
                            Bergabunglah bersama ribuan pejuang ASN di seluruh Indonesia. Kuasai ujian dengan bank soal terakurat standar CAT BKN, timer server 100 menit, dan analisis evaluasi mendalam.
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <Link
                                :href="user ? '/tryouts' : '/register'"
                                class="inline-flex h-12 items-center justify-center rounded-md bg-[#0c1f40] px-7 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-slate-900 active:scale-[0.98] transition-all"
                            >
                                Jelajahi Paket Tryout
                            </Link>
                            <Link
                                href="/practice"
                                class="inline-flex h-12 items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-6 text-xs sm:text-sm font-bold text-slate-700 shadow-xs hover:bg-slate-50 active:scale-[0.98] transition-all"
                            >
                                <span>Pelajari Sistem CAT</span>
                                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </Link>
                        </div>
                    </div>

                    <!-- Right Hero Visual with Cutout Student Photo & Floating Badges -->
                    <div class="lg:col-span-5 relative">
                        <div class="relative mx-auto max-w-md lg:max-w-none">
                            <!-- Circular Blue Backdrop Shape like reference -->
                            <div class="absolute -inset-2 rounded-full bg-[#1d6ff2]/20 blur-2xl"></div>

                            <!-- Geometric circular background -->
                            <div class="relative overflow-hidden rounded-full aspect-square border-4 border-white bg-gradient-to-b from-[#2563eb] to-[#0c1f40] shadow-2xl max-w-[380px] mx-auto">
                                <img
                                    src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80"
                                    alt="Peserta Ujian CPNS"
                                    class="h-full w-full object-cover object-top filter brightness-[0.97]"
                                >
                            </div>

                            <!-- Floating Badge 1: 20K+ Active Students (Top Left of image) -->
                            <div class="absolute top-12 -left-6 rounded-2xl border border-slate-100 bg-white p-3.5 shadow-xl flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-brand-600 font-bold">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 tabular-nums">20.000+</p>
                                    <p class="text-[10px] text-slate-500 font-medium">Peserta Aktif</p>
                                </div>
                            </div>

                            <!-- Floating Badge 2: 95% Success Rate (Top Right of image) -->
                            <div class="absolute top-6 -right-4 rounded-2xl border border-slate-100 bg-white p-3.5 shadow-xl flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white font-bold">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900">95% Lolos PG</p>
                                    <p class="text-[10px] text-slate-400">Passing Grade SKD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. FEATURED COURSES / PAKET TRYOUT UNGGULAN (Eduma 4-card grid) -->
        <section class="py-16 bg-white">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-10">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <h2 class="text-3xl font-black text-[#0c1f40] tracking-tight">Paket Tryout Unggulan</h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Pilihan simulasi paling diminati yang dirancang presisi sesuai format CAT BKN terkini.
                    </p>
                </div>

                <!-- 4 Cards Grid -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <article
                        v-for="item in featuredTryouts"
                        :key="item.id"
                        class="group flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-200"
                    >
                        <div>
                            <!-- Thumbnail with Category Pill -->
                            <div class="relative h-44 overflow-hidden bg-slate-100">
                                <img
                                    :src="item.image"
                                    :alt="item.title"
                                    class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                                <span :class="[item.catBg]" class="absolute left-3 top-3 rounded px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-xs">
                                    {{ item.category }}
                                </span>
                            </div>

                            <!-- Body -->
                            <div class="p-4 space-y-2.5">
                                <h3 class="font-bold text-sm text-[#0c1f40] leading-snug group-hover:text-brand-600 transition-colors line-clamp-2">
                                    {{ item.title }}
                                </h3>

                                <div class="flex items-center gap-3 text-[11px] text-slate-500 pt-1">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        {{ item.duration }}
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        {{ item.questions }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Price & Rating -->
                        <div class="border-t border-slate-100 p-4 flex items-center justify-between">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-sm font-extrabold text-[#ff782d]">{{ item.price }}</span>
                                <span v-if="item.originalPrice" class="text-[11px] text-slate-400 line-through">{{ item.originalPrice }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] font-bold text-amber-500">
                                <svg class="h-3.5 w-3.5 fill-amber-400" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/>
                                </svg>
                                <span>{{ item.rating }}</span>
                                <span class="text-slate-400 font-normal">({{ item.reviews }})</span>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="text-center pt-2">
                    <Link
                        href="/tryouts"
                        class="inline-flex h-11 items-center justify-center rounded-md bg-[#0c1f40] px-7 text-xs font-bold text-white shadow-xs hover:bg-slate-900 transition-colors"
                    >
                        Lihat Seluruh Paket Tryout
                    </Link>
                </div>
            </div>
        </section>

        <!-- 5. CATEGORIES + WHY CHOOSE US (Split layout like Eduma reference) -->
        <section class="py-16 bg-[#f8faff] border-y border-slate-100">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="grid gap-12 lg:grid-cols-12">
                    <!-- Left: Categories Grid (6 cards) -->
                    <div class="lg:col-span-5 space-y-6">
                        <div class="flex items-baseline justify-between">
                            <h2 class="text-2xl font-black text-[#0c1f40]">Kategori Pembelajaran</h2>
                            <Link href="/practice" class="text-xs font-bold text-brand-600 hover:underline">Lihat Semua</Link>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div
                                v-for="cat in categories"
                                :key="cat.title"
                                class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white p-3.5 shadow-xs hover:border-brand-400 hover:shadow-md transition-all cursor-pointer"
                            >
                                <span :class="['flex h-9 w-9 shrink-0 items-center justify-center rounded-lg', cat.color]">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                        <path :d="cat.icon"/>
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ cat.title }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ cat.count }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Why Choose Us (With Instructor Photo & 4 Features) -->
                    <div class="lg:col-span-7 space-y-6">
                        <h2 class="text-2xl font-black text-[#0c1f40]">Mengapa Memilih Kami?</h2>

                        <div class="grid items-center gap-6 sm:grid-cols-2">
                            <!-- Features List -->
                            <div class="space-y-4">
                                <div v-for="w in whyChooseUs" :key="w.title" class="flex items-start gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-brand-600">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2">
                                            <path :d="w.icon"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900">{{ w.title }}</h4>
                                        <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5">{{ w.desc }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Professional Photo with 10+ Years badge like reference -->
                            <div class="relative">
                                <div class="overflow-hidden rounded-2xl shadow-lg border border-slate-200">
                                    <img
                                        src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80"
                                        alt="Mentor Latihan CPNS"
                                        class="h-72 w-full object-cover"
                                    >
                                </div>
                                <div class="absolute bottom-4 right-4 rounded-xl bg-white p-3 shadow-lg text-center border border-slate-100">
                                    <p class="text-xl font-black text-[#ff782d]">10+</p>
                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Tahun Pengalaman</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. SOLID NAVY NUMBERS STRIP (20,000+ stat strip from reference) -->
        <section class="bg-[#0c1f40] py-8 text-white">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-4 text-center">
                    <div class="space-y-1">
                        <p class="text-3xl font-black text-amber-400 tabular-nums">20.000+</p>
                        <p class="text-xs font-semibold text-slate-300">Peserta Aktif</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-3xl font-black text-amber-400 tabular-nums">500+</p>
                        <p class="text-xs font-semibold text-slate-300">Mentor Berpengalaman</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-3xl font-black text-amber-400 tabular-nums">1.200+</p>
                        <p class="text-xs font-semibold text-slate-300">Bank Soal Teruji</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-3xl font-black text-amber-400 tabular-nums">95%</p>
                        <p class="text-xs font-semibold text-slate-300">Tingkat Lolos PG</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. TOP INSTRUCTORS + UPCOMING EVENTS (Eduma split section) -->
        <section class="py-16 bg-white">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-12">
                <div class="grid gap-12 lg:grid-cols-12">
                    <!-- Left: Top Mentors (4 cards) -->
                    <div class="lg:col-span-6 space-y-6">
                        <div class="flex items-baseline justify-between">
                            <h2 class="text-2xl font-black text-[#0c1f40]">Mentor Terbaik</h2>
                            <Link href="/about" class="text-xs font-bold text-brand-600 hover:underline">Lihat Semua</Link>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div
                                v-for="mentor in mentors"
                                :key="mentor.name"
                                class="rounded-xl border border-slate-200/80 bg-white p-4 text-center shadow-xs hover:shadow-md transition-all space-y-2"
                            >
                                <img :src="mentor.image" :alt="mentor.name" class="h-20 w-20 mx-auto rounded-full object-cover border-2 border-slate-100">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ mentor.name }}</h4>
                                    <p class="text-[11px] font-semibold text-[#ff782d]">{{ mentor.role }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ mentor.agency }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Upcoming Events (3 event rows with calendar date box) -->
                    <div class="lg:col-span-6 space-y-6">
                        <div class="flex items-baseline justify-between">
                            <h2 class="text-2xl font-black text-[#0c1f40]">Jadwal Tryout Akbar</h2>
                            <Link href="/events" class="text-xs font-bold text-brand-600 hover:underline">Lihat Semua</Link>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="ev in events"
                                :key="ev.title"
                                class="flex items-center gap-4 rounded-xl border border-slate-200/80 bg-white p-3.5 shadow-xs hover:border-brand-300 transition-all"
                            >
                                <!-- Date Badge (Clean solid blue box like reference) -->
                                <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-lg bg-blue-600 text-white font-bold leading-tight">
                                    <span class="text-base font-black">{{ ev.day }}</span>
                                    <span class="text-[9px] tracking-wider uppercase">{{ ev.month }}</span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-slate-900 leading-snug truncate">{{ ev.title }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                                        <span class="flex items-center gap-1">
                                            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            {{ ev.time }}
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                            </svg>
                                            {{ ev.location }}
                                        </span>
                                    </p>
                                </div>

                                <img :src="ev.image" :alt="ev.title" class="hidden sm:block h-12 w-16 rounded-md object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. THREE COLUMN SECTION (Students Say / From Blog / FAQs as in Eduma reference) -->
        <section class="py-16 bg-[#f8faff] border-y border-slate-100">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="grid gap-8 lg:grid-cols-3">
                    <!-- Col 1: What Our Students Say -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-black text-[#0c1f40]">Testimoni Peserta</h3>
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                            <svg class="h-6 w-6 text-amber-500 fill-current opacity-80" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                            <p class="text-xs text-slate-600 leading-relaxed italic">
                                Simulasi di sini timer server-nya ketat persis seperti aslinya. Pembahasan materi TKP dan analisa topik terlemah ngebantu banget lolos seleksi BKN tahun ini!
                            </p>
                            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80" alt="Alumni" class="h-10 w-10 rounded-full object-cover">
                                <div>
                                    <p class="text-xs font-bold text-slate-900">Rizky Pratama, S.T.</p>
                                    <p class="text-[10px] text-brand-600 font-semibold">Lolos BKN Pusat (Skor 474)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Col 2: From Our Blog (3 items) -->
                    <div class="space-y-4">
                        <div class="flex items-baseline justify-between">
                            <h3 class="text-lg font-black text-[#0c1f40]">Artikel &amp; Tips</h3>
                            <Link href="/blog" class="text-xs font-bold text-brand-600 hover:underline">Lihat Semua</Link>
                        </div>
                        <div class="space-y-3">
                            <div v-for="b in blogs" :key="b.title" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2.5 shadow-xs hover:border-brand-300 transition-colors">
                                <img :src="b.image" :alt="b.title" class="h-14 w-14 rounded-lg object-cover shrink-0">
                                <div class="min-w-0 flex-1">
                                    <span class="rounded bg-blue-50 px-1.5 py-0.5 text-[9px] font-bold text-brand-600">{{ b.tag }}</span>
                                    <h5 class="text-[11px] font-bold text-slate-900 truncate leading-snug mt-1">{{ b.title }}</h5>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ b.date }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Col 3: Frequently Asked Questions (Accordion) -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-black text-[#0c1f40]">Tanya Jawab (FAQ)</h3>
                        <div class="space-y-2 text-xs">
                            <div
                                v-for="(faq, i) in faqs"
                                :key="faq.q"
                                class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-xs"
                            >
                                <button
                                    class="flex w-full items-center justify-between p-3 text-left font-bold text-slate-800 hover:text-brand-600 transition-colors"
                                    @click="toggleFaq(i)"
                                >
                                    <span class="text-[11px]">{{ faq.q }}</span>
                                    <span class="text-xs font-bold text-slate-400">
                                        {{ activeFaq === i ? '−' : '+' }}
                                    </span>
                                </button>
                                <div v-show="activeFaq === i" class="border-t border-slate-100 bg-slate-50 p-3 text-[11px] text-slate-600 leading-relaxed">
                                    {{ faq.a }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. NEWSLETTER / STAY UPDATED STRIP (Eduma blue banner with envelope) -->
        <section class="py-12 bg-white">
            <div class="mx-auto max-w-5xl px-4 sm:px-6">
                <div class="rounded-2xl bg-[#0c1f40] p-8 sm:p-10 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="space-y-1 text-center md:text-left">
                        <h3 class="text-xl font-bold">Dapatkan Kisi-Kisi &amp; Update Tryout</h3>
                        <p class="text-xs text-slate-300">Daftarkan emailmu untuk menerima jadwal simulasi akbar gratis setiap minggu.</p>
                    </div>
                    <div class="flex w-full max-w-md items-center gap-2">
                        <input
                            type="email"
                            placeholder="Masukkan alamat email..."
                            class="h-11 flex-1 rounded-md bg-white/10 border border-white/20 px-4 text-xs text-white placeholder:text-slate-400 focus:outline-none focus:bg-white/15"
                        >
                        <button class="h-11 rounded-md bg-[#ff782d] px-5 text-xs font-bold text-white shadow-xs hover:bg-[#e8661d] transition-colors">
                            Langganan
                        </button>
                    </div>
                </div>

                <!-- 3 Contact Strip Pills below Newsletter (Call us, Email us, Location) -->
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div class="flex items-center justify-center gap-3 rounded-xl border border-slate-200 bg-[#f8faff] p-4 text-xs">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-brand-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </span>
                        <div class="text-left">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase">Hubungi Kami</p>
                            <p class="font-bold text-slate-800">+62 812-3456-7890</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-3 rounded-xl border border-slate-200 bg-[#f8faff] p-4 text-xs">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <div class="text-left">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase">Email Bantuan</p>
                            <p class="font-bold text-slate-800">info@latihancpns.id</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-3 rounded-xl border border-slate-200 bg-[#f8faff] p-4 text-xs">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </span>
                        <div class="text-left">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase">Sekretariat</p>
                            <p class="font-bold text-slate-800">Jakarta Pusat, DKI Jakarta</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. DARK NAVY COMPREHENSIVE FOOTER (Eduma reference footer) -->
        <footer class="bg-[#0c1f40] pt-14 pb-8 text-xs text-slate-300">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-12">
                <div class="grid gap-8 sm:grid-cols-2 md:grid-cols-5">
                    <!-- Col 1: About & Logo -->
                    <div class="space-y-4 md:col-span-1">
                        <div class="flex items-center gap-2.5 text-white">
                            <div class="flex h-8 w-8 items-center justify-center rounded-md bg-brand-600 text-white font-bold">
                                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
                                </svg>
                            </div>
                            <span class="text-base font-black tracking-tight">Eduma.</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Membekali pejuang NIP dengan simulasi presisi CAT BKN dan materi relevan menuju kelulusan ASN 2026.
                        </p>
                        <div class="flex items-center gap-2.5 pt-1 text-slate-400">
                            <a href="#" class="h-7 w-7 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 hover:text-white transition-colors" aria-label="Facebook">
                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="h-7 w-7 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 hover:text-white transition-colors" aria-label="LinkedIn">
                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 8.76c-.95 0-1.72-.77-1.72-1.72s.77-1.72 1.72-1.72 1.72.77 1.72 1.72-.77 1.72-1.72 1.72m1.39 9.74v-8.37H5.07v8.37h2.78z"/></svg>
                            </a>
                            <a href="#" class="h-7 w-7 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 hover:text-white transition-colors" aria-label="Twitter">
                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Col 2: Quick Links -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-white text-sm">Tautan Cepat</h4>
                        <ul class="space-y-2 text-[11px] text-slate-400">
                            <li><Link href="/" class="hover:text-white transition-colors">Beranda</Link></li>
                            <li><Link href="/tryouts" class="hover:text-white transition-colors">Paket Tryout</Link></li>
                            <li><Link href="/practice" class="hover:text-white transition-colors">Latihan Mandiri</Link></li>
                            <li><Link href="/rankings" class="hover:text-white transition-colors">Peringkat Nasional</Link></li>
                            <li><Link href="/forum" class="hover:text-white transition-colors">Forum Diskusi</Link></li>
                        </ul>
                    </div>

                    <!-- Col 3: Subtests -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-white text-sm">Materi SKD</h4>
                        <ul class="space-y-2 text-[11px] text-slate-400">
                            <li><span>Wawasan Kebangsaan (TWK)</span></li>
                            <li><span>Intelegensia Umum (TIU)</span></li>
                            <li><span>Karakteristik Pribadi (TKP)</span></li>
                            <li><span>Formasi SKB Khusus</span></li>
                            <li><span>Simulasi CAT 100 Menit</span></li>
                        </ul>
                    </div>

                    <!-- Col 4: Support -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-white text-sm">Bantuan</h4>
                        <ul class="space-y-2 text-[11px] text-slate-400">
                            <li><span>Panduan Sistem CAT</span></li>
                            <li><span>Syarat &amp; Ketentuan</span></li>
                            <li><span>Kebijakan Privasi</span></li>
                            <li><span>FAQ &amp; Kendala</span></li>
                            <li><span>Kontak Tim Bantuan</span></li>
                        </ul>
                    </div>

                    <!-- Col 5: Gallery (6 thumbnails like Eduma reference) -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-white text-sm">Galeri Pembelajaran</h4>
                        <div class="grid grid-cols-3 gap-1.5">
                            <img
                                v-for="(img, idx) in galleryImages"
                                :key="idx"
                                :src="img"
                                alt="Galeri Pembelajaran"
                                class="h-12 w-full rounded-sm object-cover opacity-80 hover:opacity-100 transition-opacity cursor-pointer"
                            >
                        </div>
                    </div>
                </div>

                <!-- Copyright & Disclaimer -->
                <div class="border-t border-white/10 pt-6 text-center text-[11px] text-slate-500 space-y-1">
                    <p>Platform latihan edukatif independen berstandar kisi-kisi resmi BKN &amp; KemenPAN-RB.</p>
                    <p>&copy; 2026 Eduma Latihan CPNS. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
