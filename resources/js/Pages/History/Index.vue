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
        
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Riwayat Tryout</h1>
            <p class="mt-1 text-sm text-ink-600">Catatan lengkap seluruh simulasi SKD yang pernah Anda selesaikan.</p>
        </div>

        <!-- Trend Chart Section (if multiple attempts) -->
        <section v-if="trend.length > 1" class="mt-6 rounded-2xl border border-line bg-surface p-6 shadow-card">
            <div class="flex items-baseline justify-between border-b border-line/60 pb-3">
                <h2 class="text-base font-bold text-ink-900">Perkembangan Skor</h2>
                <span class="text-xs text-ink-400">Tren skor total</span>
            </div>
            <div class="mt-4 flex h-32 items-end gap-2 px-2" role="img" :aria-label="`Grafik skor: ${trend.join(', ')}`">
                <div
                    v-for="(score, index) in trend"
                    :key="index"
                    class="group relative flex-1 flex flex-col items-center justify-end h-full"
                >
                    <div
                        class="w-full rounded-t-md bg-brand-600/85 transition-all group-hover:bg-brand-600"
                        :style="{ height: `${Math.max(6, barHeights[index])}%` }"
                    ></div>
                    <span class="mt-1 text-[10px] font-semibold text-ink-600 tabular-nums">{{ score }}</span>
                </div>
            </div>
        </section>

        <!-- Empty State -->
        <div v-if="attempts.data.length === 0" class="mt-6 rounded-2xl border border-line bg-surface p-12 text-center shadow-card">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-base font-bold text-ink-900">Belum Ada Riwayat Ujian</h2>
            <p class="mt-1 text-xs text-ink-600">Selesaikan satu paket tryout untuk melihat riwayat nilai dan evaluasi pembahasan.</p>
            <div class="mt-5">
                <Link href="/tryouts" class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-5 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 transition-colors">
                    Mulai Tryout Sekarang
                </Link>
            </div>
        </div>

        <!-- History Table -->
        <div v-else class="mt-6 overflow-hidden rounded-2xl border border-line bg-surface shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                        <tr>
                            <th class="py-3.5 px-4">Paket Tryout</th>
                            <th class="py-3.5 px-4">Tanggal Ujian</th>
                            <th class="py-3.5 px-4">Skor Total</th>
                            <th class="py-3.5 px-4">Status Kelulusan</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        <tr v-for="attempt in attempts.data" :key="attempt.id" class="transition-colors hover:bg-surface-raised">
                            <td class="py-3.5 px-4 font-bold text-ink-900">{{ attempt.title }}</td>
                            <td class="py-3.5 px-4 text-ink-600">{{ attempt.submitted_at }}</td>
                            <td class="py-3.5 px-4 text-sm font-extrabold tabular-nums text-ink-900">{{ attempt.score }}</td>
                            <td class="py-3.5 px-4">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold',
                                        attempt.is_passed ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'
                                    ]"
                                >
                                    {{ attempt.is_passed ? 'Lulus PG' : 'Belum Lulus' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <Link
                                    :href="attempt.result_url"
                                    class="inline-flex h-8 items-center rounded-lg bg-brand-50 px-3 font-semibold text-brand-600 hover:bg-brand-100 transition-colors"
                                >
                                    Bahas &rarr;
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
