<script setup>
defineProps({
    title: { type: String, default: '' },
});
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-page px-4 py-12">
        <div class="w-full max-w-[420px]">
            <div class="mb-6 flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-white" aria-hidden="true">
                        <path d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="text-lg font-bold text-ink-900">Latihan CPNS</span>
            </div>

            <h1 class="text-2xl font-bold text-ink-900">{{ title }}</h1>

            <div class="mt-4 rounded-2xl border border-line bg-surface p-6 shadow-card">
                <slot />
            </div>

            <p class="mt-8 text-center text-xs text-ink-400">
                Platform latihan independen. Bukan layanan resmi BKN.
            </p>
        </div>
    </div>
</template>
