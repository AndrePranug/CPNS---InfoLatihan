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
    if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
        router.delete(`/admin/questions/${id}`, { preserveScroll: true });
    }
};

const badgePalette = {
    TWK: 'bg-twk-bg text-twk border-twk/20',
    TIU: 'bg-tiu-bg text-tiu border-tiu/20',
    TKP: 'bg-tkp-bg text-tkp border-tkp/20',
};
</script>

<template>
    <AdminLayout>
        <Head title="Bank Soal" />

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Bank Soal Ujian</h1>
                <p class="mt-1 text-sm text-ink-600">Koleksi seluruh butir soal simulasi SKD dan SKB.</p>
            </div>

            <div class="flex items-center gap-2.5">
                <Link
                    href="/admin/questions/import"
                    class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface px-4 text-xs font-semibold text-ink-700 shadow-xs hover:bg-brand-50 hover:text-brand-700 transition-colors"
                >
                    Impor CSV/Excel
                </Link>
                <Link
                    href="/admin/questions/create"
                    class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-4 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 transition-colors"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                    </svg>
                    Tambah Soal
                </Link>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative max-w-md">
            <svg viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-ink-400">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
            </svg>
            <input
                v-model="search"
                type="search"
                placeholder="Cari konten teks soal…"
                aria-label="Cari soal"
                class="h-11 w-full rounded-xl border border-line bg-surface pl-10 pr-4 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none"
            >
        </div>

        <!-- Questions Table -->
        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                        <tr>
                            <th class="py-3.5 px-4">Isi Soal</th>
                            <th class="py-3.5 px-4 w-24">Tipe Ujian</th>
                            <th class="py-3.5 px-4 w-36">Subtes / Formasi</th>
                            <th class="py-3.5 px-4 w-40">Topik</th>
                            <th class="py-3.5 px-4 w-16 text-center">Versi</th>
                            <th class="py-3.5 px-4 text-right w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        <tr
                            v-for="question in questions.data"
                            :key="question.id"
                            class="transition-colors hover:bg-surface-raised"
                        >
                            <td class="py-3.5 px-4 max-w-md">
                                <p class="line-clamp-2 font-medium text-ink-900 leading-relaxed">{{ question.body }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="rounded bg-surface-raised px-2 py-0.5 font-bold uppercase text-[10px] text-ink-600 border border-line">
                                    {{ question.exam_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span
                                    v-if="question.exam_type === 'skd'"
                                    :class="['rounded-md px-2 py-0.5 text-[11px] font-bold border', badgePalette[question.subtest] ?? 'bg-brand-50 text-brand-600']"
                                >
                                    {{ question.subtest }}
                                </span>
                                <span v-else class="rounded-md bg-purple-50 px-2 py-0.5 text-[11px] font-bold text-purple-700 border border-purple-200">
                                    {{ question.formation ?? 'SKB' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-ink-600">{{ question.topic ?? '—' }}</td>
                            <td class="py-3.5 px-4 text-center tabular-nums font-semibold text-ink-400">v{{ question.version }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <Link
                                        :href="`/admin/questions/${question.id}/edit`"
                                        class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-600 hover:bg-brand-100 transition-colors"
                                    >
                                        Revisi
                                    </Link>
                                    <button
                                        class="rounded-lg p-1 text-ink-400 hover:bg-danger/10 hover:text-danger transition-colors"
                                        title="Hapus"
                                        @click="destroy(question.id)"
                                    >
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 1 .75.75v7a.75.75 0 0 1-1.5 0v-7a.75.75 0 0 1 .75-.75Zm3.58.75a.75.75 0 0 0-1.5 0v7a.75.75 0 0 0 1.5 0v-7Z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="questions.data.length === 0">
                            <td colspan="6" class="p-8 text-center text-xs text-ink-400">
                                Tidak ada soal yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav v-if="questions.links.length > 3" class="flex flex-wrap gap-1.5 pt-2" aria-label="Navigasi halaman">
            <Link
                v-for="link in questions.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="[
                    'h-8 min-w-8 inline-flex items-center justify-center rounded-lg px-2.5 text-xs font-semibold transition-colors',
                    link.active ? 'bg-brand-600 text-white shadow-xs' : 'border border-line bg-surface text-ink-600 hover:bg-brand-50',
                    !link.url && 'pointer-events-none opacity-40'
                ]"
            >
                {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
            </Link>
        </nav>
    </AdminLayout>
</template>
