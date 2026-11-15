<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    tryouts: { type: Array, required: true },
});

const rupiah = (val) => Number(val) === 0 ? 'Gratis' : `Rp${Number(val).toLocaleString('id-ID')}`;
</script>

<template>
    <AppLayout>
        <Head title="Paket Tryout" />

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Paket Tryout SKD</h1>
                <p class="mt-1 text-sm text-ink-600">Pilih paket simulasi CAT BKN untuk menguji batas kemampuanmu.</p>
            </div>
            <div class="inline-flex items-center gap-2 rounded-xl bg-surface border border-line px-3 py-1.5 text-xs font-semibold text-ink-600">
                <span class="h-2 w-2 rounded-full bg-brand-600"></span>
                <span>{{ tryouts.length }} Paket Tersedia</span>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="tryouts.length === 0" class="mt-8 rounded-2xl border border-line bg-surface p-12 text-center shadow-card">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-base font-bold text-ink-900">Belum Ada Paket Tryout</h2>
            <p class="mt-1 text-sm text-ink-600">Paket soal baru sedang dipersiapkan oleh tim pembuat soal.</p>
            <div class="mt-5">
                <Link href="/practice" class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors">
                    Latihan Soal Mandiri
                </Link>
            </div>
        </div>

        <!-- Tryouts Grid -->
        <div v-else class="mt-6 grid gap-5 sm:grid-cols-2">
            <article
                v-for="tryout in tryouts"
                :key="tryout.id"
                class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-line bg-surface p-6 shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover"
            >
                <div>
                    <!-- Header with title & price badge -->
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-lg font-bold text-ink-900 group-hover:text-brand-600 transition-colors">
                            {{ tryout.title }}
                        </h2>
                        <span
                            :class="[
                                'inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-bold',
                                tryout.price === 0 ? 'bg-success/10 text-success' : 'bg-brand-50 text-brand-600'
                            ]"
                        >
                            {{ rupiah(tryout.price) }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm text-ink-600 leading-relaxed line-clamp-2">
                        {{ tryout.description || 'Simulasi ujian lengkap SKD CPNS mencakup TWK, TIU, dan TKP sesuai kisi-kisi BKN.' }}
                    </p>

                    <!-- Meta specs -->
                    <div class="mt-5 grid grid-cols-2 gap-3 rounded-xl bg-surface-raised p-3 text-xs">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-ink-600">Jumlah: <strong class="text-ink-900">{{ tryout.question_count }} Soal</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-ink-600">Durasi: <strong class="text-ink-900">{{ tryout.duration_minutes }} Menit</strong></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 border-t border-line/60 pt-4 flex items-center justify-between">
                    <span class="text-xs text-ink-400">Timer otomatis &amp; kunci jawaban</span>
                    <Link
                        :href="`/tryouts/${tryout.id}`"
                        class="inline-flex h-9 items-center justify-center rounded-xl bg-brand-600 px-4 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all"
                    >
                        Lihat Detail
                    </Link>
                </div>
            </article>
        </div>
    </AppLayout>
</template>
