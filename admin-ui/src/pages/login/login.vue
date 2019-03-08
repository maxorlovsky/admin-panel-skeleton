<template>
    <section class="login-form">
        <!-- <header /> -->

        <div class="body">
            <div v-if="formError"
                class="alert alert-danger"
            >{{ formError }}</div>

            <form method="post"
                @submit.prevent="submitForm()"
            >
                <input v-model="form.login"
                    :class="{ 'error': formError }"
                    placeholder="Login"
                    type="text"
                >

                <input v-model="form.password"
                    :class="{ 'error': formError }"
                    placeholder="Password"
                    type="password"
                >

                <button :disabled="formLoading"
                    class="btn btn-lg btn-secondary"
                >Enter</button>
            </form>
        </div>

        <footer>
            <a href="https://maxorlovsky.com"
                target="_blank"
            >CMS Version: {{ version }} <span>&copy;</span> 2011-{{ year }}</a>
        </footer>
    </section>
</template>

<script>
// 3rd party libs
import firebase from 'firebase';

const loginPage = {
    data() {
        return {
            form: {
                login: '',
                password: ''
            },
            formError: '',
            formLoading: false,
            version: mo.version,
            year: 0
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

            if (!this.form.login || !this.form.password) {
                this.formError = 'Please fill in the form';
                this.formLoading = false;

                return false;
            }

            try {
                await firebase.auth().signInWithEmailAndPassword(this.form.login, this.form.password);

                this.$store.dispatch('authorization');

                this.$router.push('/dashboard');
            } catch (e) {
                this.formError = e.message;
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