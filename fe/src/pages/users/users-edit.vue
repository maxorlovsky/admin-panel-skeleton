<template>
<section class="users">
    <div class="heading">
        <h2>
            <span v-if="add">Add</span>
            <span v-else>Edit</span>
            user
        </h2>
        <router-link to="/users">
            <button class="btn btn-info">
                <span class="fa fa-step-backward"></span> Back to list
            </button>
        </router-link>
    </div>

    <loading v-if="loading"></loading>
    <form method="post" v-on:submit.prevent="submitForm()" v-else>
        <div class="form-group row">
            <label for="login-field" class="col-2 col-form-label">Login</label>
            <div class="col-10">
                <input v-model="form.login"
                    :class="{ error: errorClasses.login }"
                    :readonly="edit"
                    class="form-control"
                    type="text"
                    id="login-field" 
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="password-field" class="col-2 col-form-label">Password</label>
            <div class="col-10">
                <input v-model="form.password"
                    :class="{ error: errorClasses.password }"
                    class="form-control"
                    type="password"
                    id="password-field"
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="email-field" class="col-2 col-form-label">Email</label>
            <div class="col-10">
                <input v-model="form.email"
                    :class="{ error: errorClasses.email }"
                    class="form-control"
                    type="email"
                    id="email-field"
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="level-field" class="col-2 col-form-label">Access level</label>
            <div class="col-10">
                <select v-model="form.level"
                    :class="{ error: errorClasses.level }"
                    class="form-control"
                    id="level-field"
                >
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                </select>
            </div>
        </div>

        <button class="btn btn-primary" v-if="add" :disabled="formLoading">Add admin</button>
        <button class="btn btn-success" v-else :disabled="formLoading">Edit admin</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const usersEditPage = {
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
                login: '',
                password: '',
                email: '',
                level: 1
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

            axios.get(`/api/users/${id}`)
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

            let apiUrl = '/api/users/add';
            let apiAttributes = {
                login: this.form.login,
                password: this.form.password,
                email: this.form.email,
                level: this.form.level
            };

            if (this.edit) {
                apiUrl = '/api/users/edit';
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
                    self.$router.push('/users');
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
        path: '/users/add',
        component: usersEditPage,
        meta: {
            title: 'Add user',
            loggedIn: true
        }
    },
    {
        path: '/users/edit/:id',
        component: usersEditPage,
        meta: {
            title: 'Edit user',
            loggedIn: true
        }
    }
);

export default usersEditPage;
</script>