<template>
    <div class="tinymce-component">
        <textarea :id="id" />
    </div>
</template>

<script>
// VUE
import Vue from 'vue';

// 3rd party libs
import tinymce from 'tinymce';

// TinyMCE themes
import 'tinymce/themes/modern/theme';

// TinyMCE plugins
import 'tinymce/plugins/code/plugin';
import 'tinymce/plugins/fullscreen/plugin';
import 'tinymce/plugins/lists/plugin';
import 'tinymce/plugins/link/plugin';
import 'tinymce/plugins/autosave/plugin';
import 'tinymce/plugins/image/plugin';
import 'tinymce/plugins/imagetools/plugin';
import 'tinymce/plugins/textcolor/plugin';
import 'tinymce/plugins/colorpicker/plugin';

export default {
    name: 'tinymce',
    props: {
        id: {
            type: String,
            required: true
        },
        value: {
            type: String,
            default: ''
        },
        noParagraph: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            options: {}
        }
    },
    mounted() {
        /* eslint-disable */
        let options = {
            branding: false,
            skin_url: '/vendor/maxtream/themages/dist/styles/tinymce-skins',
            menubar: false,
            toolbar: [
                'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect fontselect fontsizeselect forecolor backcolor | removeformat',
                'undo redo | code | restoredraft | fullscreen | bullist numlist | link unlink image'
            ],
            fontsize_formats: '8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 24px 36px',
            plugins: ['code', 'fullscreen', 'lists', 'link', 'autosave', 'image', 'imagetools', 'textcolor', 'colorpicker'],
            forced_root_block : this.noParagraph ? '' : 'p'
            // https://www.tinymce.com/docs/plugins/imagetools/
        };
        /* eslint-enable */

        // Default configuration
        let s1 = (editor) => {
            this.config(editor);
        }

        if (typeof this.options === 'object') {
            options = Object.assign({}, this.options);

            /* eslint-disable */
            if (!this.options.hasOwnProperty('selector')) {
                options.selector = '#' + this.id;
            }
            /* eslint-enable */

            if (typeof this.options.setup === 'function') {
                s1 = (editor) => {
                    this.config(editor);
                    this.options.setup(editor);
                }
            }
        } else {
            options.selector = '#' + this.id;
        }

        options.setup = (editor) => s1(editor);

        Vue.nextTick(() => tinymce.init(options));
    },
    beforeDestroy() {
        tinymce.execCommand('mceRemoveEditor', false, this.id);
    },
    methods: {
        config(editor) {
            editor.on('NodeChange Change KeyUp', () => {
                this.$emit('input', tinymce.get(this.id).getContent());
                this.$emit('change', tinymce.get(this.id), tinymce.get(this.id).getContent());
            });

            editor.on('init', () => {
                if (this.value) {
                    tinymce.get(this.id).setContent(this.value);
                }

                this.$emit('input', this.value);
            });
        }
    }
}
</script>