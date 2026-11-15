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

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Laporan Rekapitulasi Mitra</h1>
                <p class="mt-1 text-sm text-ink-600">Ekspor nilai peserta dan analisis hasil tryout untuk mitra bimbingan belajar.</p>
            </div>
            <a
                :href="exportUrl"
                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z"/>
                    <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z"/>
                </svg>
                Unduh Data CSV
            </a>
        </div>

        <!-- Date Range Filter -->
        <form class="flex flex-wrap items-end gap-3 rounded-2xl border border-line bg-surface p-4 shadow-card" method="get" action="/admin/reports">
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-ink-600">Rentang Awal</label>
                <input type="date" name="from" :value="filters.from" class="h-10 rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 outline-none focus:border-brand-600">
            </div>
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-ink-600">Rentang Akhir</label>
                <input type="date" name="to" :value="filters.to" class="h-10 rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 outline-none focus:border-brand-600">
            </div>
            <button type="submit" class="h-10 rounded-xl bg-brand-600 px-4 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 transition-colors">
                Terapkan Rentang
            </button>
        </form>

        <!-- KPI 4 Cards -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Total Peserta Ujian</p>
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-ink-900">{{ summary.participants }}</p>
                <p class="mt-1 text-[11px] text-ink-400">Peserta unik terdaftar</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Total Sesi Selesai</p>
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-brand-600">{{ summary.attempts }}</p>
                <p class="mt-1 text-[11px] text-ink-400">Percobaan terkirim</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Rata-rata Skor Total</p>
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-ink-900">{{ summary.average }}</p>
                <p class="mt-1 text-[11px] text-ink-400">Seluruh tryout pada rentang</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-card">
                <p class="text-xs font-semibold text-ink-400">Lulus Passing Grade</p>
                <p class="mt-2 text-3xl font-extrabold tabular-nums text-success">{{ summary.passed }}</p>
                <p class="mt-1 text-[11px] text-ink-400">Memenuhi ambang batas</p>
            </div>
        </section>

        <!-- Recap by Participant -->
        <section class="overflow-hidden rounded-2xl border border-line bg-surface shadow-card space-y-3 p-5">
            <div class="border-b border-line/60 pb-3">
                <h2 class="text-base font-bold text-ink-900">Rekapitulasi per Peserta</h2>
                <p class="text-xs text-ink-400">Performa akumulatif masing-masing siswa bimbel</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                        <tr>
                            <th class="py-3 px-3">Nama Peserta</th>
                            <th class="py-3 px-3">Email</th>
                            <th class="py-3 px-3 text-right">Tryout Diikuti</th>
                            <th class="py-3 px-3 text-right">Skor Terbaik</th>
                            <th class="py-3 px-3 text-right">Rata-rata</th>
                            <th class="py-3 px-3">Ujian Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        <tr v-for="row in users" :key="row.email" class="transition-colors hover:bg-surface-raised">
                            <td class="py-3 px-3 font-bold text-ink-900">{{ row.name }}</td>
                            <td class="py-3 px-3 text-ink-600">{{ row.email }}</td>
                            <td class="py-3 px-3 text-right font-semibold tabular-nums text-ink-900">{{ row.attempts }}x</td>
                            <td class="py-3 px-3 text-right font-bold tabular-nums text-brand-600">{{ row.best_score }}</td>
                            <td class="py-3 px-3 text-right tabular-nums text-ink-900">{{ row.average_score }}</td>
                            <td class="py-3 px-3 text-ink-400">{{ row.last_submitted_at }}</td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="6" class="p-8 text-center text-xs text-ink-400">Belum ada data pengerjaan pada rentang tanggal ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Recap by Tryout Package -->
        <section class="overflow-hidden rounded-2xl border border-line bg-surface shadow-card space-y-3 p-5">
            <div class="border-b border-line/60 pb-3">
                <h2 class="text-base font-bold text-ink-900">Rekapitulasi per Paket Ujian</h2>
                <p class="text-xs text-ink-400">Tingkat kelulusan dan rata-rata skor per paket tryout</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                        <tr>
                            <th class="py-3 px-3">Judul Paket</th>
                            <th class="py-3 px-3 text-right">Jumlah Percobaan</th>
                            <th class="py-3 px-3 text-right">Rata-rata Skor</th>
                            <th class="py-3 px-3 text-right">Peserta Lulus PG</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        <tr v-for="row in tryouts" :key="row.title" class="transition-colors hover:bg-surface-raised">
                            <td class="py-3 px-3 font-bold text-ink-900">{{ row.title }}</td>
                            <td class="py-3 px-3 text-right font-semibold tabular-nums text-ink-900">{{ row.attempts }}x</td>
                            <td class="py-3 px-3 text-right tabular-nums text-ink-900">{{ row.average_score }}</td>
                            <td class="py-3 px-3 text-right font-bold tabular-nums text-success">{{ row.passed }} Orang</td>
                        </tr>
                        <tr v-if="tryouts.length === 0">
                            <td colspan="4" class="p-8 text-center text-xs text-ink-400">Belum ada paket yang dikerjakan pada rentang ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>
