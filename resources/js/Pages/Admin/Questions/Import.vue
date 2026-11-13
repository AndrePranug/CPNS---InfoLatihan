<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    header: { type: String, required: true },
});

const page = usePage();
const errors = computed(() => page.props.flash.import_errors ?? []);

const form = useForm({ file: null });
const pick = (event) => { form.file = event.target.files[0]; };
const submit = () => form.post('/admin/questions/import', { forceFormData: true });
</script>

<template>
    <AdminLayout>
        <Head title="Impor Soal" />
        <h1 class="text-2xl font-bold">Impor Soal CSV</h1>

        <div class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-line bg-white p-6 shadow-card">
            <div>
                <p class="text-sm font-medium">Format header (baris pertama)</p>
                <code class="mt-1 block overflow-x-auto rounded-lg bg-brand-50 p-3 text-xs">{{ header }}</code>
            </div>
            <ul class="list-disc space-y-1 pl-5 text-sm text-ink-600">
                <li><code>subtest</code>: TWK, TIU, atau TKP.</li>
                <li><code>kunci</code>: A–E, dipakai TWK/TIU.</li>
                <li><code>bobot</code>: khusus TKP, 5 angka dipisah <code>|</code>, contoh <code>5|4|3|2|1</code>.</li>
                <li><code>kesulitan</code>: 1–3, kosong = 2. <code>topik</code> dibuat otomatis bila baru.</li>
            </ul>

            <form class="space-y-3" @submit.prevent="submit">
                <input type="file" accept=".csv,text/csv" aria-label="File CSV" class="block w-full text-sm" @change="pick">
                <p v-if="form.errors.file" class="text-xs text-danger">{{ form.errors.file }}</p>
                <button type="submit" class="h-11 rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="!form.file || form.processing">
                    Impor
                </button>
            </form>

            <div v-if="errors.length" class="rounded-xl bg-danger/5 p-3">
                <p class="text-sm font-semibold text-danger">Baris yang dilewati</p>
                <ul class="mt-1 list-disc space-y-1 pl-5 text-xs text-danger">
                    <li v-for="error in errors" :key="error">{{ error }}</li>
                </ul>
            </div>
        </div>
    </AdminLayout>
</template>
