<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({});
const resend = () => form.post('/email/verification-notification');
</script>

<template>
    <GuestLayout title="Verifikasi Alamat Email">
        <Head title="Verifikasi Email" />

        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 mb-4">
            <svg viewBox="0 0 24 24" fill="none" class="h-7 w-7" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
        </div>

        <p class="text-xs text-center leading-relaxed text-ink-600">
            Kami telah mengirimkan tautan verifikasi ke email Anda. Silakan buka kotak masuk email untuk mengaktifkan akun.
        </p>
        <p class="mt-2 text-center text-[11px] text-ink-400">
            Belum menerima email? Periksa folder spam atau klik tombol di bawah untuk mengirim ulang.
        </p>

        <div class="mt-6">
            <Button class="w-full" :disabled="form.processing" @click="resend">
                {{ form.processing ? 'Mengirim Ulang…' : 'Kirim Ulang Tautan Verifikasi' }}
            </Button>
        </div>

        <p class="mt-5 text-center text-xs text-ink-600">
            Salah alamat email?
            <a href="/profile" class="font-bold text-brand-600 hover:underline">Perbarui di Pengaturan Profil</a>
        </p>
    </GuestLayout>
</template>
