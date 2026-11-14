<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    ranking: { type: Object, required: true },
});

const periods = [
    { key: 'week', label: 'Minggu ini' },
    { key: 'month', label: 'Bulan ini' },
    { key: 'all', label: 'Semua waktu' },
];
</script>

<template>
    <AppLayout>
        <Head title="Peringkat Nasional" />

        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold">Peringkat Nasional</h1>
                <p class="mt-1 text-sm text-ink-600">Skor terbaik tiap peserta dari seluruh tryout.</p>
            </div>
            <p v-if="ranking.total > 0" class="rounded-2xl border border-line bg-white px-4 py-3 text-sm shadow-card">
                Posisi Anda
                <span class="font-bold tabular-nums text-brand-600">
                    {{ ranking.position ? `#${ranking.position}` : '—' }}
                </span>
                dari {{ ranking.total }} peserta
            </p>
        </div>

        <nav class="mt-5 flex flex-wrap gap-2" aria-label="Periode peringkat">
            <Link
                v-for="period in periods"
                :key="period.key"
                :href="`/rankings?period=${period.key}`"
                preserve-scroll
                :class="[
                    'inline-flex h-9 items-center rounded-xl px-3 text-sm font-medium transition-colors duration-150',
                    ranking.period === period.key ? 'bg-brand-600 text-white' : 'bg-white text-ink-600 hover:bg-brand-50',
                ]"
                :aria-current="ranking.period === period.key ? 'page' : undefined"
            >
                {{ period.label }}
            </Link>
        </nav>

        <p v-if="ranking.total === 0" class="mt-6 rounded-2xl border border-line bg-white p-8 text-center text-ink-600">
            Belum ada skor tryout pada periode ini.
        </p>

        <div v-else class="mt-6 overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
            <table class="w-full text-sm">
                <caption class="sr-only">Peringkat nasional peserta berdasarkan skor terbaik</caption>
                <thead class="text-left text-xs text-ink-400">
                    <tr>
                        <th class="p-3">Posisi</th>
                        <th class="p-3">Peserta</th>
                        <th class="p-3 text-right">Skor terbaik</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in ranking.rows"
                        :key="`${row.position}-${row.name}`"
                        :class="['h-12 border-t border-line', row.is_me ? 'bg-brand-50' : '']"
                    >
                        <td class="p-3 font-semibold tabular-nums">{{ row.position }}</td>
                        <td class="p-3">
                            {{ row.name }}
                            <span v-if="row.is_me" class="ml-2 rounded-full bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-600">Anda</span>
                        </td>
                        <td class="p-3 text-right font-semibold tabular-nums">{{ row.score }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p v-if="ranking.total > ranking.rows.length" class="mt-3 text-xs text-ink-400">
            Menampilkan {{ ranking.rows.length }} peserta teratas dari {{ ranking.total }}.
        </p>
    </AppLayout>
</template>
