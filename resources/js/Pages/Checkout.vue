<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    tryout: { type: Object, required: true },
    order: { type: Object, default: null },
    available: { type: Boolean, required: true },
    gateway: { type: String, required: true },
});

const form = useForm({ voucher: '' });

const submit = () => form.post(`/tryouts/${props.tryout.id}/checkout`);

const rupiah = (value) => `Rp${Number(value).toLocaleString('id-ID')}`;
</script>

<template>
    <AppLayout>
        <Head :title="`Checkout · ${tryout.title}`" />
        <h1 class="text-3xl font-bold">Checkout</h1>

        <article class="mt-6 rounded-2xl border border-line bg-white p-8 shadow-card">
            <h2 class="text-xl font-bold">{{ tryout.title }}</h2>
            <p class="mt-1 text-sm text-ink-600">{{ tryout.question_count }} soal · {{ tryout.duration_minutes }} menit</p>
            <p class="mt-3 text-sm font-semibold">Harga: {{ rupiah(tryout.price) }}</p>

            <div v-if="order" class="mt-6 rounded-xl bg-brand-50 p-5">
                <p class="text-sm font-bold">Pesanan #{{ order.id }} menunggu pembayaran</p>
                <dl class="mt-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-ink-600">Harga</dt>
                        <dd class="tabular-nums">{{ rupiah(order.amount) }}</dd>
                    </div>
                    <div v-if="order.discount > 0" class="flex justify-between">
                        <dt class="text-ink-600">Diskon{{ order.voucher ? ` (${order.voucher})` : '' }}</dt>
                        <dd class="tabular-nums text-success">-{{ rupiah(order.discount) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-line pt-2 font-bold">
                        <dt>Total</dt>
                        <dd class="tabular-nums">{{ rupiah(order.total) }}</dd>
                    </div>
                </dl>
                <p class="mt-3 text-xs text-ink-400">
                    Pembayaran online diproses lewat gateway {{ gateway }} setelah integrasi aktif.
                </p>
            </div>

            <div v-else>
                <p v-if="!available" class="mt-6 rounded-xl bg-brand-50 px-4 py-3 text-sm text-ink-600">
                    Pembayaran belum tersedia. Paket berbayar akan dibuka setelah gateway pembayaran aktif.
                </p>

                <form v-else class="mt-6 space-y-3" @submit.prevent="submit">
                    <label class="block text-sm font-medium" for="voucher">Kode voucher (opsional)</label>
                    <input
                        id="voucher"
                        v-model="form.voucher"
                        class="h-11 w-full max-w-xs rounded-xl border border-line px-4 uppercase focus:border-brand-600 focus:outline-none"
                        autocomplete="off"
                    >
                    <p v-if="form.errors.voucher" class="text-sm text-danger">{{ form.errors.voucher }}</p>
                    <p v-if="form.errors.payment" class="text-sm text-danger">{{ form.errors.payment }}</p>
                    <button
                        type="submit"
                        class="inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        Buat Pesanan
                    </button>
                </form>
            </div>

            <Link :href="`/tryouts/${tryout.id}`" class="mt-6 inline-block text-sm font-semibold text-brand-600">
                Kembali ke detail tryout
            </Link>
        </article>
    </AppLayout>
</template>
