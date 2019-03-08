// 3rd party libs
import firebase from 'firebase';

// Vue
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

// Add <any> URLs to router, to push to /
mo.routes.push({
    path: '*',
    redirect: '/'
});

// Check if dashboard path exist, if not, making dashboard redirect to profile
const checkDashboard = mo.routes.findIndex((route) => route.path === '/dashboard');

if (checkDashboard === -1) {
    mo.routes.push({
        path: '/dashboard',
        redirect: '/profile'
    });
}

// Initiate the router
const router = new VueRouter({
    mode: 'history',
    routes: mo.routes
});

router.beforeEach((to, from, next) => {
    // If login is require and user state is not logged in - redirect to main page
    if (to.meta.loggedIn && !firebase.auth().currentUser) {
        console.error('Authentication failure');
        next('/');

        return false;
    }

    window.scrollTo(0, 0);

    // Set up meta title
    const metaTitle = 'MO CMS';

    document.title = metaTitle;
    if (to.meta.title) {
        document.title += ' - ' + to.meta.title;
    }

    return next();
});

export default router;