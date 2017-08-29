<template>
<section class="dashboard">
    Nothing to display here yet
</section>
</template>

<script>
const dashboardPage = {
    data: function() {
        return {
        };
    },
    created: function() {
    },
    methods: {
        
    }
};

// Routing
tm.routes.push({
    path: '/dashboard',
    component: dashboardPage,
    meta: {
        title: 'Dashboard',
        loggedIn: true
    }
});

export default dashboardPage;
</script>