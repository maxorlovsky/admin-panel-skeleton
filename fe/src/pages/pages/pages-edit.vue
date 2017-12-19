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
                <span class="fa fa-step-backward"></span> Back to list
            </button>
        </router-link>
    </div>

    <loading v-if="loading"></loading>
    <form method="post" v-on:submit.prevent="submitForm()" v-else>
        <div class="form-group row">
            <label for="meta_title-field" class="col-3 col-form-label">Meta title</label>
            <div class="col-9">
                <input v-model="form.meta_title"
                    :class="{ error: errorClasses.meta_title }"
                    class="form-control"
                    type="text"
                    id="meta_title-field" 
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="meta_description-field" class="col-3 col-form-label">Meta description</label>
            <div class="col-9">
                <input v-model="form.meta_description"
                    :class="{ error: errorClasses.meta_description }"
                    class="form-control"
                    type="text"
                    id="meta_description-field" 
                    maxlength="160"
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="link-field" class="col-3 col-form-label">Link</label>
            <div class="col-9">
                <input v-model="form.link"
                    :class="{ error: errorClasses.link }"
                    class="form-control"
                    type="text"
                    id="link-field" 
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="text-field" class="col-3 col-form-label">Text</label>
            <div class="col-9">
                <tinymce v-model="form.text"
                    :class="{ error: errorClasses.text }"
                    id="text-field"
                ></tinymce>
            </div>
        </div>
        <div class="form-group row">
            <label for="logged_in-field" class="col-3 col-form-label">Logged In only</label>
            <div class="col-9">
                <input v-model="form.logged_in"
                    :class="{ error: errorClasses.logged_in }"
                    type="checkbox"
                    id="logged_in-field"
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="enabled-field" class="col-3 col-form-label">Enabled</label>
            <div class="col-9">
                <input v-model="form.enabled"
                    :class="{ error: errorClasses.enabled }"
                    type="checkbox"
                    id="enabled-field"
                />
            </div>
        </div>

        <button class="btn btn-primary" v-if="add" :disabled="formLoading">Add page</button>
        <button class="btn btn-primary" v-else :disabled="formLoading">Edit page</button>
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
                meta_title: '',
                meta_description: '',
                link: '',
                text: '',
                logged_in: false,
                enabled: false
            },
            errorClasses: {}
        };
    },
    created: function() {
        const self = this;

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
        'multiSiteId': function() {
            if (this.$route.params.id) {
                this.fetchEditData(this.$route.params.id);
            }
        }
    },
    methods: {
        fetchEditData: function(id) {
            const self = this;

            axios.get(`/api/pages/${this.multiSiteId}/${id}`)
            .then(function (response) {
                self.form.meta_title = response.data.page.title;
                self.form.meta_description = response.data.page.description;
                self.form.link = response.data.page.link;
                self.form.text = response.data.page.text;
                self.form.logged_in = response.data.page.logged_in == 1 ? true : false;
                self.form.enabled = response.data.page.enabled == 1 ? true : false;

                self.loading = false;
            })
            .catch(function (error) {
                self.loading = false;
                console.log(error);
            });
        },
        submitForm: function() {
            const self = this;

            this.formLoading = true;

            this.errorClasses = {};

            // Frontend check
            if (!this.form.meta_title || !this.form.link) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'danger');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    meta_title: !this.form.meta_title ? true : false,
                    link: !this.form.link ? true : false
                };

                return false;
            }

            let apiUrl = '/api/pages/add';
            let apiAttributes = {
                meta_title: this.form.meta_title,
                meta_description: this.form.meta_description,
                link: this.form.link,
                text: this.form.text,
                logged_in: this.form.logged_in,
                enabled: this.form.enabled,
                site_id: this.multiSiteId
            };

            if (this.edit) {
                apiUrl = '/api/pages/edit';
                apiAttributes = {
                    id: this.$route.params.id,
                    meta_title: this.form.meta_title,
                    meta_description: this.form.meta_description,
                    link: this.form.link,
                    text: this.form.text,
                    logged_in: this.form.logged_in,
                    enabled: this.form.enabled
                };
            }

            axios.post(apiUrl, apiAttributes)
            .then(function (response) {
                self.$parent.displayMessage(response.data.message, 'success');
                if (self.add) {
                    self.$router.push('/pages');
                }
                self.formLoading = false;
            })
            .catch(function (error) {
                self.formLoading = false;

                // Display error message from API
                self.$parent.displayMessage(error.response.data.message, 'danger');

                let errorFields = error.response.data.fields;
                // In some cases slim return array as json, we need to convert it
                if (errorFields.constructor !== Array) {
                    errorFields = Object.keys(errorFields).map(key => errorFields[key]);
                }

                // Mark fields with error class
                for (let i = 0; i < errorFields.length; ++i) {
                    self.errorClasses[errorFields[i]] = true;
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