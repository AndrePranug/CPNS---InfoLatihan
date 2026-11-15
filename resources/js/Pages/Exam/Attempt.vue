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
    if (remaining.value <= 120) return 'bg-danger/10 text-danger border-danger/30';
    if (remaining.value <= 600) return 'bg-warning/10 text-warning border-warning/30';
    return 'bg-brand-50 text-brand-700 border-brand-200';
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
    <div class="min-h-screen bg-page text-ink-900 pb-16">
        <!-- Sticky Exam Header -->
        <header class="sticky top-0 z-20 border-b border-line/80 bg-surface/95 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-ink-900">{{ attempt.tryout_title }}</p>
                    <div class="flex items-center gap-2 text-xs">
                        <span
                            :class="[
                                'inline-block h-2 w-2 rounded-full',
                                saveState === 'saving' ? 'bg-amber-400 animate-pulse' :
                                saveState === 'retry' ? 'bg-danger animate-ping' :
                                saveState === 'saved' ? 'bg-success' : 'bg-brand-600'
                            ]"
                        ></span>
                        <span :class="saveState === 'retry' ? 'text-danger font-medium' : 'text-ink-400'" aria-live="polite">
                            {{ saveState === 'saving' ? 'Menyimpan jawaban…' : saveState === 'retry' ? 'Gagal menyimpan, mencoba lagi…' : saveState === 'saved' ? 'Jawaban tersimpan' : 'Autosave aktif' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Timer Badge -->
                    <div :class="['flex items-center gap-2 rounded-xl border px-3.5 py-1.5 font-bold tracking-wider tabular-nums transition-colors', timerTone]">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 shrink-0" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <span class="text-lg">{{ timeLabel }}</span>
                    </div>

                    <button
                        class="h-10 rounded-xl bg-brand-600 px-4 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all"
                        @click="showSubmitDialog = true"
                    >
                        Selesai Ujian
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Exam Workspace -->
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1fr)_300px]">
            <!-- Question & Options (Left) -->
            <main class="space-y-6">
                <!-- Question Card -->
                <div class="rounded-2xl border border-line bg-surface p-6 sm:p-8 shadow-card">
                    <!-- Question Meta Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line/60 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-600">
                                Soal {{ index + 1 }} / {{ questions.length }}
                            </span>
                            <span class="rounded-lg bg-surface-raised px-2.5 py-1 text-xs font-semibold text-ink-600">
                                {{ current.subtest }}
                            </span>
                        </div>
                        <span v-if="currentAnswer.is_flagged" class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 border border-amber-200">
                            <svg class="h-3.5 w-3.5 text-amber-600 fill-amber-500" viewBox="0 0 24 24">
                                <path d="M4 2v20h2v-8h8l1 2h7V4h-9l-1-2H4z"/>
                            </svg>
                            <span>Ragu-ragu</span>
                        </span>
                    </div>

                    <!-- Question Body -->
                    <div class="rich-text mt-5 text-[16px] leading-relaxed text-ink-900 font-medium" tabindex="-1" v-html="current.body"></div>

                    <!-- Question Image (if any) -->
                    <div v-if="current.image_path" class="mt-4 overflow-hidden rounded-xl border border-line bg-surface-raised p-2">
                        <img :src="current.image_path" alt="Ilustrasi Soal" class="max-h-80 w-auto rounded-lg mx-auto object-contain">
                    </div>

                    <!-- Options List -->
                    <ul class="mt-6 space-y-2.5" role="radiogroup" :aria-label="`Pilihan jawaban soal nomor ${index + 1}`">
                        <li v-for="(option, i) in current.options" :key="option.id">
                            <button
                                :aria-checked="currentAnswer.option_id === option.id"
                                role="radio"
                                :class="[
                                    'group flex w-full items-center gap-3.5 rounded-xl border p-3.5 sm:p-4 text-left transition-all duration-150 active:scale-[0.99]',
                                    currentAnswer.option_id === option.id
                                        ? 'border-brand-600 bg-brand-50/70 shadow-xs ring-1 ring-brand-600/30'
                                        : 'border-line bg-surface hover:bg-surface-raised hover:border-ink-400/40'
                                ]"
                                @click="select(option.id)"
                            >
                                <span
                                    :class="[
                                        'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold transition-colors',
                                        currentAnswer.option_id === option.id
                                            ? 'bg-brand-600 text-white shadow-xs'
                                            : 'border border-line bg-surface-raised text-ink-600 group-hover:bg-brand-50 group-hover:text-brand-600'
                                    ]"
                                >
                                    {{ letters[i] }}
                                </span>
                                <span class="text-[14px] leading-relaxed text-ink-900 flex-1">{{ option.body }}</span>
                                <span v-if="currentAnswer.option_id === option.id" class="text-brand-600">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Action / Navigation Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button
                            class="h-10 rounded-xl border border-line bg-surface px-4 text-xs font-semibold text-ink-600 hover:bg-brand-50 active:scale-[0.98] transition-all disabled:opacity-40 disabled:pointer-events-none"
                            :disabled="index === 0"
                            @click="go(index - 1)"
                        >
                            &larr; Sebelumnya
                        </button>
                        <button
                            :class="[
                                'h-10 rounded-xl border px-4 text-xs font-semibold active:scale-[0.98] transition-all',
                                currentAnswer.is_flagged
                                    ? 'border-amber-300 bg-amber-50 text-amber-700'
                                    : 'border-line bg-surface text-ink-600 hover:bg-brand-50'
                            ]"
                            :aria-pressed="currentAnswer.is_flagged"
                            @click="toggleFlag"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" :class="currentAnswer.is_flagged ? 'text-amber-600 fill-amber-500' : 'text-ink-400 fill-none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 0 1 2-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 0 0-2 2zm9-13.5V9" />
                                </svg>
                                <span>{{ currentAnswer.is_flagged ? 'Ditandai Ragu' : 'Tandai Ragu-ragu' }}</span>
                            </span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            class="h-10 rounded-xl bg-brand-600 px-5 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-40 disabled:pointer-events-none"
                            :disabled="index === questions.length - 1"
                            @click="go(index + 1)"
                        >
                            Berikutnya &rarr;
                        </button>
                        <button
                            class="h-10 rounded-xl border border-line bg-surface px-3 text-xs font-semibold text-brand-600 lg:hidden hover:bg-brand-50"
                            @click="showGrid = true"
                        >
                            Daftar Soal
                        </button>
                    </div>
                </div>
            </main>

            <!-- Question Number Grid (Desktop Sidebar & Mobile Drawer) -->
            <aside
                :class="[
                    'rounded-2xl border border-line bg-surface p-5 shadow-card',
                    'fixed inset-x-0 bottom-0 z-30 max-h-[80vh] overflow-y-auto transition-transform duration-200 lg:static lg:max-h-none lg:translate-y-0 lg:rounded-2xl',
                    showGrid ? 'translate-y-0 shadow-2xl' : 'translate-y-full lg:translate-y-0'
                ]"
            >
                <div class="flex items-center justify-between pb-3 border-b border-line/60">
                    <h2 class="text-sm font-bold text-ink-900">Navigasi Soal</h2>
                    <button class="inline-flex items-center gap-1 text-xs font-semibold text-ink-500 hover:text-ink-900 lg:hidden" @click="showGrid = false">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        <span>Tutup</span>
                    </button>
                </div>

                <!-- Summary Status Badges -->
                <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="rounded-lg bg-brand-50 p-2 text-brand-700">
                        <p class="font-bold tabular-nums text-sm">{{ answeredCount }}</p>
                        <p class="text-[10px]">Dijawab</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-2 text-amber-700">
                        <p class="font-bold tabular-nums text-sm">{{ flaggedCount }}</p>
                        <p class="text-[10px]">Ragu</p>
                    </div>
                    <div class="rounded-lg bg-surface-raised p-2 text-ink-400">
                        <p class="font-bold tabular-nums text-sm">{{ emptyCount }}</p>
                        <p class="text-[10px]">Kosong</p>
                    </div>
                </div>

                <!-- Number Grid -->
                <ul class="mt-4 grid grid-cols-5 gap-1.5" aria-label="Daftar nomor soal">
                    <li v-for="(question, i) in questions" :key="question.id">
                        <button
                            :class="[
                                'relative flex h-9 w-full items-center justify-center rounded-lg text-xs font-bold transition-all duration-150',
                                i === index ? 'ring-2 ring-brand-600 ring-offset-1 z-10' : '',
                                answers[i].is_flagged
                                    ? 'bg-amber-400 text-white shadow-xs'
                                    : answers[i].option_id !== null
                                        ? 'bg-brand-600 text-white shadow-xs'
                                        : 'border border-line bg-surface-raised text-ink-600 hover:bg-brand-50'
                            ]"
                            :aria-label="`Soal ${i + 1}${answers[i].option_id !== null ? ', terjawab' : ', belum dijawab'}${answers[i].is_flagged ? ', ragu-ragu' : ''}`"
                            @click="go(i)"
                        >
                            {{ i + 1 }}
                        </button>
                    </li>
                </ul>

                <div class="mt-4 pt-3 border-t border-line/60 text-[11px] text-ink-400 text-center">
                    Gunakan tombol keyboard <kbd class="px-1 py-0.5 rounded bg-surface-raised border border-line text-ink-600">A</kbd>-<kbd class="px-1 py-0.5 rounded bg-surface-raised border border-line text-ink-600">E</kbd> &amp; panah kiri/kanan.
                </div>
            </aside>
        </div>

        <!-- Backdrop for mobile question grid -->
        <div v-if="showGrid" class="fixed inset-0 z-20 bg-ink-900/30 lg:hidden" @click="showGrid = false"></div>

        <!-- Submit Confirmation Modal -->
        <div v-if="showSubmitDialog" class="fixed inset-0 z-40 flex items-center justify-center bg-ink-900/50 p-4 backdrop-blur-xs" role="dialog" aria-modal="true" aria-labelledby="submit-title">
            <div class="w-full max-w-sm rounded-2xl border border-line bg-surface p-6 shadow-2xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>

                <h2 id="submit-title" class="mt-4 text-center text-lg font-bold text-ink-900">Selesaikan Sesi Ujian?</h2>
                <p class="mt-1.5 text-center text-xs text-ink-600">
                    Setelah dikirim, jawaban tidak dapat diubah lagi dan skor langsung dihitung.
                </p>

                <div class="my-4 rounded-xl bg-surface-raised p-3 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-ink-600">Soal Terjawab:</span>
                        <span class="font-bold text-success">{{ answeredCount }} Soal</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-600">Ditandai Ragu-ragu:</span>
                        <span class="font-bold text-amber-600">{{ flaggedCount }} Soal</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-600">Belum Terjawab:</span>
                        <span class="font-bold text-danger">{{ emptyCount }} Soal</span>
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <button
                        class="h-10 flex-1 rounded-xl border border-line text-xs font-semibold text-ink-600 hover:bg-brand-50 transition-colors"
                        @click="showSubmitDialog = false"
                    >
                        Periksa Kembali
                    </button>
                    <button
                        class="h-10 flex-1 rounded-xl bg-brand-600 text-xs font-semibold text-white shadow-sm hover:bg-brand-700 transition-colors"
                        @click="submit"
                    >
                        Kirim &amp; Nilai
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
