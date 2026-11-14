<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    filters: { type: Object, required: true },
    summary: { type: Object, required: true },
    users: { type: Array, required: true },
    tryouts: { type: Array, required: true },
});

const exportUrl = `/admin/reports/export?from=${props.filters.from}&to=${props.filters.to}`;
</script>

<template>
    <AdminLayout>
        <Head title="Laporan Mitra" />
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Laporan Mitra</h1>
                <p class="mt-1 text-sm text-ink-600">Rekap hasil tryout untuk dibagikan ke mitra bimbel.</p>
            </div>
            <a :href="exportUrl" class="inline-flex h-11 items-center rounded-xl border border-line bg-white px-5 font-semibold hover:bg-brand-50">
                Unduh CSV
            </a>
        </div>

        <form class="mt-5 flex flex-wrap items-end gap-3 rounded-2xl border border-line bg-white p-4 shadow-card" method="get" action="/admin/reports">
            <label class="text-sm">
                <span class="mb-1 block font-medium">Dari</span>
                <input type="date" name="from" :value="filters.from" class="h-10 rounded-xl border border-line px-3">
            </label>
            <label class="text-sm">
                <span class="mb-1 block font-medium">Sampai</span>
                <input type="date" name="to" :value="filters.to" class="h-10 rounded-xl border border-line px-3">
            </label>
            <button type="submit" class="h-10 rounded-xl bg-brand-600 px-4 font-semibold text-white hover:bg-brand-700">Terapkan</button>
        </form>

        <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Peserta</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ summary.participants }}</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Tryout selesai</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ summary.attempts }}</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Rata-rata skor</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ summary.average }}</p>
            </div>
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <p class="text-xs text-ink-400">Lulus passing grade</p>
                <p class="mt-1 text-2xl font-bold tabular-nums">{{ summary.passed }}</p>
            </div>
        </section>

        <section class="mt-6 overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-ink-400">
                    <tr>
                        <th class="p-3">Peserta</th>
                        <th class="p-3">Email</th>
                        <th class="p-3 text-right">Tryout</th>
                        <th class="p-3 text-right">Terbaik</th>
                        <th class="p-3 text-right">Rata-rata</th>
                        <th class="p-3">Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in users" :key="row.email" class="h-12 border-t border-line">
                        <td class="p-3 font-semibold">{{ row.name }}</td>
                        <td class="p-3 text-ink-600">{{ row.email }}</td>
                        <td class="p-3 text-right tabular-nums">{{ row.attempts }}</td>
                        <td class="p-3 text-right tabular-nums">{{ row.best_score }}</td>
                        <td class="p-3 text-right tabular-nums">{{ row.average_score }}</td>
                        <td class="p-3">{{ row.last_submitted_at }}</td>
                    </tr>
                    <tr v-if="users.length === 0">
                        <td colspan="6" class="p-8 text-center text-ink-600">Belum ada hasil pada rentang ini.</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="mt-6 overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-ink-400">
                    <tr>
                        <th class="p-3">Paket tryout</th>
                        <th class="p-3 text-right">Percobaan</th>
                        <th class="p-3 text-right">Rata-rata</th>
                        <th class="p-3 text-right">Lulus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in tryouts" :key="row.title" class="h-12 border-t border-line">
                        <td class="p-3 font-semibold">{{ row.title }}</td>
                        <td class="p-3 text-right tabular-nums">{{ row.attempts }}</td>
                        <td class="p-3 text-right tabular-nums">{{ row.average_score }}</td>
                        <td class="p-3 text-right tabular-nums">{{ row.passed }}</td>
                    </tr>
                    <tr v-if="tryouts.length === 0">
                        <td colspan="4" class="p-8 text-center text-ink-600">Belum ada data paket.</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </AdminLayout>
</template>
