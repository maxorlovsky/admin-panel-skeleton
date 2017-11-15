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
                    <option v-for="level in maxLevel"
                        :value="level"
                        v-bind:key="level"
                    >{{level}}</option>
                    <option value="0">Custom</option>
                </select>
            </div>
        </div>
        <div class="form-group row" v-if="form.level == 0">
            <label for="permissions-field" class="col-2 col-form-label">Access permissions</label>
            <div class="col-10">
                <div v-for="permission in permissions"
                    :key="permission.key"
                    class="inline-element"
                >
                    <label :for="'permission'+permission.key">
                        <input type="checkbox"
                            :value="permission.key"
                            :id="'permission'+permission.key"
                            :disabled="permission.key === defaultPage"
                            v-model="form.permissions"
                        />
                        {{permission.name}}
                    </label>

                    <label v-for="subpermission in permission.subCategories"
                        :key="subpermission.key"
                        :for="'permission'+subpermission.key"
                        class="inline-element"
                    >
                        <input type="checkbox"
                            :value="subpermission.key"
                            :id="'permission'+subpermission.key"
                            :disabled="subpermission.key === defaultPage"
                            v-model="form.permissions"
                        />
                        {{subpermission.name}}
                    </label>
                </div>
            </div>
        </div>

        <button class="btn btn-primary" v-if="add" :disabled="formLoading">Add admin</button>
        <button class="btn btn-primary" v-else :disabled="formLoading">Edit admin</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

// Website custom config
import websiteConfig from '../../../../../../../mocms/config.json';

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
            defaultPage: 'dashboard',
            form: {
                login: '',
                password: '',
                email: '',
                level: 1,
                permissions: ['dashboard']
            },
            permissions: [],
            maxLevel: websiteConfig.maxLevel,
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
            this.fetchAddData();
        }
    },
    methods: {
        fetchEditData: function(id) {
            const self = this;

            axios.all([
                axios.get(`/api/users/${id}`),
                axios.get('/api/permissions'),
            ])
            .then(axios.spread((
                userData,
                permissionsData
            ) => {
                self.form.login = userData.data.admin.login;
                self.form.email = userData.data.admin.email;
                self.form.level = userData.data.admin.level;
                self.form.permissions = (userData.data.admin.custom_access ? userData.data.admin.custom_access : [self.defaultPage]);

                self.permissions = permissionsData.data.permissions;

                self.loading = false;
            }))
            .catch((error) => {
                self.$parent.authRequiredState(error);
                self.$parent.displayMessage('Error, during the process of updating user data, please repeat the process or re-login', 'danger');
                console.log('Error fetching user resources: ' + error);
            });
        },
        fetchAddData: function() {
            const self = this;

            axios.get('/api/permissions')
            .then(function (response) {
                self.permissions = response.data.permissions;
                self.loading = false;
            })
            .catch(function (error) {
                self.$parent.authRequiredState(error);
                self.loading = false;
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
                    level: !this.form.level ? true : false,
                    permissions: false
                };

                return false;
            }

            let apiUrl = '/api/users/add';
            let apiAttributes = {
                login: this.form.login,
                password: this.form.password,
                email: this.form.email,
                level: this.form.level,
                permissions: this.form.permissions
            };

            if (this.edit) {
                apiUrl = '/api/users/edit';
                apiAttributes = {
                    id: this.$route.params.id,
                    password: this.form.password,
                    email: this.form.email,
                    level: this.form.level,
                    permissions: this.form.permissions
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