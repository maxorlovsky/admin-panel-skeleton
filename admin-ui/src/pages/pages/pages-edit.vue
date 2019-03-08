<template>
<section class="pages">
    <div class="heading">
        <h2>
            <span v-if="add">Add</span>
            <span v-else>Edit</span>
            page
        </h2>
        <router-link to="/pages">
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
            <label for="title-field"
                class="col-3 col-form-label"
            >Title</label>
            <div class="col-9">
                <input id="title-field"
                    v-model="form.title"
                    :class="{ error: errorClasses.title }"
                    class="form-control"
                    type="text"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="metaTitle-field"
                class="col-3 col-form-label"
            >Meta title</label>
            <div class="col-9">
                <input id="metaTitle-field"
                    v-model="form.metaTitle"
                    :class="{ error: errorClasses.metaTitle }"
                    class="form-control"
                    type="text"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="metaDescription-field"
                class="col-3 col-form-label"
            >Meta description</label>
            <div class="col-9">
                <input id="metaDescription-field"
                    v-model="form.metaDescription"
                    :class="{ error: errorClasses.metaDescription }"
                    class="form-control"
                    type="text"
                    maxlength="160"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="link-field"
                class="col-3 col-form-label"
            >Link</label>
            <div class="col-9">
                <input id="link-field"
                    v-model="form.link"
                    :class="{ error: errorClasses.link }"
                    class="form-control"
                    type="text"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="text-field"
                class="col-3 col-form-label"
            >Text</label>
            <div class="col-9">
                <tinymce id="text-field"
                    v-model="form.text"
                    :class="{ error: errorClasses.text }"
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="loggedIn-field"
                class="col-3 col-form-label"
            >Logged In only</label>
            <div class="col-9">
                <input id="loggedIn-field"
                    v-model="form.loggedIn"
                    :class="{ error: errorClasses.loggedIn }"
                    type="checkbox"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="enabled-field"
                class="col-3 col-form-label"
            >Enabled</label>
            <div class="col-9">
                <input id="enabled-field"
                    v-model="form.enabled"
                    :class="{ error: errorClasses.enabled }"
                    type="checkbox"
                >
            </div>
        </div>

        <button v-if="add"
            :disabled="formLoading"
            class="btn btn-primary"
        >Add page</button>
        <button v-else
            :disabled="formLoading"
            class="btn btn-primary"
        >Edit page</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';
import tinymce from '../../components/tinymce/tinymce.vue';

// 3rd party libs
import axios from 'axios';

const pagesEditPage = {
    components: {
        loading,
        tinymce
    },
    props: {
        multiSiteId: Number
    },
    data: function() {
        return {
            add: false,
            edit: false,
            loading: true,
            formLoading: false,
            form: {
                title: '',
                metaTitle: '',
                metaDescription: '',
                link: '',
                text: '',
                loggedIn: false,
                enabled: false
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
            axios.get(`/api/pages/${id}`)
            .then((response) => {
                this.form.title = response.data.page.title;
                this.form.metaTitle = response.data.page.meta_title;
                this.form.metaDescription = response.data.page.meta_description;
                this.form.link = response.data.page.link;
                this.form.text = response.data.page.text;
                this.form.loggedIn = response.data.page.logged_in === 1;
                this.form.enabled = response.data.page.enabled === 1;

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
            if (!this.form.metaTitle || !this.form.link) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'error');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    metaTitle: !this.form.metaTitle,
                    link: !this.form.link
                };

                return false;
            }

            let apiUrl = '/api/pages/add';
            let apiAttributes = {
                title: this.form.title,
                metaTitle: this.form.metaTitle,
                metaDescription: this.form.metaDescription,
                link: this.form.link,
                text: this.form.text,
                loggedIn: this.form.loggedIn,
                enabled: this.form.enabled,
                siteId: this.multiSiteId
            };

            if (this.edit) {
                apiUrl = '/api/pages/edit';
                apiAttributes = {
                    id: this.$route.params.id,
                    title: this.form.title,
                    metaTitle: this.form.metaTitle,
                    metaDescription: this.form.metaDescription,
                    link: this.form.link,
                    text: this.form.text,
                    loggedIn: this.form.loggedIn,
                    enabled: this.form.enabled
                };
            }

            axios.post(apiUrl, apiAttributes)
            .then((response) => {
                this.$parent.displayMessage(response.data.message, 'success');

                if (this.add) {
                    this.$router.push('/pages');
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
        path: '/pages/add',
        component: pagesEditPage,
        meta: {
            title: 'Add page',
            loggedIn: true
        }
    },
    {
        path: '/pages/edit/:id',
        component: pagesEditPage,
        meta: {
            title: 'Edit page',
            loggedIn: true
        }
    }
);

export default pagesEditPage;
</script>