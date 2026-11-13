<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    tryouts: { type: Array, required: true },
});

const form = useForm({
    title: '',
    description: '',
    duration_minutes: 100,
    price: 0,
    is_published: false,
});

const submit = () => form.post('/admin/tryouts', {
    preserveScroll: true,
    onSuccess: () => form.reset('title', 'description'),
});

const togglePublish = (tryout) => {
    useForm({ is_published: !tryout.is_published }).patch(`/admin/tryouts/${tryout.id}`, { preserveScroll: true, errorBag: 'update' });
};
</script>

<template>
    <AdminLayout>
        <Head title="Paket Tryout" />
        <h1 class="text-2xl font-bold">Paket Tryout</h1>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
                <table class="w-full text-sm">
                    <thead class="text-left text-xs text-ink-400">
                        <tr>
                            <th class="p-3">Judul</th>
                            <th class="p-3">Soal</th>
                            <th class="p-3">Durasi</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="tryout in tryouts" :key="tryout.id" class="h-12 border-t border-line">
                            <td class="p-3 font-semibold">{{ tryout.title }}</td>
                            <td class="p-3 tabular-nums">{{ tryout.question_count }}</td>
                            <td class="p-3 tabular-nums">{{ tryout.duration_minutes }} mnt</td>
                            <td class="p-3">
                                <span :class="['rounded-full px-2 py-1 text-xs font-semibold', tryout.is_published ? 'bg-success/10 text-success' : 'bg-ink-400/10 text-ink-600']">
                                    {{ tryout.is_published ? 'Terbit' : 'Draf' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <Link :href="`/admin/tryouts/${tryout.id}`" class="font-semibold text-brand-600">Atur soal</Link>
                                <button class="ml-3 font-semibold text-ink-600" @click="togglePublish(tryout)">
                                    {{ tryout.is_published ? 'Jadikan draf' : 'Terbitkan' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="tryouts.length === 0">
                            <td colspan="5" class="p-8 text-center text-ink-600">Belum ada paket.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form class="space-y-3 rounded-2xl border border-line bg-white p-6 shadow-card" @submit.prevent="submit">
                <p class="text-sm font-bold">Paket baru</p>
                <input v-model="form.title" placeholder="Judul paket" aria-label="Judul paket" class="h-11 w-full rounded-xl border border-line px-4 focus:border-brand-600 focus:outline-none">
                <p v-if="form.errors.title" class="text-xs text-danger">{{ form.errors.title }}</p>
                <textarea v-model="form.description" rows="3" placeholder="Deskripsi" aria-label="Deskripsi" class="w-full rounded-xl border border-line p-3 focus:border-brand-600 focus:outline-none"></textarea>
                <div class="flex gap-3">
                    <label class="flex-1 text-xs text-ink-600">
                        Durasi (menit)
                        <input v-model.number="form.duration_minutes" type="number" min="1" class="mt-1 h-11 w-full rounded-xl border border-line px-3 tabular-nums focus:border-brand-600 focus:outline-none">
                    </label>
                    <label class="flex-1 text-xs text-ink-600">
                        Harga (Rp)
                        <input v-model.number="form.price" type="number" min="0" class="mt-1 h-11 w-full rounded-xl border border-line px-3 tabular-nums focus:border-brand-600 focus:outline-none">
                    </label>
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.is_published" type="checkbox" class="h-4 w-4 rounded border-line">
                    Langsung terbitkan
                </label>
                <button type="submit" class="h-11 w-full rounded-xl bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="form.processing">
                    Simpan paket
                </button>
            </form>
        </div>
    </AdminLayout>
</template>
