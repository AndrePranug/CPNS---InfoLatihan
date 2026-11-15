<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    vouchers: { type: Array, required: true },
});

const form = useForm({
    code: '',
    type: 'percent',
    value: 10,
    max_uses: null,
    expires_at: '',
    is_active: true,
});

const submit = () => form.post('/admin/vouchers', {
    preserveScroll: true,
    onSuccess: () => form.reset('code', 'value', 'max_uses', 'expires_at'),
});

const toggle = (voucher) => {
    useForm({}).patch(`/admin/vouchers/${voucher.id}`, { preserveScroll: true });
};

const formatValue = (voucher) => (voucher.type === 'percent'
    ? `${voucher.value}%`
    : `Rp${Number(voucher.value).toLocaleString('id-ID')}`);
</script>

<template>
    <AdminLayout>
        <Head title="Voucher & Promo" />

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Voucher &amp; Kupon Diskon</h1>
            <p class="mt-1 text-sm text-ink-600">Kelola kode promosi paket tryout untuk peserta ujian.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <!-- Table of Vouchers (Left) -->
            <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                            <tr>
                                <th class="py-3.5 px-4">Kode Kupon</th>
                                <th class="py-3.5 px-4">Potongan Nilai</th>
                                <th class="py-3.5 px-4">Penggunaan</th>
                                <th class="py-3.5 px-4">Masa Berlaku</th>
                                <th class="py-3.5 px-4 w-24">Status</th>
                                <th class="py-3.5 px-4 text-right w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            <tr v-for="voucher in vouchers" :key="voucher.id" class="transition-colors hover:bg-surface-raised">
                                <td class="py-3.5 px-4 font-mono font-bold text-ink-900 tracking-wider uppercase">{{ voucher.code }}</td>
                                <td class="py-3.5 px-4 font-extrabold text-brand-600 tabular-nums">{{ formatValue(voucher) }}</td>
                                <td class="py-3.5 px-4 tabular-nums text-ink-600">
                                    {{ voucher.used_count }}{{ voucher.max_uses ? ` / ${voucher.max_uses}` : ' kali' }}
                                </td>
                                <td class="py-3.5 px-4 text-ink-600">{{ voucher.expires_at ?? 'Tanpa batas waktu' }}</td>
                                <td class="py-3.5 px-4">
                                    <span :class="['rounded-full px-2 py-0.5 text-[10px] font-bold', voucher.is_active ? 'bg-success/10 text-success' : 'bg-slate-100 text-slate-600']">
                                        {{ voucher.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button
                                        :class="['rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors', voucher.is_active ? 'text-danger hover:bg-danger/10' : 'text-success hover:bg-success/10']"
                                        @click="toggle(voucher)"
                                    >
                                        {{ voucher.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="vouchers.length === 0">
                                <td colspan="6" class="p-8 text-center text-xs text-ink-400">
                                    Belum ada voucher aktif. Tambahkan melalui formulir di samping.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Voucher Form (Right) -->
            <aside class="self-start">
                <form class="rounded-2xl border border-line bg-surface p-5 shadow-card space-y-4" @submit.prevent="submit">
                    <div>
                        <h2 class="text-sm font-bold text-ink-900">Buat Voucher Baru</h2>
                        <p class="text-xs text-ink-400">Tentukan kode unik dan besaran potongan</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink-900 mb-1" for="v-code">Kode Voucher</label>
                        <input
                            id="v-code"
                            v-model="form.code"
                            placeholder="Contoh: CPNS2026"
                            class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-mono font-bold uppercase tracking-wider text-ink-900 placeholder:font-sans placeholder:normal-case placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
                        >
                        <p v-if="form.errors.code" class="mt-1 text-xs text-danger">{{ form.errors.code }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1">Tipe Diskon</label>
                            <select v-model="form.type" class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 focus:border-brand-600 focus:outline-none">
                                <option value="percent">Persen (%)</option>
                                <option value="fixed">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1">Nilai Potongan</label>
                            <input
                                v-model.number="form.value"
                                type="number"
                                min="1"
                                class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 focus:border-brand-600 focus:outline-none"
                            >
                        </div>
                    </div>
                    <p v-if="form.errors.value" class="text-xs text-danger">{{ form.errors.value }}</p>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1">Batas Pakai</label>
                            <input
                                v-model.number="form.max_uses"
                                type="number"
                                min="1"
                                placeholder="Kosong = &infin;"
                                class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 focus:border-brand-600 focus:outline-none"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1">Kedaluwarsa</label>
                            <input
                                v-model="form.expires_at"
                                type="date"
                                class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 focus:border-brand-600 focus:outline-none"
                            >
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-xs font-medium text-ink-700 cursor-pointer pt-1">
                        <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-line text-brand-600 focus:ring-brand-600/30">
                        <span>Langsung aktifkan kupon</span>
                    </label>

                    <button
                        type="submit"
                        class="w-full h-10 rounded-xl bg-brand-600 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Voucher' }}
                    </button>
                </form>
            </aside>
        </div>
    </AdminLayout>
</template>
