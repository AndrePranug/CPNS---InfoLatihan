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
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    leave-active-class="transition duration-200 ease-in"
                    enter-from-class="-translate-y-2 opacity-0"
                    leave-to-class="-translate-y-2 opacity-0"
                >
                    <div v-if="flash?.success" role="status" class="mx-4 mt-4 flex items-center gap-2 rounded-xl bg-success/10 px-4 py-3 text-sm font-medium text-success md:mx-6">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        {{ flash.success }}
                    </div>
                </Transition>

                <div class="mx-auto max-w-5xl px-4 py-6 md:px-6 lg:py-8">
                    <slot />
                </div>

                <footer class="border-t border-line/50 px-4 pb-6 pt-4 text-center text-xs text-ink-400 md:px-6">
                    Platform latihan independen. Bukan layanan resmi BKN atau instansi pemerintah.
                </footer>
            </div>
        </div>
    </div>
</template>
