<template>
    <section class="login">
        <v-layout class="login-form">
            <header class="logo-wrapper">
                <div class="logo-circle">
                    <div class="logo" />
                </div>
            </header>

            <v-form @submit.prevent="submitForm()">
                <v-card-text>
                    <v-text-field v-model="form.login"
                        :rules="validationRules"
                        name="login"
                        label="Login"
                        type="login"
                        outline
                    />

                    <v-text-field v-model="form.password"
                        :type="visible ? 'text' : 'password'"
                        :append-icon="visible ? 'visibility_off' : 'visibility'"
                        :rules="validationRules"
                        name="password"
                        label="Password"
                        outline
                        @click:append="visible = !visible"
                    />

                    <v-alert :value="error.display"
                        type="error"
                        outline
                    >{{ error.message }}</v-alert>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn :loading="formLoading"
                        :disabled="formLoading"
                        type="submit"
                        color="blue btn-login"
                        round
                        depressed
                    >Login</v-btn>
                </v-card-actions>
            </v-form>

            <footer>
                <a href="https://maxorlovsky.com"
                    target="_blank"
                >CMS Version: {{ version }} <span>&copy;</span> 2011-{{ year }}</a>
            </footer>
        </v-layout>
    </section>
</template>

<script>
// 3rd party libs
import axios from 'axios';

// Globals functions
import { functions } from '../../functions.js';

// Mixins
import formMixin from '../../mixins/form-mixin.js';

const loginPage = {
    mixins: [formMixin],
    data() {
        return {
            formLoading: false,
            form: {
                login: '',
                password: ''
            },
            error: {
                display: false,
                message: ''
            },
            visible: false,
            version: mo.version,
            year: 0,
            validationRules: [(value) => this.formMixinIsRequired(value) || 'Required']
        };
    },
    created() {
        if (this.$store.getters.get('loggedIn')) {
            this.$router.push('dashboard');
        } else {
            this.year = new Date().getFullYear();
        }
    },
    methods: {
        async submitForm() {
            this.formLoading = true;
            this.error.display = false;

            try {
                const response = await axios.post(`${mo.apiUrl}/login`, {
                    login: this.form.login,
                    password: this.form.password
                });

                const token = response.data.sessionToken;

                // 7 days
                functions.storage('set', 'token', token, 604800000);

                this.$store.dispatch('authorization', {
                    token: token
                });

                mo.loggedIn = true;

                this.$router.push('/profile');
            } catch (e) {
                this.error.message = e.response.data.message;
                this.error.display = true;
            } finally {
                this.formLoading = false;
            }
        }
    }
};

// Routing
mo.routes.push({
    path: '/',
    component: loginPage,
    meta: {
        title: 'Login Page'
    }
});

export default loginPage;
</script>