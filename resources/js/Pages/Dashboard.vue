<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProgressRing from '@/Components/ui/ProgressRing.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    resume: { type: Object, default: null },
    goal: { type: Object, required: true },
    leaderboards: { type: Object, required: true },
    missions: { type: Array, required: true },
    subtests: { type: Array, required: true },
    user: { type: Object, required: true },
});

const periodTabs = [
    { value: 'week', label: 'Minggu Ini' },
    { value: 'month', label: 'Bulan Ini' },
];

const period = ref('week');
const activeLeaderboard = computed(() => props.leaderboards[period.value] ?? []);

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 11) return 'Selamat pagi';
    if (hour < 15) return 'Selamat siang';
    if (hour < 19) return 'Selamat sore';
    return 'Selamat malam';
});

const palette = {
    TWK: {
        bg: 'bg-twk-bg',
        text: 'text-twk',
        border: 'border-twk/20',
        bar: 'bg-twk',
    },
    TIU: {
        bg: 'bg-tiu-bg',
        text: 'text-tiu',
        border: 'border-tiu/20',
        bar: 'bg-tiu',
    },
    TKP: {
        bg: 'bg-tkp-bg',
        text: 'text-tkp',
        border: 'border-tkp/20',
        bar: 'bg-tkp',
    },
};

const dailyDone = computed(() => props.goal.days.filter((day) => day.done).length);
</script>

