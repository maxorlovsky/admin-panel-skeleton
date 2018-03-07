// Globals functions
import { functions } from './functions.js';

// VUE
import Vue from 'vue';
import VueRouter from 'vue-router';
import VueNotification from 'vue-notification';

// 3rd party libs
import axios from 'axios';
import Hammer from 'hammerjs';

// Components
import headerComponent from './components/header/header.vue';
import loading from './components/loading/loading.vue';
import leftSide from './components/left-side/left-side.vue';
import fileUpload from './components/file-upload/file-upload.vue';

// Pages
import loginPage from './pages/login/login.vue';
import dashboardPage from './pages/dashboard/dashboard.vue';
import usersPage from './pages/users/users.vue';
import usersEditPage from './pages/users/users-edit.vue';
import permissionsPage from './pages/permissions/permissions.vue';
import logsPage from './pages/logs/logs.vue';
import pagesPage from './pages/pages/pages.vue';
import pagesEditPage from './pages/pages/pages-edit.vue';
import labelsPage from './pages/labels/labels.vue';
import labelsEditPage from './pages/labels/labels-edit.vue';
import logoutPage from './pages/logout/logout.vue';

// Website custom config, those files must exist no matter what
import websiteConfig from '../../../../../mocms/config.json';
import * as customItems from './custom-components/custom.js';

functions.storageCacheBuster();

mo.env = functions.getEnv();

// Add <any> URLs to router, to push to /login
mo.routes.push({
    path: '*',
    redirect: '/'
});

// Initiate the router
const router = new VueRouter({
    mode: 'history',
    routes: mo.routes
});

Vue.use(VueRouter);
Vue.use(VueNotification);

router.beforeEach((to, from, next) => {
    // If login is require and user state is not logged in - redirect to main page
    if (to.meta.loggedIn && !mo.loggedIn) {
        next({
            path: '/'
        });
    }
    window.scrollTo(0, 0);
    
    // Set up meta title
    let metaTitle = 'MO CMS';
    if (websiteConfig.title) {
        metaTitle = websiteConfig.title;
    }
    document.title = metaTitle;
    if (to.meta.title) {
        document.title += ' - ' + to.meta.title;
    }

    return next();
});

const vm = new Vue({
    el: '#app',
    router: router,
    components: {
        headerComponent,
        loading,
        leftSide,
        fileUpload
    },
    data: {
        menu: {},
        leftSideMenu: false,
        loggedIn: functions.checkUserAuth(),
        userData: {},
        multiSiteId: 0,
        bodyClass: mo.env,
    },
    mounted() {
        let self = this;
        
        delete Hammer.defaults.cssProps.userSelect;

        if (this.loggedIn) {
            this.fetchLoggedInData();
        }
    },
    methods: {
        burgerMenu: function() {
            if (this.leftSideMenu) {
                this.leftSideMenu = false;
                document.querySelector('body').className = document.querySelector('body').className.replace('open left', '').trim();
            } else {
                this.leftSideMenu = true;
                //window.location.hash = '#side-menu-open';
                document.querySelector('body').className = document.querySelector('body').className + ' open left'.trim();
            }
        },
        updateMultiSite: function(value) {
            this.multiSiteId = parseInt(value);
            axios.defaults.headers.common.siteId = this.multiSiteId;
        },
        login: function() {
            this.loggedIn = functions.checkUserAuth();
            this.fetchLoggedInData();
        },
        logout: function() {
            functions.storage('remove', 'token');
            functions.storage('remove', 'structure-user-data');
            delete(axios.defaults.headers.common.sessionToken);
            delete(axios.defaults.headers.common.siteId);
            mo.loggedIn = false;
            this.loggedIn = false;
            this.$router.push('/');
        },
        fetchLoggedInData: function() {
            let self = this;

            axios.all([
                axios.get('/api/menu')
            ])
            .then(axios.spread((
                menuData
            ) => {
                self.menu = menuData.data;
            }))
            .catch((error) => {
                self.authRequiredState(error);
                self.displayMessage('Error, during the process of updating user data, please repeat the process or re-login', 'error');
                console.log('Error fetching user resources: ' + error);
            });
        },
        displayMessage: function(message, type) {
            if (!type) {
                type = 'info';
            }

            this.$notify({
                type: type,
                title: type.charAt(0).toUpperCase() + type.slice(1),
                text: message,
                duration: 10000
            });
        },
        authRequiredState: function(error) {
            console.log(error.response);
            if (error.response.status === 401) {
                this.displayMessage('You must be logged in to enter this page', 'error');
                this.logout();
            }

            return false;
        }
    }
});

document.getElementById('pre-loading').remove();