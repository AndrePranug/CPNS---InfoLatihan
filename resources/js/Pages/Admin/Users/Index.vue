<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    users: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const search = ref(props.filters.q ?? '');
let debounce = null;

watch(search, (value) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get('/admin/users', { q: value || undefined }, { preserveState: true, replace: true });
    }, 300);
});

const toggle = (id) => router.patch(`/admin/users/${id}/toggle`, {}, { preserveScroll: true });
</script>

<template>
    <AdminLayout>
        <Head title="Pengguna" />
        <h1 class="text-2xl font-bold">Pengguna</h1>

        <input
            v-model="search"
            type="search"
            placeholder="Cari nama atau email"
            aria-label="Cari pengguna"
            class="mt-4 h-11 w-full max-w-sm rounded-xl border border-line bg-white px-4 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none"
        >

        <div class="mt-4 overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-ink-400">
                    <tr>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Peran</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id" class="h-12 border-t border-line">
                        <td class="p-3 font-semibold">{{ user.name }}</td>
                        <td class="p-3 text-ink-600">{{ user.email }}</td>
                        <td class="p-3">{{ user.role }}</td>
                        <td class="p-3">
                            <span :class="['rounded-full px-2 py-1 text-xs font-semibold', user.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger']">
                                {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <button class="font-semibold text-brand-600" @click="toggle(user.id)">
                                {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="5" class="p-8 text-center text-ink-600">Tidak ada pengguna.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
