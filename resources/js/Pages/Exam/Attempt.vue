<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    attempt: { type: Object, required: true },
    questions: { type: Array, required: true },
});

const index = ref(0);
const answers = ref(props.questions.map((q) => ({
    option_id: q.selected_option_id,
    is_flagged: q.is_flagged,
})));
const remaining = ref(props.attempt.remaining_seconds);
const saveState = ref('idle');
const showGrid = ref(false);
const showSubmitDialog = ref(false);

let timerId = null;
let retryQueue = [];

const current = computed(() => props.questions[index.value]);
const currentAnswer = computed(() => answers.value[index.value]);
const letters = ['A', 'B', 'C', 'D', 'E'];

const answeredCount = computed(() => answers.value.filter((a) => a.option_id !== null).length);
const flaggedCount = computed(() => answers.value.filter((a) => a.is_flagged).length);
const emptyCount = computed(() => answers.value.length - answeredCount.value);

const timeLabel = computed(() => {
    const m = Math.floor(remaining.value / 60).toString().padStart(2, '0');
    const s = (remaining.value % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
});

const timerTone = computed(() => {
    if (remaining.value <= 120) return 'bg-danger text-white';
    if (remaining.value <= 600) return 'bg-warning text-white';
    return 'bg-brand-50 text-ink-900';
});

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content
    || document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1];

const persist = async (questionIndex) => {
    const question = props.questions[questionIndex];
    const answer = answers.value[questionIndex];
    const body = { question_id: question.id, option_id: answer.option_id, is_flagged: answer.is_flagged };

    saveState.value = 'saving';
    try {
        const response = await fetch(props.attempt.answer_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf() ?? '',
            },
            body: JSON.stringify(body),
        });

        if (!response.ok) throw new Error(String(response.status));
        saveState.value = 'saved';
    } catch {
        saveState.value = 'retry';
        retryQueue.push(questionIndex);
    }
};

const flushQueue = () => {
    if (retryQueue.length === 0) return;
    const pending = [...new Set(retryQueue)];
    retryQueue = [];
    pending.forEach(persist);
};

const select = (optionId) => {
    if (currentAnswer.value.option_id === optionId) return;
    currentAnswer.value.option_id = optionId;
    persist(index.value);
};

const toggleFlag = () => {
    currentAnswer.value.is_flagged = !currentAnswer.value.is_flagged;
    persist(index.value);
};

const go = (target) => {
    index.value = Math.min(Math.max(target, 0), props.questions.length - 1);
    showGrid.value = false;
};

const submit = () => {
    router.post(props.attempt.submit_url);
};

const onKeydown = (event) => {
    if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return;

    const key = event.key.toUpperCase();
    const letterIndex = letters.indexOf(key);
    if (letterIndex >= 0 && current.value.options[letterIndex]) {
        select(current.value.options[letterIndex].id);
    } else if (event.key === 'ArrowRight') {
        go(index.value + 1);
    } else if (event.key === 'ArrowLeft') {
        go(index.value - 1);
    }
};

