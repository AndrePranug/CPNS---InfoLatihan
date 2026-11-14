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
        <Head title="Forum" />
        <h1 class="text-3xl font-bold">Forum Diskusi</h1>
        <p class="mt-1 text-sm text-ink-600">Tanya strategi, bahas soal, atau bagikan pengalaman tryout.</p>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-3">
                <p v-if="threads.data.length === 0" class="rounded-2xl border border-line bg-white p-8 text-center text-ink-600">
                    Belum ada diskusi. Mulai topik pertama.
                </p>

                <article v-for="thread in threads.data" :key="thread.id" class="rounded-2xl border border-line bg-white p-5 shadow-card">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span v-if="thread.is_pinned" class="rounded-full bg-brand-100 px-2 py-0.5 font-semibold text-brand-600">Disematkan</span>
                        <span v-if="thread.is_locked" class="rounded-full bg-ink-400/10 px-2 py-0.5 font-semibold text-ink-600">Dikunci</span>
                    </div>
                    <h2 class="mt-2 text-lg font-bold">
                        <Link :href="`/forum/${thread.id}`" class="hover:text-brand-600">{{ thread.title }}</Link>
                    </h2>
                    <p class="mt-1 text-sm text-ink-600">{{ thread.excerpt }}</p>
                    <p class="mt-3 text-xs text-ink-400">
                        {{ thread.author }} · {{ thread.replies_count }} balasan · {{ thread.updated_at }}
                    </p>
                </article>

                <nav v-if="threads.links.length > 3" class="flex flex-wrap gap-1" aria-label="Navigasi halaman">
                    <Link
                        v-for="link in threads.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="['rounded-lg px-3 py-2 text-sm', link.active ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-brand-50', !link.url && 'pointer-events-none opacity-40']"
                    >
                        {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
                    </Link>
                </nav>
            </div>

            <form class="space-y-3 self-start rounded-2xl border border-line bg-white p-6 shadow-card" @submit.prevent="submit">
                <p class="text-sm font-bold">Diskusi baru</p>
                <input v-model="form.title" placeholder="Judul diskusi" aria-label="Judul diskusi" class="h-11 w-full rounded-xl border border-line px-4 focus:border-brand-600 focus:outline-none">
                <p v-if="form.errors.title" class="text-xs text-danger">{{ form.errors.title }}</p>
                <textarea v-model="form.body" rows="6" placeholder="Tulis pertanyaan atau pengalaman…" aria-label="Isi diskusi" class="w-full rounded-xl border border-line p-3 focus:border-brand-600 focus:outline-none"></textarea>
                <p v-if="form.errors.body" class="text-xs text-danger">{{ form.errors.body }}</p>
                <button type="submit" class="h-11 w-full rounded-xl bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="form.processing">
                    Kirim diskusi
                </button>
            </form>
        </div>
    </AppLayout>
</template>
