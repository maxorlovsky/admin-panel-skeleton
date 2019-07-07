<template>
    <v-app id="app"
        :class="bodyClass"
        class="app"
    >
        <v-content :class="{ 'collapsed-drawer': drawer }">
            <header-component v-if="loggedIn" />

            <v-navigation-drawer v-if="loggedIn"
                :value="drawer"
                width="230"
                app
            >
                <left-side />
            </v-navigation-drawer>

            <router-view class="body-content" />
        </v-content>

        <notifications position="bottom right"
            group="general"
        />

        <section v-if="loading"
            class="pre-loading"
        >
            <loading />
        </section>
    </v-app>
</template>

<script>
// Globals functions
import { functions } from './functions.js';

// Store
import { store } from './store/index.js';

// Components
import headerComponent from './components/header/header.vue';
import leftSide from './components/left-side/left-side.vue';
import loading from './components/loading/loading.vue';

export default {
    name: 'app',
    store: store,
    components: {
        headerComponent,
        leftSide,
        loading
    },
    data() {
        return {
            loading: true,
            bodyClass: mo.env
        };
    },
    computed: {
        loggedIn() {
            return this.$store.getters.get('loggedIn');
        },
        drawer() {
            return this.$store.getters.get('drawer');
        }
    },
    async created() {
        try {
            // If user is logged in, storring in Vuex
            if (mo.loggedIn) {
                await this.$store.dispatch('authorization', {
                    token: functions.storage('get', 'token')
                });

                // If store loggedIn state is true and user is on homepage, reidrect to dashboard
                if (this.$store.getters.get('loggedIn') && this.$route.path === '/') {
                    this.$router.push('/profile');
                }
            }
        } catch (e) {
            this.$store.dispatch('logout');

            this.$router.push('/');

            console.error(e);
        } finally {
            this.removeLoader();
        }
    },
    mounted() {
        // If user is not logged in, display login form directly after the render
        if (!mo.loggedIn) {
            this.removeLoader();
        }
    },
    methods: {
        removeLoader() {
            // Remove loader
            this.loading = false;
        }
    }
};
</script>