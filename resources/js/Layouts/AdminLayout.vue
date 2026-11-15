<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const mobileOpen = ref(false);

const user = computed(() => page.props.auth?.user);
const initial = computed(() => (user.value?.name ?? 'A').trim().charAt(0).toUpperCase());

const navigation = [
    {
        group: 'Menu Utama',
        items: [
            {
                href: '/admin',
                label: 'Dasbor',
                icon: 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
            },
            {
                href: '/admin/questions',
                label: 'Bank Soal',
                icon: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z',
            },
            {
                href: '/admin/tryouts',
                label: 'Paket Tryout',
                icon: 'M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m-15 0a2.246 2.246 0 0 0-.75.128m15.75 0c-.235-.083-.487-.128-.75-.128m-14.25 0h14.25m-15 3A2.25 2.25 0 0 0 3 15v3A2.25 2.25 0 0 0 5.25 20.25h13.5A2.25 2.25 0 0 0 21 18v-3a2.25 2.25 0 0 0-1.5-2.122',
            },
            {
                href: '/admin/vouchers',
                label: 'Voucher & Promo',
                icon: 'M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z',
            },
        ],
    },
    {
        group: 'Struktur Ujian',
        items: [
            {
                href: '/admin/formations',
                label: 'Formasi SKB',
                icon: 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0',
            },
            {
                href: '/admin/subtests',
                label: 'Subtes & PG',
                icon: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
            },
        ],
    },
    {
        group: 'Data & Akun',
        items: [
            {
                href: '/admin/reports',
                label: 'Laporan Mitra',
                icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
            },
            {
                href: '/admin/users',
                label: 'Pengguna',
                icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a3.75 3.75 0 0 1 6.375-2.625 9.07 9.07 0 0 0 2.625.375c.9 0 1.765-.132 2.58-.378a3.75 3.75 0 0 1 4.17 2.631Zm2.625-11.628a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Zm-9 3.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
            },
        ],
    },
];

const isActive = (href) => {
    if (href === '/admin') {
        return page.url === '/admin' || page.url === '/admin/';
    }
    return page.url === href || page.url.startsWith(`${href}/`);
};
</script>

