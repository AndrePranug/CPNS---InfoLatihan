<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    tryout: { type: Object, required: true },
});

const form = useForm({});

const start = () => form.post(`/tryouts/${props.tryout.id}/start`);

const rupiah = (value) => `Rp${Number(value).toLocaleString('id-ID')}`;
</script>

<template>
    <AppLayout>
        <Head :title="tryout.title" />
        <article class="rounded-2xl border border-line bg-white p-8 shadow-card">
            <h1 class="text-3xl font-bold">{{ tryout.title }}</h1>
            <p class="mt-2 text-[15px] text-ink-600">{{ tryout.description }}</p>
            <p class="mt-4 text-sm font-medium text-ink-600">
                {{ tryout.question_count }} soal · {{ tryout.duration_minutes }} menit · {{ tryout.price === 0 ? 'Gratis' : rupiah(tryout.price) }}
            </p>
            <p class="mt-2 text-xs text-ink-400">
                Timer berjalan di server. Jawaban tersimpan otomatis setiap kali dipilih.
            </p>

            <button
                v-if="tryout.price === 0 || tryout.has_access"
                class="mt-6 inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                :disabled="form.processing"
                @click="start"
            >
                Mulai Tryout
            </button>
            <Link
                v-else-if="tryout.payments_available"
                :href="`/tryouts/${tryout.id}/checkout`"
                class="mt-6 inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700"
            >
                Beli &amp; Mulai
            </Link>
            <p v-else class="mt-6 rounded-xl bg-brand-50 px-4 py-3 text-sm text-ink-600">
                Paket ini berbayar. Pembayaran sedang dinonaktifkan, coba lagi nanti.
            </p>

            <p v-if="form.errors.attempt" class="mt-3 text-sm text-danger">{{ form.errors.attempt }}</p>
        </article>
    </AppLayout>
</template>
