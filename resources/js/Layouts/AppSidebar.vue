<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const open = ref(false);

const items = [
    { href: '/dashboard', label: 'Beranda' },
    { href: '/tryouts', label: 'Tryout' },
    { href: '/practice', label: 'Latihan' },
    { href: '/progress', label: 'Progres' },
    { href: '/rankings', label: 'Peringkat' },
    { href: '/history', label: 'Riwayat' },
];

const isActive = (href) => page.url === href || page.url.startsWith(`${href}/`);

const initial = computed(() => (user.value?.name ?? 'P').trim().charAt(0).toUpperCase());
</script>

<template>
    <aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col border-r border-line bg-surface p-4 md:flex">
        <Link href="/dashboard" class="px-2 py-3">
            <span class="block text-lg font-extrabold">Latihan CPNS</span>
            <span class="text-xs text-ink-400">Simulasi SKD</span>
        </Link>

        <nav class="mt-2 space-y-1" aria-label="Navigasi utama">
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                :class="[
                    'flex h-11 items-center rounded-xl px-3 text-sm font-medium transition-colors duration-150',
                    isActive(item.href) ? 'bg-brand-100 text-brand-600' : 'text-ink-600 hover:bg-brand-50',
                ]"
                :aria-current="isActive(item.href) ? 'page' : undefined"
            >
                {{ item.label }}
            </Link>
            <Link
                v-if="user?.role === 'admin'"
                href="/admin"
                class="flex h-11 items-center rounded-xl px-3 text-sm font-medium text-ink-600 hover:bg-brand-50"
            >
                Admin
            </Link>
        </nav>

        <div class="mt-4 rounded-2xl bg-brand-50 p-4">
            <p class="text-sm font-bold">Paket Premium</p>
            <p class="mt-1 text-xs text-ink-600">Tryout tanpa batas dan peringkat nasional.</p>
            <p class="mt-2 text-xs font-semibold text-brand-600">Segera hadir</p>
        </div>

        <div class="mt-auto flex items-center gap-3 border-t border-line pt-4">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 font-bold text-white">{{ initial }}</span>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold">
                    <Link href="/profile" class="hover:text-brand-600">{{ user?.name }}</Link>
                </p>
                <p class="text-xs text-ink-400">Peserta</p>
            </div>
            <Link href="/logout" method="post" as="button" class="ml-auto text-xs font-semibold text-danger">Keluar</Link>
        </div>
    </aside>

    <nav class="fixed inset-x-0 bottom-0 z-20 flex border-t border-line bg-surface md:hidden" aria-label="Navigasi bawah">
        <Link
            v-for="item in items"
            :key="item.href"
            :href="item.href"
            :class="['flex h-14 flex-1 flex-col items-center justify-center text-xs font-medium', isActive(item.href) ? 'text-brand-600' : 'text-ink-600']"
            :aria-current="isActive(item.href) ? 'page' : undefined"
        >
            {{ item.label }}
        </Link>
        <button class="flex h-14 flex-1 items-center justify-center text-xs font-medium text-danger" @click="open = !open">
            Akun
        </button>
    </nav>

    <div v-if="open" class="fixed inset-x-4 bottom-16 z-20 rounded-2xl border border-line bg-white p-4 shadow-card md:hidden">
        <p class="text-sm font-semibold">{{ user?.name }}</p>
        <p class="text-xs text-ink-400">{{ user?.email }}</p>
        <Link href="/profile" class="mt-3 block text-sm font-semibold text-brand-600">Profil</Link>
        <Link href="/logout" method="post" as="button" class="mt-1 block text-sm font-semibold text-danger">Keluar</Link>
    </div>
</template>
