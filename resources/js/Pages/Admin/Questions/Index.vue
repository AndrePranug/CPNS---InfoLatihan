<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    questions: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.q ?? '');
let debounce = null;

watch(search, (value) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get('/admin/questions', { q: value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

const destroy = (id) => {
    if (confirm('Hapus soal ini?')) {
        router.delete(`/admin/questions/${id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Bank Soal" />
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">Bank Soal</h1>
            <div class="flex gap-2">
                <Link href="/admin/questions/import" class="inline-flex h-11 items-center rounded-xl border border-line bg-white px-5 font-semibold hover:bg-brand-50">
                    Impor CSV/Excel
                </Link>
                <Link href="/admin/questions/create" class="inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700">
                    Tambah soal
                </Link>
            </div>
        </div>

        <input
            v-model="search"
            type="search"
            placeholder="Cari isi soal"
            aria-label="Cari soal"
            class="mt-4 h-11 w-full max-w-sm rounded-xl border border-line bg-white px-4 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none"
        >

        <div class="mt-4 overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-white text-left text-xs text-ink-400">
                    <tr>
                        <th class="p-3">Soal</th>
                        <th class="p-3">Jenis</th>
                        <th class="p-3">Subtes / Formasi</th>
                        <th class="p-3">Topik</th>
                        <th class="p-3">Versi</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="question in questions.data" :key="question.id" class="h-12 border-t border-line">
                        <td class="max-w-md truncate p-3">{{ question.body }}</td>
                        <td class="p-3 font-semibold uppercase">{{ question.exam_type }}</td>
                        <td class="p-3 font-semibold">{{ question.exam_type === 'skb' ? (question.formation ?? '—') : question.subtest }}</td>
                        <td class="p-3 text-ink-600">{{ question.topic ?? '—' }}</td>
                        <td class="p-3 tabular-nums">{{ question.version }}</td>
                        <td class="p-3 text-right">
                            <Link :href="`/admin/questions/${question.id}/edit`" class="font-semibold text-brand-600">Revisi</Link>
                            <button class="ml-3 font-semibold text-danger" @click="destroy(question.id)">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="questions.data.length === 0">
                        <td colspan="6" class="p-8 text-center text-ink-600">Belum ada soal.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav v-if="questions.links.length > 3" class="mt-4 flex flex-wrap gap-1" aria-label="Navigasi halaman">
            <Link
                v-for="link in questions.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="['rounded-lg px-3 py-2 text-sm', link.active ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-brand-50', !link.url && 'pointer-events-none opacity-40']"
            >
                {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
            </Link>
        </nav>
    </AdminLayout>
</template>
