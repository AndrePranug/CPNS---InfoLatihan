<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    questions: { type: Array, required: true },
    subtests: { type: Array, required: true },
    topics: { type: Array, required: true },
    formations: { type: Array, required: true },
    filters: { type: Object, required: true },
});

const index = ref(0);
const chosen = ref(null);
const result = ref(null);
const checking = ref(false);
const stats = ref({ benar: 0, salah: 0 });
const answered = ref(new Set());

const current = computed(() => props.questions[index.value]);
const letters = ['A', 'B', 'C', 'D', 'E'];
const finished = computed(() => index.value >= props.questions.length);

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const filterMode = ref(props.filters.mode ?? 'skd');
const filterSubtest = ref(props.filters.subtest ?? '');
const filterTopic = ref(props.filters.topic ?? '');
const filterFormation = ref(props.filters.formation ?? '');

const applyFilter = () => {
    const query = filterMode.value === 'skb'
        ? { mode: 'skb', formation: filterFormation.value || undefined }
        : {
            mode: 'skd',
            subtest: filterSubtest.value || undefined,
            topic: filterTopic.value || undefined,
        };

    router.get('/practice', query, { preserveState: false });
};

const visibleTopics = computed(() => (
    filterSubtest.value
        ? props.topics.filter((topic) => topic.subtest === filterSubtest.value)
        : props.topics
));

