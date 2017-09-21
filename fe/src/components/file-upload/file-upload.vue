<template>
    <div class="file-upload">
        <div class="drop-box" :style="'height: '+elementHeight">
            <input type="file"
                :id="inputId"
                :accept="inputAccept"
                :name="inputName"
                :disabled="isSaving"
                @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length"
                multiple
            />

            <p v-if="isSaving">
                Uploading {{ fileCount }} files...
            </p>
            <p v-else>
                Drag your file(s) here to begin<br /> or click to browse
            </p>
        </div>
    </div>
</template>

<script>
// 3rd party libs
import axios from 'axios';

const STATUS_INITIAL = 0, STATUS_SAVING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3;

export default {
    name: 'file-upload',
    props: {
        inputId: String,
        inputAccept: String,
        inputName: String,
        apiEndPoint: String,
        elementHeight: {
            type: String,
            default: '200px'
        }
	},
   	data: function() {
   		return {
            currentStatus: null,
            uploadedFiles: [],
            uploadError: null,
        };
	},
    computed: {
        isInitial() {
            return this.currentStatus === STATUS_INITIAL;
        },
        isSaving() {
            return this.currentStatus === STATUS_SAVING;
        },
        isSuccess() {
            return this.currentStatus === STATUS_SUCCESS;
        },
        isFailed() {
            return this.currentStatus === STATUS_FAILED;
        }
    },
    methods: {
        reset() {
            // reset form to initial state
            this.currentStatus = STATUS_INITIAL;
            this.uploadedFiles = [];
            this.uploadError = null;
        },
        save(formData) {
            const self = this;

            this.currentStatus = STATUS_SAVING;

            axios.post(this.apiEndPoint, formData)
            .then(function (response) {
                //response.map(img => Object.assign({}, img, { url: `/images/${img.id}` }))
                console.log(response);
                //self.uploadedFiles = [].concat(x);
                self.currentStatus = STATUS_SUCCESS;
            })
            .catch(function (error) {
                self.uploadError = err.response;
                self.currentStatus = STATUS_FAILED;
            });
        },
        filesChange(fieldName, fileList) {
            // handle file changes
            const formData = new FormData();

            if (!fileList.length) {
                return;
            }

            // append the files to FormData
            Array.from(Array(fileList.length).keys()).map(x => {
                formData.append(fieldName, fileList[x], fileList[x].name);
            });

            // save it
            this.save(formData);
        }
    },
	mounted() {
        this.reset();
    }
}
</script>