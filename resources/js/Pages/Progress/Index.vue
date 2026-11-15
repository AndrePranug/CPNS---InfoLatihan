<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    summary: { type: Object, required: true },
    trend: { type: Array, required: true },
    weakTopics: { type: Array, required: true },
    activity: { type: Array, required: true },
});

const maxTotal = computed(() => Math.max(1, ...props.trend.map((row) => row.total)));
const maxMinutes = computed(() => Math.max(1, ...props.activity.map((row) => row.minutes)));

const subtestColors = {
    twk: { bg: 'bg-twk', label: 'TWK' },
    tiu: { bg: 'bg-tiu', label: 'TIU' },
    tkp: { bg: 'bg-tkp', label: 'TKP' },
};
</script>

<template>
    <AppLayout>
        <Head title="Progres Belajar" />
        
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Progres &amp; Analisis Belajar</h1>
            <p class="mt-1 text-sm text-ink-600">Pantau peningkatan skor, kebiasaan belajar, dan materi yang perlu diperkuat.</p>
        </div>

        <!-- 4 Metric Cards -->
        <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Total Tryout Selesai</p>
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-ink-900">{{ summary.attempts }}</p>
                <p class="mt-1 text-[11px] text-ink-400">Sesi ujian terkirim</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Skor Tertinggi</p>
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-brand-600">{{ summary.best }}</p>
                <p class="mt-1 text-[11px] text-ink-400">Pencapaian terbaik</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Rata-rata Skor</p>
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-ink-900">{{ summary.average }}</p>
                <p class="mt-1 text-[11px] text-ink-400">Seluruh sesi ujian</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Tren Peningkatan</p>
                <p :class="['mt-2 text-3xl font-extrabold tabular-nums', summary.improvement >= 0 ? 'text-success' : 'text-danger']">
                    {{ summary.improvement > 0 ? '+' : '' }}{{ summary.improvement }}%
                </p>
                <p class="mt-1 text-[11px] text-ink-400">Dibandingkan sesi awal</p>
            </div>
        </section>

        <!-- Score Evolution Chart -->
        <section class="mt-6 rounded-2xl border border-line bg-surface p-6 shadow-card">
            <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-line/60 pb-3">
                <div>
                    <h2 class="text-base font-bold text-ink-900">Grafik Perkembangan Skor</h2>
                    <p class="text-xs text-ink-400">Komposisi nilai TWK, TIU, TKP pada 20 percobaan terakhir</p>
                </div>
                <div class="flex items-center gap-3 text-xs text-ink-600">
                    <span class="flex items-center gap-1.5 font-medium">
                        <span class="h-2.5 w-2.5 rounded-sm bg-twk"></span> TWK
                    </span>
                    <span class="flex items-center gap-1.5 font-medium">
                        <span class="h-2.5 w-2.5 rounded-sm bg-tiu"></span> TIU
                    </span>
                    <span class="flex items-center gap-1.5 font-medium">
                        <span class="h-2.5 w-2.5 rounded-sm bg-tkp"></span> TKP
                    </span>
                </div>
            </div>

            <p v-if="trend.length === 0" class="py-12 text-center text-xs text-ink-400">
                Belum ada data tryout. <Link href="/tryouts" class="font-semibold text-brand-600 hover:underline">Mulai tryout pertamamu</Link>.
            </p>

            <template v-else>
                <div class="mt-6 flex h-48 items-end gap-2 px-2" role="img" :aria-label="`Grafik skor total: ${trend.map((row) => row.total).join(', ')}`">
                    <div v-for="(row, index) in trend" :key="index" class="flex flex-1 flex-col items-center gap-1.5 h-full justify-end">
                        <div class="flex h-full w-full flex-col justify-end gap-0.5 rounded-t-md overflow-hidden bg-surface-raised">
                            <div class="w-full bg-tkp/80 transition-all hover:bg-tkp" :style="{ height: `${(row.tkp / maxTotal) * 100}%` }" :title="`TKP: ${row.tkp}`"></div>
                            <div class="w-full bg-tiu/80 transition-all hover:bg-tiu" :style="{ height: `${(row.tiu / maxTotal) * 100}%` }" :title="`TIU: ${row.tiu}`"></div>
                            <div class="w-full bg-twk/80 transition-all hover:bg-twk" :style="{ height: `${(row.twk / maxTotal) * 100}%` }" :title="`TWK: ${row.twk}`"></div>
                        </div>
                        <span class="text-[10px] font-semibold text-ink-600 tabular-nums">{{ row.total }}</span>
                    </div>
                </div>
            </template>
        </section>

        <!-- 2 Grid Sections: Weak Topics & Study Minutes -->
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <!-- Weak Topics -->
            <section class="rounded-2xl border border-line bg-surface p-6 shadow-card">
                <div class="flex items-center justify-between border-b border-line/60 pb-3">
                    <h2 class="text-base font-bold text-ink-900">Topik Perlu Latihan</h2>
                    <span class="text-xs text-ink-400">Akurasi terendah</span>
                </div>

                <p v-if="weakTopics.length === 0" class="py-8 text-center text-xs text-ink-400">
                    Belum ada data topik yang tersimpan.
                </p>
                <ul v-else class="mt-4 space-y-3">
                    <li v-for="topic in weakTopics" :key="topic.topic" class="rounded-xl bg-surface-raised p-3 text-xs">
                        <div class="flex justify-between font-medium">
                            <span class="text-ink-900 font-semibold">{{ topic.topic }}</span>
                            <span class="tabular-nums text-ink-600">{{ topic.correct }}/{{ topic.total }} Benar ({{ topic.percent }}%)</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-brand-100">
                            <div
                                :class="[
                                    'h-full rounded-full transition-all duration-300',
                                    topic.percent < 50 ? 'bg-danger' : topic.percent < 75 ? 'bg-amber-500' : 'bg-success'
                                ]"
                                :style="{ width: `${topic.percent}%` }"
                            ></div>
                        </div>
                    </li>
                </ul>
            </section>

            <!-- Study Minutes -->
            <section class="rounded-2xl border border-line bg-surface p-6 shadow-card">
                <div class="flex items-center justify-between border-b border-line/60 pb-3">
                    <h2 class="text-base font-bold text-ink-900">Aktivitas Belajar</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 border border-amber-200">
                        <svg class="h-3.5 w-3.5 text-amber-500 fill-amber-500" viewBox="0 0 24 24">
                            <path d="M12 23c-4.97 0-9-3.582-9-8 0-3.074 1.77-5.94 4.5-7.5.2-.11.45-.04.57.16.11.19.06.44-.12.57C6.08 9.58 5 11.72 5 15c0 3.86 3.14 7 7 7s7-3.14 7-7c0-2.31-.95-4.43-2.5-6-.17-.17-.18-.44-.02-.63.16-.19.45-.22.65-.06C19.24 9.94 20.5 12.35 20.5 15c0 4.418-4.03 8-8.5 8zm.5-17.5c-.28 0-.5-.22-.5-.5 0-1.38-.89-2.58-2.22-3.02-.26-.09-.4-.37-.31-.63.09-.26.37-.4.63-.31C12.01 1.68 13 3.23 13 5c0 .28-.22.5-.5.5z"/>
                        </svg>
                        <span>Streak {{ summary.streak }} Hari</span>
                    </span>
                </div>

                <p v-if="activity.length === 0" class="py-8 text-center text-xs text-ink-400">
                    Belum ada riwayat aktivitas belajar.
                </p>
                <div v-else class="mt-4">
                    <div class="flex h-36 items-end gap-1.5 px-1" role="img" :aria-label="`Menit belajar: ${activity.map((row) => row.minutes).join(', ')}`">
                        <div
                            v-for="row in activity"
                            :key="row.date"
                            class="flex-1 rounded-t-md bg-brand-600/90 transition-all hover:bg-brand-600"
                            :style="{ height: `${Math.max(4, (row.minutes / maxMinutes) * 100)}%` }"
                            :title="`${row.date}: ${row.minutes} menit`"
                        ></div>
                    </div>
                    <div class="mt-3 flex justify-between text-[10px] text-ink-400 border-t border-line/60 pt-2">
                        <span>14 Hari Lalu</span>
                        <span>Hari Ini</span>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
