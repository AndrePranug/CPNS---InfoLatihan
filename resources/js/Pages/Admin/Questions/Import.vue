<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
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
        <Head title="Impor Soal CSV/Excel" />

        <div class="mb-4">
            <Link href="/admin/questions" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline">
                &larr; Kembali ke Bank Soal
            </Link>
        </div>

        <div class="max-w-3xl space-y-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Impor Soal Massal</h1>
                <p class="mt-1 text-sm text-ink-600">Unggah ratusan butir soal sekaligus menggunakan file spreadsheet CSV atau Microsoft Excel (.xlsx).</p>
            </div>

            <!-- Upload Card -->
            <div class="rounded-2xl border border-line bg-surface p-6 sm:p-7 shadow-card space-y-5">
                <div class="space-y-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-ink-400">Struktur Format Baris Pertama (Header)</p>
                    <code class="block overflow-x-auto rounded-xl bg-surface-raised p-3.5 font-mono text-xs text-ink-900 border border-line">
                        {{ header }}
                    </code>
                </div>

                <div class="rounded-xl bg-brand-50/60 p-4 border border-brand-100 text-xs text-ink-700 space-y-2">
                    <p class="font-bold text-brand-900">Petunjuk Pengisian Kolom:</p>
                    <ul class="list-disc space-y-1 pl-4 leading-relaxed">
                        <li><strong>subtest</strong>: Diisi salah satu dari <code>TWK</code>, <code>TIU</code>, atau <code>TKP</code>.</li>
                        <li><strong>kunci</strong>: Huruf pilihan benar (<code>A</code>–<code>E</code>), digunakan untuk soal TWK dan TIU.</li>
                        <li><strong>bobot</strong>: Khusus subtes TKP (skala nilai 1–5), format 5 angka dipisah tanda <code>|</code>, contoh: <code>5|4|3|2|1</code>.</li>
                        <li><strong>kesulitan</strong>: Angka 1 (Mudah), 2 (Sedang), 3 (Sulit). Bila kosong akan otomatis bernilai 2.</li>
                        <li><strong>topik</strong>: Nama topik materi. Topik baru akan dibuat secara otomatis jika belum ada.</li>
                    </ul>
                </div>

                <form class="space-y-4 pt-2" @submit.prevent="submit">
                    <div>
                        <label class="block text-xs font-semibold text-ink-900 mb-2">Pilih File Spreadsheet</label>
                        <input
                            type="file"
                            accept=".csv,.txt,.xlsx,.xls,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            aria-label="File CSV atau Excel"
                            class="block w-full text-xs text-ink-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer"
                            @change="pick"
                        >
                        <p v-if="form.errors.file" class="mt-1.5 text-xs text-danger font-medium">{{ form.errors.file }}</p>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center rounded-xl bg-brand-600 px-6 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-40 disabled:pointer-events-none"
                            :disabled="!form.file || form.processing"
                        >
                            {{ form.processing ? 'Memproses Berkas…' : 'Mulai Proses Impor' }}
                        </button>
                    </div>
                </form>

                <!-- Skipped Lines / Error Report -->
                <div v-if="errors.length" class="rounded-xl border border-danger/20 bg-danger/5 p-4 space-y-2">
                    <p class="text-xs font-bold text-danger flex items-center gap-1.5">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                        </svg>
                        Baris Data yang Dilewati / Gagal Validasi:
                    </p>
                    <ul class="list-disc space-y-1 pl-5 text-xs text-danger leading-relaxed">
                        <li v-for="error in errors" :key="error">{{ error }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
