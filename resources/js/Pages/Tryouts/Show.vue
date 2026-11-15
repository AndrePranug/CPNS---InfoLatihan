<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    tryout: { type: Object, required: true },
});

const form = useForm({});

const start = () => form.post(`/tryouts/${props.tryout.id}/start`);

const rupiah = (value) => `Rp${Number(value).toLocaleString('id-ID')}`;
</script>

<template>
    <AppLayout>
        <Head :title="tryout.title" />

        <div class="mb-4">
            <Link href="/tryouts" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline">
                &larr; Kembali ke Daftar Tryout
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <!-- Main Content Area -->
            <article class="rounded-2xl border border-line bg-surface p-6 sm:p-8 shadow-card space-y-6">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            :class="[
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold',
                                tryout.price === 0 ? 'bg-success/10 text-success' : 'bg-brand-50 text-brand-600'
                            ]"
                        >
                            {{ tryout.price === 0 ? 'Gratis' : rupiah(tryout.price) }}
                        </span>
                        <span class="rounded-full bg-surface-raised px-2.5 py-0.5 text-xs font-semibold text-ink-600">
                            Format CAT BKN
                        </span>
                    </div>

                    <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink-900 sm:text-3xl">
                        {{ tryout.title }}
                    </h1>
                    <p class="mt-2.5 text-sm leading-relaxed text-ink-600">
                        {{ tryout.description || 'Simulasi ujian seleksi kompetensi dasar dirancang persis dengan komposisi dan pembobotan nilai resmi BKN.' }}
                    </p>
                </div>

                <!-- Specs Grid -->
                <div class="grid grid-cols-3 gap-3 rounded-2xl bg-surface-raised p-4 text-center">
                    <div>
                        <p class="text-xs text-ink-400">Total Soal</p>
                        <p class="mt-1 text-xl font-bold text-ink-900 tabular-nums">{{ tryout.question_count }}</p>
                        <p class="text-[11px] text-ink-600 mt-0.5">Butir Soal</p>
                    </div>
                    <div class="border-x border-line">
                        <p class="text-xs text-ink-400">Durasi Pengerjaan</p>
                        <p class="mt-1 text-xl font-bold text-ink-900 tabular-nums">{{ tryout.duration_minutes }}</p>
                        <p class="text-[11px] text-ink-600 mt-0.5">Menit</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-400">Passing Grade</p>
                        <p class="mt-1 text-xl font-bold text-success">Aktif</p>
                        <p class="text-[11px] text-ink-600 mt-0.5">Kepmenpan RB</p>
                    </div>
                </div>

                <!-- Guidelines / Rules -->
                <div class="space-y-3 border-t border-line/60 pt-6">
                    <h2 class="text-base font-bold text-ink-900">Ketentuan &amp; Tata Tertib Ujian</h2>
                    <ul class="space-y-2 text-xs text-ink-600 leading-relaxed">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-brand-50 text-[10px] font-bold text-brand-600">1</span>
                            <span>Timer ujian dihitung secara terpusat dari server. Ujian akan otomatis terkirim begitu sisa waktu mencapai 00:00.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-brand-50 text-[10px] font-bold text-brand-600">2</span>
                            <span>Setiap pilihan jawaban tersimpan secara otomatis secara instan. Anda tidak perlu khawatir kehilangan progres jika koneksi terputus.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-brand-50 text-[10px] font-bold text-brand-600">3</span>
                            <span>Komposisi penilaian: TWK (0/5), TIU (0/5), dan TKP (skala bobot 1 sampai 5). Tidak ada pengurangan nilai untuk jawaban yang salah.</span>
                        </li>
                    </ul>
                </div>
            </article>

            <!-- Action Card Sidebar -->
            <aside class="space-y-4">
                <div class="rounded-2xl border border-line bg-surface p-6 shadow-card">
                    <h2 class="text-sm font-bold text-ink-900">Mulai Sesi Ujian</h2>
                    <p class="mt-1 text-xs text-ink-600">
                        Pastikan koneksi internet stabil dan waktu luangmu cukup sebelum memulai.
                    </p>

                    <div class="my-5 border-t border-line/60"></div>

                    <div class="flex items-baseline justify-between mb-4">
                        <span class="text-xs text-ink-600">Biaya Paket:</span>
                        <span class="text-lg font-bold text-ink-900">
                            {{ tryout.price === 0 ? 'Gratis' : rupiah(tryout.price) }}
                        </span>
                    </div>

                    <button
                        v-if="tryout.price === 0 || tryout.has_access"
                        class="w-full h-11 inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                        :disabled="form.processing"
                        @click="start"
                    >
                        {{ form.processing ? 'Menyiapkan Soal…' : 'Mulai Sekarang' }}
                    </button>
                    <Link
                        v-else-if="tryout.payments_available"
                        :href="`/tryouts/${tryout.id}/checkout`"
                        class="w-full h-11 inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all"
                    >
                        Beli &amp; Mulai Ujian
                    </Link>
                    <div v-else class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800">
                        Paket ini berbayar. Integrasi pembayaran sedang dinonaktifkan sementara.
                    </div>

                    <p v-if="form.errors.attempt" class="mt-3 text-xs font-semibold text-danger">
                        {{ form.errors.attempt }}
                    </p>
                </div>

                <div class="rounded-2xl bg-surface-raised p-4 text-xs text-ink-600 space-y-2">
                    <p class="font-semibold text-ink-900">Tips Pengerjaan:</p>
                    <p>Kerjakan subtes TKP terlebih dahulu untuk mengamankan poin maksimal, lanjutkan ke TWK, lalu selesaikan hitungan TIU.</p>
                </div>
            </aside>
        </div>
    </AppLayout>
</template>
