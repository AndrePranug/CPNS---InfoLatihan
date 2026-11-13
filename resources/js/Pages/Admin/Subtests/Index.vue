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
</script>

<template>
    <AdminLayout>
        <Head title="Subtes & Passing Grade" />
        <h1 class="text-2xl font-bold">Subtes & Passing Grade</h1>
        <p class="mt-1 text-sm text-ink-600">Parameter dipakai penilaian otomatis. Tidak ada angka permanen di kode.</p>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <form
                v-for="subtest in subtests"
                :key="subtest.id"
                class="space-y-3 rounded-2xl border border-line bg-white p-5 shadow-card"
                @submit.prevent="save(subtest)"
            >
                <div class="flex items-baseline justify-between">
                    <h2 class="text-lg font-bold">{{ subtest.code }}</h2>
                    <span class="text-xs text-ink-400">{{ subtest.questions_count }} soal · {{ subtest.topics_count }} topik</span>
                </div>
                <label class="block text-xs text-ink-600">
                    Jumlah soal
                    <input v-model.number="editForm(subtest).question_count" type="number" min="1" class="mt-1 h-11 w-full rounded-xl border border-line px-3 tabular-nums focus:border-brand-600 focus:outline-none">
                </label>
                <label class="block text-xs text-ink-600">
                    Passing grade
                    <input v-model.number="editForm(subtest).passing_grade" type="number" min="0" class="mt-1 h-11 w-full rounded-xl border border-line px-3 tabular-nums focus:border-brand-600 focus:outline-none">
                </label>
                <p v-if="editForm(subtest).errors.passing_grade" class="text-xs text-danger">{{ editForm(subtest).errors.passing_grade }}</p>
                <button type="submit" class="h-10 w-full rounded-xl bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="editForm(subtest).processing">
                    Simpan
                </button>
            </form>
        </div>
    </AdminLayout>
</template>
