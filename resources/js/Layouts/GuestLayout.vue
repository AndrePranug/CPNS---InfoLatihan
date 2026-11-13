<script setup>
defineProps({
    title: { type: String, default: '' },
});
</script>

<template>
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-4 py-12">
        <h1 class="mb-6 text-3xl font-bold">{{ title }}</h1>
        <div class="rounded-2xl border border-line bg-white p-6 shadow-card">
            <slot />
        </div>
    </div>
</template>
