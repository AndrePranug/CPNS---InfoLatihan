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
        <Link href="/forum" class="text-sm font-semibold text-brand-600">← Forum</Link>

        <article class="mt-3 rounded-2xl border border-line bg-white p-6 shadow-card">
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span v-if="thread.is_pinned" class="rounded-full bg-brand-100 px-2 py-0.5 font-semibold text-brand-600">Disematkan</span>
                <span v-if="thread.is_locked" class="rounded-full bg-ink-400/10 px-2 py-0.5 font-semibold text-ink-600">Dikunci</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold">{{ thread.title }}</h1>
            <p class="mt-1 text-xs text-ink-400">{{ thread.author }} · {{ thread.created_at }}</p>
            <p class="mt-4 whitespace-pre-line break-words text-[15px] leading-relaxed text-ink-600">{{ thread.body }}</p>

            <div class="mt-5 flex flex-wrap gap-3 border-t border-line pt-4 text-sm">
                <button v-if="canDeleteThread" class="font-semibold text-danger" @click="destroyThread">Hapus diskusi</button>
                <template v-if="isAdmin">
                    <button class="font-semibold text-ink-600" @click="toggleLock">{{ thread.is_locked ? 'Buka diskusi' : 'Kunci diskusi' }}</button>
                    <button class="font-semibold text-ink-600" @click="togglePin">{{ thread.is_pinned ? 'Lepas sematan' : 'Sematkan' }}</button>
                </template>
            </div>
        </article>

        <section class="mt-6 space-y-3">
            <h2 class="text-lg font-bold">{{ thread.replies_count }} balasan</h2>

            <p v-if="replies.data.length === 0" class="rounded-2xl border border-line bg-white p-6 text-sm text-ink-600">
                Belum ada balasan.
            </p>

            <article v-for="reply in replies.data" :key="reply.id" class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <p class="text-sm font-semibold">{{ reply.author }}</p>
                    <p class="text-xs text-ink-400">{{ reply.created_at }}</p>
                </div>
                <p class="mt-2 whitespace-pre-line break-words text-[15px] text-ink-600">{{ reply.body }}</p>
                <button v-if="canDeleteReply(reply)" class="mt-3 text-xs font-semibold text-danger" @click="destroyReply(reply.id)">Hapus</button>
            </article>

            <nav v-if="replies.links.length > 3" class="flex flex-wrap gap-1" aria-label="Navigasi balasan">
                <Link
                    v-for="link in replies.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    preserve-scroll
                    :class="['rounded-lg px-3 py-2 text-sm', link.active ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-brand-50', !link.url && 'pointer-events-none opacity-40']"
                >
                    {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
                </Link>
            </nav>
        </section>

        <form v-if="!thread.is_locked" class="mt-6 rounded-2xl border border-line bg-white p-6 shadow-card" @submit.prevent="submitReply">
            <label for="reply" class="text-sm font-bold">Tulis balasan</label>
            <textarea id="reply" v-model="replyForm.body" rows="4" class="mt-2 w-full rounded-xl border border-line p-3 focus:border-brand-600 focus:outline-none"></textarea>
            <p v-if="replyForm.errors.body" class="mt-1 text-xs text-danger">{{ replyForm.errors.body }}</p>
            <button type="submit" class="mt-3 h-11 rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="replyForm.processing">
                Kirim balasan
            </button>
        </form>

        <p v-else class="mt-6 rounded-2xl bg-brand-50 px-4 py-3 text-sm text-ink-600">
            Diskusi dikunci. Balasan baru tidak bisa ditambahkan.
        </p>
    </AppLayout>
</template>
