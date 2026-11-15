<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const moreOpen = ref(false);
const accountOpen = ref(false);

const items = [
    { href: '/dashboard', label: 'Beranda', icon: 'M3 12.5l9-8.5 9 8.5M5 11v9a1 1 0 001 1h3.5a1 1 0 001-1v-4.5a1 1 0 011-1h3a1 1 0 011 1V21a1 1 0 001 1H19a1 1 0 001-1v-9' },
    { href: '/tryouts', label: 'Tryout', icon: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
    { href: '/practice', label: 'Latihan', icon: 'M12 3v1m0 16v1m-8-9H3m18 0h-1m-2.636-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z' },
    { href: '/forum', label: 'Forum', icon: 'M7.5 8.25h9m-9 3h6m-10.863 7.02c-.382.921.356 1.98 1.352 1.98h13.022c.996 0 1.734-1.059 1.352-1.98L18.69 3.73A1.5 1.5 0 0017.362 3H6.638a1.5 1.5 0 00-1.328.73L2.637 18.27z', mobileHidden: true },
    { href: '/progress', label: 'Progres', icon: 'M3 17l6-6 4 4 8-8M15 7h6v6' },
    { href: '/rankings', label: 'Peringkat', icon: 'M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-4.5A3.75 3.75 0 0012 10.5a3.75 3.75 0 00-4.5 3.75v4.5m9 0h.008M12 3v2.25m6.364.886l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591', mobileHidden: true },
    { href: '/history', label: 'Riwayat', icon: 'M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z', mobileHidden: true },
];

const mobileItems = items.filter((i) => !i.mobileHidden);
const moreItems = items.filter((i) => i.mobileHidden);

const isActive = (href) => page.url === href || page.url.startsWith(`${href}/`);

const initial = computed(() => (user.value?.name ?? 'P').trim().charAt(0).toUpperCase());
</script>

<template>
    <!-- Desktop sidebar -->
    <aside class="sticky top-0 hidden h-screen w-[248px] shrink-0 flex-col border-r border-line bg-surface-raised md:flex">
        <Link href="/dashboard" class="flex items-center gap-2.5 px-5 py-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-white" aria-hidden="true">
                    <path d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div>
                <span class="block text-sm font-bold leading-tight text-ink-900">Latihan CPNS</span>
                <span class="text-[11px] font-medium text-ink-400">Simulasi SKD</span>
            </div>
        </Link>

        <nav class="mt-1 flex-1 space-y-0.5 px-3" aria-label="Navigasi utama">
            <Link
                v-for="item in items"
                :key="item.href"
                :href="item.href"
                :class="[
                    'group flex h-10 items-center gap-2.5 rounded-lg px-3 text-[13px] font-medium transition-colors duration-150',
                    isActive(item.href) ? 'bg-brand-600/10 font-semibold text-brand-600' : 'text-ink-600 hover:bg-ink-900/[0.04] hover:text-ink-900',
                ]"
                :aria-current="isActive(item.href) ? 'page' : undefined"
            >
                <svg viewBox="0 0 24 24" fill="none" class="h-[18px] w-[18px] shrink-0" :class="isActive(item.href) ? 'text-brand-600' : 'text-ink-400 group-hover:text-ink-600'" aria-hidden="true">
                    <path :d="item.icon" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ item.label }}
            </Link>
            <Link
                v-if="user?.role === 'admin'"
                href="/admin"
                :class="[
                    'group flex h-10 items-center gap-2.5 rounded-lg px-3 text-[13px] font-medium transition-colors duration-150',
                    isActive('/admin') ? 'bg-brand-600/10 font-semibold text-brand-600' : 'text-ink-600 hover:bg-ink-900/[0.04] hover:text-ink-900',
                ]"
            >
                <svg viewBox="0 0 24 24" fill="none" class="h-[18px] w-[18px] shrink-0" :class="isActive('/admin') ? 'text-brand-600' : 'text-ink-400 group-hover:text-ink-600'" aria-hidden="true">
                    <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Admin
            </Link>
        </nav>

        <div class="mx-3 rounded-xl bg-gradient-to-br from-brand-50 to-brand-100/50 p-4">
            <p class="text-[13px] font-semibold text-ink-900">Paket Premium</p>
            <p class="mt-0.5 text-xs text-ink-600">Tryout tanpa batas dan peringkat nasional.</p>
            <p class="mt-2 text-xs font-semibold text-brand-600">Segera hadir</p>
        </div>

        <div class="mt-3 flex items-center gap-2.5 border-t border-line px-4 py-3.5">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ initial }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-[13px] font-semibold leading-tight">
                    <Link href="/profile" class="hover:text-brand-600 transition-colors">{{ user?.name }}</Link>
                </p>
                <p class="text-[11px] text-ink-400">Peserta</p>
            </div>
            <Link href="/logout" method="post" as="button" class="rounded-lg p-1.5 text-ink-400 transition-colors hover:bg-danger/10 hover:text-danger" title="Keluar">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </Link>
        </div>
    </aside>

    <!-- Mobile bottom nav (5 items) -->
    <nav class="fixed inset-x-0 bottom-0 z-20 border-t border-line bg-surface/95 backdrop-blur-lg md:hidden" aria-label="Navigasi bawah">
        <div class="flex">
            <Link
                v-for="item in mobileItems"
                :key="item.href"
                :href="item.href"
                :class="['flex h-14 flex-1 flex-col items-center justify-center gap-0.5 transition-colors duration-150', isActive(item.href) ? 'text-brand-600' : 'text-ink-400']"
                :aria-current="isActive(item.href) ? 'page' : undefined"
            >
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                    <path :d="item.icon" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-[10px] font-medium">{{ item.label }}</span>
            </Link>
            <button
                class="flex h-14 flex-1 flex-col items-center justify-center gap-0.5 text-ink-400 transition-colors duration-150"
                :class="moreOpen && 'text-brand-600'"
                @click="moreOpen = !moreOpen"
            >
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-[10px] font-medium">Lainnya</span>
            </button>
        </div>
    </nav>

    <!-- Mobile "more" sheet -->
    <Transition
        enter-active-class="transition duration-200 ease-out"
        leave-active-class="transition duration-150 ease-in"
        enter-from-class="translate-y-4 opacity-0"
        leave-to-class="translate-y-4 opacity-0"
    >
        <div v-if="moreOpen" class="fixed inset-x-3 bottom-[60px] z-20 rounded-2xl border border-line bg-surface p-2 shadow-card-hover md:hidden">
            <Link
                v-for="item in moreItems"
                :key="item.href"
                :href="item.href"
                class="flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium text-ink-600 transition-colors hover:bg-brand-50"
                @click="moreOpen = false"
            >
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-ink-400" aria-hidden="true">
                    <path :d="item.icon" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ item.label }}
            </Link>
            <div class="my-1 border-t border-line"></div>
            <Link
                href="/profile"
                class="flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium text-ink-600 transition-colors hover:bg-brand-50"
                @click="moreOpen = false"
            >
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">{{ initial }}</span>
                {{ user?.name }}
            </Link>
            <Link
                href="/logout"
                method="post"
                as="button"
                class="flex h-11 w-full items-center gap-3 rounded-lg px-3 text-sm font-medium text-danger transition-colors hover:bg-danger/5"
                @click="moreOpen = false"
            >
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                    <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Keluar
            </Link>
        </div>
    </Transition>

    <!-- Overlay for mobile more sheet -->
    <Transition
        enter-active-class="transition duration-200 ease-out"
        leave-active-class="transition duration-150 ease-in"
        enter-from-class="opacity-0"
        leave-to-class="opacity-0"
    >
        <div v-if="moreOpen" class="fixed inset-0 z-10 bg-ink-900/20 md:hidden" @click="moreOpen = false"></div>
    </Transition>
</template>
