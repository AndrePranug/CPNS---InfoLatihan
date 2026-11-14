<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
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
        <h1 class="text-2xl font-bold">{{ isEdit ? `Revisi Soal (v${question.version})` : 'Tambah Soal' }}</h1>
        <p v-if="isEdit" class="mt-1 text-sm text-ink-600">
            Menyimpan akan membuat versi {{ question.version + 1 }}. Versi lama diarsipkan agar skor peserta tidak berubah.
            Versi ini bisa disalin ke paket lewat menu Paket Tryout.
        </p>

        <form class="mt-6 grid gap-6 lg:grid-cols-2" @submit.prevent="submit">
            <div class="space-y-4 rounded-2xl border border-line bg-white p-6 shadow-card">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="exam_type" class="mb-1 block text-sm font-medium">Jenis soal</label>
                        <select id="exam_type" v-model="form.exam_type" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                            <option value="skd">SKD</option>
                            <option value="skb">SKB</option>
                        </select>
                        <p v-if="form.errors.exam_type" class="mt-1 text-xs text-danger">{{ form.errors.exam_type }}</p>
                    </div>
                    <div v-if="isSkb">
                        <label for="formation" class="mb-1 block text-sm font-medium">Formasi SKB</label>
                        <select id="formation" v-model="form.formation_id" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                            <option :value="null">— pilih formasi —</option>
                            <option v-for="formation in formations" :key="formation.id" :value="formation.id">{{ formation.name }}</option>
                        </select>
                        <p v-if="form.errors.formation_id" class="mt-1 text-xs text-danger">{{ form.errors.formation_id }}</p>
                    </div>
                    <div v-else>
                        <label for="subtest" class="mb-1 block text-sm font-medium">Subtes</label>
                        <select id="subtest" v-model="form.subtest_id" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                            <option v-for="subtest in subtests" :key="subtest.id" :value="subtest.id">{{ subtest.code }} — {{ subtest.name }}</option>
                        </select>
                        <p v-if="form.errors.subtest_id" class="mt-1 text-xs text-danger">{{ form.errors.subtest_id }}</p>
                    </div>
                </div>

                <div v-if="!isSkb">
                    <label for="topic" class="mb-1 block text-sm font-medium">Topik</label>
                    <select id="topic" v-model="form.topic_id" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                        <option :value="null">— tanpa topik —</option>
                        <option v-for="topic in filteredTopics" :key="topic.id" :value="topic.id">{{ topic.name }}</option>
                    </select>
                </div>

                <div>
                    <p class="mb-1 block text-sm font-medium">Isi soal</p>
                    <RichTextEditor v-model="form.body" placeholder="Tulis isi soal di sini…" label="Isi soal" />
                    <p class="mt-1 text-xs text-ink-400">Bold, miring, garis bawah, superskrip/subskrip untuk rumus, dan daftar.</p>
                    <p v-if="form.errors.body" class="mt-1 text-xs text-danger">{{ form.errors.body }}</p>
                </div>

                <div>
                    <p class="mb-1 block text-sm font-medium">Pembahasan</p>
                    <RichTextEditor v-model="form.explanation" placeholder="Tulis pembahasan…" label="Pembahasan" />
                </div>

                <div>
                    <label for="difficulty" class="mb-1 block text-sm font-medium">Tingkat kesulitan</label>
                    <select id="difficulty" v-model="form.difficulty" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                        <option :value="1">Mudah</option>
                        <option :value="2">Sedang</option>
                        <option :value="3">Sulit</option>
                    </select>
                </div>

                <div class="border-t border-line pt-4">
                    <p class="text-sm font-medium">Gambar soal</p>
                    <template v-if="isEdit">
                        <img v-if="question.image_path" :src="question.image_path" alt="Gambar soal saat ini" class="mt-2 max-h-40 rounded-xl border border-line">
                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/webp"
                            aria-label="Unggah gambar soal"
                            class="mt-2 block w-full text-sm"
                            @input="imageForm.image = $event.target.files[0]"
                        >
                        <p class="mt-1 text-xs text-ink-400">JPG, PNG, atau WEBP. Maks 2 MB. Mengunggah akan mengganti gambar lama.</p>
                        <p v-if="imageForm.errors.image" class="mt-1 text-xs text-danger">{{ imageForm.errors.image }}</p>
                        <button
                            type="button"
                            class="mt-2 h-10 rounded-xl border border-line bg-white px-4 text-sm font-semibold hover:bg-brand-50 disabled:opacity-50"
                            :disabled="!imageForm.image || imageForm.processing"
                            @click="uploadImage"
                        >
                            Unggah gambar
                        </button>
                    </template>
                    <p v-else class="mt-1 text-xs text-ink-400">Simpan soal dulu, lalu unggah gambar dari halaman revisi.</p>
                </div>
            </div>

            <div class="space-y-3 rounded-2xl border border-line bg-white p-6 shadow-card">
                <p class="text-sm font-medium">
                    Pilihan jawaban · {{ isTkp ? 'bobot 1–5 per pilihan' : 'tandai satu kunci' }}
                </p>
                <div v-for="(option, index) in form.options" :key="option.label" class="rounded-xl border border-line p-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-line font-bold">{{ option.label }}</span>
                        <input v-model="option.body" :aria-label="`Isi pilihan ${option.label}`" class="h-10 w-full rounded-lg border border-line px-3 text-[15px] focus:border-brand-600 focus:outline-none">
                    </div>
                    <div class="mt-2 flex items-center gap-4 pl-11 text-sm">
                        <label v-if="!isTkp" class="flex items-center gap-2">
                            <input type="radio" name="correct" :checked="option.is_correct" @change="setCorrect(index)">
                            Kunci
                        </label>
                        <label v-if="isTkp" class="flex items-center gap-2">
                            Bobot
                            <input v-model.number="option.weight" type="number" min="0" max="5" class="h-8 w-16 rounded-lg border border-line px-2 tabular-nums">
                        </label>
                    </div>
                </div>
                <p v-if="form.errors.options" class="text-xs text-danger">{{ form.errors.options }}</p>
                <p v-if="form.errors.question" class="text-xs text-danger">{{ form.errors.question }}</p>
                <button type="submit" class="h-11 w-full rounded-xl bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:opacity-50" :disabled="form.processing">
                    {{ isEdit ? `Simpan sebagai v${question.version + 1}` : 'Simpan soal' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
