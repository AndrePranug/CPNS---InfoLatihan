<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const links = [
    { href: '/admin', label: 'Dasbor' },
    { href: '/admin/questions', label: 'Bank Soal' },
    { href: '/admin/tryouts', label: 'Paket Tryout' },
    { href: '/admin/subtests', label: 'Subtes' },
    { href: '/admin/users', label: 'Pengguna' },
];

const isActive = (href) => computed(() => page.url === href || page.url.startsWith(`${href}/`));
</script>

<template>
    <div class="min-h-screen bg-page">
        <header class="border-b border-line bg-white">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">
                <Link href="/admin" class="font-extrabold">Admin · Latihan CPNS</Link>
                <nav class="flex gap-4 text-sm font-medium text-ink-600">
                    <Link v-for="link in links" :key="link.href" :href="link.href" class="hover:text-brand-600">
                        {{ link.label }}
                    </Link>
                    <Link href="/dashboard" class="text-brand-600">Situs</Link>
                </nav>
            </div>
        </header>

        <div v-if="page.props.flash?.success" class="mx-auto mt-4 max-w-6xl rounded-xl bg-success/10 px-4 py-3 text-sm text-success">
            {{ page.props.flash.success }}
        </div>

        <div v-if="page.props.flash?.error" class="mx-auto mt-4 max-w-6xl rounded-xl bg-danger/10 px-4 py-3 text-sm text-danger">
            {{ page.props.flash.error }}
        </div>

        <main class="mx-auto max-w-6xl px-4 py-8">
            <slot />
        </main>
    </div>
</template>
