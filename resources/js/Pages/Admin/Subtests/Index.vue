<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    subtests: { type: Array, required: true },
});

const forms = {};
const editForm = (subtest) => {
    forms[subtest.id] ??= useForm({
        question_count: subtest.question_count,
        passing_grade: subtest.passing_grade,
    });

    return forms[subtest.id];
};

const save = (subtest) => {
    editForm(subtest).patch(`/admin/subtests/${subtest.id}`, { preserveScroll: true });
};

const badgePalette = {
    TWK: { bg: 'bg-twk-bg', text: 'text-twk', border: 'border-twk/30' },
    TIU: { bg: 'bg-tiu-bg', text: 'text-tiu', border: 'border-tiu/30' },
    TKP: { bg: 'bg-tkp-bg', text: 'text-tkp', border: 'border-tkp/30' },
};
</script>

<template>
    <AdminLayout>
        <Head title="Subtes & Passing Grade" />

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Subtes &amp; Standar Passing Grade</h1>
            <p class="mt-1 text-sm text-ink-600">Konfigurasi parameter penilaian otomatis SKD sesuai Kepmenpan RB terkini.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
            <form
                v-for="subtest in subtests"
                :key="subtest.id"
                class="rounded-2xl border border-line bg-surface p-6 shadow-card space-y-4 flex flex-col justify-between"
                @submit.prevent="save(subtest)"
            >
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-line/60 pb-3">
                        <span :class="[badgePalette[subtest.code]?.bg ?? 'bg-brand-50', badgePalette[subtest.code]?.text ?? 'text-brand-600', badgePalette[subtest.code]?.border ?? 'border-brand-200']" class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-xs font-bold border">
                            {{ subtest.code }}
                        </span>
                        <span class="rounded bg-surface-raised px-2 py-0.5 text-[11px] font-semibold text-ink-400">
                            {{ subtest.questions_count }} Soal Terdaftar
                        </span>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-ink-900">{{ subtest.name }}</h2>
                        <p class="text-xs text-ink-400 mt-0.5">{{ subtest.topics_count }} Topik Materi</p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1">Target Jumlah Soal (Ujian)</label>
                            <input
                                v-model.number="editForm(subtest).question_count"
                                type="number"
                                min="1"
                                class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-bold tabular-nums text-ink-900 focus:border-brand-600 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1">Ambang Batas Passing Grade (PG)</label>
                            <input
                                v-model.number="editForm(subtest).passing_grade"
                                type="number"
                                min="0"
                                class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-bold tabular-nums text-success focus:border-brand-600 focus:outline-none"
                            >
                            <p v-if="editForm(subtest).errors.passing_grade" class="mt-1 text-xs text-danger">{{ editForm(subtest).errors.passing_grade }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-line/60">
                    <button
                        type="submit"
                        class="w-full h-10 rounded-xl bg-brand-600 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                        :disabled="editForm(subtest).processing"
                    >
                        {{ editForm(subtest).processing ? 'Menyimpan…' : 'Simpan Parameter' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
