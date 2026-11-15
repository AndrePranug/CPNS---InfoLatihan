<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tryout: { type: Object, required: true },
    selected: { type: Array, required: true },
    bank: { type: Array, required: true },
});

const chosen = ref([...props.selected]);
const search = ref('');
const subtestFilter = ref('semua');
const filterOnlyValid = ref(true);
const confirmEmpty = ref(false);

const subtests = (list) => [...new Set(list.map((question) => question.subtest))];

const visible = computed(() =>
    props.bank.filter((question) => {
        if (subtestFilter.value !== 'semua' && question.subtest !== subtestFilter.value) return false;
        if (filterOnlyValid.value && !question.valid) return false;
        if (search.value !== '' && !question.body.toLowerCase().includes(search.value.toLowerCase())) return false;

        return true;
    }),
);

const countBySubtest = computed(() => {
    const counts = {};
    chosen.value.forEach((id) => {
        const question = props.bank.find((item) => item.id === id);
        if (question) counts[question.subtest] = (counts[question.subtest] ?? 0) + 1;
    });

    return counts;
});

const isChosen = (id) => chosen.value.includes(id);

const toggle = (id) => {
    chosen.value = isChosen(id) ? chosen.value.filter((item) => item !== id) : [...chosen.value, id];
};

const move = (index, direction) => {
    const target = index + direction;
    if (target < 0 || target >= chosen.value.length) return;
    const copy = [...chosen.value];
    [copy[index], copy[target]] = [copy[target], copy[index]];
    chosen.value = copy;
};

const remove = (id) => {
    chosen.value = chosen.value.filter((item) => item !== id);
};

const chosenQuestions = computed(() =>
    chosen.value.map((id) => props.bank.find((question) => question.id === id)).filter(Boolean),
);

const form = useForm({ question_ids: [] });

const settings = useForm({
    title: props.tryout.title,
    description: props.tryout.description ?? '',
    duration_minutes: props.tryout.duration_minutes,
    price: props.tryout.price,
    opens_at: props.tryout.opens_at ?? '',
    closes_at: props.tryout.closes_at ?? '',
    is_published: props.tryout.is_published,
});

const saveSettings = () => settings.patch(`/admin/tryouts/${props.tryout.id}`, { preserveScroll: true });

const save = () => {
    form.question_ids = chosen.value;
    form.post(`/admin/tryouts/${props.tryout.id}/questions`, {
        preserveScroll: true,
        data: { ...form.data(), confirm_empty: confirmEmpty.value },
    });
};

const archive = () => {
    if (confirm(`Arsipkan paket "${props.tryout.title}"?`)) {
        router.delete(`/admin/tryouts/${props.tryout.id}`);
    }
};
</script>

