<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const page = usePage();
const form = useForm({ email: '' });
const success = computed(() => page.props.flash?.success);
const submit = () => form.post('/forgot-password');
</script>

<template>
    <GuestLayout title="Lupa kata sandi">
        <Head title="Lupa kata sandi" />
        <p class="mb-4 text-sm text-ink-600">Masukkan email akunmu. Kami kirim tautan untuk membuat kata sandi baru.</p>
        <div v-if="success" role="status" class="mb-4 rounded-xl bg-success/10 px-4 py-3 text-sm text-success">{{ success }}</div>
        <form class="space-y-4" @submit.prevent="submit">
            <Input v-model="form.email" id="email" label="Email" type="email" autocomplete="email" :error="form.errors.email" />
            <Button type="submit" class="w-full" :disabled="form.processing">Kirim tautan</Button>
        </form>
        <p class="mt-4 text-sm text-ink-600">
            Ingat kata sandi?
            <Link href="/login" class="font-semibold text-brand-600">Masuk</Link>
        </p>
    </GuestLayout>
</template>
