<script setup>
defineProps({
    href: { type: String, default: null },
    variant: { type: String, default: 'primary' },
    type: { type: String, default: 'button' },
});

const styles = {
    primary: 'bg-brand-600 text-white hover:bg-brand-700',
    secondary: 'border border-line bg-white text-ink-900 hover:bg-brand-50',
    ghost: 'text-brand-600 hover:bg-brand-50',
    danger: 'bg-danger text-white',
};
</script>

<template>
    <a v-if="href" :href="href" :class="['inline-flex h-11 items-center justify-center rounded-xl px-5 font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2', styles[variant]]">
        <slot />
    </a>
    <button v-else :type="type" :class="['inline-flex h-11 items-center justify-center rounded-xl px-5 font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50', styles[variant]]">
        <slot />
    </button>
</template>
