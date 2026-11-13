<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    attempts: { type: Object, required: true },
    trend: { type: Array, required: true },
});

const barHeights = computed(() => {
    const max = Math.max(1, ...props.trend);
    return props.trend.map((score) => Math.round((score / max) * 100));
});
</script>

<template>
    <AppLayout>
        <Head title="Riwayat Nilai" />
        <h1 class="text-3xl font-bold">Riwayat Nilai</h1>

        <section v-if="trend.length > 1" class="mt-6 rounded-2xl border border-line bg-white p-6 shadow-card">
            <h2 class="text-sm font-bold">Perkembangan skor</h2>
            <div class="mt-4 flex h-32 items-end gap-2" role="img" :aria-label="`Grafik skor: ${trend.join(', ')}`">
                <div
                    v-for="(score, index) in trend"
                    :key="index"
                    class="flex-1 rounded-t-lg bg-brand-600"
                    :style="{ height: `${Math.max(4, barHeights[index])}%` }"
                    :title="`Tryout ${index + 1}: ${score}`"
                ></div>
            </div>
        </section>

        <p v-if="attempts.data.length === 0" class="mt-6 rounded-2xl border border-line bg-white p-8 text-center text-ink-600">
            Belum ada riwayat. Mulai tryout pertama.
        </p>

        <div v-else class="mt-6 overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-ink-400">
                    <tr>
                        <th class="p-3">Paket</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Skor</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="attempt in attempts.data" :key="attempt.id" class="h-12 border-t border-line">
                        <td class="p-3 font-semibold">{{ attempt.title }}</td>
                        <td class="p-3 text-ink-600">{{ attempt.submitted_at }}</td>
                        <td class="p-3 font-bold tabular-nums">{{ attempt.score }}</td>
                        <td class="p-3">
                            <span :class="['rounded-full px-2 py-1 text-xs font-semibold', attempt.is_passed ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger']">
                                {{ attempt.is_passed ? 'Lulus' : 'Belum lulus' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <Link :href="attempt.result_url" class="font-semibold text-brand-600">Pembahasan</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
