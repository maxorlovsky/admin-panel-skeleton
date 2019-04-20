<template>
    <div class="tinymce-component">
        <textarea :id="id"
            :value="value"
        />
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
    mounted() {
        // If value is not set, recheck in 1 second
        if (!this.value) {
            setTimeout(() => {
                if (this.value) {
                    tinymce.get(this.id).setContent(this.value);
                }
            }, 1000);
        }

        this.initiate();
    },
    beforeDestroy () {
        tinymce.execCommand('mceRemoveEditor', false, this.id);
    },
    methods: {
        initiate() {
            /* eslint-disable */
            let options = {
                branding: false,
                skin_url: '/dist/assets/tinymce-skins/lightgray',
                menubar: false,
                toolbar: [
                    'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect fontselect fontsizeselect forecolor backcolor | removeformat',
                    'undo redo | code | restoredraft | fullscreen | bullist numlist | link unlink image'
                ],
                fontsize_formats: '8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 24px 36px',
                plugins: ['code', 'fullscreen', 'lists', 'link', 'autosave', 'image', 'imagetools', 'textcolor', 'colorpicker'],
                forced_root_block: this.noParagraph ? '' : 'p',
                selector: `#${this.id}`,
                init_instance_callback: (editor) => {
                    editor.on('KeyUp', (e) => {
                        this.$emit('input', editor.getContent());
                    });

                    editor.on('Change', (e) => {
                        this.$emit('input', editor.getContent());
                    });
                    
                    editor.setContent(this.value);
                }
            };
            /* eslint-enable */

            Vue.nextTick(() => tinymce.init(options));
        }
    }
};
</script>