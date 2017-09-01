<template>
    <section class="login-form">
        <loading v-if="loading"></loading>

        <header v-if="!loading"></header>
        
        <div class="body" v-if="!loading">
            <div class="alert alert-danger" v-if="formError" v-html="formError"></div>
            
            <form method="post" v-on:submit.prevent="submitForm()">
                <input type="text" v-model="form.login" placeholder="Login" :class="{ 'error': formError }" />
                <input type="password" v-model="form.password" placeholder="Password" :class="{ 'error': formError }" />
                
                <div v-if="displayCaptcha" class="recaptcha">
                    Too many fail attempts, please prove that you're not a robot!
                    <div class="g-recaptcha" :data-sitekey="recaptchaSiteKey"></div>
                </div>
                
                <button class="btn btn-lg btn-secondary">Enter</button>
                
                <div v-if="demo" class="guest_form">
                    <button class="btn btn-lg btn-warning" :disabled="formLoading">Login as Demo</button>
                </div>
            </form>
        </div>
        
        <footer v-if="!loading">
            <a href="https://cms.maxorlovsky.com" target="_blank">CMS Version: {{version}} by Max Orlovsky <span>&copy;</span> 2011-{{year}}</a>
        </footer>
    </section>
</template>

<script>
// Globals functions
import { functions } from '../../functions.js';

// VUE
import VueRouter from 'vue-router';

// Components
import loading from '../loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const loginPage = {
    components: {
        loading
    },
    data: function() {
        return {
            form: {
                login: '',
                password: ''
            },
            formError: '',
            loading: true,
            formLoading: false,
            displayCaptcha: false,
            demo: false,
            version: mo.version,
            year: 0,
            recaptchaSiteKey: ''
        };
    },
    created: function() {
        if (mo.loggedIn) {
            this.$router.push('dashboard');
        } else {
            this.year = new Date().getFullYear();
            this.loading = false;
        }
    },
    methods: {
        submitForm: function() {
            const self = this;

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
            .then(function (response) {
                functions.storage('set', 'token', response.data, 604800000); // 7 days
                self.$parent.login();
                self.$router.push('dashboard');
                self.formLoading = false;
            })
            .catch(function (error) {
                console.log(error);
                self.formError = error.response.data.message;
                self.formLoading = false;
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