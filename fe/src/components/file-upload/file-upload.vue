<template>
    <div class="file-upload">
        <div :style="'height: '+elementHeight"
            class="drop-box"
        >
            <input :id="inputId"
                :accept="inputAccept"
                :name="inputName + '[]'"
                :disabled="isSaving"
                multiple
                type="file"
                @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length"
            >

            <p v-if="isSaving">
                Uploading {{ fileCount }} files...
            </p>
            <p v-else>
                Drag your file(s) here to begin <br> or click to browse
            </p>
        </div>
    </div>
</template>

<script>
// 3rd party libs
import axios from 'axios';

const status = {
    initial: 0,
    saving: 1,
    success: 2,
    failed: 3
};

export default {
    name: 'file-upload',
    props: {
        inputId: {
            type: String,
            default: ''
        },
        inputAccept: {
            type: String,
            default: ''
        },
        inputName: {
            type: String,
            default: ''
        },
        apiEndPoint: {
            type: String,
            default: ''
        },
        elementHeight: {
            type: String,
            default: '200px'
        }
    },
    data() {
        return {
            currentStatus: null,
            uploadedFiles: [],
            uploadError: null
        };
    },
    computed: {
        isInitial() {
            return this.currentStatus === status.initial;
        },
        isSaving() {
            return this.currentStatus === status.saving;
        },
        isSuccess() {
            return this.currentStatus === status.success;
        },
        isFailed() {
            return this.currentStatus === status.failed;
        }
    },
    mounted() {
        this.reset();
    },
    methods: {
        reset() {
            // reset form to initial state
            this.currentStatus = status.initial;
            this.uploadedFiles = [];
            this.uploadError = null;
        },
        save(formData) {
            this.currentStatus = status.saving;

            axios.post(this.apiEndPoint, formData)
            .then((response) => {
                this.currentStatus = status.success;
                this.$emit('upload-complete', response);

                // Clearing file field with just Javascript as type="file" does not support v-model
                document.querySelector(`#${this.inputId}`).value = '';
            })
            .catch((error) => {
                this.uploadError = error.response;
                this.currentStatus = status.failed;
                this.$emit('upload-complete', error);

                // Clearing file field with just Javascript as type="file" does not support v-model
                document.querySelector(`#${this.inputId}`).value = '';
            });
        },
        filesChange(fieldName, fileList) {
            // Handle file changes
            const formData = new FormData();

            if (!fileList.length) {
                return;
            }

            // Append the files to FormData
            const fileListLength = Array(fileList.length).keys();

            Array.from(fileListLength).map((x) => formData.append(fieldName, fileList[x], fileList[x].name));

            // Save it
            this.save(formData);
        }
    }
}
</script>