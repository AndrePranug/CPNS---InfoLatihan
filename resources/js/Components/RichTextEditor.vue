<script setup>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    label: { type: String, default: 'Editor teks kaya' },
});

const emit = defineEmits(['update:modelValue']);

const host = ref(null);
let quill = null;

onMounted(() => {
    quill = new Quill(host.value, {
        theme: 'snow',
        placeholder: props.placeholder,
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ script: 'super' }, { script: 'sub' }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['clean'],
            ],
        },
    });

    if (props.modelValue) {
        quill.clipboard.dangerouslyPasteHTML(props.modelValue, 'silent');
    }

    quill.on('text-change', () => {
        emit('update:modelValue', quill.root.innerHTML);
    });
});

watch(
    () => props.modelValue,
    (value) => {
        if (!quill || quill.hasFocus() || value === quill.root.innerHTML) {
            return;
        }

        if (value) {
            quill.clipboard.dangerouslyPasteHTML(value, 'silent');
        } else {
            quill.setText('');
        }
    },
);

onBeforeUnmount(() => {
    quill = null;
});
</script>

<template>
    <div class="rich-text-editor" :aria-label="label" role="group">
        <div ref="host"></div>
    </div>
</template>
