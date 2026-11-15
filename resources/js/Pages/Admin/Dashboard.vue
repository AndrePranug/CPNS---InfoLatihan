<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    hardestQuestions: { type: Array, required: true },
});

const cards = [
    {
        key: 'users',
        label: 'Total Pengguna',
        sub: 'Peserta terdaftar',
        icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a3.75 3.75 0 0 1 6.375-2.625 9.07 9.07 0 0 0 2.625.375c.9 0 1.765-.132 2.58-.378a3.75 3.75 0 0 1 4.17 2.631Zm2.625-11.628a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Zm-9 3.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        color: 'text-blue-600',
        bg: 'bg-blue-50',
        bar: 'bg-blue-600',
    },
    {
        key: 'questions',
        label: 'Bank Soal',
        sub: 'Butir soal SKD & SKB',
        icon: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z',
        color: 'text-indigo-600',
        bg: 'bg-indigo-50',
        bar: 'bg-indigo-600',
    },
    {
        key: 'published_tryouts',
        label: 'Paket Terbit',
        sub: 'Dapat diakses peserta',
        icon: 'M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.58-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z',
        color: 'text-emerald-600',
        bg: 'bg-emerald-50',
        bar: 'bg-emerald-600',
    },
    {
        key: 'completed_attempts',
        label: 'Tryout Selesai',
        sub: 'Sesi ujian terkirim',
        icon: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        color: 'text-amber-600',
        bg: 'bg-amber-50',
        bar: 'bg-amber-600',
    },
    {
        key: 'average_score',
        label: 'Rata-rata Skor',
        sub: 'Seluruh peserta',
        icon: 'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 013-3m9 0v-4.5A3.75 3.75 0 0 0 12 10.5a3.75 3.75 0 0 0-4.5 3.75v4.5m9 0h.008M12 3v2.25m6.364.886-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591',
        color: 'text-rose-600',
        bg: 'bg-rose-50',
        bar: 'bg-rose-600',
    },
];

const quickActions = [
    {
        href: '/admin/questions/create',
        title: 'Buat Soal Baru',
        desc: 'Input soal TWK, TIU, TKP, atau SKB',
        icon: 'M12 4.5v15m7.5-7.5h-15',
        badge: 'Editor',
    },
    {
        href: '/admin/questions/import',
        title: 'Impor CSV/Excel',
        desc: 'Unggah kumpulan soal sekaligus',
        icon: 'M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5',
        badge: 'Massal',
    },
    {
        href: '/admin/tryouts',
        title: 'Atur Paket Ujian',
        desc: 'Kelola komposisi butir soal & durasi',
        icon: 'M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878',
        badge: 'Tryout',
    },
    {
        href: '/admin/reports',
        title: 'Unduh Laporan',
        desc: 'Rekapitulasi nilai mitra & peserta',
        icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75',
        badge: 'Laporan',
    },
];
</script>

