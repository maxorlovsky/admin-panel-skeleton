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
            <label for="title-field" class="col-2 col-form-label">Title/Link</label>
            <div class="col-10">
                <input v-model="form.title"
                    :class="{ error: errorClasses.title }"
                    class="form-control"
                    type="text"
                    id="title-field"
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="name-field" class="col-2 col-form-label">Page name</label>
            <div class="col-10">
                <input v-model="form.name"
                    :class="{ error: errorClasses.name }"
                    class="form-control"
                    type="text"
                    id="name-field"
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="text-field" class="col-2 col-form-label">Page text</label>
            <div class="col-10">
                <vue-tinymce v-model="form.text"
                    :class="{ error: errorClasses.text }"
                    class="form-control"
                    type="text"
                    id="text-field"
                    height="300"
                ></vue-tinymce>
            </div>
        </div>

        <button class="btn btn-primary" v-if="add" :disabled="formLoading">Add page</button>
        <button class="btn btn-success" v-else :disabled="formLoading">Edit page</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../loading/loading.vue';

// 3rd party libs
import axios from 'axios';
import vueTinymce from '@deveodk/vue-tinymce';

const pagesEditPage = {
    components: {
        loading
    },
    data: function() {
        return {
            add: false,
            edit: false,
            loading: true,
            formLoading: false,
            form: {
                title: '',
                name: '',
                text: ''
            },
            errorClasses: {}
        };
    },
    created: function() {
        // Define if we add or edit
        if (this.$route.params.id) {
            this.edit = true;
            this.fetchEditData(this.$route.params.id);
        } else {
            this.add = true;
            this.loading = false;
        }
    },
    methods: {
        fetchEditData: function(id) {
            const self = this;

            axios.get(`/api/pages/${id}`)
            .then(function (response) {
                self.form.login = response.data.admin.login;
                self.form.email = response.data.admin.email;
                self.form.level = response.data.admin.level;

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
            if (this.add && (!this.form.login || !this.form.password || !this.form.level)) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'danger');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    login: !this.form.login ? true : false,
                    password: !this.form.password ? true : false,
                    email: false,
                    level: !this.form.level ? true : false
                };

                return false;
            }

            let apiUrl = '/api/pages/add';
            let apiAttributes = {
                login: this.form.login,
                password: this.form.password,
                email: this.form.email,
                level: this.form.level
            };

            if (this.edit) {
                apiUrl = '/api/pages/edit';
                apiAttributes = {
                    id: this.$route.params.id,
                    password: this.form.password,
                    email: this.form.email,
                    level: this.form.level
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