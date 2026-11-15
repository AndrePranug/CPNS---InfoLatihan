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
    <GuestLayout title="Buat Kata Sandi Baru">
        <Head title="Kata Sandi Baru" />

        <p class="mb-4 text-xs leading-relaxed text-ink-600">
            Tentukan kata sandi baru yang aman untuk akun latihan Anda.
        </p>

        <form class="space-y-4" @submit.prevent="submit">
            <Input v-model="form.email" id="email" label="Alamat Email" type="email" autocomplete="email" :error="form.errors.email" />
            <Input v-model="form.password" id="password" label="Kata Sandi Baru" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter" :error="form.errors.password" />
            <Input v-model="form.password_confirmation" id="password_confirmation" label="Konfirmasi Kata Sandi" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi baru" />
            
            <div class="pt-2">
                <Button type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan…' : 'Simpan Kata Sandi Baru' }}
                </Button>
            </div>
        </form>

        <p class="mt-5 text-center text-xs text-ink-600">
            <Link href="/login" class="font-bold text-brand-600 hover:underline">&larr; Kembali ke halaman masuk</Link>
        </p>
    </GuestLayout>
</template>
