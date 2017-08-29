<template></template>

<script>
// Globals functions
import { functions } from '../../functions.js';

// 3rd party libs
import axios from 'axios';

const logoutPage = {
    data: function() {
        return {};
    },
    created: function() {
        const self = this;

        functions.storage('remove', 'token');
        functions.storage('remove', 'structure-user-data');

        axios.post('/api/logout')
        .then(function (response) {
            delete(axios.defaults.headers.common.sessionToken);
            tm.loggedIn = false;
            self.$parent.loggedIn = false;
            self.$router.push('/');
        })
        .catch(function (error) {
            console.log(error);
        });
    }
};

// Routing
tm.routes.push({
    path: '/logout',
    component: logoutPage,
    meta: {
        title: 'Logout Page',
        loggedIn: true
    }
});

export default logoutPage;
</script>