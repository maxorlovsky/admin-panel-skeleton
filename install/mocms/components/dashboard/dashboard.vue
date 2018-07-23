<template>
<section class="dashboard">
    <h3>Dashboard</h3>
    <p>This is a first page user see when he enters the admin panel, this is installed with custom modules and should be replaced with whatever platform needs</p>
</section>
</template>

<script>
const dashboardPage = {
    data() {
        return {};
    }
};

// Routing
mo.routes.push({
    path: '/dashboard',
    component: dashboardPage,
    meta: {
        title: 'Dashboard',
        loggedIn: true
    }
});

export default dashboardPage;
</script>