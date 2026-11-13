<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    stats: { type: Object, required: true },
    hardestQuestions: { type: Array, required: true },
});

const cards = [
    { key: 'users', label: 'Pengguna' },
    { key: 'questions', label: 'Soal' },
    { key: 'published_tryouts', label: 'Paket terbit' },
    { key: 'completed_attempts', label: 'Tryout selesai' },
    { key: 'average_score', label: 'Rata-rata skor' },
];
</script>

<template>
    <AdminLayout>
        <Head title="Dasbor Admin" />
        <h1 class="text-2xl font-bold">Dasbor</h1>
        <section class="mt-6 grid gap-4 sm:grid-cols-3 lg:grid-cols-5">
            <div v-for="card in cards" :key="card.key" class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">{{ card.label }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ stats[card.key] }}</p>
            </div>
        </section>

        <section class="mt-6 rounded-2xl border border-line bg-white p-6 shadow-card">
            <h2 class="text-lg font-bold">Soal paling sering salah</h2>
            <p v-if="hardestQuestions.length === 0" class="mt-2 text-sm text-ink-600">
                Belum ada jawaban terkirim. Data muncul setelah peserta menyelesaikan tryout.
            </p>
            <ul v-else class="mt-3 divide-y divide-line">
                <li v-for="row in hardestQuestions" :key="row.id" class="flex items-start gap-3 py-3 text-sm">
                    <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-600">{{ row.subtest }}</span>
                    <p class="min-w-0 flex-1 line-clamp-2">{{ row.body }}</p>
                    <div class="shrink-0 text-right">
                        <p :class="['font-bold tabular-nums', row.correct_rate < 50 ? 'text-danger' : 'text-warning']">{{ row.correct_rate }}%</p>
                        <p class="text-xs text-ink-400">{{ row.answered }} jawaban</p>
                    </div>
                </li>
            </ul>
        </section>
    </AdminLayout>
</template>
