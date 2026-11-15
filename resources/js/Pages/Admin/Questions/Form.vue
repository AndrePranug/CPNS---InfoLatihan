<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    subtests: { type: Array, required: true },
    topics: { type: Array, required: true },
    formations: { type: Array, required: true },
    question: { type: Object, default: null },
});

const letters = ['A', 'B', 'C', 'D', 'E'];
const isEdit = computed(() => props.question !== null);

const form = useForm({
    exam_type: props.question?.exam_type ?? 'skd',
    subtest_id: props.question?.subtest_id ?? props.subtests[0]?.id ?? null,
    formation_id: props.question?.formation_id ?? null,
    topic_id: props.question?.topic_id ?? null,
    body: props.question?.body ?? '',
    explanation: props.question?.explanation ?? '',
    difficulty: props.question?.difficulty ?? 2,
    options: (props.question?.options ?? letters.map((label) => ({ label, body: '', is_correct: false, weight: 0 })))
        .map((option) => ({ ...option })),
});

const isSkb = computed(() => form.exam_type === 'skb');
const selectedSubtest = computed(() => props.subtests.find((s) => s.id === Number(form.subtest_id)));
const isTkp = computed(() => !isSkb.value && selectedSubtest.value?.code === 'TKP');
const filteredTopics = computed(() => props.topics.filter((t) => t.subtest_id === Number(form.subtest_id)));

const imageForm = useForm({ image: null });

const uploadImage = () => imageForm.post(`/admin/questions/${props.question.id}/image`, {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => imageForm.reset('image'),
});

const setCorrect = (index) => {
    form.options.forEach((option, i) => {
        option.is_correct = i === index;
    });
};

const submit = () => {
    if (isEdit.value) {
        router.post(`/admin/questions/${props.question.id}/revise`, {}, { preserveScroll: true });
        return;
    }

    form.post('/admin/questions');
};
</script>