const pick = async (optionId) => {
    if (result.value || checking.value) return;

    chosen.value = optionId;
    checking.value = true;

    try {
        const response = await fetch(`/practice/questions/${current.value.id}/check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf(),
            },
            body: JSON.stringify({ option_id: optionId }),
        });

        if (!response.ok) throw new Error(String(response.status));

        result.value = await response.json();
        answered.value.add(current.value.id);
        stats.value[result.value.is_correct ? 'benar' : 'salah']++;
    } catch {
        chosen.value = null;
    } finally {
        checking.value = false;
    }
};

const next = () => {
    result.value = null;
    chosen.value = null;
    index.value++;
};

const restart = () => {
    index.value = 0;
    result.value = null;
    chosen.value = null;
    stats.value = { benar: 0, salah: 0 };
    answered.value = new Set();
    router.reload({ only: ['questions'] });
};

const optionClass = (option) => {
    if (!result.value) {
        return chosen.value === option.id ? 'border-brand-600 bg-brand-50' : 'border-line bg-white hover:bg-brand-50';
    }

    if (option.id === result.value.correct_option_id) return 'border-success bg-success/10 text-success font-semibold';
    if (option.id === chosen.value) return 'border-danger bg-danger/10 text-danger';

    return 'border-line bg-white opacity-70';
};

watch(filterSubtest, () => {
    filterTopic.value = '';
});

watch(filterMode, () => {
    filterSubtest.value = '';
    filterTopic.value = '';
    filterFormation.value = '';
});
</script>

<template>
    <AppLayout>
        <Head title="Latihan" />
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold">Latihan</h1>
                <p class="mt-1 text-sm text-ink-600">Tanpa timer. Pilih jawaban, langsung lihat kunci dan pembahasan.</p>
            </div>
            <p class="text-sm text-ink-600 tabular-nums">
                Benar <span class="font-bold text-success">{{ stats.benar }}</span> ·
                Salah <span class="font-bold text-danger">{{ stats.salah }}</span>
            </p>
        </div>

        <form class="mt-5 flex flex-wrap items-end gap-2 rounded-2xl border border-line bg-white p-4 shadow-card" @submit.prevent="applyFilter">
            <label class="text-sm">
                <span class="mb-1 block font-medium">Jenis</span>
                <select v-model="filterMode" class="h-10 rounded-xl border border-line px-3">
                    <option value="skd">SKD</option>
                    <option value="skb">SKB per formasi</option>
                </select>
            </label>
            <template v-if="filterMode === 'skd'">
                <label class="text-sm">
                    <span class="mb-1 block font-medium">Subtes</span>
                    <select v-model="filterSubtest" class="h-10 rounded-xl border border-line px-3">
                        <option value="">Semua</option>
                        <option v-for="subtest in subtests" :key="subtest.code" :value="subtest.code">{{ subtest.code }} — {{ subtest.name }}</option>
                    </select>
                </label>
                <label class="text-sm">
                    <span class="mb-1 block font-medium">Topik</span>
                    <select v-model="filterTopic" class="h-10 rounded-xl border border-line px-3">
                        <option value="">Semua</option>
                        <option v-for="topic in visibleTopics" :key="topic.id" :value="topic.id">{{ topic.subtest }} · {{ topic.name }}</option>
                    </select>
                </label>
            </template>
            <label v-else class="text-sm">
                <span class="mb-1 block font-medium">Formasi</span>
                <select v-model="filterFormation" class="h-10 rounded-xl border border-line px-3">
                    <option value="">Semua formasi</option>
                    <option v-for="formation in formations" :key="formation.id" :value="formation.id">{{ formation.name }} ({{ formation.question_count }})</option>
                </select>
            </label>
            <button type="submit" class="h-10 rounded-xl bg-brand-600 px-4 font-semibold text-white hover:bg-brand-700">Terapkan</button>
            <Link href="/practice" class="h-10 rounded-xl border border-line px-4 py-2 text-sm font-semibold hover:bg-brand-50">Reset</Link>
        </form>

        <p v-if="questions.length === 0" class="mt-6 rounded-2xl border border-line bg-white p-6 text-sm text-ink-600">
            Belum ada soal {{ filterMode === 'skb' ? 'SKB' : 'SKD' }} untuk filter ini.
        </p>

        <section v-else-if="!finished" class="mt-6 rounded-2xl border border-line bg-white p-6 shadow-card">
            <p class="text-xs text-ink-400">
                Soal {{ index + 1 }} dari {{ questions.length }} · {{ current.label }}
                <span v-if="current.topic"> · {{ current.topic }}</span>
            </p>
            <div class="rich-text mt-2 text-lg font-semibold leading-relaxed" v-html="current.body"></div>
            <img v-if="current.image_path" :src="current.image_path" alt="Gambar soal" class="mt-4 max-h-72 rounded-xl border border-line">

            <ul class="mt-6 space-y-3" role="radiogroup" :aria-label="`Pilihan jawaban soal ${index + 1}`">
                <li v-for="(option, i) in current.options" :key="option.id">
                    <button
                        type="button"
                        role="radio"
                        :aria-checked="chosen === option.id"
                        :disabled="Boolean(result) || checking"
                        :class="['flex w-full items-center gap-3 rounded-xl border p-4 text-left transition-colors duration-150 disabled:cursor-default', optionClass(option)]"
                        @click="pick(option.id)"
                    >
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-current font-bold">{{ letters[i] }}</span>
                        <span class="text-[15px]">{{ option.body }}</span>
                    </button>
                </li>
            </ul>

            <div v-if="result" role="status" class="mt-5 rounded-xl p-4 text-sm" :class="result.is_correct ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'">
                <p class="font-bold">{{ result.is_correct ? 'Benar' : 'Belum tepat' }}</p>
                <div v-if="result.explanation" class="rich-text mt-1 text-ink-600" v-html="result.explanation"></div>
            </div>

            <div class="mt-6 flex gap-3">
                <button
                    class="h-11 rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                    :disabled="!result"
                    @click="next"
                >
                    Soal berikutnya
                </button>
            </div>
        </section>

        <section v-else class="mt-6 rounded-2xl border border-line bg-white p-8 text-center shadow-card">
            <h2 class="text-2xl font-bold">Sesi selesai</h2>
            <p class="mt-2 text-sm text-ink-600">Benar {{ stats.benar }} dari {{ questions.length }} soal.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <button class="h-11 rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700" @click="restart">Soal baru</button>
                <Link href="/tryouts" class="inline-flex h-11 items-center rounded-xl border border-line px-5 font-semibold">Coba tryout penuh</Link>
            </div>
        </section>
    </AppLayout>
</template>
