<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    profile: { type: Object, required: true },
});

const info = useForm({ name: props.profile.name, email: props.profile.email });
const password = useForm({ current_password: '', password: '', password_confirmation: '' });

const saveInfo = () => info.patch('/profile', { preserveScroll: true });
const savePassword = () => password.put('/profile/password', {
    preserveScroll: true,
    onFinish: () => password.reset('current_password', 'password', 'password_confirmation'),
});
</script>

<template>
    <AppLayout>
        <Head title="Profil" />
        <h1 class="text-3xl font-bold">Profil</h1>

        <div v-if="!profile.verified" role="status" class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-warning/10 px-4 py-3 text-sm text-warning">
            <p>Email <span class="font-semibold">{{ profile.email }}</span> belum diverifikasi.</p>
            <Link href="/email/verification-notification" method="post" as="button" class="font-semibold underline">Kirim ulang tautan</Link>
        </div>

        <div class="mt-5 grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-line bg-white p-6 shadow-card">
                <h2 class="text-lg font-bold">Data akun</h2>
                <form class="mt-4 space-y-4" @submit.prevent="saveInfo">
                    <Input v-model="info.name" id="name" label="Nama" autocomplete="name" :error="info.errors.name" />
                    <Input v-model="info.email" id="profile-email" label="Email" type="email" autocomplete="email" :error="info.errors.email" :disabled="Boolean(profile.google_id)" />
                    <p v-if="profile.google_id" class="text-xs text-ink-400">Email terhubung ke Google dan tidak bisa diubah di sini.</p>
                    <Button type="submit" :disabled="info.processing">Simpan</Button>
                </form>
            </section>

            <section class="rounded-2xl border border-line bg-white p-6 shadow-card">
                <h2 class="text-lg font-bold">Ganti kata sandi</h2>
                <form class="mt-4 space-y-4" @submit.prevent="savePassword">
                    <Input v-model="password.current_password" id="current_password" label="Kata sandi saat ini" type="password" autocomplete="current-password" :error="password.errors.current_password" />
                    <Input v-model="password.password" id="new_password" label="Kata sandi baru" type="password" autocomplete="new-password" :error="password.errors.password" />
                    <Input v-model="password.password_confirmation" id="password_confirmation" label="Ulangi kata sandi baru" type="password" autocomplete="new-password" />
                    <Button type="submit" :disabled="password.processing">Perbarui kata sandi</Button>
                </form>
            </section>
        </div>

        <p class="mt-6 text-sm text-ink-600">
            <Link href="/dashboard" class="font-semibold text-brand-600">Kembali ke dasbor</Link>
        </p>
    </AppLayout>
</template>
