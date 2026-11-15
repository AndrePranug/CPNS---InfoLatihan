<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });

const submit = () => form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template>
    <GuestLayout title="Daftar Akun Baru">
        <Head title="Daftar Akun" />

        <form class="space-y-4" @submit.prevent="submit">
            <Input v-model="form.name" id="name" label="Nama Lengkap" autocomplete="name" placeholder="Sesuai kartu identitas" :error="form.errors.name" />
            <Input v-model="form.email" id="email" label="Alamat Email" type="email" autocomplete="email" placeholder="nama@email.com" :error="form.errors.email" />
            <Input v-model="form.password" id="password" label="Kata Sandi" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter" :error="form.errors.password" />
            <Input v-model="form.password_confirmation" id="password_confirmation" label="Ulangi Kata Sandi" type="password" autocomplete="new-password" placeholder="Ulangi kata sandi" />
            
            <div class="pt-2">
                <Button type="submit" class="w-full" :disabled="form.processing">
                    {{ form.processing ? 'Mendaftarkan…' : 'Daftar Sekarang' }}
                </Button>
            </div>
        </form>

        <div class="my-5 flex items-center gap-3">
            <div class="h-px flex-1 bg-line"></div>
            <span class="text-[11px] font-medium text-ink-400 uppercase tracking-wider">atau</span>
            <div class="h-px flex-1 bg-line"></div>
        </div>

        <div>
            <a
                href="/auth/google/redirect"
                class="inline-flex h-11 w-full items-center justify-center gap-2.5 rounded-xl border border-line bg-surface text-xs font-semibold text-ink-900 shadow-xs hover:bg-surface-raised active:scale-[0.98] transition-all"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" aria-hidden="true">
                    <path fill="#4285F4" d="M23 12.27c0-.79-.07-1.54-.2-2.27H12v4.51h6.16a5.27 5.27 0 0 1-2.29 3.46v2.88h3.7C21.7 18.58 23 15.7 23 12.27Z" />
                    <path fill="#34A853" d="M12 23.5c2.97 0 5.46-.98 7.28-2.65l-3.7-2.88c-1.03.69-2.35 1.1-3.58 1.1-2.86 0-5.29-1.93-6.16-4.52H1.99v2.97A11.5 11.5 0 0 0 12 23.5Z" />
                    <path fill="#FBBC05" d="M5.84 14.55a6.9 6.9 0 0 1 0-4.4V7.18H1.99a11.5 11.5 0 0 0 0 10.34l3.85-2.97Z" />
                    <path fill="#EA4335" d="M12 5.02c1.62 0 3.07.56 4.21 1.65l3.28-3.28A11.5 11.5 0 0 0 1.99 7.18l3.85 2.97C6.71 6.95 9.14 5.02 12 5.02Z" />
                </svg>
                Daftar Cepat dengan Google
            </a>
        </div>

        <p class="mt-5 text-center text-xs text-ink-600">
            Sudah memiliki akun?
            <Link href="/login" class="font-bold text-brand-600 hover:underline">Masuk di Sini</Link>
        </p>
    </GuestLayout>
</template>
