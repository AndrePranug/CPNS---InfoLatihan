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
        <label :for="id" class="mb-1 block text-sm font-medium">{{ label }}</label>
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            v-bind="$attrs"
            :aria-invalid="error ? 'true' : undefined"
            :aria-describedby="error ? `${id}-error` : undefined"
            class="h-11 w-full rounded-xl border border-line bg-white px-4 text-[15px] outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20"
            :class="error && 'border-danger'"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        <p v-if="error" :id="`${id}-error`" class="mt-1 text-xs text-danger">{{ error }}</p>
    </div>
</template>
