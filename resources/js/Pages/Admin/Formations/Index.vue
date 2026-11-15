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

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Formasi Jabatan SKB</h1>
            <p class="mt-1 text-sm text-ink-600">Formasi digunakan untuk pengelompokan bank soal dan latihan mandiri SKB per keahlian.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <!-- List Formations (Left) -->
            <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                            <tr>
                                <th class="py-3.5 px-4">Nama Formasi</th>
                                <th class="py-3.5 px-4 w-32">Total Soal</th>
                                <th class="py-3.5 px-4 w-28">Status</th>
                                <th class="py-3.5 px-4 text-right w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            <tr v-for="formation in formations" :key="formation.id" class="transition-colors hover:bg-surface-raised">
                                <td class="py-3.5 px-4 font-bold text-ink-900">{{ formation.name }}</td>
                                <td class="py-3.5 px-4 font-semibold tabular-nums text-ink-900">{{ formation.question_count }} Butir Soal</td>
                                <td class="py-3.5 px-4">
                                    <span :class="['rounded-full px-2 py-0.5 text-[10px] font-bold', formation.is_active ? 'bg-success/10 text-success' : 'bg-slate-100 text-slate-600']">
                                        {{ formation.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button
                                        :class="['rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors', formation.is_active ? 'text-danger hover:bg-danger/10' : 'text-success hover:bg-success/10']"
                                        @click="toggle(formation)"
                                    >
                                        {{ formation.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="formations.length === 0">
                                <td colspan="4" class="p-8 text-center text-xs text-ink-400">
                                    Belum ada formasi SKB terdaftar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Form (Right) -->
            <aside class="self-start">
                <form class="rounded-2xl border border-line bg-surface p-5 shadow-card space-y-4" @submit.prevent="submit">
                    <div>
                        <h2 class="text-sm font-bold text-ink-900">Tambah Formasi Baru</h2>
                        <p class="text-xs text-ink-400">Contoh: Guru Ahli Pertama, Pranata Komputer, Auditor</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink-900 mb-1" for="f-name">Nama Formasi</label>
                        <input
                            id="f-name"
                            v-model="form.name"
                            placeholder="Contoh: Tenaga Medis / Dokter"
                            class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
                        >
                        <p v-if="form.errors.name" class="mt-1 text-xs text-danger">{{ form.errors.name }}</p>
                    </div>

                    <button
                        type="submit"
                        class="w-full h-10 rounded-xl bg-brand-600 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Formasi' }}
                    </button>
                </form>
            </aside>
        </div>
    </AdminLayout>
</template>
