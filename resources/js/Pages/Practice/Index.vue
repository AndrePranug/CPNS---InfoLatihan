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
        return chosen.value === option.id
            ? 'border-brand-600 bg-brand-50 shadow-xs'
            : 'border-line bg-surface hover:bg-surface-raised';
    }

    if (option.id === result.value.correct_option_id) {
        return 'border-success bg-success/10 text-success font-semibold shadow-xs';
    }
    if (option.id === chosen.value) {
        return 'border-danger bg-danger/10 text-danger font-medium';
    }

    return 'border-line bg-surface-raised opacity-60';
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
        <Head title="Latihan Soal" />

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink-900">Latihan Soal Mandiri</h1>
                <p class="mt-1 text-sm text-ink-600">Latihan santai tanpa batas timer. Jawaban benar dan pembahasan langsung tampil seketika.</p>
            </div>
            
            <!-- Live Score Ticker -->
            <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-2 text-xs shadow-card self-start sm:self-auto">
                <span class="flex items-center gap-1.5 font-semibold text-ink-600">
                    <span class="h-2 w-2 rounded-full bg-success"></span> Benar: <strong class="text-success tabular-nums text-sm">{{ stats.benar }}</strong>
                </span>
                <span class="h-3 w-px bg-line"></span>
                <span class="flex items-center gap-1.5 font-semibold text-ink-600">
                    <span class="h-2 w-2 rounded-full bg-danger"></span> Salah: <strong class="text-danger tabular-nums text-sm">{{ stats.salah }}</strong>
                </span>
            </div>
        </div>

        <!-- Filter Controls -->
        <form class="mt-6 flex flex-wrap items-end gap-3 rounded-2xl border border-line bg-surface p-4 sm:p-5 shadow-card" @submit.prevent="applyFilter">
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-ink-600">Jenis Soal</label>
                <select v-model="filterMode" class="h-10 rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 outline-none focus:border-brand-600">
                    <option value="skd">SKD (Umum)</option>
                    <option value="skb">SKB (per Formasi)</option>
                </select>
            </div>

            <template v-if="filterMode === 'skd'">
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-ink-600">Subtes</label>
                    <select v-model="filterSubtest" class="h-10 rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 outline-none focus:border-brand-600">
                        <option value="">Semua Subtes</option>
                        <option v-for="subtest in subtests" :key="subtest.code" :value="subtest.code">{{ subtest.code }} — {{ subtest.name }}</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-ink-600">Topik Materi</label>
                    <select v-model="filterTopic" class="h-10 rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 outline-none focus:border-brand-600">
                        <option value="">Semua Topik</option>
                        <option v-for="topic in visibleTopics" :key="topic.id" :value="topic.id">{{ topic.subtest }} · {{ topic.name }}</option>
                    </select>
                </div>
            </template>
            <div v-else class="space-y-1">
                <label class="block text-xs font-semibold text-ink-600">Formasi Jabatan</label>
                <select v-model="filterFormation" class="h-10 rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 outline-none focus:border-brand-600">
                    <option value="">Semua Formasi</option>
                    <option v-for="formation in formations" :key="formation.id" :value="formation.id">{{ formation.name }} ({{ formation.question_count }} Soal)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 mt-2 sm:mt-0">
                <button type="submit" class="h-10 rounded-xl bg-brand-600 px-4 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 transition-colors">
                    Terapkan Filter
                </button>
                <Link href="/practice" class="h-10 inline-flex items-center justify-center rounded-xl border border-line px-3 text-xs font-semibold text-ink-600 hover:bg-brand-50 transition-colors">
                    Reset
                </Link>
            </div>
        </form>

        <!-- No Questions Found -->
        <div v-if="questions.length === 0" class="mt-6 rounded-2xl border border-line bg-surface p-12 text-center text-xs text-ink-600 shadow-card">
            Belum ada butir soal {{ filterMode === 'skb' ? 'SKB' : 'SKD' }} yang cocok dengan filter yang dipilih. Silakan pilih subtes atau topik lain.
        </div>

        <!-- Question View (Not Finished) -->
        <section v-else-if="!finished" class="mt-6 rounded-2xl border border-line bg-surface p-6 sm:p-8 shadow-card space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line/60 pb-4">
                <div class="flex items-center gap-2">
                    <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-600">
                        Soal {{ index + 1 }} dari {{ questions.length }}
                    </span>
                    <span class="rounded-lg bg-surface-raised px-2.5 py-1 text-xs font-semibold text-ink-600">
                        {{ current.label }}
                    </span>
                </div>
                <span v-if="current.topic" class="text-xs text-ink-400 font-medium">
                    Topik: {{ current.topic }}
                </span>
            </div>

            <!-- Body -->
            <div class="rich-text text-[16px] leading-relaxed text-ink-900 font-medium" v-html="current.body"></div>

            <!-- Image (if any) -->
            <img v-if="current.image_path" :src="current.image_path" alt="Gambar Soal" class="max-h-72 rounded-xl border border-line bg-surface-raised p-2">

            <!-- Options -->
            <ul class="space-y-2.5" role="radiogroup" :aria-label="`Pilihan jawaban nomor ${index + 1}`">
                <li v-for="(option, i) in current.options" :key="option.id">
                    <button
                        type="button"
                        role="radio"
                        :aria-checked="chosen === option.id"
                        :disabled="Boolean(result) || checking"
                        :class="['flex w-full items-center gap-3.5 rounded-xl border p-4 text-left transition-all duration-150', optionClass(option)]"
                        @click="pick(option.id)"
                    >
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-current text-xs font-bold">
                            {{ letters[i] }}
                        </span>
                        <span class="text-sm leading-relaxed flex-1">{{ option.body }}</span>
                    </button>
                </li>
            </ul>

            <!-- Instant Validation Banner -->
            <div
                v-if="result"
                role="status"
                :class="[
                    'rounded-2xl border p-5 transition-all text-xs space-y-2',
                    result.is_correct ? 'border-success/30 bg-success/10 text-success' : 'border-danger/30 bg-danger/10 text-danger'
                ]"
            >
                <div class="flex items-center gap-2 font-bold text-sm">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-current text-white text-xs">
                        <svg v-if="result.is_correct" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        <svg v-else class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </span>
                    <span>{{ result.is_correct ? 'Jawaban Anda Benar!' : 'Jawaban Belum Tepat' }}</span>
                </div>
                <div v-if="result.explanation" class="rich-text text-ink-900 leading-relaxed border-t border-current/20 pt-2" v-html="result.explanation"></div>
            </div>

            <!-- Next button -->
            <div class="flex justify-end pt-2">
                <button
                    class="h-11 rounded-xl bg-brand-600 px-6 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-40 disabled:pointer-events-none"
                    :disabled="!result"
                    @click="next"
                >
                    Lanjut ke Soal Berikutnya &rarr;
                </button>
            </div>
        </section>

        <!-- Session Finished View -->
        <section v-else class="mt-6 rounded-3xl border border-line bg-surface p-10 text-center shadow-card space-y-4">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-success/10 text-success">
                <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-ink-900">Sesi Latihan Selesai!</h2>
            <p class="text-sm text-ink-600">
                Anda telah menjawab <strong class="text-success">{{ stats.benar }} benar</strong> dan <strong class="text-danger">{{ stats.salah }} salah</strong> dari total {{ questions.length }} butir soal.
            </p>
            <div class="pt-4 flex flex-wrap justify-center gap-3">
                <button class="h-11 rounded-xl bg-brand-600 px-6 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 transition-colors" @click="restart">
                    Latihan Soal Baru
                </button>
                <Link href="/tryouts" class="inline-flex h-11 items-center justify-center rounded-xl border border-line bg-surface px-6 text-xs font-semibold text-ink-900 hover:bg-brand-50 transition-colors">
                    Coba Tryout Penuh
                </Link>
            </div>
        </section>
    </AppLayout>
</template>
