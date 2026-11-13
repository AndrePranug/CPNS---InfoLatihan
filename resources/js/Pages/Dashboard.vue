<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProgressRing from '@/Components/ui/ProgressRing.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    resume: { type: Object, default: null },
    goal: { type: Object, required: true },
    leaderboard: { type: Array, required: true },
    subtests: { type: Array, required: true },
    user: { type: Object, required: true },
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 11) return 'Selamat pagi';
    if (hour < 15) return 'Selamat siang';
    if (hour < 19) return 'Selamat sore';

    return 'Selamat malam';
});

const palette = {
    TWK: 'bg-twk-bg text-twk',
    TIU: 'bg-tiu-bg text-tiu',
    TKP: 'bg-tkp-bg text-tkp',
};

const dailyDone = computed(() => goal.days.filter((day) => day.done).length);
</script>

<template>
    <AppLayout>
        <Head title="Dasbor" />
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="rounded-3xl bg-gradient-to-br from-brand-100 to-brand-50 p-8">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h1 class="text-3xl font-bold">{{ greeting }}, {{ user.name }}!</h1>
                            <p class="mt-2 text-[15px] text-ink-600">
                                {{ resume ? 'Masih ada tryout yang belum selesai. Lanjutkan sebelum waktunya habis.' : 'Siapkan dirimu untuk SKD hari ini.' }}
                            </p>
                        </div>
                        <span class="rounded-full bg-white/70 px-3 py-1 text-xs font-semibold text-brand-600">Target: CPNS 2026</span>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <Link
                            :href="resume ? resume.url : '/tryouts'"
                            class="inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700"
                        >
                            {{ resume ? 'Lanjutkan Tryout' : 'Mulai Tryout' }}
                        </Link>
                        <Link href="/history" class="inline-flex h-11 items-center rounded-xl border border-line bg-white px-5 font-semibold hover:bg-brand-50">
                            Lihat progres
                        </Link>
                    </div>
                </section>

                <section class="rounded-2xl border border-line bg-surface p-6 shadow-card">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold">Lanjutkan Latihan</h2>
                            <template v-if="resume">
                                <p class="mt-1 truncate text-sm font-medium text-ink-600">
                                    {{ resume.title }} · {{ resume.answered }} dari {{ resume.total }} soal
                                </p>
                                <p class="mt-1 text-xs text-ink-400">Sisa waktu {{ resume.remaining_minutes }} menit</p>
                                <div class="mt-3 h-2 w-full max-w-sm overflow-hidden rounded-full bg-brand-100">
                                    <div class="h-full rounded-full bg-brand-600" :style="{ width: `${resume.percent}%` }"></div>
                                </div>
                                <p class="mt-1 text-xs font-semibold text-ink-600">{{ resume.percent }}%</p>
                            </template>
                            <p v-else class="mt-1 text-sm text-ink-600">Belum ada tryout berjalan. Mulai paket gratis untuk mengukur kemampuanmu.</p>
                        </div>
                        <Link
                            :href="resume ? resume.url : '/tryouts'"
                            class="inline-flex h-11 shrink-0 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700"
                        >
                            {{ resume ? 'Lanjutkan' : 'Pilih paket' }}
                        </Link>
                    </div>
                </section>

                <section>
                    <h2 class="text-lg font-bold">Latih Kemampuan</h2>
                    <div class="mt-3 grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <Link
                            v-for="subtest in subtests"
                            :key="subtest.code"
                            href="/tryouts"
                            :class="['group rounded-2xl border border-line p-5 shadow-card transition-transform duration-150 hover:-translate-y-0.5', palette[subtest.code]?.split(' ')[0] ?? 'bg-brand-50']"
                        >
                            <span :class="['inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white font-bold', palette[subtest.code]?.split(' ')[1] ?? 'text-brand-600']">
                                {{ subtest.code }}
                            </span>
                            <p class="mt-3 text-sm font-bold text-ink-900">{{ subtest.name }}</p>
                            <p class="text-xs text-ink-600">{{ subtest.question_count }} soal tersedia</p>
                        </Link>
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                    <h2 class="text-sm font-bold">Target harian</h2>
                    <div class="mt-3 flex items-center gap-4">
                        <ProgressRing :percent="goal.percent" :label="`${goal.minutes}/${goal.target}`" :size="104" />
                        <p class="text-xs text-ink-600">
                            {{ goal.percent >= 100 ? 'Target hari ini tercapai!' : `Kurang ${Math.max(0, goal.target - goal.minutes)} menit lagi.` }}
                            <span class="mt-1 block font-semibold text-warning">Streak {{ goal.streak }} hari</span>
                        </p>
                    </div>
                    <ol class="mt-4 flex justify-between" aria-label="Tujuh hari terakhir">
                        <li v-for="day in goal.days" :key="day.date" class="text-center">
                            <span
                                :class="[
                                    'flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold',
                                    day.done ? 'bg-brand-600 text-white' : 'border border-line text-ink-400',
                                    day.today ? 'ring-2 ring-brand-600 ring-offset-2' : '',
                                ]"
                                :aria-label="`${day.label}: ${day.done ? 'tercapai' : 'belum'}`"
                            >
                                {{ day.done ? '✓' : '' }}
                            </span>
                            <span class="mt-1 block text-[10px] text-ink-400">{{ day.label }}</span>
                        </li>
                    </ol>
                    <p class="mt-3 text-xs text-ink-400">{{ dailyDone }} dari 7 hari minggu ini tercapai.</p>
                </section>

                <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                    <h2 class="text-sm font-bold">Peringkat</h2>
                    <p v-if="leaderboard.length === 0" class="mt-2 text-xs text-ink-600">Belum ada peserta menyelesaikan tryout.</p>
                    <ol v-else class="mt-3 space-y-1">
                        <li
                            v-for="(row, index) in leaderboard"
                            :key="`${row.name}-${index}`"
                            :class="['flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm', row.is_me ? 'bg-brand-100' : '']"
                        >
                            <span :class="['flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold', index === 0 ? 'bg-warning text-white' : index === 1 ? 'bg-ink-400 text-white' : index === 2 ? 'bg-[#d08a52] text-white' : 'bg-brand-50 text-ink-600']">
                                {{ index + 1 }}
                            </span>
                            <span class="min-w-0 flex-1 truncate">{{ row.name }}</span>
                            <span class="font-semibold tabular-nums">{{ row.score }}</span>
                            <span v-if="row.is_me" class="text-[10px] font-bold text-brand-600">Kamu</span>
                        </li>
                    </ol>
                    <p class="mt-3 text-xs text-ink-400">Skor terbaik per peserta, semua paket.</p>
                </section>

                <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                    <h2 class="text-sm font-bold">Ringkasan</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-ink-600">Skor terbaik</dt>
                            <dd class="font-bold tabular-nums">{{ stats.best_score }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-600">Tryout selesai</dt>
                            <dd class="font-bold tabular-nums">{{ stats.completed }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-600">Paket tersedia</dt>
                            <dd class="font-bold tabular-nums">{{ stats.available }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </AppLayout>
</template>
