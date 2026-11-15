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

const rupiah = (val) => Number(val) === 0 ? 'Gratis' : `Rp${Number(val).toLocaleString('id-ID')}`;
</script>

<template>
    <AdminLayout>
        <Head title="Paket Tryout" />

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Kelola Paket Tryout</h1>
            <p class="mt-1 text-sm text-ink-600">Buat paket simulasi, tentukan durasi pengerjaan, dan atur status publikasi.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <!-- List of Tryouts (Left) -->
            <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-card">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                            <tr>
                                <th class="py-3.5 px-4">Nama Paket</th>
                                <th class="py-3.5 px-4 w-20">Soal</th>
                                <th class="py-3.5 px-4 w-24">Durasi</th>
                                <th class="py-3.5 px-4 w-24">Harga</th>
                                <th class="py-3.5 px-4 w-24">Status</th>
                                <th class="py-3.5 px-4 text-right w-44">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line/60">
                            <tr v-for="tryout in tryouts" :key="tryout.id" class="transition-colors hover:bg-surface-raised">
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-ink-900 leading-snug">{{ tryout.title }}</p>
                                    <p v-if="tryout.description" class="text-[11px] text-ink-400 truncate max-w-xs mt-0.5">{{ tryout.description }}</p>
                                </td>
                                <td class="py-3.5 px-4 font-semibold tabular-nums text-ink-900">{{ tryout.question_count }} Soal</td>
                                <td class="py-3.5 px-4 tabular-nums text-ink-600">{{ tryout.duration_minutes }} mnt</td>
                                <td class="py-3.5 px-4 font-semibold tabular-nums">{{ rupiah(tryout.price) }}</td>
                                <td class="py-3.5 px-4">
                                    <span :class="['rounded-full px-2 py-0.5 text-[10px] font-bold', tryout.is_published ? 'bg-success/10 text-success' : 'bg-slate-100 text-slate-600']">
                                        {{ tryout.is_published ? 'Terbit' : 'Draf' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <Link :href="`/admin/tryouts/${tryout.id}`" class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-600 hover:bg-brand-100 transition-colors">
                                            Atur Soal
                                        </Link>
                                        <button
                                            :class="['rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors', tryout.is_published ? 'text-amber-600 hover:bg-amber-50' : 'text-success hover:bg-success/10']"
                                            @click="togglePublish(tryout)"
                                        >
                                            {{ tryout.is_published ? 'Jadikan Draf' : 'Terbitkan' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="tryouts.length === 0">
                                <td colspan="6" class="p-8 text-center text-xs text-ink-400">
                                    Belum ada paket tryout. Buat paket pertama melalui formulir di samping.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Tryout Form (Right) -->
            <aside class="self-start">
                <form class="rounded-2xl border border-line bg-surface p-5 shadow-card space-y-4" @submit.prevent="submit">
                    <div>
                        <h2 class="text-sm font-bold text-ink-900">Buat Paket Tryout Baru</h2>
                        <p class="text-xs text-ink-400">Konfigurasi informasi umum paket</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink-900 mb-1" for="title">Judul Paket</label>
                        <input
                            id="title"
                            v-model="form.title"
                            placeholder="Contoh: Tryout Akbar SKD Nasional #1"
                            class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
                        >
                        <p v-if="form.errors.title" class="mt-1 text-xs text-danger">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink-900 mb-1" for="description">Deskripsi Singkat</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            placeholder="Keterangan singkat paket ujian…"
                            class="w-full rounded-xl border border-line bg-surface p-3 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1" for="duration">Durasi (Menit)</label>
                            <input
                                id="duration"
                                v-model.number="form.duration_minutes"
                                type="number"
                                class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 focus:border-brand-600 focus:outline-none"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-ink-900 mb-1" for="price">Harga (Rp)</label>
                            <input
                                id="price"
                                v-model.number="form.price"
                                type="number"
                                placeholder="0 = Gratis"
                                class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs text-ink-900 focus:border-brand-600 focus:outline-none"
                            >
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-xs font-medium text-ink-700 cursor-pointer pt-1">
                        <input v-model="form.is_published" type="checkbox" class="h-4 w-4 rounded border-line text-brand-600 focus:ring-brand-600/30">
                        <span>Langsung terbitkan paket</span>
                    </label>

                    <button
                        type="submit"
                        class="w-full h-10 rounded-xl bg-brand-600 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan…' : 'Simpan Paket' }}
                    </button>
                </form>
            </aside>
        </div>
    </AdminLayout>
</template>
