<template>
    <section class="login-form">
        <loading v-if="loading"/>

        <header v-if="!loading"/>

        <div v-if="!loading"
            class="body"
        >
            <div v-if="formError"
                class="alert alert-danger"
                v-html="formError"
            />

            <form method="post"
                @submit.prevent="submitForm()"
            >
                <input :class="{ 'error': formError }"
                    v-model="form.login"
                    placeholder="Login"
                    type="text"
                >

                <input :class="{ 'error': formError }"
                    v-model="form.password"
                    placeholder="Password"
                    type="password"
                >

                <div v-if="displayCaptcha"
                    class="recaptcha"
                >
                    Too many fail attempts, please prove that you're not a robot!
                    <div :data-sitekey="recaptchaSiteKey"
                        class="g-recaptcha"
                    />
                </div>

                <button :disabled="formLoading"
                    class="btn btn-lg btn-secondary"
                >Enter</button>
            </form>
        </div>

        <footer v-if="!loading">
            <a href="https://maxorlovsky.com"
                target="_blank"
            >CMS Version: {{ version }} <span>&copy;</span> 2011-{{ year }}</a>
        </footer>
    </section>
</template>

<script>
// Globals functions
import { functions } from '../../functions.js';

// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const loginPage = {
    components: {
        loading
    },
    data() {
        return {
            form: {
                login: '',
                password: ''
            },
            formError: '',
            loading: true,
            formLoading: false,
            displayCaptcha: false,
            version: mo.version,
            year: 0,
            recaptchaSiteKey: ''
        };
    },
    created() {
        if (mo.loggedIn) {
            this.$router.push('dashboard');
        } else {
            this.year = new Date().getFullYear();
            this.loading = false;
        }
    },
    methods: {
        submitForm() {
            this.formLoading = true;

            if (!this.form.login || !this.form.password) {
                this.formError = 'Please fill in the form';
                this.formLoading = false;
                return false;
            }

            axios.post('/api/login', {
                login: this.form.login,
                password: this.form.password
            })
            .then((response) => {
                const token = response.data.sessionToken;

                // 7 days
                functions.storage('set', 'token', token, 604800000);

                this.$root.storeUser({
                    user: {},
                    token: token
                });

                this.$root.fetchLoggedInData();

                this.$router.push('dashboard');

                this.formLoading = false;
            })
            .catch((error) => {
                this.formError = error.response.data.message;
                this.formLoading = false;
            });
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