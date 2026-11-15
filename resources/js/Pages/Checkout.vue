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

        <div class="mb-4">
            <Link :href="`/tryouts/${tryout.id}`" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline">
                &larr; Kembali ke Detail Tryout
            </Link>
        </div>

        <div class="max-w-2xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Konfirmasi Pemesanan</h1>
                <p class="mt-1 text-sm text-ink-600">Periksa detail paket tryout sebelum menyelesaikan transaksi.</p>
            </div>

            <article class="rounded-2xl border border-line bg-surface p-6 sm:p-8 shadow-card space-y-6">
                <!-- Package Summary Card -->
                <div class="flex items-start justify-between border-b border-line/60 pb-5">
                    <div>
                        <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-bold text-brand-600">
                            Paket Tryout Premium
                        </span>
                        <h2 class="mt-2 text-xl font-bold text-ink-900">{{ tryout.title }}</h2>
                        <p class="text-xs text-ink-600 mt-1">
                            {{ tryout.question_count }} butir soal standar CAT BKN · Durasi pengerjaan {{ tryout.duration_minutes }} menit
                        </p>
                    </div>
                    <p class="text-lg font-extrabold text-ink-900 tabular-nums">
                        {{ rupiah(tryout.price) }}
                    </p>
                </div>

                <!-- Existing Order Awaiting Payment -->
                <div v-if="order" class="rounded-2xl border border-amber-200 bg-amber-50/60 p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800">
                            <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Pesanan #{{ order.id }} Menunggu Pembayaran
                        </span>
                        <span class="text-xs font-semibold text-amber-700">Pending</span>
                    </div>

                    <dl class="space-y-2 text-xs border-t border-amber-200/80 pt-3">
                        <div class="flex justify-between">
                            <dt class="text-ink-600">Harga Paket:</dt>
                            <dd class="font-semibold text-ink-900 tabular-nums">{{ rupiah(order.amount) }}</dd>
                        </div>
                        <div v-if="order.discount > 0" class="flex justify-between text-success">
                            <dt>Diskon Voucher{{ order.voucher ? ` (${order.voucher})` : '' }}:</dt>
                            <dd class="font-bold tabular-nums">-{{ rupiah(order.discount) }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-amber-200/80 pt-2 text-sm font-bold text-ink-900">
                            <dt>Total Pembayaran:</dt>
                            <dd class="text-brand-600 tabular-nums">{{ rupiah(order.total) }}</dd>
                        </div>
                    </dl>

                    <div class="rounded-xl bg-surface p-3 text-[11px] text-ink-600 border border-amber-200">
                        Pembayaran online otomatis diproses melalui gateway {{ gateway }} begitu pembayaran diaktifkan oleh admin.
                    </div>
                </div>

                <!-- New Order Form -->
                <div v-else>
                    <div v-if="!available" class="rounded-2xl border border-line bg-surface-raised p-5 text-center text-xs text-ink-600 space-y-2">
                        <p class="font-semibold text-ink-900">Gateway Pembayaran Belum Aktif</p>
                        <p>Fitur pembayaran paket sedang dinonaktifkan sementara. Silakan nikmati paket gratis yang tersedia.</p>
                    </div>

                    <form v-else class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1.5" for="voucher">
                                Punya Kode Voucher? (Opsional)
                            </label>
                            <div class="flex gap-2">
                                <input
                                    id="voucher"
                                    v-model="form.voucher"
                                    placeholder="MASUKKAN KODE"
                                    class="h-11 flex-1 rounded-xl border border-line bg-surface px-4 text-xs font-semibold uppercase tracking-wider text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
                                    autocomplete="off"
                                >
                            </div>
                            <p v-if="form.errors.voucher" class="mt-1 text-xs text-danger font-medium">{{ form.errors.voucher }}</p>
                            <p v-if="form.errors.payment" class="mt-1 text-xs text-danger font-medium">{{ form.errors.payment }}</p>
                        </div>

                        <div class="pt-3">
                            <button
                                type="submit"
                                class="w-full h-11 inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Memproses Pesanan…' : 'Lanjutkan ke Pembayaran' }}
                            </button>
                        </div>
                    </form>
                </div>
            </article>
        </div>
    </AppLayout>
</template>
