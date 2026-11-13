<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    tryouts: { type: Array, required: true },
});
</script>

<template>
    <AppLayout>
        <Head title="Tryout" />
        <h1 class="text-3xl font-bold">Paket Tryout</h1>

        <p v-if="tryouts.length === 0" class="mt-6 rounded-2xl border border-line bg-white p-8 text-center text-ink-600">
            Belum ada paket tryout. Cek lagi nanti.
        </p>

        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            <article v-for="tryout in tryouts" :key="tryout.id" class="rounded-2xl border border-line bg-white p-6 shadow-card">
                <h2 class="text-lg font-bold">{{ tryout.title }}</h2>
                <p class="mt-1 text-sm font-medium text-ink-600">{{ tryout.description }}</p>
                <dl class="mt-4 flex gap-6 text-sm">
                    <div>
                        <dt class="text-xs text-ink-400">Soal</dt>
                        <dd class="font-semibold tabular-nums">{{ tryout.question_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-ink-400">Durasi</dt>
                        <dd class="font-semibold tabular-nums">{{ tryout.duration_minutes }} mnt</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-ink-400">Harga</dt>
                        <dd class="font-semibold">{{ tryout.price === 0 ? 'Gratis' : `Rp${tryout.price}` }}</dd>
                    </div>
                </dl>
                <Link :href="`/tryouts/${tryout.id}`" class="mt-5 inline-flex h-11 items-center rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700">
                    Lihat detail
                </Link>
            </article>
        </div>
    </AppLayout>
</template>