<template>
    <div class="min-h-screen bg-page flex">
        <!-- Desktop Admin Sidebar (Left) -->
        <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-line bg-surface-raised md:flex z-20">
            <!-- Brand & Portal Info -->
            <div class="flex items-center gap-3 border-b border-line px-5 py-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
                    </svg>
                </span>
                <div>
                    <span class="block text-sm font-bold text-ink-900 leading-tight">Admin Portal</span>
                    <span class="inline-flex items-center gap-1 rounded bg-brand-50 px-1.5 py-0.2 text-[10px] font-bold text-brand-700">
                        Latihan CPNS
                    </span>
                </div>
            </div>

            <!-- Grouped Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6" aria-label="Navigasi admin">
                <div v-for="group in navigation" :key="group.group" class="space-y-1">
                    <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-ink-400">
                        {{ group.group }}
                    </p>
                    <div class="mt-1.5 space-y-0.5">
                        <Link
                            v-for="item in group.items"
                            :key="item.href"
                            :href="item.href"
                            :class="[
                                'group flex h-9 items-center gap-2.5 rounded-xl px-3 text-xs font-semibold transition-all duration-150',
                                isActive(item.href)
                                    ? 'bg-brand-600 text-white shadow-xs font-bold'
                                    : 'text-ink-600 hover:bg-brand-50 hover:text-brand-700'
                            ]"
                            :aria-current="isActive(item.href) ? 'page' : undefined"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                class="h-4 w-4 shrink-0 transition-colors"
                                :class="isActive(item.href) ? 'text-white' : 'text-ink-400 group-hover:text-brand-600'"
                                stroke="currentColor"
                                stroke-width="1.75"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path :d="item.icon" />
                            </svg>
                            <span>{{ item.label }}</span>
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Bottom User & Switch to Public Site -->
            <div class="border-t border-line p-3 space-y-2">
                <Link
                    href="/dashboard"
                    class="flex h-9 w-full items-center justify-center gap-2 rounded-xl border border-line bg-surface text-xs font-semibold text-ink-700 shadow-xs hover:bg-brand-50 hover:text-brand-700 transition-colors"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 text-brand-600" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    Lihat Situs Peserta
                </Link>

                <div class="flex items-center gap-2.5 rounded-xl bg-surface p-2.5 border border-line/80">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-600 text-xs font-bold text-white">
                        {{ initial }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-ink-900 leading-tight">{{ user?.name }}</p>
                        <p class="text-[10px] text-ink-400">Administrator</p>
                    </div>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="rounded-lg p-1 text-ink-400 hover:bg-danger/10 hover:text-danger transition-colors"
                        title="Keluar"
                    >
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 min-w-0 flex flex-col min-h-screen">
            <!-- Mobile Header Bar -->
            <header class="sticky top-0 z-10 flex h-14 items-center justify-between border-b border-line bg-surface/95 px-4 backdrop-blur-md md:hidden">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-600 text-white font-bold text-xs">A</span>
                    <span class="text-sm font-bold text-ink-900">Admin Portal</span>
                </div>
                <button
                    class="rounded-lg border border-line bg-surface p-2 text-ink-600"
                    @click="mobileOpen = !mobileOpen"
                >
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
            </header>

            <!-- Mobile Drawer Menu -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                leave-active-class="transition duration-150 ease-in"
                enter-from-class="-translate-x-full"
                leave-to-class="-translate-x-full"
            >
                <div v-if="mobileOpen" class="fixed inset-y-0 left-0 z-30 w-72 bg-surface p-5 shadow-2xl flex flex-col md:hidden">
                    <div class="flex items-center justify-between pb-4 border-b border-line">
                        <span class="font-bold text-sm text-ink-900">Menu Administrator</span>
                        <button class="inline-flex items-center gap-1 text-xs font-semibold text-ink-500 hover:text-ink-900" @click="mobileOpen = false">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <span>Tutup</span>
                        </button>
                    </div>

                    <nav class="mt-4 flex-1 overflow-y-auto space-y-4">
                        <div v-for="group in navigation" :key="group.group" class="space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-ink-400">{{ group.group }}</p>
                            <Link
                                v-for="item in group.items"
                                :key="item.href"
                                :href="item.href"
                                :class="[
                                    'flex h-9 items-center gap-2.5 rounded-xl px-3 text-xs font-semibold',
                                    isActive(item.href) ? 'bg-brand-600 text-white' : 'text-ink-600 hover:bg-brand-50'
                                ]"
                                @click="mobileOpen = false"
                            >
                                <span>{{ item.label }}</span>
                            </Link>
                        </div>
                    </nav>

                    <div class="pt-4 border-t border-line space-y-2">
                        <Link href="/dashboard" class="block text-center py-2 text-xs font-semibold text-brand-600">
                            Lihat Situs Peserta &rarr;
                        </Link>
                        <Link href="/logout" method="post" as="button" class="w-full py-2 text-center text-xs font-semibold text-danger">
                            Keluar Akun
                        </Link>
                    </div>
                </div>
            </Transition>
            <div v-if="mobileOpen" class="fixed inset-0 z-20 bg-ink-900/40 md:hidden" @click="mobileOpen = false"></div>

            <!-- Flash Messages -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                leave-active-class="transition duration-200 ease-in"
                enter-from-class="-translate-y-2 opacity-0"
                leave-to-class="-translate-y-2 opacity-0"
            >
                <div v-if="page.props.flash?.success" class="mx-4 sm:mx-8 mt-5">
                    <div class="flex items-center gap-2 rounded-xl bg-success/10 px-4 py-3 text-xs font-semibold text-success border border-success/20">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        {{ page.props.flash.success }}
                    </div>
                </div>
            </Transition>

            <Transition
                enter-active-class="transition duration-300 ease-out"
                leave-active-class="transition duration-200 ease-in"
                enter-from-class="-translate-y-2 opacity-0"
                leave-to-class="-translate-y-2 opacity-0"
            >
                <div v-if="page.props.flash?.error" class="mx-4 sm:mx-8 mt-5">
                    <div class="flex items-center gap-2 rounded-xl bg-danger/10 px-4 py-3 text-xs font-semibold text-danger border border-danger/20">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        {{ page.props.flash.error }}
                    </div>
                </div>
            </Transition>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto space-y-6">
                <slot />
            </main>
        </div>
    </div>
</template>