<template>
    <AdminLayout>
        <Head :title="isEdit ? 'Revisi Soal' : 'Tambah Soal'" />

        <div class="mb-4">
            <Link href="/admin/questions" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 hover:underline">
                &larr; Kembali ke Bank Soal
            </Link>
        </div>

        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-ink-900">
                {{ isEdit ? `Revisi Butir Soal (v${question.version})` : 'Buat Butir Soal Baru' }}
            </h1>
            <p v-if="isEdit" class="text-xs text-ink-600 leading-relaxed max-w-2xl">
                Menyimpan revisi akan membuat versi baru (v{{ question.version + 1 }}). Versi sebelumnya tetap diarsipkan secara otomatis agar integritas riwayat penilaian peserta tetap konsisten.
            </p>
            <p v-else class="text-xs text-ink-600">
                Lengkapi informasi soal, kunci jawaban yang benar, serta materi pembahasan.
            </p>
        </div>

        <form class="mt-6 grid gap-6 lg:grid-cols-2" @submit.prevent="submit">
            <!-- Left Column: Question Details -->
            <div class="space-y-4 rounded-2xl border border-line bg-surface p-6 shadow-card">
                <h2 class="text-sm font-bold text-ink-900 border-b border-line/60 pb-3">Parameter Butir Soal</h2>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="exam_type" class="mb-1 block text-xs font-semibold text-ink-900">Jenis Soal</label>
                        <select id="exam_type" v-model="form.exam_type" class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 focus:border-brand-600 focus:outline-none">
                            <option value="skd">SKD (Umum)</option>
                            <option value="skb">SKB (Formasi)</option>
                        </select>
                        <p v-if="form.errors.exam_type" class="mt-1 text-xs text-danger">{{ form.errors.exam_type }}</p>
                    </div>

                    <div v-if="isSkb">
                        <label for="formation" class="mb-1 block text-xs font-semibold text-ink-900">Formasi SKB</label>
                        <select id="formation" v-model="form.formation_id" class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 focus:border-brand-600 focus:outline-none">
                            <option :value="null">— Pilih Formasi —</option>
                            <option v-for="formation in formations" :key="formation.id" :value="formation.id">{{ formation.name }}</option>
                        </select>
                        <p v-if="form.errors.formation_id" class="mt-1 text-xs text-danger">{{ form.errors.formation_id }}</p>
                    </div>
                    <div v-else>
                        <label for="subtest" class="mb-1 block text-xs font-semibold text-ink-900">Subtes SKD</label>
                        <select id="subtest" v-model="form.subtest_id" class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 focus:border-brand-600 focus:outline-none">
                            <option v-for="subtest in subtests" :key="subtest.id" :value="subtest.id">{{ subtest.code }} — {{ subtest.name }}</option>
                        </select>
                        <p v-if="form.errors.subtest_id" class="mt-1 text-xs text-danger">{{ form.errors.subtest_id }}</p>
                    </div>
                </div>

                <div v-if="!isSkb">
                    <label for="topic" class="mb-1 block text-xs font-semibold text-ink-900">Topik Materi</label>
                    <select id="topic" v-model="form.topic_id" class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 focus:border-brand-600 focus:outline-none">
                        <option :value="null">— Tanpa Topik Khusus —</option>
                        <option v-for="topic in filteredTopics" :key="topic.id" :value="topic.id">{{ topic.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-ink-900">Isi Teks Soal</label>
                    <RichTextEditor v-model="form.body" placeholder="Tuliskan butir pertanyaan di sini…" label="Isi soal" />
                    <p class="mt-1 text-[11px] text-ink-400">Mendukung format teks tebal, miring, rumus (superscript/subscript), dan daftar.</p>
                    <p v-if="form.errors.body" class="mt-1 text-xs text-danger font-medium">{{ form.errors.body }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-ink-900">Pembahasan &amp; Kunci Solusi</label>
                    <RichTextEditor v-model="form.explanation" placeholder="Tuliskan ulasan materi dan solusi jawaban…" label="Pembahasan" />
                </div>

                <div>
                    <label for="difficulty" class="mb-1 block text-xs font-semibold text-ink-900">Tingkat Kesulitan</label>
                    <select id="difficulty" v-model="form.difficulty" class="h-10 w-full rounded-xl border border-line bg-surface px-3 text-xs font-medium text-ink-900 focus:border-brand-600 focus:outline-none">
                        <option :value="1">1 — Mudah</option>
                        <option :value="2">2 — Sedang</option>
                        <option :value="3">3 — Sulit</option>
                    </select>
                </div>

                <!-- Question Image Section -->
                <div class="border-t border-line/60 pt-4 space-y-2">
                    <p class="text-xs font-semibold text-ink-900">Ilustrasi Gambar (Opsional)</p>
                    <template v-if="isEdit">
                        <img v-if="question.image_path" :src="question.image_path" alt="Gambar Soal" class="mt-2 max-h-40 rounded-xl border border-line p-1 bg-surface-raised">
                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/webp"
                            aria-label="Unggah gambar soal"
                            class="block w-full text-xs text-ink-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700"
                            @input="imageForm.image = $event.target.files[0]"
                        >
                        <p class="text-[11px] text-ink-400">JPG, PNG, atau WEBP maks 2 MB.</p>
                        <p v-if="imageForm.errors.image" class="text-xs text-danger">{{ imageForm.errors.image }}</p>
                        <button
                            type="button"
                            class="h-9 rounded-xl border border-line bg-surface px-4 text-xs font-semibold text-ink-700 hover:bg-brand-50 transition-colors disabled:opacity-50"
                            :disabled="!imageForm.image || imageForm.processing"
                            @click="uploadImage"
                        >
                            Unggah Gambar Baru
                        </button>
                    </template>
                    <p v-else class="text-[11px] text-ink-400 bg-surface-raised p-3 rounded-xl border border-line">
                        Gambar dapat diunggah setelah butir soal disimpan (melalui menu Revisi Soal).
                    </p>
                </div>
            </div>

            <!-- Right Column: Options & Submit -->
            <div class="space-y-4 rounded-2xl border border-line bg-surface p-6 shadow-card flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="border-b border-line/60 pb-3 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-ink-900">Pilihan Jawaban</h2>
                        <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700">
                            {{ isTkp ? 'Skala Bobot 1–5' : 'Pilih 1 Kunci Benar' }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(option, index) in form.options"
                            :key="option.label"
                            :class="[
                                'rounded-xl border p-3 transition-colors',
                                option.is_correct ? 'border-success/40 bg-success/5' : 'border-line bg-surface-raised'
                            ]"
                        >
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-line bg-surface font-bold text-xs text-ink-900">
                                    {{ option.label }}
                                </span>
                                <input
                                    v-model="option.body"
                                    :placeholder="`Pilihan ${option.label}…`"
                                    :aria-label="`Isi pilihan ${option.label}`"
                                    class="h-9 w-full rounded-lg border border-line bg-surface px-3 text-xs text-ink-900 focus:border-brand-600 focus:outline-none"
                                >
                            </div>

                            <div class="mt-2.5 flex items-center gap-4 pl-9 text-xs">
                                <label v-if="!isTkp" class="flex items-center gap-2 cursor-pointer font-medium text-ink-700 select-none">
                                    <input
                                        type="radio"
                                        name="correct"
                                        :checked="option.is_correct"
                                        class="h-4 w-4 text-brand-600 focus:ring-brand-600"
                                        @change="setCorrect(index)"
                                    >
                                    <span :class="option.is_correct && 'font-bold text-success'">Kunci Jawaban Benar</span>
                                </label>
                                <label v-if="isTkp" class="flex items-center gap-2 font-medium text-ink-700">
                                    <span>Bobot Nilai (1–5):</span>
                                    <input
                                        v-model.number="option.weight"
                                        type="number"
                                        min="0"
                                        max="5"
                                        class="h-8 w-16 rounded-lg border border-line bg-surface px-2 text-center text-xs font-bold tabular-nums text-ink-900 focus:border-brand-600 focus:outline-none"
                                    >
                                </label>
                            </div>
                        </div>
                    </div>

                    <p v-if="form.errors.options" class="text-xs text-danger font-medium">{{ form.errors.options }}</p>
                    <p v-if="form.errors.question" class="text-xs text-danger font-medium">{{ form.errors.question }}</p>
                </div>

                <div class="pt-4 border-t border-line/60">
                    <button
                        type="submit"
                        class="w-full h-11 rounded-xl bg-brand-600 font-semibold text-xs text-white shadow-sm hover:bg-brand-700 active:scale-[0.98] transition-all disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan…' : (isEdit ? `Simpan sebagai Versi Baru (v${question.version + 1})` : 'Simpan Butir Soal') }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
