<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => form.post('/reset-password', { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <GuestLayout title="Kata sandi baru">
        <Head title="Kata sandi baru" />
        <form class="space-y-4" @submit.prevent="submit">
            <Input v-model="form.email" id="email" label="Email" type="email" autocomplete="email" :error="form.errors.email" />
            <Input v-model="form.password" id="password" label="Kata sandi baru" type="password" autocomplete="new-password" :error="form.errors.password" />
            <Input v-model="form.password_confirmation" id="password_confirmation" label="Ulangi kata sandi" type="password" autocomplete="new-password" />
            <Button type="submit" class="w-full" :disabled="form.processing">Simpan kata sandi</Button>
        </form>
        <p class="mt-4 text-sm text-ink-600">
            <Link href="/login" class="font-semibold text-brand-600">Kembali ke halaman masuk</Link>
        </p>
    </GuestLayout>
</template>
