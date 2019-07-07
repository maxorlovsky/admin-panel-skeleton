// Globals functions
import { functions } from './functions.js';

// VUE
import Vue from 'vue';
import Vuetify from 'vuetify';

// Main app file
import App from './app.vue';

// Pages
import './pages/pages/pages.vue';
import './pages/pages/pages-edit.vue';
import './pages/permissions/permissions.vue';
import './pages/profile/profile.vue';
import './pages/labels/labels.vue';
import './pages/labels/labels-edit.vue';
import './pages/login/login.vue';
import './pages/logs/logs.vue';
import './pages/admins/admins.vue';
import './pages/admins/admins-edit.vue';

// Mixins
import './mixins/global-mixin.js';

// Destroying old cache
functions.storageCacheBuster();

// Router
import router from './router.js';

Vue.use(Vuetify);

mo.env = functions.getEnv();

// Check if there is a token
if (functions.storage('get', 'token')) {
    mo.loggedIn = true;
}

// Load the app
mo.app = new Vue({
    router: router,
    render: (h) => h(App)
}).$mount('#app');