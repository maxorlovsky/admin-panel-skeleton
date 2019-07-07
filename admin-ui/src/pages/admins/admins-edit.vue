<template>
    <section class="admins">
        <v-card-actions>
            <h2>
                <span v-if="add">Add</span>
                <span v-else>Edit</span>
                admin
            </h2>
            <v-spacer />
            <v-btn round
                depressed
                class="button blue"
                to="/admins"
            >Back to list</v-btn>
        </v-card-actions>

        <loading v-if="loading" />

        <v-form v-else
            class="block"
            @submit.prevent="submitForm()"
        >
            <v-container grid-list-xl
                fluid
                text-xs-center
            >
                <v-layout row
                    wrap
                >
                    <v-flex xs12>
                        <v-text-field v-model="form.login"
                            :error="errorClasses.login"
                            :rules="loginRules"
                            outline
                            counter="32"
                            name="login"
                            label="Login"
                            type="text"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-text-field v-model="form.password"
                            :error="errorClasses.password"
                            :rules="passwordRules"
                            outline
                            name="password"
                            label="Password"
                            type="password"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-select v-model="form.level"
                            :items="levels"
                            :error="errorClasses.level"
                            outline
                            label="Access permissions"
                        />
                        <div v-if="form.level === 'Custom'"
                            class="permissions"
                        >
                            <v-select v-model="form.permissions"
                                :items="permissions"
                                chips
                                outline
                                label="Custom access select"
                                multiple
                            />
                        </div>
                    </v-flex>
                </v-layout>
            </v-container>

            <v-card-actions>
                <v-btn :loading="formLoading"
                    :disabled="submitDisabled"
                    type="submit"
                    color="blue"
                    class="button"
                    round
                    depressed
                >Save</v-btn>
            </v-card-actions>
        </v-form>
    </section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';

// Mixins
import formMixin from '../../mixins/form-mixin.js';

// 3rd party libs
import axios from 'axios';

// Website custom config
import websiteConfig from '../../../config/config.json';

const adminsEditPage = {
    components: {
        loading
    },
    mixins: [formMixin],
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
                level: 0,
                permissions: ['dashboard']
            },
            permissions: [],
            maxLevel: websiteConfig.maxLevel,
            submitDisabled: true,
            errorClasses: {},
            levels: [],
            loginRules: [
                (value) => this.formMixinIsRequired(value) || 'Required',
                (value) => value.indexOf(' ') === -1 || 'Must not include spaces, use _ or -'
            ],
            passwordRules: [
                (value) => (this.add && this.formMixinIsRequired(value)) || 'Required',
                (value) => value.length >= 6 || 'Must be at least 6 characters long'
            ]
        };
    },
    watch: {
        form: {
            deep: true,
            handler() {
                this.disableSubmit();
            }
        }
    },
    created() {
        this.levels = this.setUpLevels();

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
        disableSubmit() {
            this.submitDisabled = !this.form.login || (this.add && !this.form.password) || !this.form.level;
        },
        setUpLevels() {
            const levels = [];

            for (let i = 1; i <= this.maxLevel; ++i) {
                levels.push(i);
            }

            levels.push('Custom');

            return levels;
        },
        async fetchEditData(id) {
            try {
                const [admin, permissions] = await axios.all([
                    axios.get(`${mo.apiUrl}/admin/${id}`),
                    axios.get(`${mo.apiUrl}/permissions`)
                ]);

                this.form.login = admin.data.data.login;
                this.form.email = admin.data.data.email;
                this.form.level = admin.data.data.level === 0 ? 'Custom' : admin.data.data.level;
                this.form.permissions = (admin.data.data.customAccess ? JSON.parse(admin.data.data.customAccess) : [this.defaultPage]);

                this.setPermissions(permissions.data.data);
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchAddData() {
            try {
                const response = await axios.get(`${mo.apiUrl}/permissions`);

                this.setPermissions(response.data.data);
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        setPermissions(permissions) {
            const returnPermissions = [];

            // Gather permissions
            for (const permission of permissions) {
                returnPermissions.push({
                    value: permission.key,
                    text: permission.name
                });

                // Check if permission have subnavigation and loop through that as well
                if (permission.subCategories.length > 0) {
                    for (const subPermission of permission.subCategories) {
                        returnPermissions.push({
                            value: subPermission.key,
                            text: `${permission.name} -> ${subPermission.name}`
                        });
                    }
                }
            }

            this.permissions = returnPermissions;
        },
        submitForm() {
            this.formLoading = true;
            this.errorClasses = {};

            // Check in case if it's an edit form
            if (this.edit) {
                this.submitEditForm();

                return true;
            }

            this.submitAddForm();

            return true;
        },
        async submitAddForm() {
            try {
                // Send request to API to create a label
                const response = await axios.post(`${mo.apiUrl}/admin`, {
                    login: this.form.login,
                    password: this.form.password,
                    level: this.form.level === 'Custom' ? 0 : this.form.level,
                    permissions: this.form.permissions
                });

                this.displayMessage(response.data.message, { type: 'success' });

                // Redirect back to the list on success
                this.$router.push('/admins');
            } catch (error) {
                // Display error message from API
                this.displayMessage(error.response.data.message, { type: 'error' });

                this.errorClasses = this.formMixinHandleErrors(error);
            } finally {
                // Unblock the form
                this.formLoading = false;
            }
        },
        async submitEditForm() {
            try {
                const response = await axios.put(`${mo.apiUrl}/admin`, {
                    id: this.$route.params.id,
                    login: this.form.login,
                    password: this.form.password,
                    level: this.form.level === 'Custom' ? 0 : this.form.level,
                    permissions: this.form.permissions
                });

                this.displayMessage(response.data.message, { type: 'success' });
            } catch (error) {
                // Display error message from API
                this.displayMessage(error.response.data.message, { type: 'error' });

                this.errorClasses = this.formMixinHandleErrors(error);
            } finally {
                // Unblock the form
                this.formLoading = false;
            }
        }
    }
};

// Routing
mo.routes.push(
    {
        path: '/admins/add',
        component: adminsEditPage,
        meta: {
            title: 'Add admin',
            loggedIn: true
        }
    },
    {
        path: '/admins/edit/:id',
        component: adminsEditPage,
        meta: {
            title: 'Edit admin',
            loggedIn: true
        }
    }
);

export default adminsEditPage;
</script>