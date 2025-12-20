<template>
    <div>
        <textarea
            ref="summernote"
            :value="modelValue"
            class="summernote-editor">
        </textarea>
    </div>
</template>

<script>
import $ from 'jquery';
import 'summernote/dist/summernote-lite.min.js';
import 'summernote/dist/summernote-lite.min.css';

// Đảm bảo jQuery global cho Summernote
window.jQuery = window.$ = $;

export default {
    name: 'SummernoteEditor',
    props: {
        modelValue: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: 'Enter your content here...'
        },
        height: {
            type: Number,
            default: 200
        },
        config: {
            type: Object,
            default: () => ({})
        }
    },
    emits: ['update:modelValue'],
    mounted() {
        this.initSummernote();
    },
    beforeUnmount() {
        this.destroySummernote();
    },
    methods: {
        initSummernote() {
            const defaultConfig = {
                height: this.height,
                placeholder: this.placeholder,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: (contents) => {
                        this.$emit('update:modelValue', contents);
                    },
                    onPaste: (e) => {
                        // Clean pasted content
                        const bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                        e.preventDefault();
                        document.execCommand('insertText', false, bufferText);
                    }
                }
            };

            const finalConfig = { ...defaultConfig, ...this.config };

            $(this.$refs.summernote).summernote(finalConfig);

            // Set initial value
            if (this.modelValue) {
                $(this.$refs.summernote).summernote('code', this.modelValue);
            }
        },
        destroySummernote() {
            if ($(this.$refs.summernote).summernote) {
                $(this.$refs.summernote).summernote('destroy');
            }
        },
        getValue() {
            return $(this.$refs.summernote).summernote('code');
        },
        setValue(value) {
            $(this.$refs.summernote).summernote('code', value);
        }
    }
}
</script>

<style scoped>
.summernote-editor {
    border: none;
}

/* Override Summernote styles */
:global(.note-editor.note-frame) {
    border: none !important;
    border-radius: 0.375rem;
}

:global(.note-toolbar) {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.375rem 0.375rem 0 0;
}

:global(.note-editing-area) {
    background-color: white;
}

:global(.note-editable) {
    padding: 15px;
    min-height: 150px;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.5;
}

:global(.note-editable:focus) {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
