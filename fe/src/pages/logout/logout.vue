<template>
    <div/>
</template>

<script>
// Globals functions
import { functions } from '../../functions.js';

// 3rd party libs
import axios from 'axios';

const logoutPage = {
    data() {
        return {};
    },
    created() {
        functions.storage('remove', 'token');

        axios.post('/api/logout')
        .then(() => {
            this.$root.logout();
        })
        .catch((error) => {
            console.log(error);
        });
    }
};

// Routing
mo.routes.push({
    path: '/logout',
    component: logoutPage,
    meta: {
        title: 'Logout Page',
        loggedIn: true
    }
});

export default logoutPage;
</script>