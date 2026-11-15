<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    result: { type: Object, required: true },
    review: { type: Array, required: true },
    ranking: { type: Object, required: true },
});

const filter = ref('semua');
const filters = [
    { value: 'semua', label: 'Semua Soal' },
    { value: 'salah', label: 'Salah' },
    { value: 'ragu', label: 'Ragu-ragu' },
    { value: 'kosong', label: 'Kosong' },
];

const visible = computed(() => {
    if (filter.value === 'semua') return props.review;
    if (filter.value === 'ragu') return props.review.filter((item) => item.is_flagged);
    return props.review.filter((item) => item.status === filter.value);
});

const badge = {
    benar: 'bg-success/10 text-success border-success/20',
    sebagian: 'bg-amber-50 text-amber-700 border-amber-200',
    salah: 'bg-danger/10 text-danger border-danger/20',
    kosong: 'bg-slate-100 text-slate-600 border-slate-200',
};

const subtestPalette = {
    TWK: { bg: 'bg-twk-bg', text: 'text-twk', bar: 'bg-twk' },
    TIU: { bg: 'bg-tiu-bg', text: 'text-tiu', bar: 'bg-tiu' },
    TKP: { bg: 'bg-tkp-bg', text: 'text-tkp', bar: 'bg-tkp' },
};
</script>

