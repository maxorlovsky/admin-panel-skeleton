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
            <label for="login-field"
                class="col-2 col-form-label"
            >Login</label>
            <div class="col-10">
                <input id="login-field"
                    v-model="form.login"
                    :class="{ error: errorClasses.login }"
                    :readonly="edit"
                    class="form-control"
                    type="text"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="password-field"
                class="col-2 col-form-label"
            >Password</label>
            <div class="col-10">
                <input id="password-field"
                    v-model="form.password"
                    :class="{ error: errorClasses.password }"
                    class="form-control"
                    type="password"
                >
            </div>
        </div>

        <div class="form-group row">
            <label for="email-field"
                class="col-2 col-form-label"
            >Email</label>
            <div class="col-10">
                <input id="email-field"
                    v-model="form.email"
                    :class="{ error: errorClasses.email }"
                    class="form-control"
                    type="email"
                >
            </div>
        </div>
        <div class="form-group row">
            <label for="level-field"
                class="col-2 col-form-label"
            >Access level</label>
            <div class="col-10">
                <select id="level-field"
                    v-model="form.level"
                    :class="{ error: errorClasses.level }"
                    class="form-control"
                >
                    <option v-for="level in maxLevel"
                        :value="level"
                        :key="level"
                    >{{ level }}</option>
                    <option value="0">Custom</option>
                </select>
            </div>
        </div>

        <div v-if="form.level == 0"
            class="form-group row"
        >
            <label for="permissions-field"
                class="col-2 col-form-label"
            >Access permissions</label>

            <div class="col-10">
                <div v-for="permission in permissions"
                    :key="permission.key"
                    class="inline-element"
                >
                    <label :for="'permission'+permission.key">
                        <input v-model="form.permissions"
                            :value="permission.key"
                            :id="'permission'+permission.key"
                            :disabled="permission.key === defaultPage"
                            type="checkbox"
                        >
                        {{ permission.name }}
                    </label>

                    <label v-for="subpermission in permission.subCategories"
                        :key="subpermission.key"
                        :for="'permission' + subpermission.key"
                        class="inline-element"
                    >
                        <input v-model="form.permissions"
                            :value="subpermission.key"
                            :id="'permission'+subpermission.key"
                            :disabled="subpermission.key === defaultPage"
                            type="checkbox"
                        >
                        {{ subpermission.name }}
                    </label>
                </div>
            </div>
        </div>

        <button v-if="add"
            :disabled="formLoading"
            class="btn btn-primary"
        >Add admin</button>

        <button v-else
            :disabled="formLoading"
            class="btn btn-primary"
        >Edit admin</button>
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
    data() {
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
    created() {
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
        fetchEditData(id) {
            axios.all([
                axios.get(`/api/users/${id}`),
                axios.get('/api/permissions')
            ])
            .then(axios.spread((
                userData,
                permissionsData
            ) => {
                this.form.login = userData.data.admin.login;
                this.form.email = userData.data.admin.email;
                this.form.level = userData.data.admin.level;
                this.form.permissions = (userData.data.admin.custom_access ? userData.data.admin.custom_access : [this.defaultPage]);

                this.permissions = permissionsData.data.permissions;

                this.loading = false;
            }))
            .catch((error) => {
                this.$parent.authRequiredState(error);
                this.$parent.displayMessage('Error, during the process of updating user data, please repeat the process or re-login', 'error');
                console.log('Error fetching user resources: ' + error);
            });
        },
        fetchAddData: function() {
            axios.get('/api/permissions')
            .then((response) => {
                this.permissions = response.data.permissions;
                this.loading = false;
            })
            .catch((error) => {
                this.$parent.authRequiredState(error);
                this.loading = false;
            });
        },
        submitForm() {
            this.formLoading = true;

            this.errorClasses = {};

            // Frontend check
            if (this.add && (!this.form.login || !this.form.password || !this.form.level)) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'error');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    login: !this.form.login,
                    password: !this.form.password,
                    email: false,
                    level: !this.form.level,
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
            .then((response) => {
                this.$parent.displayMessage(response.data.message, 'success');

                if (this.add) {
                    this.$router.push('/users');
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