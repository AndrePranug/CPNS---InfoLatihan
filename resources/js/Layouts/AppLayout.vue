<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppSidebar from '@/Layouts/AppSidebar.vue';

const page = usePage();
const flash = computed(() => page.props.flash);
</script>

<template>
    <div class="min-h-screen bg-page">
        <div class="flex">
            <AppSidebar />

            <div class="min-w-0 flex-1 pb-20 md:pb-0">
                <div v-if="flash?.success" role="status" class="mx-4 mt-4 rounded-xl bg-success/10 px-4 py-3 text-sm text-success md:mx-6">
                    {{ flash.success }}
                </div>

                <div class="mx-auto max-w-6xl px-4 py-6 md:px-6">
                    <slot />
                </div>

                <footer class="px-4 pb-6 text-xs text-ink-400 md:px-6">
                    Platform latihan independen. Bukan layanan resmi BKN atau instansi pemerintah.
                </footer>
            </div>
        </div>
    </div>
</template>
