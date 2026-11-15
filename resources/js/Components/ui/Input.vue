<script setup>
defineProps({
    label: { type: String, required: true },
    id: { type: String, required: true },
    type: { type: String, default: 'text' },
    error: { type: String, default: null },
    modelValue: { type: String, default: '' },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label :for="id" class="mb-1.5 block text-sm font-medium text-ink-900">{{ label }}</label>
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            v-bind="$attrs"
            :aria-invalid="error ? 'true' : undefined"
            :aria-describedby="error ? `${id}-error` : undefined"
            class="h-11 w-full rounded-xl border border-line bg-surface px-4 text-[15px] text-ink-900 outline-none transition-colors duration-150 placeholder:text-ink-400 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 disabled:cursor-not-allowed disabled:bg-surface-raised disabled:text-ink-400"
            :class="error && 'border-danger focus:border-danger focus:ring-danger/20'"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        <p v-if="error" :id="`${id}-error`" class="mt-1.5 text-xs font-medium text-danger">{{ error }}</p>
    </div>
</template>
