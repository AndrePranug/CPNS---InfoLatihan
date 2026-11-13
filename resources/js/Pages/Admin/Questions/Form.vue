<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    subtests: { type: Array, required: true },
    topics: { type: Array, required: true },
    question: { type: Object, default: null },
});

const letters = ['A', 'B', 'C', 'D', 'E'];
const isEdit = computed(() => props.question !== null);

const form = useForm({
    subtest_id: props.question?.subtest_id ?? props.subtests[0]?.id ?? null,
    topic_id: props.question?.topic_id ?? null,
    body: props.question?.body ?? '',
    explanation: props.question?.explanation ?? '',
    difficulty: props.question?.difficulty ?? 2,
    options: (props.question?.options ?? letters.map((label) => ({ label, body: '', is_correct: false, weight: 0 })))
        .map((option) => ({ ...option })),
});

const selectedSubtest = computed(() => props.subtests.find((s) => s.id === Number(form.subtest_id)));
const isTkp = computed(() => selectedSubtest.value?.code === 'TKP');
const filteredTopics = computed(() => props.topics.filter((t) => t.subtest_id === Number(form.subtest_id)));

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
                        <label for="subtest" class="mb-1 block text-sm font-medium">Subtes</label>
                        <select id="subtest" v-model="form.subtest_id" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                            <option v-for="subtest in subtests" :key="subtest.id" :value="subtest.id">{{ subtest.code }} — {{ subtest.name }}</option>
                        </select>
                        <p v-if="form.errors.subtest_id" class="mt-1 text-xs text-danger">{{ form.errors.subtest_id }}</p>
                    </div>
                    <div>
                        <label for="topic" class="mb-1 block text-sm font-medium">Topik</label>
                        <select id="topic" v-model="form.topic_id" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                            <option :value="null">— tanpa topik —</option>
                            <option v-for="topic in filteredTopics" :key="topic.id" :value="topic.id">{{ topic.name }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="body" class="mb-1 block text-sm font-medium">Isi soal</label>
                    <textarea id="body" v-model="form.body" rows="6" class="w-full rounded-xl border border-line bg-white p-3 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none"></textarea>
                    <p v-if="form.errors.body" class="mt-1 text-xs text-danger">{{ form.errors.body }}</p>
                </div>

                <div>
                    <label for="explanation" class="mb-1 block text-sm font-medium">Pembahasan</label>
                    <textarea id="explanation" v-model="form.explanation" rows="4" class="w-full rounded-xl border border-line bg-white p-3 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 focus:outline-none"></textarea>
                </div>

                <div>
                    <label for="difficulty" class="mb-1 block text-sm font-medium">Tingkat kesulitan</label>
                    <select id="difficulty" v-model="form.difficulty" class="h-11 w-full rounded-xl border border-line bg-white px-3">
                        <option :value="1">Mudah</option>
                        <option :value="2">Sedang</option>
                        <option :value="3">Sulit</option>
                    </select>
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
