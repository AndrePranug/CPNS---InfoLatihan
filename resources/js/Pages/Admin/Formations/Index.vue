<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    formations: { type: Array, required: true },
});

const form = useForm({ name: '' });

const submit = () => form.post('/admin/formations', {
    preserveScroll: true,
    onSuccess: () => form.reset('name'),
});

const toggle = (formation) => {
    useForm({}).patch(`/admin/formations/${formation.id}`, { preserveScroll: true });
};
</script>

<template>
    <AdminLayout>
        <Head title="Formasi SKB" />
        <h1 class="text-2xl font-bold">Formasi SKB</h1>
        <p class="mt-1 text-sm text-ink-600">Formasi dipakai untuk mengelompokkan soal latihan SKB.</p>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
                <table class="w-full text-sm">
                    <thead class="text-left text-xs text-ink-400">
                        <tr>
                            <th class="p-3">Formasi</th>
                            <th class="p-3">Soal</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="formation in formations" :key="formation.id" class="h-12 border-t border-line">
                            <td class="p-3 font-semibold">{{ formation.name }}</td>
                            <td class="p-3 tabular-nums">{{ formation.question_count }}</td>
                            <td class="p-3">
                                <span :class="['rounded-full px-2 py-1 text-xs font-semibold', formation.is_active ? 'bg-success/10 text-success' : 'bg-ink-400/10 text-ink-600']">
                                    {{ formation.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <button class="font-semibold text-ink-600" @click="toggle(formation)">
                                    {{ formation.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="formations.length === 0">
                            <td colspan="4" class="p-8 text-center text-ink-600">Belum ada formasi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form class="space-y-3 rounded-2xl border border-line bg-white p-6 shadow-card" @submit.prevent="submit">
                <p class="text-sm font-bold">Formasi baru</p>
                <input v-model="form.name" placeholder="Nama formasi, mis. Guru" aria-label="Nama formasi" class="h-11 w-full rounded-xl border border-line px-4 focus:border-brand-600 focus:outline-none">
                <p v-if="form.errors.name" class="text-xs text-danger">{{ form.errors.name }}</p>
                <button type="submit" class="h-11 w-full rounded-xl bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="form.processing">
                    Simpan formasi
                </button>
            </form>
        </div>
    </AdminLayout>
</template>
