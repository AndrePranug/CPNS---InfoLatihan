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
    <GuestLayout title="Lupa Kata Sandi">
        <Head title="Lupa Kata Sandi" />

        <p class="mb-4 text-xs leading-relaxed text-ink-600">
            Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk membuat kata sandi baru akun Anda.
        </p>

        <div v-if="success" role="status" class="mb-4 flex items-center gap-2 rounded-xl bg-success/10 px-4 py-3 text-xs font-semibold text-success">
            <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            {{ success }}
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <Input v-model="form.email" id="email" label="Alamat Email" type="email" autocomplete="email" placeholder="nama@email.com" :error="form.errors.email" />
            
            <div class="pt-2">
                <Button type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Mengirim Tautan…' : 'Kirim Tautan Pemulihan' }}
                </Button>
            </div>
        </form>

        <p class="mt-5 text-center text-xs text-ink-600">
            Ingat kata sandi akunmu?
            <Link href="/login" class="font-bold text-brand-600 hover:underline">Masuk Kembali</Link>
        </p>
    </GuestLayout>
</template>
