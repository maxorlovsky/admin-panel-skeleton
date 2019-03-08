// Globals functions
// import { functions } from './functions.js';

// VUE
import Vue from 'vue';
import Vuetify from 'vuetify';

// 3rd party libs
import firebase from 'firebase';
// import axios from 'axios';
// import Hammer from 'hammerjs';

const config = {
    apiKey: 'AIzaSyBGCxlmCrgyZC85DIGKdr8o5IYgrnoO38g',
    authDomain: 'maltadriving-225f3.firebaseapp.com',
    databaseURL: 'https://maltadriving-225f3.firebaseio.com',
    projectId: 'maltadriving-225f3',
    storageBucket: 'maltadriving-225f3.appspot.com',
    messagingSenderId: '667356511602'
};

firebase.initializeApp(config);

Vue.use(Vuetify);

// Components
/* import headerComponent from './components/header/header.vue';
import loading from './components/loading/loading.vue';
import leftSide from './components/left-side/left-side.vue';
import fileUpload from './components/file-upload/file-upload.vue'; */

// Pages
/* eslint-disable */
/* import pagesPage from './pages/pages/pages.vue';
import pagesEditPage from './pages/pages/pages-edit.vue';
import permissionsPage from './pages/permissions/permissions.vue'; */
import profilePage from './pages/profile/profile.vue';
/* import labelsPage from './pages/labels/labels.vue';
import labelsEditPage from './pages/labels/labels-edit.vue'; */
import loginPage from './pages/login/login.vue';
/*import logoutPage from './pages/logout/logout.vue';
import logsPage from './pages/logs/logs.vue';
import usersPage from './pages/users/users.vue';
import usersEditPage from './pages/users/users-edit.vue'; */
/* eslint-enable */

//functions.storageCacheBuster();

// Router
import router from './router.js';

// Main app file
import App from './app.vue';

// Check if there is a token
/* if (functions.storage('get', 'token')) {
    // Add JWT token as default header
    axios.defaults.headers.common.sessionToken = functions.storage('get', 'token');

    // Check user
    axios.get('/api/me')
    .then((response) => {
        const store = {
            user: response.data,
            token: functions.storage('get', 'token')
        }

        // Marking user as logged in for routing check
        if (store.user) {
            mo.loggedIn = true;
        }

        mo.app = loadApp(store);
    })
    .catch(() => {
        // If something went wrong, loading app for logged out user
        mo.app = loadApp({});
    });
} else {
    // Load as logged out state
    mo.app = loadApp({});
} */

// Load when firebase is on
firebase.auth().onAuthStateChanged(() => {
    mo.app = new Vue({
        router: router,
        render: (h) => h(App)
    }).$mount('#app');
});

/* 
mo.app = loadApp({}).$mount('#app');

function loadApp(storage) {
    return new Vue({
        el: '#app',
        router: router,
        components: {
            headerComponent,
            loading,
            leftSide,
            fileUpload
        },
        data() {
            return {
                menu: {},
                leftSideMenu: false,
                loggedIn: false,
                userData: {},
                multiSiteId: 0
            };
        },
        created() {
            // If user is logged in, storring in Vuex
            if (Object.keys(storage).length !== 0) {
                this.storeUser(storage);
            }
        },
        mounted() {
            document.getElementById('pre-loading').remove();

            delete Hammer.defaults.cssProps.userSelect;

            if (this.loggedIn) {
                this.fetchLoggedInData();
            }
        },
        methods: {
            storeUser(data) {
                // Add JWT token as default header
                axios.defaults.headers.common.sessionToken = data.token;

                // Marking user for app as logged in, for easier check
                this.loggedIn = true;
                mo.loggedIn = true;
            },
            burgerMenu() {
                if (this.leftSideMenu) {
                    this.leftSideMenu = false;
                    document.querySelector('body').className = document.querySelector('body').className.replace('open left', '').trim();
                } else {
                    this.leftSideMenu = true;
                    // window.location.hash = '#side-menu-open';
                    document.querySelector('body').className = document.querySelector('body').className + ' open left'.trim();
                }
            },
            updateMultiSite(value) {
                this.multiSiteId = parseInt(value);
                axios.defaults.headers.common.siteId = this.multiSiteId;
            },
            logout() {
                functions.storage('remove', 'token');
                delete (axios.defaults.headers.common.sessionToken);
                delete (axios.defaults.headers.common.siteId);
                mo.loggedIn = false;
                this.loggedIn = false;
                this.$router.push('/');
            },
            fetchLoggedInData() {
                axios.get('/api/menu')
                .then((response) => {
                    this.menu = response.data;
                })
                .catch((error) => {
                    this.authRequiredState(error);
                    this.displayMessage('Error, during the process of updating user data, please repeat the process or re-login', 'error');
                    console.log('Error fetching user resources: ' + error);
                });
            },
            displayMessage(message, type) {
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
            authRequiredState(error) {
                console.log(error.response);

                if (error.response.status === 401) {
                    this.displayMessage('You must be logged in to enter this page', 'error');
                    this.logout();
                }

                return false;
            }
        }
    });
} */