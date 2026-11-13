<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });

const submit = () => form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <GuestLayout title="Daftar">
        <Head title="Daftar" />
        <form class="space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" id="name" label="Nama" autocomplete="name" :error="form.errors.name" />
            <Input v-model="form.email" id="email" label="Email" type="email" autocomplete="email" :error="form.errors.email" />
            <Input v-model="form.password" id="password" label="Kata sandi" type="password" autocomplete="new-password" :error="form.errors.password" />
            <Input v-model="form.password_confirmation" id="password_confirmation" label="Ulangi kata sandi" type="password" autocomplete="new-password" />
            <Button type="submit" class="w-full" :disabled="form.processing">Daftar</Button>
        </form>
        <p class="mt-4 text-sm text-ink-600">
            Sudah punya akun?
            <Link href="/login" class="font-semibold text-brand-600">Masuk</Link>
        </p>
        <div class="mt-4 border-t border-line pt-4">
            <a href="/auth/google/redirect" class="inline-flex h-11 w-full items-center justify-center rounded-xl border border-line bg-white font-semibold hover:bg-brand-50">
                Daftar dengan Google
            </a>
        </div>
    </GuestLayout>
</template>
