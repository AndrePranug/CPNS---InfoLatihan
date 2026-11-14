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
        <Head title="Voucher" />
        <h1 class="text-2xl font-bold">Voucher</h1>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
                <table class="w-full text-sm">
                    <thead class="text-left text-xs text-ink-400">
                        <tr>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Potongan</th>
                            <th class="p-3">Pemakaian</th>
                            <th class="p-3">Berlaku s.d.</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="voucher in vouchers" :key="voucher.id" class="h-12 border-t border-line">
                            <td class="p-3 font-semibold">{{ voucher.code }}</td>
                            <td class="p-3 tabular-nums">{{ formatValue(voucher) }}</td>
                            <td class="p-3 tabular-nums">
                                {{ voucher.used_count }}{{ voucher.max_uses ? ` / ${voucher.max_uses}` : '' }}
                            </td>
                            <td class="p-3">{{ voucher.expires_at ?? 'Tanpa batas' }}</td>
                            <td class="p-3">
                                <span :class="['rounded-full px-2 py-1 text-xs font-semibold', voucher.is_active ? 'bg-success/10 text-success' : 'bg-ink-400/10 text-ink-600']">
                                    {{ voucher.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <button class="font-semibold text-ink-600" @click="toggle(voucher)">
                                    {{ voucher.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="vouchers.length === 0">
                            <td colspan="6" class="p-8 text-center text-ink-600">Belum ada voucher.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form class="space-y-3 rounded-2xl border border-line bg-white p-6 shadow-card" @submit.prevent="submit">
                <p class="text-sm font-bold">Voucher baru</p>
                <input v-model="form.code" placeholder="Kode, mis. HEMAT20" aria-label="Kode voucher" class="h-11 w-full rounded-xl border border-line px-4 uppercase focus:border-brand-600 focus:outline-none">
                <p v-if="form.errors.code" class="text-xs text-danger">{{ form.errors.code }}</p>
                <div class="flex gap-3">
                    <label class="flex-1 text-xs text-ink-600">
                        Jenis
                        <select v-model="form.type" class="mt-1 h-11 w-full rounded-xl border border-line px-3 focus:border-brand-600 focus:outline-none">
                            <option value="percent">Persen (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                    </label>
                    <label class="flex-1 text-xs text-ink-600">
                        Nilai
                        <input v-model.number="form.value" type="number" min="1" class="mt-1 h-11 w-full rounded-xl border border-line px-3 tabular-nums focus:border-brand-600 focus:outline-none">
                    </label>
                </div>
                <p v-if="form.errors.value" class="text-xs text-danger">{{ form.errors.value }}</p>
                <div class="flex gap-3">
                    <label class="flex-1 text-xs text-ink-600">
                        Maks. pemakaian
                        <input v-model.number="form.max_uses" type="number" min="1" placeholder="Tanpa batas" class="mt-1 h-11 w-full rounded-xl border border-line px-3 tabular-nums focus:border-brand-600 focus:outline-none">
                    </label>
                    <label class="flex-1 text-xs text-ink-600">
                        Kedaluwarsa
                        <input v-model="form.expires_at" type="date" class="mt-1 h-11 w-full rounded-xl border border-line px-3 focus:border-brand-600 focus:outline-none">
                    </label>
                </div>
                <p v-if="form.errors.max_uses" class="text-xs text-danger">{{ form.errors.max_uses }}</p>
                <p v-if="form.errors.expires_at" class="text-xs text-danger">{{ form.errors.expires_at }}</p>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-line">
                    Langsung aktif
                </label>
                <button type="submit" class="h-11 w-full rounded-xl bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="form.processing">
                    Simpan voucher
                </button>
            </form>
        </div>
    </AdminLayout>
</template>
