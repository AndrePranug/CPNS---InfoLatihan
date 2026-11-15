<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    ranking: { type: Object, required: true },
});

const periods = [
    { key: 'week', label: 'Minggu Ini' },
    { key: 'month', label: 'Bulan Ini' },
    { key: 'all', label: 'Semua Waktu' },
];
</script>

<template>
    <AppLayout>
        <Head title="Peringkat Nasional" />

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Peringkat Nasional</h1>
                <p class="mt-1 text-sm text-ink-600">Skor tertinggi masing-masing peserta dari seluruh simulasi tryout.</p>
            </div>

            <!-- Position Callout -->
            <div v-if="ranking.total > 0" class="rounded-2xl border border-line bg-surface px-4 py-3 shadow-card self-start sm:self-auto">
                <p class="text-xs text-ink-400">Peringkat Anda:</p>
                <p class="text-sm font-bold text-ink-900">
                    <span class="text-lg font-extrabold text-brand-600">{{ ranking.position ? `#${ranking.position}` : '—' }}</span>
                    <span class="text-xs font-normal text-ink-600"> dari {{ ranking.total }} peserta</span>
                </p>
            </div>
        </div>

        <!-- Period Filter Pills -->
        <nav class="mt-6 flex gap-2" aria-label="Periode peringkat">
            <Link
                v-for="period in periods"
                :key="period.key"
                :href="`/rankings?period=${period.key}`"
                preserve-scroll
                :class="[
                    'inline-flex h-9 items-center rounded-xl px-4 text-xs font-semibold transition-all',
                    ranking.period === period.key
                        ? 'bg-brand-600 text-white shadow-xs'
                        : 'border border-line bg-surface text-ink-600 hover:bg-brand-50'
                ]"
                :aria-current="ranking.period === period.key ? 'page' : undefined"
            >
                {{ period.label }}
            </Link>
        </nav>

        <!-- Empty State -->
        <div v-if="ranking.total === 0" class="mt-6 rounded-2xl border border-line bg-surface p-12 text-center text-xs text-ink-400 shadow-card">
            Belum ada data nilai tryout pada periode ini.
        </div>

        <!-- Ranking Table -->
        <div v-else class="mt-6 overflow-hidden rounded-2xl border border-line bg-surface shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <caption class="sr-only">Peringkat nasional peserta berdasarkan skor terbaik</caption>
                    <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                        <tr>
                            <th class="py-3.5 px-4 w-20">Peringkat</th>
                            <th class="py-3.5 px-4">Nama Peserta</th>
                            <th class="py-3.5 px-4 text-right">Skor Tertinggi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        <tr
                            v-for="row in ranking.rows"
                            :key="`${row.position}-${row.name}`"
                            :class="[
                                'transition-colors',
                                row.is_me ? 'bg-brand-50/70 font-semibold text-brand-900' : 'hover:bg-surface-raised text-ink-900'
                            ]"
                        >
                            <td class="py-3.5 px-4">
                                <span
                                    :class="[
                                        'flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-bold shadow-xs',
                                        row.position === 1 ? 'bg-amber-400 text-white' :
                                        row.position === 2 ? 'bg-slate-400 text-white' :
                                        row.position === 3 ? 'bg-amber-700 text-white' :
                                        'bg-surface-raised border border-line text-ink-600'
                                    ]"
                                >
                                    {{ row.position }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[13px]">
                                {{ row.name }}
                                <span v-if="row.is_me" class="ml-2 rounded-full bg-brand-600 px-2 py-0.5 text-[10px] font-bold text-white">
                                    Anda
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right text-sm font-extrabold tabular-nums text-ink-900">
                                {{ row.score }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p v-if="ranking.total > ranking.rows.length" class="mt-3 text-center text-xs text-ink-400">
            Menampilkan {{ ranking.rows.length }} peserta teratas dari total {{ ranking.total }} peserta aktif.
        </p>
    </AppLayout>
</template>
