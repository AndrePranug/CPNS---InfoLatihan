<script setup>
import { computed } from 'vue';

const props = defineProps({
    percent: { type: Number, default: 0 },
    size: { type: Number, default: 120 },
    label: { type: String, default: '' },
});

const radius = computed(() => (props.size - 12) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);
const dash = computed(() => (circumference.value * Math.min(100, props.percent)) / 100);
</script>

<template>
    <div class="relative inline-flex items-center justify-center" :style="{ width: `${size}px`, height: `${size}px` }">
        <svg :width="size" :height="size" class="-rotate-90" aria-hidden="true">
            <circle :cx="size / 2" :cy="size / 2" :r="radius" fill="none" stroke="currentColor" class="text-brand-100" stroke-width="10" />
            <circle
                :cx="size / 2"
                :cy="size / 2"
                :r="radius"
                fill="none"
                stroke="currentColor"
                class="text-brand-600"
                stroke-width="10"
                stroke-linecap="round"
                :stroke-dasharray="`${dash} ${circumference}`"
            />
        </svg>
        <div class="absolute text-center">
            <p class="text-lg font-bold tabular-nums">{{ label }}</p>
            <p class="text-xs text-ink-400">{{ percent }}%</p>
        </div>
    </div>
</template>
