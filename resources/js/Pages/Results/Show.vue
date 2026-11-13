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
    { value: 'semua', label: 'Semua' },
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
    benar: 'bg-success/10 text-success',
    sebagian: 'bg-warning/10 text-warning',
    salah: 'bg-danger/10 text-danger',
    kosong: 'bg-ink-400/10 text-ink-600',
};
</script>

<template>
    <AppLayout>
        <Head title="Hasil Tryout" />

        <section class="rounded-3xl border border-line bg-white p-8 text-center shadow-card">
            <p class="text-sm font-medium text-ink-600">{{ result.title }}</p>
            <p class="mt-2 text-5xl font-extrabold tabular-nums">{{ result.total_score }}</p>
            <p :class="['mt-3 inline-block rounded-full px-4 py-1 text-sm font-semibold', result.is_passed ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger']">
                {{ result.is_passed ? 'Lulus passing grade' : 'Belum lulus passing grade' }}
            </p>
        </section>

        <section class="mt-6 grid gap-6 sm:grid-cols-3">
            <article v-for="subtest in result.subtests" :key="subtest.code" class="rounded-2xl border border-line bg-white p-6 shadow-card">
                <p class="text-xs text-ink-400">{{ subtest.code }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ subtest.score }}</p>
                <p class="text-xs text-ink-600">passing grade {{ subtest.passing_grade }}</p>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-brand-100">
                    <div class="h-full rounded-full bg-brand-600" :style="{ width: `${Math.min(100, (subtest.score / (subtest.passing_grade * 1.5)) * 100)}%` }"></div>
                </div>
            </article>
        </section>

        <section class="mt-6 rounded-2xl border border-line bg-white p-6 shadow-card">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="text-lg font-bold">Peringkat paket ini</h2>
                <p v-if="ranking.position" class="text-sm text-ink-600">
                    Posisimu <span class="font-bold text-brand-600">#{{ ranking.position }}</span> dari {{ ranking.total }} peserta
                </p>
            </div>
            <p v-if="ranking.rows.length === 0" class="mt-2 text-sm text-ink-600">Belum ada peserta menyelesaikan paket ini.</p>
            <ol v-else class="mt-3 divide-y divide-line">
                <li
                    v-for="row in ranking.rows"
                    :key="row.position"
                    :class="['flex items-center gap-3 py-2 text-sm', row.is_me ? 'font-semibold text-brand-600' : '']"
                >
                    <span :class="['flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold', row.is_me ? 'bg-brand-600 text-white' : 'bg-brand-50 text-ink-600']">
                        {{ row.position }}
                    </span>
                    <span class="min-w-0 flex-1 truncate">{{ row.name }}</span>
                    <span class="tabular-nums">{{ row.score }}</span>
                </li>
            </ol>
            <p class="mt-2 text-xs text-ink-400">Skor terbaik per peserta.</p>
        </section>

        <section class="mt-6 rounded-2xl border border-line bg-white p-6 shadow-card">
            <h2 class="text-lg font-bold">Topik terlemah</h2>
            <p v-if="result.topics.length === 0" class="mt-2 text-sm text-ink-600">Belum ada data topik.</p>
            <ul v-else class="mt-3 divide-y divide-line">
                <li v-for="topic in result.topics" :key="topic.topic" class="flex items-center justify-between py-2 text-sm">
                    <span>{{ topic.topic }}</span>
                    <span class="font-semibold tabular-nums">{{ topic.correct }}/{{ topic.total }}</span>
                </li>
            </ul>
        </section>

        <section class="mt-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold">Pembahasan</h2>
                <div class="flex gap-1" role="tablist" aria-label="Filter pembahasan">
                    <button
                        v-for="item in filters"
                        :key="item.value"
                        role="tab"
                        :aria-selected="filter === item.value"
                        :class="['h-9 rounded-lg px-3 text-sm font-semibold', filter === item.value ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-brand-50']"
                        @click="filter = item.value"
                    >
                        {{ item.label }}
                    </button>
                </div>
            </div>

            <p v-if="visible.length === 0" class="mt-4 rounded-2xl border border-line bg-white p-6 text-sm text-ink-600">
                Tidak ada soal pada filter ini.
            </p>

            <ol class="mt-4 space-y-4">
                <li v-for="item in visible" :key="item.id" class="rounded-2xl border border-line bg-white p-6 shadow-card">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-brand-50 px-2 py-1 font-semibold text-brand-600">{{ item.subtest }}</span>
                        <span v-if="item.topic" class="text-ink-600">{{ item.topic }}</span>
                        <span :class="['ml-auto rounded-full px-2 py-1 font-semibold', badge[item.status]]">{{ item.status }}</span>
                        <span v-if="item.is_flagged" class="rounded-full bg-warning/10 px-2 py-1 font-semibold text-warning">ragu-ragu</span>
                    </div>

                    <p class="mt-3 font-semibold leading-relaxed">{{ item.body }}</p>
                    <img v-if="item.image_path" :src="item.image_path" alt="Gambar soal" class="mt-3 max-h-60 rounded-xl border border-line">

                    <ul class="mt-3 space-y-1 text-sm">
                        <li
                            v-for="option in item.options"
                            :key="option.id"
                            :class="[
                                'flex items-start gap-2 rounded-lg px-2 py-1',
                                option.is_correct ? 'bg-success/10 font-semibold text-success' : '',
                                option.is_selected && !option.is_correct ? 'bg-danger/10 text-danger' : '',
                            ]"
                        >
                            <span class="font-bold">{{ option.label }}.</span>
                            <span>{{ option.body }}</span>
                            <span v-if="option.is_selected" class="ml-auto text-xs">jawabanmu</span>
                            <span v-else-if="option.is_correct" class="ml-auto text-xs">kunci</span>
                        </li>
                    </ul>

                    <p v-if="item.explanation" class="mt-3 rounded-xl bg-brand-50 p-3 text-sm text-ink-600">
                        {{ item.explanation }}
                    </p>
                </li>
            </ol>
        </section>

        <div class="mt-6 flex gap-3">
            <Link href="/tryouts" class="inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700">
                Coba tryout lain
            </Link>
            <Link href="/history" class="inline-flex h-11 items-center rounded-xl border border-line bg-white px-5 font-semibold">
                Riwayat
            </Link>
        </div>
    </AppLayout>
</template>
