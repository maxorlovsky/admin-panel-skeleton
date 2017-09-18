// Globals functions
import { functions } from './functions.js';

// VUE
import Vue from 'vue';
import VueRouter from 'vue-router';

// 3rd party libs
import axios from 'axios';
import Hammer from 'hammerjs';

// Components
import headerComponent from './components/header/header.vue';
import loading from './components/loading/loading.vue';
import leftSide from './components/left-side/left-side.vue';
import floatMessage from './components/float-message/float-message.vue';

// Pages
import loginPage from './components/login/login.vue';
import dashboardPage from './components/dashboard/dashboard.vue';
import usersPage from './components/users/users.vue';
import usersEditPage from './components/users/users-edit.vue';
import permissionsPage from './components/permissions/permissions.vue';
import logsPage from './components/logs/logs.vue';
import pagesPage from './components/pages/pages.vue';
import pagesEditPage from './components/pages/pages-edit.vue';
import logoutPage from './components/logout/logout.vue';

// Website custom config, those files must exist no matter what
import websiteConfig from '../../../../../mocms/config.json';
import * as customItems from './custom-components/custom.js';

functions.storageCacheBuster();

if (location.host.indexOf('dev') === 0) {
    mo.env = 'dev';
}

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

router.beforeEach((to, from, next) => {
    mo.loginCheckError = false;
    if (to.meta.loggedIn && !mo.loggedIn) {
        mo.loginCheckError = true;
    }
    window.scrollTo(0, 0);
    
    // Set up meta title
    let metaTitle = 'TM CMS';
    if (websiteConfig.title) {
        metaTitle = websiteConfig.title;
    }
    document.title = metaTitle;
    if (to.meta.title) {
        document.title += ' - ' + to.meta.title;
    }

    next();
});

router.afterEach((to, from) => {
    if (mo.loginCheckError) {
        // Displaying error message to the user
        router.app.authRequired();
        return false;
    }
});

function loadApp() {
    new Vue({
        el: '#app',
        router: router,
        components: {
            headerComponent,
            loading,
            leftSide,
            floatMessage
        },
        data: {
            menu: {},
            leftSideMenu: false,
            loggedIn: functions.checkUserAuth(),
            userData: {},
            floatingMessage: {}
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
            login: function() {
                this.loggedIn = functions.checkUserAuth();
                this.fetchLoggedInData();
            },
            logout: function() {
                functions.storage('remove', 'token');
                functions.storage('remove', 'structure-user-data');
                delete(axios.defaults.headers.common.sessionToken);
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
                    self.displayMessage('Error, during the process of updating user data, please repeat the process or re-login', 'danger');
                    console.log('Error fetching user resources: ' + error);
                });
            },
            displayMessage: function(message, type) {
                if (!type) {
                    type = 'info';
                }

                this.floatingMessage = {
                    message: message,
                    type: type
                };
            },
            authRequired: function() {
                // Displaying error message to the user
                this.displayMessage('You must be logged in to enter this page', 'danger');

                // Redirect to home page
                this.$router.push('/');
            },
            authRequiredState: function(error) {
                console.log(error.response);
                if (error.response.status === 401) {
                    this.displayMessage('You must be logged in to enter this page', 'danger');
                    this.logout();
                }

                return false;
            }
        }
    });

    document.getElementById('pre-loading').remove();
}

loadApp();