onMounted(() => {
    timerId = setInterval(() => {
        remaining.value = Math.max(0, remaining.value - 1);
        if (remaining.value === 0) {
            clearInterval(timerId);
            submit();
        }
        flushQueue();
    }, 1000);

    window.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    clearInterval(timerId);
    window.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <div class="min-h-screen bg-page">
        <header class="border-b border-line bg-white">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold">{{ attempt.tryout_title }}</p>
                    <p class="text-xs" :class="saveState === 'retry' ? 'text-danger' : 'text-ink-400'" aria-live="polite">
                        {{ saveState === 'saving' ? 'Menyimpan…' : saveState === 'retry' ? 'Mencoba lagi…' : saveState === 'saved' ? 'Tersimpan' : 'Simpan otomatis aktif' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span :class="['rounded-full px-4 py-2 text-xl font-bold tabular-nums', timerTone]">{{ timeLabel }}</span>
                    <button class="h-11 rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700" @click="showSubmitDialog = true">
                        Selesai
                    </button>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <main>
                <p class="text-xs text-ink-400">
                    Soal {{ index + 1 }} dari {{ questions.length }} · {{ current.subtest }}
                </p>
                <div class="rich-text mt-2 text-lg font-semibold leading-relaxed" tabindex="-1" v-html="current.body"></div>
                <img v-if="current.image_path" :src="current.image_path" alt="Gambar soal" class="mt-4 max-h-72 rounded-xl border border-line">

                <ul class="mt-6 space-y-3" role="radiogroup" :aria-label="`Pilihan jawaban soal ${index + 1}`">
                    <li v-for="(option, i) in current.options" :key="option.id">
                        <button
                            :aria-checked="currentAnswer.option_id === option.id"
                            role="radio"
                            :class="[
                                'flex w-full items-center gap-3 rounded-xl border p-4 text-left transition-colors duration-150',
                                currentAnswer.option_id === option.id
                                    ? 'border-brand-600 bg-brand-50'
                                    : 'border-line bg-white hover:bg-brand-50',
                            ]"
                            @click="select(option.id)"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-line font-bold">{{ letters[i] }}</span>
                            <span class="text-[15px]">{{ option.body }}</span>
                        </button>
                    </li>
                </ul>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button class="h-11 rounded-xl border border-line bg-white px-5 font-semibold disabled:opacity-50" :disabled="index === 0" @click="go(index - 1)">
                        Sebelumnya
                    </button>
                    <button
                        :class="['h-11 rounded-xl border px-5 font-semibold', currentAnswer.is_flagged ? 'border-warning bg-warning/10 text-warning' : 'border-line bg-white']"
                        :aria-pressed="currentAnswer.is_flagged"
                        @click="toggleFlag"
                    >
                        Ragu-ragu
                    </button>
                    <button class="h-11 rounded-xl bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="index === questions.length - 1" @click="go(index + 1)">
                        Berikutnya
                    </button>
                    <button class="ml-auto h-11 rounded-xl px-4 font-semibold text-brand-600 lg:hidden" @click="showGrid = true">
                        Nomor soal
                    </button>
                </div>
            </main>

            <aside :class="[
                'rounded-2xl border border-line bg-white p-5 shadow-card',
                'fixed inset-x-0 bottom-0 z-20 max-h-[70vh] overflow-auto transition-transform duration-200 lg:static lg:max-h-none lg:translate-y-0 lg:rounded-2xl',
                showGrid ? 'translate-y-0' : 'translate-y-full lg:translate-y-0',
            ]">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold">Nomor soal</h2>
                    <button class="text-sm text-ink-600 lg:hidden" @click="showGrid = false">Tutup</button>
                </div>
                <ul class="mt-4 grid grid-cols-6 gap-2 lg:grid-cols-5">
                    <li v-for="(question, i) in questions" :key="question.id">
                        <button
                            :class="[
                                'relative flex h-10 w-10 items-center justify-center rounded-lg text-sm font-semibold',
                                i === index ? 'ring-2 ring-brand-600 ring-offset-1' : '',
                                answers[i].is_flagged
                                    ? 'bg-warning text-white'
                                    : answers[i].option_id !== null
                                        ? 'bg-brand-600 text-white'
                                        : 'border border-line bg-white text-ink-900',
                            ]"
                            :aria-label="`Soal ${i + 1}${answers[i].option_id !== null ? ', terjawab' : ', kosong'}${answers[i].is_flagged ? ', ragu-ragu' : ''}`"
                            @click="go(i)"
                        >
                            {{ i + 1 }}
                        </button>
                    </li>
                </ul>
                <div class="mt-5 space-y-1 text-xs text-ink-600">
                    <p>Terjawab: {{ answeredCount }}</p>
                    <p>Ragu-ragu: {{ flaggedCount }}</p>
                    <p>Kosong: {{ emptyCount }}</p>
                </div>
            </aside>
        </div>

        <div v-if="showSubmitDialog" class="fixed inset-0 z-30 flex items-center justify-center bg-ink-900/40 p-4" role="dialog" aria-modal="true" aria-labelledby="submit-title">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-card">
                <h2 id="submit-title" class="text-lg font-bold">Selesai dan nilai?</h2>
                <p class="mt-2 text-sm text-ink-600">
                    Terjawab {{ answeredCount }}, ragu-ragu {{ flaggedCount }}, kosong {{ emptyCount }}.
                </p>
                <div class="mt-6 flex gap-3">
                    <button class="h-11 flex-1 rounded-xl border border-line font-semibold" @click="showSubmitDialog = false">Kembali</button>
                    <button class="h-11 flex-1 rounded-xl bg-brand-600 font-semibold text-white hover:bg-brand-700" @click="submit">Selesai dan Nilai</button>
                </div>
            </div>
        </div>
    </div>
</template>
