<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const page = usePage();
const form = useForm({ email: '', password: '', remember: false });

const error = computed(() => form.errors.email);

const submit = () => form.post('/login', { onFinish: () => form.reset('password') });
</script>

<template>
    <GuestLayout title="Masuk">
        <Head title="Masuk" />
        <form class="space-y-4" @submit.prevent="submit">
            <Input v-model="form.email" id="email" label="Email" type="email" autocomplete="email" :error="error" />
            <Input v-model="form.password" id="password" label="Kata sandi" type="password" autocomplete="current-password" :error="form.errors.password" />
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-line">
                Ingat saya
            </label>
            <div class="text-right">
                <Link href="/forgot-password" class="text-xs font-semibold text-brand-600">Lupa kata sandi?</Link>
            </div>
            <Button type="submit" class="w-full" :disabled="form.processing">Masuk</Button>
        </form>
        <p class="mt-4 text-sm text-ink-600">
            Belum punya akun?
            <Link href="/register" class="font-semibold text-brand-600">Daftar</Link>
        </p>
        <div class="mt-4 border-t border-line pt-4">
            <a href="/auth/google/redirect" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-line bg-white font-semibold hover:bg-brand-50">
                <svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                    <path fill="#4285F4" d="M23 12.27c0-.79-.07-1.54-.2-2.27H12v4.51h6.16a5.27 5.27 0 0 1-2.29 3.46v2.88h3.7C21.7 18.58 23 15.7 23 12.27Z" />
                    <path fill="#34A853" d="M12 23.5c2.97 0 5.46-.98 7.28-2.65l-3.7-2.88c-1.03.69-2.35 1.1-3.58 1.1-2.86 0-5.29-1.93-6.16-4.52H1.99v2.97A11.5 11.5 0 0 0 12 23.5Z" />
                    <path fill="#FBBC05" d="M5.84 14.55a6.9 6.9 0 0 1 0-4.4V7.18H1.99a11.5 11.5 0 0 0 0 10.34l3.85-2.97Z" />
                    <path fill="#EA4335" d="M12 5.02c1.62 0 3.07.56 4.21 1.65l3.28-3.28A11.5 11.5 0 0 0 1.99 7.18l3.85 2.97C6.71 6.95 9.14 5.02 12 5.02Z" />
                </svg>
                Masuk dengan Google
            </a>
        </div>
    </GuestLayout>
</template>
