<template>
    <v-app class="app">
        <header-component v-if="loggedIn" />

        <router-view class="body-content" />

        <section v-if="loading"
            class="pre-loading"
        >
            <loading />
        </section>
    </v-app>
</template>

<script>
// 3rd party libs
import firebase from 'firebase';

// Store
import { store } from './store/index.js';

// Components
import headerComponent from './components/header/header.vue';
import loading from './components/loading/loading.vue';

export default {
    name: 'app',
    store: store,
    components: {
        headerComponent,
        loading
    },
    data() {
        return {
            loading: true
        };
    },
    computed: {
        loggedIn() {
            return this.$store.getters.get('loggedIn');
        }
    },
    created() {
        // Authenticating user on the site
        if (firebase.auth().currentUser) {
            this.$store.dispatch('authorization');
        }
    },
    mounted() {
        // If user is not logged in, display login form directly after the render
        this.removeLoader();
    },
    methods: {
        removeLoader() {
            // Remove loader
            this.loading = false;
        }
    }
};
</script>