<template>
    <AdminLayout>
        <Head title="Dasbor Admin" />

        <!-- Top Header & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-bold text-brand-700 mb-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-600 animate-pulse"></span>
                    Panel Kontrol Platform
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink-900 sm:text-3xl">Dasbor Administrator</h1>
                <p class="mt-1 text-xs sm:text-sm text-ink-600">Ringkasan performa platform, aktivitas peserta, dan analisis butir soal simulasi.</p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto">
                <Link
                    href="/admin/questions/import"
                    class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface px-4 text-xs font-bold text-ink-700 shadow-xs hover:bg-brand-50 hover:text-brand-700 active:scale-[0.98] transition-all"
                >
                    Impor Soal
                </Link>
                <Link
                    href="/admin/questions/create"
                    class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                    </svg>
                    Tambah Soal
                </Link>
            </div>
        </div>

        <!-- 5 KPI Cards Grid -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div
                v-for="card in cards"
                :key="card.key"
                class="group relative overflow-hidden rounded-2xl border border-line bg-surface p-5 shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover"
            >
                <div class="flex items-center justify-between">
                    <span :class="[card.bg, card.color]" class="flex h-10 w-10 items-center justify-center rounded-xl shadow-xs">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path :d="card.icon" />
                        </svg>
                    </span>
                    <span class="text-[10px] font-bold text-ink-400 uppercase tracking-wider">Aktif</span>
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold text-ink-600">{{ card.label }}</p>
                    <p class="mt-1 text-3xl font-extrabold tabular-nums tracking-tight text-ink-900">
                        {{ stats[card.key] }}
                    </p>
                    <p class="mt-1 text-[11px] text-ink-400">{{ card.sub }}</p>
                </div>

                <div :class="[card.bar]" class="absolute bottom-0 inset-x-0 h-1 opacity-80 group-hover:opacity-100 transition-opacity"></div>
            </div>
        </section>

        <!-- Quick Workflow Actions Hub -->
        <section class="space-y-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-ink-400">Jalur Cepat Manajemen</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="act in quickActions"
                    :key="act.title"
                    :href="act.href"
                    class="group flex flex-col justify-between rounded-2xl border border-line bg-surface p-4 shadow-card transition-all duration-200 hover:border-brand-300 hover:shadow-card-hover hover:-translate-y-0.5"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-surface-raised text-ink-600 group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path :d="act.icon" />
                                </svg>
                            </span>
                            <span class="rounded bg-surface-raised px-2 py-0.5 text-[10px] font-bold text-ink-400 group-hover:text-brand-600">
                                {{ act.badge }}
                            </span>
                        </div>
                        <h3 class="mt-3 text-sm font-bold text-ink-900 group-hover:text-brand-600 transition-colors">
                            {{ act.title }}
                        </h3>
                        <p class="mt-1 text-[11px] text-ink-600 leading-relaxed">{{ act.desc }}</p>
                    </div>

                    <div class="mt-3 pt-2 border-t border-line/60 flex items-center justify-between text-[11px] font-semibold text-brand-600">
                        <span>Buka &rarr;</span>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Two Column Section: Problem Questions & Platform System Status -->
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <!-- Hardest Questions List (Left) -->
            <section class="rounded-2xl border border-line bg-surface p-6 shadow-card space-y-4">
                <div class="flex items-center justify-between border-b border-line/60 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-ink-900">Soal Paling Sering Salah</h2>
                        <p class="text-xs text-ink-400">Butir soal dengan rasio jawaban benar terendah (akurasi rendah).</p>
                    </div>
                    <span class="rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700 border border-rose-200">
                        Evaluasi
                    </span>
                </div>

                <div v-if="hardestQuestions.length === 0" class="py-12 text-center text-xs text-ink-400">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-surface-raised text-ink-400 mb-2">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <p class="font-medium text-ink-600">Belum Ada Data Pengerjaan Soal</p>
                    <p class="mt-0.5">Analisis akurasi akan otomatis dihitung saat peserta mulai menyelesaikan paket tryout.</p>
                </div>

                <ul v-else class="divide-y divide-line/60">
                    <li v-for="row in hardestQuestions" :key="row.id" class="flex items-start gap-4 py-3.5 text-xs group">
                        <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-600 shrink-0">
                            {{ row.subtest }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="leading-relaxed text-ink-900 font-medium line-clamp-2">
                                {{ row.body }}
                            </p>
                            <div class="mt-2 flex items-center gap-3 text-[11px] text-ink-400">
                                <span>{{ row.answered }} kali dikerjakan</span>
                                <span>·</span>
                                <Link :href="`/admin/questions/${row.id}/edit`" class="font-semibold text-brand-600 hover:underline">
                                    Edit Kunci/Soal &rarr;
                                </Link>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <p :class="['text-base font-extrabold tabular-nums', row.correct_rate < 40 ? 'text-danger' : 'text-amber-600']">
                                {{ row.correct_rate }}%
                            </p>
                            <p class="text-[10px] text-ink-400">Akurasi Benar</p>
                        </div>
                    </li>
                </ul>
            </section>

            <!-- System & Architecture Overview (Right) -->
            <aside class="space-y-4">
                <div class="rounded-2xl border border-line bg-surface p-5 shadow-card space-y-4">
                    <div class="border-b border-line/60 pb-3">
                        <h2 class="text-sm font-bold text-ink-900">Status Platform &amp; Engine</h2>
                        <p class="text-[11px] text-ink-400">Infrastruktur simulasi SKD CAT</p>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between rounded-xl bg-surface-raised p-2.5">
                            <span class="text-ink-600">Server &amp; Database</span>
                            <span class="inline-flex items-center gap-1 font-bold text-success">
                                <span class="h-2 w-2 rounded-full bg-success"></span> Online (MySQL)
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl bg-surface-raised p-2.5">
                            <span class="text-ink-600">Sistem Skoring</span>
                            <span class="font-bold text-ink-900">Otomatis BKN</span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl bg-surface-raised p-2.5">
                            <span class="text-ink-600">Timer Server-Side</span>
                            <span class="font-bold text-ink-900">Presisi 100 Mnt</span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl bg-surface-raised p-2.5">
                            <span class="text-ink-600">PWA Offline Caching</span>
                            <span class="inline-flex items-center gap-1 font-bold text-brand-600">
                                Aktif (v1)
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl bg-surface-raised p-2.5">
                            <span class="text-ink-600">Payment Gateway</span>
                            <span class="font-semibold text-amber-700">Mode Sandbox</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-brand-700 p-5 text-white shadow-card space-y-3">
                    <h3 class="text-sm font-bold">Butuh Bantuan Pengelolaan?</h3>
                    <p class="text-xs text-brand-100 leading-relaxed">
                        Gunakan fitur impor soal Excel untuk memasukkan ratusan soal sekaligus tanpa input manual satu per satu.
                    </p>
                    <Link
                        href="/admin/questions/import"
                        class="inline-flex h-9 items-center justify-center rounded-xl bg-white px-4 text-xs font-bold text-brand-700 shadow-xs hover:bg-brand-50 transition-colors"
                    >
                        Buka Impor Excel &rarr;
                    </Link>
                </div>
            </aside>
        </div>
    </AdminLayout>
</template>
