<template>
<section class="labels">
    <div class="heading">
        <h2>
            <span v-if="add">Add</span>
            <span v-else>Edit</span>
            label
        </h2>
        <router-link to="/labels">
            <button class="btn btn-info">
                <span class="fa fa-step-backward"/> Back to list
            </button>
        </router-link>
    </div>

    <loading v-if="loading"/>
    <form v-else
        method="post"
        @submit.prevent="submitForm()"
    >
        <div class="form-group row">
            <label for="name-field"
                class="col-3 col-form-label"
            >
                Name
                <span class="small">No spaces or dashes, use underscore "_"</span>
            </label>
            <div class="col-9">
                <input id="name-field"
                    v-model="form.name"
                    :class="{ error: errorClasses.name }"
                    class="form-control"
                    type="text"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="output-field"
                class="col-3 col-form-label"
            >
                Text
                <span class="small">Unlike in other places, this text won't be wrapper in "paragraph" tag</span>
            </label>
            <div class="col-9">
                <tinymce id="output-field"
                    v-model="form.output"
                    :class="{ error: errorClasses.output }"
                    :no-paragraph="true"
                />
            </div>
        </div>

        <button v-if="add"
            :disabled="formLoading"
            class="btn btn-primary"
        >Add label</button>
        <button v-else
            :disabled="formLoading"
            class="btn btn-primary"
        >Edit label</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';
import tinymce from '../../components/tinymce/tinymce.vue';

// 3rd party libs
import axios from 'axios';

const labelsEditPage = {
    components: {
        loading,
        tinymce
    },
    props: {
        multiSiteId: Number
    },
    data() {
        return {
            add: false,
            edit: false,
            loading: true,
            formLoading: false,
            form: {
                name: '',
                output: ''
            },
            errorClasses: {}
        };
    },
    created() {
        // Define if we add or edit
        if (this.$route.params.id) {
            this.edit = true;
            this.fetchEditData(this.$route.params.id);
        } else {
            this.add = true;
            this.loading = false;
        }
    },
    watch: {
        'multiSiteId'() {
            if (this.$route.params.id) {
                this.fetchEditData(this.$route.params.id);
            }
        }
    },
    methods: {
        fetchEditData(id) {
            axios.get(`/api/labels/${id}`)
            .then((response) => {
                this.form.name = response.data.label.name;
                this.form.output = response.data.label.output;

                this.loading = false;
            })
            .catch((error) => {
                this.loading = false;
                console.log(error);
            });
        },
        submitForm() {
            this.formLoading = true;

            this.errorClasses = {};

            // Frontend check
            if (!this.form.name) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'error');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    name: !this.form.name
                };

                return false;
            }

            let apiUrl = '/api/labels/add';
            let apiAttributes = {
                name: this.form.name,
                output: this.form.output,
                siteId: this.multiSiteId
            };

            if (this.edit) {
                apiUrl = '/api/labels/edit';
                apiAttributes = {
                    id: this.$route.params.id,
                    name: this.form.name,
                    output: this.form.output
                };
            }

            axios.post(apiUrl, apiAttributes)
            .then((response) => {
                this.$parent.displayMessage(response.data.message, 'success');

                if (this.add) {
                    this.$router.push('/labels');
                }

                this.formLoading = false;
            })
            .catch((error) => {
                this.formLoading = false;

                // Display error message from API
                this.$parent.displayMessage(error.response.data.message, 'error');

                let errorFields = error.response.data.fields;

                // In some cases slim return array as json, we need to convert it
                if (errorFields.constructor !== Array) {
                    errorFields = Object.keys(errorFields).map((key) => errorFields[key]);
                }

                // Mark fields with error class
                for (let i = 0; i < errorFields.length; ++i) {
                    this.errorClasses[errorFields[i]] = true;
                }
            });
        }
    }
};

// Routing
mo.routes.push(
    {
        path: '/labels/add',
        component: labelsEditPage,
        meta: {
            title: 'Add label',
            loggedIn: true
        }
    },
    {
        path: '/labels/edit/:id',
        component: labelsEditPage,
        meta: {
            title: 'Edit label',
            loggedIn: true
        }
    }
);

export default labelsEditPage;
</script>