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
        <Head title="Pengaturan Profil" />

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">Pengaturan Akun</h1>
            <p class="mt-1 text-sm text-ink-600">Perbarui identitas profil dan kelola kata sandi akun latihanmu.</p>
        </div>

        <!-- Email Verification Banner -->
        <div v-if="!profile.verified" role="status" class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Email <strong>{{ profile.email }}</strong> belum diverifikasi.</span>
            </div>
            <Link href="/email/verification-notification" method="post" as="button" class="font-bold underline text-amber-900 hover:text-amber-700">
                Kirim Ulang Tautan Verifikasi
            </Link>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <!-- Account Info Section -->
            <section class="rounded-2xl border border-line bg-surface p-6 shadow-card space-y-4">
                <div>
                    <h2 class="text-base font-bold text-ink-900">Informasi Pribadi</h2>
                    <p class="text-xs text-ink-400">Nama lengkap dan email aktif Anda</p>
                </div>

                <form class="space-y-4 pt-2" @submit.prevent="saveInfo">
                    <Input v-model="info.name" id="name" label="Nama Lengkap" autocomplete="name" :error="info.errors.name" />
                    <Input
                        v-model="info.email"
                        id="profile-email"
                        label="Alamat Email"
                        type="email"
                        autocomplete="email"
                        :error="info.errors.email"
                        :disabled="Boolean(profile.google_id)"
                    />
                    <p v-if="profile.google_id" class="text-[11px] text-ink-400">
                        Akun ini terhubung dengan Google OAuth sehingga alamat email tidak dapat diubah langsung.
                    </p>
                    <div class="pt-2">
                        <Button type="submit" :disabled="info.processing">
                            {{ info.processing ? 'Menyimpan…' : 'Simpan Perubahan' }}
                        </Button>
                    </div>
                </form>
            </section>

            <!-- Password Change Section -->
            <section class="rounded-2xl border border-line bg-surface p-6 shadow-card space-y-4">
                <div>
                    <h2 class="text-base font-bold text-ink-900">Ganti Kata Sandi</h2>
                    <p class="text-xs text-ink-400">Gunakan kata sandi yang kuat minimal 8 karakter</p>
                </div>

                <form class="space-y-4 pt-2" @submit.prevent="savePassword">
                    <Input
                        v-model="password.current_password"
                        id="current_password"
                        label="Kata Sandi Saat Ini"
                        type="password"
                        autocomplete="current-password"
                        :error="password.errors.current_password"
                    />
                    <Input
                        v-model="password.password"
                        id="new_password"
                        label="Kata Sandi Baru"
                        type="password"
                        autocomplete="new-password"
                        :error="password.errors.password"
                    />
                    <Input
                        v-model="password.password_confirmation"
                        id="password_confirmation"
                        label="Konfirmasi Kata Sandi Baru"
                        type="password"
                        autocomplete="new-password"
                    />
                    <div class="pt-2">
                        <Button type="submit" :disabled="password.processing">
                            {{ password.processing ? 'Memperbarui…' : 'Perbarui Kata Sandi' }}
                        </Button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
