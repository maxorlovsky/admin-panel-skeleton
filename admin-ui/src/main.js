// Globals functions
import { functions } from './functions.js';

// VUE
import Vue from 'vue';
import Vuetify from 'vuetify';

// Main app file
import App from './app.vue';

/* eslint-disable */
// Pages
import pagesPage from './pages/pages/pages.vue';
import pagesEditPage from './pages/pages/pages-edit.vue';
import permissionsPage from './pages/permissions/permissions.vue';
import profilePage from './pages/profile/profile.vue';
import labelsPage from './pages/labels/labels.vue';
import labelsEditPage from './pages/labels/labels-edit.vue';
import loginPage from './pages/login/login.vue';
import logsPage from './pages/logs/logs.vue';
import adminsPage from './pages/admins/admins.vue';
import adminsEditPage from './pages/admins/admins-edit.vue';

// Mixins
import globalMixins from './mixins/global-mixin.js';
/* eslint-enable */

// Destroying old cache
functions.storageCacheBuster();

// Router
import router from './router.js';

Vue.use(Vuetify);

// Check if there is a token
if (functions.storage('get', 'token')) {
    mo.loggedIn = true;
}

// Load the app
mo.app = new Vue({
    router: router,
    render: (h) => h(App)
}).$mount('#app');