<template>
    <AppLayout>
        <Head title="Hasil Tryout" />

        <!-- Total Score Hero Card -->
        <section class="relative overflow-hidden rounded-3xl border border-line bg-surface p-8 text-center shadow-card">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-raised px-3 py-1 text-xs font-semibold text-ink-600">
                {{ result.title }}
            </span>

            <div class="mt-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-ink-400">Total Skor Akhir</p>
                <p class="mt-1 text-5xl sm:text-6xl font-extrabold tracking-tight tabular-nums text-ink-900">
                    {{ result.total_score }}
                </p>
            </div>

            <div class="mt-4">
                <span
                    :class="[
                        'inline-flex items-center gap-1.5 rounded-full px-4 py-1 text-xs font-bold shadow-xs',
                        result.is_passed
                            ? 'bg-success/10 text-success border border-success/30'
                            : 'bg-danger/10 text-danger border border-danger/30'
                    ]"
                >
                    <span :class="['h-2 w-2 rounded-full', result.is_passed ? 'bg-success' : 'bg-danger']"></span>
                    {{ result.is_passed ? 'Memenuhi Passing Grade SKD' : 'Belum Memenuhi Ambang Batas' }}
                </span>
            </div>
            
            <p class="mt-3 text-xs text-ink-400">
                Penilaian dihitung otomatis berdasarkan formula standar BKN.
            </p>
        </section>

        <!-- Subtests Breakdown -->
        <section class="mt-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-ink-400 mb-3">Rincian Nilai per Subtes</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <article
                    v-for="subtest in result.subtests"
                    :key="subtest.code"
                    class="rounded-2xl border border-line bg-surface p-5 shadow-card"
                >
                    <div class="flex items-center justify-between">
                        <span
                            :class="[
                                subtestPalette[subtest.code]?.bg ?? 'bg-brand-50',
                                subtestPalette[subtest.code]?.text ?? 'text-brand-600'
                            ]"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold"
                        >
                            {{ subtest.code }}
                        </span>
                        <span
                            :class="[
                                'text-[11px] font-bold px-2 py-0.5 rounded-full',
                                subtest.score >= subtest.passing_grade ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'
                            ]"
                        >
                            {{ subtest.score >= subtest.passing_grade ? 'Lulus PG' : 'Di Bawah PG' }}
                        </span>
                    </div>

                    <p class="mt-4 text-3xl font-bold tabular-nums text-ink-900">{{ subtest.score }}</p>
                    <p class="text-xs text-ink-600 mt-0.5">Ambang batas (PG): <strong class="text-ink-900">{{ subtest.passing_grade }}</strong></p>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-brand-100">
                        <div
                            :class="['h-full rounded-full transition-all duration-300', subtestPalette[subtest.code]?.bar ?? 'bg-brand-600']"
                            :style="{ width: `${Math.min(100, (subtest.score / (subtest.passing_grade * 1.5)) * 100)}%` }"
                        ></div>
                    </div>
                </article>
            </div>
        </section>

        <!-- Ranking & Weak Topics Split -->
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <!-- Ranking Card -->
            <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <div class="flex items-baseline justify-between border-b border-line/60 pb-3">
                    <h2 class="text-sm font-bold text-ink-900">Peringkat Tryout Ini</h2>
                    <span v-if="ranking.position" class="text-xs text-ink-600">
                        Posisi <strong class="text-brand-600">#{{ ranking.position }}</strong> dari {{ ranking.total }} peserta
                    </span>
                </div>

                <p v-if="ranking.rows.length === 0" class="mt-4 text-center py-6 text-xs text-ink-400">
                    Belum ada peserta lain yang menyelesaikan paket ini.
                </p>
                <ol v-else class="mt-3 divide-y divide-line/60">
                    <li
                        v-for="row in ranking.rows"
                        :key="row.position"
                        :class="[
                            'flex items-center gap-3 py-2 text-xs transition-colors',
                            row.is_me ? 'bg-brand-50 px-2 rounded-lg font-bold text-brand-700' : 'text-ink-900'
                        ]"
                    >
                        <span
                            :class="[
                                'flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-bold',
                                row.position === 1 ? 'bg-amber-400 text-white' :
                                row.position === 2 ? 'bg-slate-400 text-white' :
                                row.position === 3 ? 'bg-amber-700 text-white' :
                                'bg-surface-raised text-ink-600'
                            ]"
                        >
                            {{ row.position }}
                        </span>
                        <span class="min-w-0 flex-1 truncate">{{ row.name }}</span>
                        <span class="font-bold tabular-nums text-ink-900">{{ row.score }}</span>
                        <span v-if="row.is_me" class="rounded bg-brand-600 px-1 py-0.2 text-[9px] font-bold text-white">Kamu</span>
                    </li>
                </ol>
            </section>

            <!-- Weak Topics Card -->
            <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <div class="flex items-baseline justify-between border-b border-line/60 pb-3">
                    <h2 class="text-sm font-bold text-ink-900">Analisis Materi Lemah</h2>
                    <span class="text-xs text-ink-400">Butuh penguatan</span>
                </div>

                <p v-if="result.topics.length === 0" class="mt-4 text-center py-6 text-xs text-ink-400">
                    Tidak ada topik terlemah yang terdeteksi.
                </p>
                <ul v-else class="mt-3 space-y-2.5">
                    <li v-for="topic in result.topics" :key="topic.topic" class="rounded-xl bg-surface-raised p-3 text-xs">
                        <div class="flex items-center justify-between font-medium">
                            <span class="text-ink-900 font-semibold">{{ topic.topic }}</span>
                            <span class="tabular-nums text-ink-600">{{ topic.correct }}/{{ topic.total }} Soal Benar</span>
                        </div>
                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-brand-100">
                            <div
                                :class="[
                                    'h-full rounded-full',
                                    (topic.correct / topic.total) < 0.5 ? 'bg-danger' :
                                    (topic.correct / topic.total) < 0.8 ? 'bg-amber-500' : 'bg-success'
                                ]"
                                :style="{ width: `${Math.round((topic.correct / topic.total) * 100)}%` }"
                            ></div>
                        </div>
                    </li>
                </ul>
            </section>
        </div>

        <!-- Question Review Section -->
        <section class="mt-8 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-ink-900">Pembahasan Soal</h2>
                    <p class="text-xs text-ink-600">Pelajari kunci jawaban dan penjelasan rinci untuk setiap butir soal.</p>
                </div>

                <!-- Filter Tabs -->
                <div class="flex gap-1.5 rounded-xl border border-line bg-surface p-1 shadow-xs" role="tablist" aria-label="Filter pembahasan">
                    <button
                        v-for="item in filters"
                        :key="item.value"
                        role="tab"
                        :aria-selected="filter === item.value"
                        :class="[
                            'h-8 rounded-lg px-3 text-xs font-semibold transition-colors',
                            filter === item.value ? 'bg-brand-600 text-white shadow-xs' : 'text-ink-600 hover:bg-brand-50'
                        ]"
                        @click="filter = item.value"
                    >
                        {{ item.label }}
                    </button>
                </div>
            </div>

            <!-- Empty review filter -->
            <div v-if="visible.length === 0" class="rounded-2xl border border-line bg-surface p-8 text-center text-xs text-ink-600 shadow-card">
                Tidak ada soal yang sesuai dengan filter ini.
            </div>

            <!-- Review Items List -->
            <ol class="space-y-4">
                <li
                    v-for="(item, idx) in visible"
                    :key="item.id"
                    class="rounded-2xl border border-line bg-surface p-6 shadow-card space-y-4"
                >
                    <!-- Meta info -->
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line/60 pb-3">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="rounded-lg bg-brand-50 px-2.5 py-1 font-bold text-brand-600">
                                {{ item.subtest }}
                            </span>
                            <span v-if="item.topic" class="text-ink-600 font-medium">
                                {{ item.topic }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span v-if="item.is_flagged" class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                <svg class="h-3 w-3 text-amber-600 fill-amber-500" viewBox="0 0 24 24">
                                    <path d="M4 2v20h2v-8h8l1 2h7V4h-9l-1-2H4z"/>
                                </svg>
                                <span>Ragu</span>
                            </span>
                            <span :class="['rounded-full border px-2.5 py-0.5 text-xs font-bold uppercase', badge[item.status]]">
                                {{ item.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Question body -->
                    <div class="rich-text text-[15px] leading-relaxed text-ink-900 font-medium" v-html="item.body"></div>

                    <!-- Image (if any) -->
                    <img v-if="item.image_path" :src="item.image_path" alt="Ilustrasi Soal" class="max-h-64 rounded-xl border border-line bg-surface-raised p-2">

                    <!-- Options list with indicators -->
                    <ul class="space-y-2 text-xs">
                        <li
                            v-for="option in item.options"
                            :key="option.id"
                            :class="[
                                'flex items-start gap-3 rounded-xl border p-3 transition-colors',
                                option.is_correct ? 'border-success/40 bg-success/10 text-success font-semibold' :
                                option.is_selected && !option.is_correct ? 'border-danger/40 bg-danger/10 text-danger font-medium' :
                                'border-line/60 bg-surface-raised text-ink-900'
                            ]"
                        >
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/80 font-bold border border-current">
                                {{ option.label }}
                            </span>
                            <span class="flex-1 text-[13px] leading-relaxed">{{ option.body }}</span>
                            <span v-if="option.is_selected" class="rounded bg-brand-600 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                Jawaban Anda
                            </span>
                            <span v-else-if="option.is_correct" class="rounded bg-success px-1.5 py-0.5 text-[10px] font-bold text-white">
                                Kunci Benar
                            </span>
                        </li>
                    </ul>

                    <!-- Explanation Box -->
                    <div v-if="item.explanation" class="rounded-xl border border-brand-200/60 bg-brand-50/70 p-4">
                        <p class="text-xs font-bold text-brand-700 mb-1 flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pembahasan &amp; Solusi:
                        </p>
                        <div class="rich-text text-xs leading-relaxed text-ink-900" v-html="item.explanation"></div>
                    </div>
                </li>
            </ol>
        </section>

        <!-- Bottom Action Bar -->
        <div class="mt-8 flex flex-wrap gap-3">
            <Link
                href="/tryouts"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-brand-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all"
            >
                Coba Paket Tryout Lainnya
            </Link>
            <Link
                href="/history"
                class="inline-flex h-11 items-center justify-center rounded-xl border border-line bg-surface px-5 text-sm font-semibold text-ink-900 hover:bg-brand-50 active:scale-[0.98] transition-all"
            >
                Lihat Riwayat Nilai
            </Link>
        </div>
    </AppLayout>
</template>