<template>
    <AppLayout>
        <Head title="Dasbor" />
        
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <!-- Main Content (Left) -->
            <div class="space-y-6">
                <!-- Greeting Hero Banner -->
                <section class="relative overflow-hidden rounded-2xl border border-line bg-surface p-6 shadow-card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-600">
                                Target Seleksi 2026
                            </span>
                            <h1 class="mt-2 text-2xl font-bold tracking-tight text-ink-900">
                                {{ greeting }}, {{ user.name }}!
                            </h1>
                            <p class="mt-1 text-sm text-ink-600">
                                {{ resume ? 'Ada sesi tryout yang sedang berlangsung. Selesaikan sebelum waktu habis.' : 'Konsistensi latihan adalah kunci menembus ambang batas kelulusan.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <Link
                            :href="resume ? resume.url : '/tryouts'"
                            class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all"
                        >
                            {{ resume ? 'Lanjutkan Tryout' : 'Mulai Tryout Baru' }}
                        </Link>
                        <Link
                            href="/practice"
                            class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface px-4 text-sm font-semibold text-ink-600 hover:bg-brand-50 active:scale-[0.98] transition-all"
                        >
                            Latihan Santai (Tanpa Timer)
                        </Link>
                    </div>
                </section>

                <!-- Active Attempt Section (if exists) -->
                <section v-if="resume" class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-card">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700">
                                <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Sesi Tryout Sedang Berjalan
                            </div>
                            <h2 class="mt-1 truncate text-base font-bold text-ink-900">{{ resume.title }}</h2>
                            <p class="text-xs text-ink-600 mt-0.5">
                                Terjawab {{ resume.answered }} dari {{ resume.total }} soal · Sisa waktu <span class="font-bold text-ink-900">{{ resume.remaining_minutes }} menit</span>
                            </p>
                            
                            <div class="mt-3 flex items-center gap-3">
                                <div class="h-2 flex-1 max-w-xs overflow-hidden rounded-full bg-amber-200">
                                    <div class="h-full rounded-full bg-amber-600 transition-all duration-300" :style="{ width: `${resume.percent}%` }"></div>
                                </div>
                                <span class="text-xs font-semibold tabular-nums text-amber-800">{{ resume.percent }}%</span>
                            </div>
                        </div>

                        <Link
                            :href="resume.url"
                            class="inline-flex h-10 shrink-0 items-center justify-center rounded-xl bg-amber-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition-colors"
                        >
                            Lanjutkan
                        </Link>
                    </div>
                </section>

                <!-- Subtest Category Cards -->
                <section class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-ink-900">Materi Ujian SKD</h2>
                        <Link href="/practice" class="text-xs font-semibold text-brand-600 hover:underline">
                            Latihan per materi &rarr;
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <Link
                            v-for="subtest in subtests"
                            :key="subtest.code"
                            :href="`/practice?subtest=${subtest.code}`"
                            class="group relative overflow-hidden rounded-2xl border border-line bg-surface p-4 shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover"
                        >
                            <div class="flex items-center justify-between">
                                <span :class="[palette[subtest.code]?.bg ?? 'bg-brand-50', palette[subtest.code]?.text ?? 'text-brand-600']" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold">
                                    {{ subtest.code }}
                                </span>
                                <span class="text-[11px] font-semibold text-ink-400">
                                    {{ subtest.question_count }} soal
                                </span>
                            </div>
                            <h3 class="mt-3 text-sm font-bold text-ink-900 group-hover:text-brand-600 transition-colors">
                                {{ subtest.name }}
                            </h3>
                            <p class="mt-1 text-xs text-ink-600">Klik untuk mulai latihan mandiri</p>
                            
                            <div :class="[palette[subtest.code]?.bar ?? 'bg-brand-600']" class="absolute bottom-0 inset-x-0 h-0.5 opacity-60"></div>
                        </Link>
                    </div>
                </section>

                <!-- Quick Stats Strip -->
                <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-ink-400 mb-3">Statistik Kilas</h2>
                    <div class="grid grid-cols-3 gap-4 text-center divide-x divide-line">
                        <div>
                            <p class="text-xs text-ink-600">Skor Terbaik</p>
                            <p class="mt-1 text-2xl font-bold tabular-nums text-ink-900">{{ stats.best_score ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-600">Tryout Selesai</p>
                            <p class="mt-1 text-2xl font-bold tabular-nums text-ink-900">{{ stats.completed ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-600">Paket Siap</p>
                            <p class="mt-1 text-2xl font-bold tabular-nums text-ink-900">{{ stats.available ?? 0 }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar Widgets (Right) -->
            <aside class="space-y-5">
                <!-- Daily Target Widget -->
                <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-ink-900">Target Harian</h2>
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-600">
                            <svg class="h-3.5 w-3.5 text-amber-500 fill-amber-500" viewBox="0 0 24 24">
                                <path d="M12 23c-4.97 0-9-3.582-9-8 0-3.074 1.77-5.94 4.5-7.5.2-.11.45-.04.57.16.11.19.06.44-.12.57C6.08 9.58 5 11.72 5 15c0 3.86 3.14 7 7 7s7-3.14 7-7c0-2.31-.95-4.43-2.5-6-.17-.17-.18-.44-.02-.63.16-.19.45-.22.65-.06C19.24 9.94 20.5 12.35 20.5 15c0 4.418-4.03 8-8.5 8zm.5-17.5c-.28 0-.5-.22-.5-.5 0-1.38-.89-2.58-2.22-3.02-.26-.09-.4-.37-.31-.63.09-.26.37-.4.63-.31C12.01 1.68 13 3.23 13 5c0 .28-.22.5-.5.5z"/>
                            </svg>
                            <span>{{ goal.streak }} Hari</span>
                        </span>
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <ProgressRing :percent="goal.percent" :label="`${goal.minutes}m`" :size="88" />
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-ink-900">
                                {{ goal.percent >= 100 ? 'Target hari ini tercapai!' : `Kurang ${Math.max(0, goal.target - goal.minutes)} menit belajar lagi.` }}
                            </p>
                            <p class="text-[11px] text-ink-400 mt-1">
                                Target: {{ goal.target }} menit/hari
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-line/60 pt-3">
                        <ol class="flex justify-between" aria-label="Aktivitas 7 hari terakhir">
                            <li v-for="day in goal.days" :key="day.date" class="flex flex-col items-center">
                                <span
                                    :class="[
                                        'flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-bold transition-all',
                                        day.done ? 'bg-success text-white shadow-xs' : 'border border-line bg-surface-raised text-ink-400',
                                        day.today ? 'ring-2 ring-brand-600 ring-offset-1 font-extrabold' : '',
                                    ]"
                                    :title="`${day.label}: ${day.done ? 'Tercapai' : 'Belum'}`"
                                >
                                    <svg v-if="day.done" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </span>
                                <span class="mt-1 text-[10px] font-medium text-ink-400">{{ day.label }}</span>
                            </li>
                        </ol>
                        <p class="mt-2 text-center text-[11px] text-ink-400">{{ dailyDone }} dari 7 hari aktif</p>
                    </div>
                </section>

                <!-- Weekly Missions -->
                <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-ink-900">Misi Mingguan</h2>
                        <span class="text-[10px] text-ink-400">Reset tiap Senin</span>
                    </div>

                    <ul class="mt-3.5 space-y-3">
                        <li v-for="mission in missions" :key="mission.key" class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span :class="mission.done ? 'font-semibold text-success flex items-center gap-1.5' : 'font-medium text-ink-600'">
                                    <svg v-if="mission.done" class="h-3 w-3 shrink-0 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    {{ mission.label }}
                                </span>
                                <span class="font-semibold tabular-nums text-ink-600">{{ mission.current }}/{{ mission.target }}</span>
                            </div>
                            <div class="h-1.5 overflow-hidden rounded-full bg-brand-100">
                                <div
                                    :class="['h-full rounded-full transition-all duration-300', mission.done ? 'bg-success' : 'bg-brand-600']"
                                    :style="{ width: `${Math.min(100, Math.round((mission.current / mission.target) * 100))}%` }"
                                ></div>
                            </div>
                        </li>
                    </ul>
                </section>

                <!-- Leaderboard Mini Widget -->
                <section class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-sm font-bold text-ink-900">Peringkat</h2>
                        <div class="flex gap-1" role="tablist" aria-label="Periode peringkat">
                            <button
                                v-for="tab in periodTabs"
                                :key="tab.value"
                                role="tab"
                                :aria-selected="period === tab.value"
                                :class="[
                                    'h-6 rounded-md px-2 text-[10px] font-semibold transition-colors',
                                    period === tab.value ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-brand-50'
                                ]"
                                @click="period = tab.value"
                            >
                                {{ tab.label }}
                            </button>
                        </div>
                    </div>

                    <p v-if="activeLeaderboard.length === 0" class="mt-3 text-xs text-ink-400 text-center py-4">
                        Belum ada data nilai periode ini.
                    </p>
                    <ol v-else class="mt-3 space-y-1">
                        <li
                            v-for="(row, index) in activeLeaderboard.slice(0, 5)"
                            :key="`${row.name}-${index}`"
                            :class="[
                                'flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs transition-colors',
                                row.is_me ? 'bg-brand-50 font-semibold text-brand-700' : 'text-ink-900 hover:bg-surface-raised'
                            ]"
                        >
                            <span
                                :class="[
                                    'flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-bold',
                                    index === 0 ? 'bg-amber-400 text-white' : index === 1 ? 'bg-slate-400 text-white' : index === 2 ? 'bg-amber-700 text-white' : 'bg-surface-raised text-ink-600'
                                ]"
                            >
                                {{ index + 1 }}
                            </span>
                            <span class="min-w-0 flex-1 truncate">{{ row.name }}</span>
                            <span class="font-bold tabular-nums text-ink-900">{{ row.score }}</span>
                            <span v-if="row.is_me" class="rounded bg-brand-600 px-1 py-0.2 text-[9px] font-bold text-white">Kamu</span>
                        </li>
                    </ol>

                    <div class="mt-3 border-t border-line/60 pt-2 text-center">
                        <Link href="/rankings" class="text-[11px] font-semibold text-brand-600 hover:underline">
                            Lihat peringkat lengkap &rarr;
                        </Link>
                    </div>
                </section>
            </aside>
        </div>
    </AppLayout>
</template>
