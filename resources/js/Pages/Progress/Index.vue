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

const subtestColors = { twk: 'bg-twk', tiu: 'bg-tiu', tkp: 'bg-tkp' };
</script>

<template>
    <AppLayout>
        <Head title="Progres" />
        <h1 class="text-3xl font-bold">Progres</h1>

        <section class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Tryout selesai</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ summary.attempts }}</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Skor terbaik</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ summary.best }}</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Rata-rata</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ summary.average }}</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Kenaikan skor</p>
                <p :class="['mt-1 text-2xl font-bold tabular-nums', summary.improvement >= 0 ? 'text-success' : 'text-danger']">
                    {{ summary.improvement > 0 ? '+' : '' }}{{ summary.improvement }}%
                </p>
            </div>
        </section>

        <section class="mt-6 rounded-2xl border border-line bg-white p-6 shadow-card">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="text-lg font-bold">Perkembangan skor</h2>
                <span class="text-xs text-ink-400">20 percobaan terakhir</span>
            </div>

            <p v-if="trend.length === 0" class="mt-3 text-sm text-ink-600">
                Belum ada tryout selesai. <Link href="/tryouts" class="font-semibold text-brand-600">Mulai tryout</Link>.
            </p>

            <template v-else>
                <div class="mt-4 flex h-40 items-end gap-2" role="img" :aria-label="`Grafik skor total: ${trend.map((row) => row.total).join(', ')}`">
                    <div v-for="(row, index) in trend" :key="index" class="flex flex-1 flex-col items-center gap-1">
                        <div class="flex h-full w-full flex-col justify-end gap-0.5">
                            <div :class="['w-full rounded-t bg-tkp/70', 'rounded-t-none']" :style="{ height: `${(row.tkp / maxTotal) * 100}%` }" :title="`TKP ${row.tkp}`"></div>
                            <div class="w-full bg-tiu/70" :style="{ height: `${(row.tiu / maxTotal) * 100}%` }" :title="`TIU ${row.tiu}`"></div>
                            <div class="w-full bg-twk/70" :style="{ height: `${(row.twk / maxTotal) * 100}%` }" :title="`TWK ${row.twk}`"></div>
                        </div>
                        <span class="text-[10px] text-ink-400 tabular-nums">{{ row.total }}</span>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap gap-4 text-xs text-ink-600">
                    <span v-for="(color, key) in subtestColors" :key="key" class="flex items-center gap-1">
                        <span :class="['h-2.5 w-2.5 rounded-sm', color]"></span>{{ key.toUpperCase() }}
                    </span>
                </div>
            </template>
        </section>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-line bg-white p-6 shadow-card">
                <h2 class="text-lg font-bold">Topik terlemah</h2>
                <p v-if="weakTopics.length === 0" class="mt-2 text-sm text-ink-600">Belum ada data topik.</p>
                <ul v-else class="mt-3 space-y-3">
                    <li v-for="topic in weakTopics" :key="topic.topic">
                        <div class="flex justify-between text-sm">
                            <span>{{ topic.topic }}</span>
                            <span class="font-semibold tabular-nums">{{ topic.correct }}/{{ topic.total }}</span>
                        </div>
                        <div class="mt-1 h-2 overflow-hidden rounded-full bg-brand-100">
                            <div
                                :class="['h-full rounded-full', topic.percent < 50 ? 'bg-danger' : topic.percent < 75 ? 'bg-warning' : 'bg-success']"
                                :style="{ width: `${topic.percent}%` }"
                            ></div>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="rounded-2xl border border-line bg-white p-6 shadow-card">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-lg font-bold">Menit belajar</h2>
                    <span class="text-xs text-ink-400">14 hari · streak {{ summary.streak }}</span>
                </div>
                <p v-if="activity.length === 0" class="mt-2 text-sm text-ink-600">Belum ada aktivitas.</p>
                <div v-else class="mt-4 flex h-32 items-end gap-1" role="img" :aria-label="`Menit belajar: ${activity.map((row) => row.minutes).join(', ')}`">
                    <div
                        v-for="row in activity"
                        :key="row.date"
                        class="flex-1 rounded-t bg-brand-600"
                        :style="{ height: `${Math.max(3, (row.minutes / maxMinutes) * 100)}%` }"
                        :title="`${row.date}: ${row.minutes} menit`"
                    ></div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
