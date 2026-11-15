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
        <Head title="Manajemen Pengguna" />

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Manajemen Pengguna</h1>
                <p class="mt-1 text-sm text-ink-600">Daftar seluruh akun peserta dan staf administrator platform.</p>
            </div>
        </div>

        <!-- Search input -->
        <div class="relative max-w-md">
            <svg viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-ink-400">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
            </svg>
            <input
                v-model="search"
                type="search"
                placeholder="Cari berdasarkan nama atau alamat email…"
                aria-label="Cari pengguna"
                class="h-11 w-full rounded-xl border border-line bg-surface pl-10 pr-4 text-xs text-ink-900 placeholder:text-ink-400 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none"
            >
        </div>

        <!-- Users Table -->
        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-line bg-surface-raised font-semibold text-ink-400">
                        <tr>
                            <th class="py-3.5 px-4">Nama Lengkap</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4 w-28">Peran</th>
                            <th class="py-3.5 px-4 w-28">Status</th>
                            <th class="py-3.5 px-4 text-right w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/60">
                        <tr v-for="user in users.data" :key="user.id" class="transition-colors hover:bg-surface-raised">
                            <td class="py-3.5 px-4 font-bold text-ink-900">{{ user.name }}</td>
                            <td class="py-3.5 px-4 text-ink-600">{{ user.email }}</td>
                            <td class="py-3.5 px-4">
                                <span :class="['rounded px-2 py-0.5 text-[10px] font-bold uppercase', user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-brand-50 text-brand-700']">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span :class="['inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold', user.is_active ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger']">
                                    <span :class="['h-1.5 w-1.5 rounded-full', user.is_active ? 'bg-success' : 'bg-danger']"></span>
                                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button
                                    :class="['rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors', user.is_active ? 'text-danger hover:bg-danger/10' : 'text-success hover:bg-success/10']"
                                    @click="toggle(user.id)"
                                >
                                    {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="p-8 text-center text-xs text-ink-400">
                                Tidak ada akun pengguna yang cocok dengan pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav v-if="users.links.length > 3" class="flex flex-wrap gap-1.5 pt-2" aria-label="Navigasi halaman">
            <Link
                v-for="link in users.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="[
                    'h-8 min-w-8 inline-flex items-center justify-center rounded-lg px-2.5 text-xs font-semibold transition-colors',
                    link.active ? 'bg-brand-600 text-white shadow-xs' : 'border border-line bg-surface text-ink-600 hover:bg-brand-50',
                    !link.url && 'pointer-events-none opacity-40'
                ]"
            >
                {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
            </Link>
        </nav>
    </AdminLayout>
</template>
