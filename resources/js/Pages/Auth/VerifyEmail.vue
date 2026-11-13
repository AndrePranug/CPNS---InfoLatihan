<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({});
const resend = () => form.post('/email/verification-notification');
</script>

<template>
    <GuestLayout title="Verifikasi email">
        <Head title="Verifikasi email" />
        <p class="text-sm text-ink-600">
            Kami sudah mengirim tautan verifikasi ke emailmu. Buka tautan itu untuk mengaktifkan akun.
        </p>
        <p class="mt-2 text-xs text-ink-400">
            Tidak ada email masuk? Cek folder spam, atau kirim ulang di bawah.
        </p>
        <Button class="mt-5 w-full" :disabled="form.processing" @click="resend">Kirim ulang tautan</Button>
        <p class="mt-4 text-sm text-ink-600">
            Salah alamat email?
            <a href="/profile" class="font-semibold text-brand-600">Ubah di profil</a>
        </p>
    </GuestLayout>
</template>
