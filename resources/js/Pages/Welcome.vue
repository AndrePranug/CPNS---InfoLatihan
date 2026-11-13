<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const subtests = [
    { code: 'TWK', name: 'Tes Wawasan Kebangsaan', count: 30, bg: 'bg-twk-bg', fg: 'text-twk' },
    { code: 'TIU', name: 'Tes Intelegensia Umum', count: 35, bg: 'bg-tiu-bg', fg: 'text-tiu' },
    { code: 'TKP', name: 'Tes Karakteristik Pribadi', count: 45, bg: 'bg-tkp-bg', fg: 'text-tkp' },
];
</script>

<template>
    <main class="mx-auto min-h-screen max-w-5xl px-4 py-12 sm:px-6">
        <section class="rounded-3xl bg-gradient-to-br from-brand-100 to-brand-50 p-8 sm:p-12">
            <p class="text-sm font-medium text-ink-600">Simulasi SKD</p>
            <h1 class="mt-2 text-4xl font-extrabold">Latihan CPNS 2026</h1>
            <p class="mt-3 max-w-xl text-[15px] text-ink-600">
                Tryout 110 soal dengan timer 100 menit, penilaian otomatis, dan pembahasan per soal.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <Link :href="user ? '/tryouts' : '/login'" class="inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                    Mulai Tryout
                </Link>
                <Link :href="user ? '/dashboard' : '/register'" class="inline-flex h-11 items-center rounded-xl border border-line bg-white px-5 font-semibold hover:bg-brand-50">
                    {{ user ? 'Dasbor' : 'Daftar' }}
                </Link>
            </div>
        </section>

        <section class="mt-6 grid gap-6 sm:grid-cols-3">
            <article v-for="s in subtests" :key="s.code" class="rounded-2xl border border-line bg-white p-6 shadow-card">
                <div :class="[s.bg, s.fg]" class="inline-flex h-10 w-10 items-center justify-center rounded-xl font-bold">
                    {{ s.code }}
                </div>
                <h2 class="mt-4 text-lg font-bold">{{ s.name }}</h2>
                <p class="mt-1 text-sm font-medium text-ink-600">{{ s.count }} soal</p>
            </article>
        </section>

        <footer class="mt-12 text-xs text-ink-400">
            Platform latihan independen. Bukan layanan resmi BKN atau instansi pemerintah.
        </footer>
    </main>
</template>