<template>
    <AdminLayout>
        <Head :title="`Isi Paket · ${tryout.title}`" />
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ tryout.title }}</h1>
                <p class="mt-1 text-sm text-ink-600">
                    {{ chosen.length }} soal dipilih · {{ tryout.duration_minutes }} menit
                </p>
            </div>
            <div class="flex gap-2">
                <button class="h-11 rounded-xl border border-line bg-white px-4 font-semibold text-danger hover:bg-danger/5" @click="archive">
                    Arsipkan
                </button>
                <button
                    class="h-11 rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                    :disabled="form.processing"
                    @click="save"
                >
                    Simpan soal
                </button>
            </div>
        </div>

        <p v-if="form.errors.question_ids" class="mt-3 text-sm text-danger">{{ form.errors.question_ids }}</p>

        <details class="mt-5 rounded-2xl border border-line bg-white shadow-card">
            <summary class="cursor-pointer px-5 py-4 text-sm font-bold">Pengaturan paket</summary>
            <form class="grid gap-3 border-t border-line p-5 sm:grid-cols-2" @submit.prevent="saveSettings">
                <label class="block text-sm sm:col-span-2">
                    <span class="mb-1 block font-medium">Judul</span>
                    <input v-model="settings.title" class="h-10 w-full rounded-xl border border-line px-3">
                </label>
                <label class="block text-sm sm:col-span-2">
                    <span class="mb-1 block font-medium">Deskripsi</span>
                    <textarea v-model="settings.description" rows="2" class="w-full rounded-xl border border-line px-3 py-2"></textarea>
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium">Durasi (menit)</span>
                    <input v-model.number="settings.duration_minutes" type="number" min="1" max="600" class="h-10 w-full rounded-xl border border-line px-3">
                    <span v-if="settings.errors.duration_minutes" class="text-xs text-danger">{{ settings.errors.duration_minutes }}</span>
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium">Harga (Rp)</span>
                    <input v-model.number="settings.price" type="number" min="0" class="h-10 w-full rounded-xl border border-line px-3">
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium">Buka</span>
                    <input v-model="settings.opens_at" type="datetime-local" class="h-10 w-full rounded-xl border border-line px-3">
                </label>
                <label class="block text-sm">
                    <span class="mb-1 block font-medium">Tutup</span>
                    <input v-model="settings.closes_at" type="datetime-local" class="h-10 w-full rounded-xl border border-line px-3">
                    <span v-if="settings.errors.closes_at" class="text-xs text-danger">{{ settings.errors.closes_at }}</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="settings.is_published" type="checkbox" class="h-4 w-4 rounded border-line">
                    Terbitkan paket
                </label>
                <div class="sm:col-span-2">
                    <button type="submit" class="h-10 rounded-xl border border-line bg-white px-4 font-semibold hover:bg-brand-50 disabled:opacity-50" :disabled="settings.processing">
                        Simpan pengaturan
                    </button>
                </div>
            </form>
        </details>

        <div class="mt-5 grid gap-6 lg:grid-cols-2">
            <section aria-labelledby="bank-title" class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <h2 id="bank-title" class="text-sm font-bold">Bank soal</h2>

                <div class="mt-3 flex flex-wrap gap-2">
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Cari soal…"
                        aria-label="Cari soal"
                        class="h-10 min-w-40 flex-1 rounded-xl border border-line px-3 text-sm focus:border-brand-600 focus:outline-none"
                    >
                    <select v-model="subtestFilter" aria-label="Filter subtes" class="h-10 rounded-xl border border-line px-3 text-sm">
                        <option value="semua">Semua</option>
                        <option v-for="code in subtests(bank)" :key="code" :value="code">{{ code }}</option>
                    </select>
                </div>

                <label class="mt-2 flex items-center gap-2 text-xs text-ink-600">
                    <input v-model="filterOnlyValid" type="checkbox" class="h-4 w-4 rounded border-line">
                    Sembunyikan soal yang pilihannya tidak lengkap (5)
                </label>

                <ul class="mt-3 max-h-[28rem] space-y-2 overflow-y-auto pr-1">
                    <li v-for="question in visible" :key="question.id">
                        <button
                            :class="[
                                'w-full rounded-xl border p-3 text-left text-sm transition-colors duration-150',
                                isChosen(question.id) ? 'border-brand-600 bg-brand-50' : 'border-line hover:bg-brand-50',
                            ]"
                            :aria-pressed="isChosen(question.id)"
                            @click="toggle(question.id)"
                        >
                            <div class="flex items-center gap-2 text-xs">
                                <span class="rounded-full bg-white px-2 py-0.5 font-semibold text-brand-600">{{ question.subtest }}</span>
                                <span class="text-ink-400">v{{ question.version }}</span>
                                <span v-if="!question.valid" class="ml-auto font-semibold text-danger">pilihan kurang</span>
                                <span v-else-if="isChosen(question.id)" class="ml-auto font-semibold text-brand-600">dipilih</span>
                            </div>
                            <p class="mt-1 line-clamp-2">{{ question.body }}</p>
                        </button>
                    </li>
                    <li v-if="visible.length === 0" class="p-6 text-center text-sm text-ink-600">Tidak ada soal cocok.</li>
                </ul>
            </section>

            <section aria-labelledby="chosen-title" class="rounded-2xl border border-line bg-white p-5 shadow-card">
                <div class="flex items-baseline justify-between">
                    <h2 id="chosen-title" class="text-sm font-bold">Urutan soal paket</h2>
                    <span class="text-xs text-ink-400">
                        <template v-for="(count, code) in countBySubtest" :key="code">{{ code }} {{ count }} · </template>
                        total {{ chosen.length }}
                    </span>
                </div>

                <ol class="mt-3 max-h-[28rem] space-y-2 overflow-y-auto pr-1">
                    <li
                        v-for="(question, index) in chosenQuestions"
                        :key="question.id"
                        class="flex items-start gap-2 rounded-xl border border-line p-3 text-sm"
                    >
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white tabular-nums">
                            {{ index + 1 }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-ink-600">{{ question.subtest }}</p>
                            <p class="line-clamp-2">{{ question.body }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col gap-1">
                            <button class="text-xs text-ink-600 hover:text-brand-600 disabled:opacity-30" :disabled="index === 0" :aria-label="`Naikkan soal ${index + 1}`" @click="move(index, -1)">↑</button>
                            <button class="text-xs text-ink-600 hover:text-brand-600 disabled:opacity-30" :disabled="index === chosen.length - 1" :aria-label="`Turunkan soal ${index + 1}`" @click="move(index, 1)">↓</button>
                        </div>
                        <button class="shrink-0 rounded p-1 text-ink-400 hover:text-danger hover:bg-danger/10 transition-colors" :aria-label="`Hapus soal ${index + 1}`" @click="remove(question.id)">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </li>
                    <li v-if="chosen.length === 0" class="p-6 text-center text-sm text-ink-600">
                        Belum ada soal. Pilih dari bank di kiri.
                    </li>
                </ol>

                <label v-if="chosen.length === 0" class="mt-3 flex items-center gap-2 text-xs text-danger">
                    <input v-model="confirmEmpty" type="checkbox" class="h-4 w-4 rounded border-line">
                    Saya sadar paket ini akan kosong
                </label>
            </section>
        </div>
    </AdminLayout>
</template>
