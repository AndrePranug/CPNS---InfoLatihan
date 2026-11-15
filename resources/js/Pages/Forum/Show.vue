<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    thread: { type: Object, required: true },
    replies: { type: Object, required: true },
    is_owner: { type: Boolean, required: true },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const canDeleteThread = computed(() => props.is_owner || isAdmin.value);

const replyForm = useForm({ body: '' });

const submitReply = () => replyForm.post(`/forum/${props.thread.id}/replies`, {
    preserveScroll: true,
    onSuccess: () => replyForm.reset(),
});

const destroyThread = () => {
    if (confirm('Hapus diskusi ini beserta semua balasannya?')) {
        router.delete(`/forum/${props.thread.id}`);
    }
};

const destroyReply = (id) => {
    if (confirm('Hapus balasan ini?')) {
        router.delete(`/forum/replies/${id}`, { preserveScroll: true });
    }
};

const toggleLock = () => {
    router.patch(`/forum/${props.thread.id}/lock`, {}, { preserveScroll: true });
};

const togglePin = () => {
    router.patch(`/forum/${props.thread.id}/pin`, {}, { preserveScroll: true });
};

const canDeleteReply = (reply) => isAdmin.value || reply.user_id === user.value?.id;
</script>

<template>
    <AppLayout>
        <Head :title="thread.title" />

        <div class="mb-4">
            <Link href="/forum" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline">
                &larr; Kembali ke Daftar Diskusi
            </Link>
        </div>

        <!-- Thread Header & Main Post -->
        <article class="rounded-2xl border border-line bg-surface p-6 sm:p-7 shadow-card space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span v-if="thread.is_pinned" class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-bold text-brand-700 border border-brand-200">
                    <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                    <span>Disematkan</span>
                </span>
                <span v-if="thread.is_locked" class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                    <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                    <span>Dikunci</span>
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-ink-900">{{ thread.title }}</h1>
            
            <p class="text-xs text-ink-400">
                Ditulis oleh <strong class="text-ink-900">{{ thread.author }}</strong> · {{ thread.created_at }}
            </p>

            <div class="border-t border-line/60 pt-4">
                <p class="whitespace-pre-line break-words text-sm leading-relaxed text-ink-900">{{ thread.body }}</p>
            </div>

            <!-- Mod Actions -->
            <div v-if="canDeleteThread || isAdmin" class="flex flex-wrap gap-3 border-t border-line/60 pt-4 text-xs">
                <button v-if="canDeleteThread" class="font-semibold text-danger hover:underline" @click="destroyThread">
                    Hapus Topik
                </button>
                <template v-if="isAdmin">
                    <span class="text-ink-400">·</span>
                    <button class="font-semibold text-ink-600 hover:text-ink-900" @click="toggleLock">
                        {{ thread.is_locked ? 'Buka Kunci' : 'Kunci Diskusi' }}
                    </button>
                    <span class="text-ink-400">·</span>
                    <button class="font-semibold text-ink-600 hover:text-ink-900" @click="togglePin">
                        {{ thread.is_pinned ? 'Lepas Sematan' : 'Sematkan Topik' }}
                    </button>
                </template>
            </div>
        </article>

        <!-- Replies Section -->
        <section class="mt-8 space-y-4">
            <h2 class="text-base font-bold text-ink-900">{{ thread.replies_count }} Komentar &amp; Tanggapan</h2>

            <div v-if="replies.data.length === 0" class="rounded-2xl border border-line bg-surface p-8 text-center text-xs text-ink-400 shadow-card">
                Belum ada balasan pada topik ini. Jadilah yang pertama memberikan solusi atau tanggapan!
            </div>

            <article
                v-for="reply in replies.data"
                :key="reply.id"
                class="rounded-2xl border border-line bg-surface p-5 shadow-card space-y-2.5"
            >
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-ink-900">{{ reply.author }}</span>
                    <span class="text-ink-400 text-[11px]">{{ reply.created_at }}</span>
                </div>

                <p class="whitespace-pre-line break-words text-xs leading-relaxed text-ink-600">{{ reply.body }}</p>

                <div v-if="canDeleteReply(reply)" class="pt-2 text-right">
                    <button class="text-[11px] font-semibold text-danger hover:underline" @click="destroyReply(reply.id)">
                        Hapus Balasan
                    </button>
                </div>
            </article>

            <!-- Pagination Links -->
            <nav v-if="replies.links.length > 3" class="flex flex-wrap gap-1.5 pt-2" aria-label="Navigasi balasan">
                <Link
                    v-for="link in replies.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    preserve-scroll
                    :class="[
                        'h-8 min-w-8 inline-flex items-center justify-center rounded-lg px-2.5 text-xs font-semibold transition-colors',
                        link.active ? 'bg-brand-600 text-white shadow-xs' : 'border border-line bg-surface text-ink-600 hover:bg-brand-50',
                        !link.url && 'pointer-events-none opacity-40'
                    ]"
                >
                    {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
                </Link>
            </nav>
        </section>

        <!-- Reply Form -->
        <form v-if="!thread.is_locked" class="mt-6 rounded-2xl border border-line bg-surface p-6 shadow-card space-y-3" @submit.prevent="submitReply">
            <h3 class="text-sm font-bold text-ink-900">Tulis Balasan</h3>
            <textarea
                v-model="replyForm.body"
                rows="4"
                placeholder="Bagikan pandangan atau solusi untuk pertanyaan ini…"
                class="w-full rounded-xl border border-line bg-surface p-3 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
            ></textarea>
            <p v-if="replyForm.errors.body" class="text-xs text-danger">{{ replyForm.errors.body }}</p>
            <button
                type="submit"
                class="h-10 rounded-xl bg-brand-600 px-5 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                :disabled="replyForm.processing"
            >
                {{ replyForm.processing ? 'Mengirim…' : 'Kirim Balasan' }}
            </button>
        </form>

        <div v-else class="mt-6 flex items-center justify-center gap-2 rounded-2xl bg-surface-raised border border-line p-4 text-center text-xs text-ink-500">
            <svg class="h-4 w-4 fill-current text-ink-400" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
            <span>Diskusi ini telah dikunci oleh moderator. Balasan baru tidak dapat ditambahkan.</span>
        </div>
    </AppLayout>
</template>
