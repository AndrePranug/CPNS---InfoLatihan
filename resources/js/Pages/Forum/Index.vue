<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    threads: { type: Object, required: true },
});

const form = useForm({ title: '', body: '' });

const submit = () => form.post('/forum', {
    preserveScroll: true,
    onSuccess: () => form.reset(),
});
</script>

<template>
    <AppLayout>
        <Head title="Forum Diskusi" />
        
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Forum Diskusi Peserta</h1>
            <p class="mt-1 text-sm text-ink-600">Diskusikan strategi lolos passing grade, bedah soal rumit, dan berbagi pengalaman tes.</p>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <!-- Thread List (Left) -->
            <div class="space-y-4">
                <div v-if="threads.data.length === 0" class="rounded-2xl border border-line bg-surface p-12 text-center text-xs text-ink-400 shadow-card">
                    Belum ada topik diskusi. Mulai diskusi pertama melalui formulir di samping!
                </div>

                <article
                    v-for="thread in threads.data"
                    :key="thread.id"
                    :class="[
                        'rounded-2xl border bg-surface p-5 shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover space-y-2.5',
                        thread.is_pinned ? 'border-brand-300/80 bg-brand-50/20' : 'border-line'
                    ]"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <span v-if="thread.is_pinned" class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-[11px] font-bold text-brand-700 border border-brand-200">
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                            <span>Disematkan</span>
                        </span>
                        <span v-if="thread.is_locked" class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600">
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                            <span>Dikunci</span>
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-ink-900">
                        <Link :href="`/forum/${thread.id}`" class="hover:text-brand-600 transition-colors">
                            {{ thread.title }}
                        </Link>
                    </h2>
                    
                    <p class="text-xs text-ink-600 leading-relaxed line-clamp-2">
                        {{ thread.excerpt }}
                    </p>

                    <div class="flex items-center justify-between border-t border-line/60 pt-3 text-[11px] text-ink-400">
                        <span>Oleh: <strong class="text-ink-900 font-medium">{{ thread.author }}</strong> · {{ thread.updated_at }}</span>
                        <span class="inline-flex items-center gap-1.5 font-semibold text-brand-600">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-.974-.94 5.942 5.942 0 0 0 .902-3.033A7.545 7.545 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                            </svg>
                            <span>{{ thread.replies_count }} Balasan</span>
                        </span>
                    </div>
                </article>

                <!-- Pagination -->
                <nav v-if="threads.links.length > 3" class="flex flex-wrap gap-1.5 pt-2" aria-label="Navigasi halaman">
                    <Link
                        v-for="link in threads.links"
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
            </div>

            <!-- New Discussion Form (Right) -->
            <aside class="self-start">
                <form class="rounded-2xl border border-line bg-surface p-5 shadow-card space-y-3.5" @submit.prevent="submit">
                    <h2 class="text-sm font-bold text-ink-900">Buat Topik Baru</h2>
                    
                    <div>
                        <label class="block text-xs font-medium text-ink-600 mb-1">Judul Diskusi</label>
                        <input
                            v-model="form.title"
                            placeholder="Contoh: Tips trik hitung cepat silogisme TIU"
                            class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
                        >
                        <p v-if="form.errors.title" class="mt-1 text-xs text-danger">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-ink-600 mb-1">Isi Diskusi / Pertanyaan</label>
                        <textarea
                            v-model="form.body"
                            rows="5"
                            placeholder="Tuliskan detail pertanyaan atau pengalamanmu…"
                            class="w-full rounded-xl border border-line bg-surface p-3 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
                        ></textarea>
                        <p v-if="form.errors.body" class="mt-1 text-xs text-danger">{{ form.errors.body }}</p>
                    </div>

                    <button
                        type="submit"
                        class="w-full h-10 rounded-xl bg-brand-600 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Mengirim…' : 'Kirim Diskusi' }}
                    </button>
                </form>
            </aside>
        </div>
    </AppLayout>
</template>
