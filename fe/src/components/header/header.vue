<template>
<header class="header-component">
    <div class="fa fa-bars burger"
        v-on:click="burgerMenu()"
        :class="{ 'active': burgerStatus }"></div>

    <router-link to="/dashboard" class="logo"></router-link>

    <select class="multi-brand">
        <option>floweradmin.maxorlovsky.net</option>
    </select>
</header>
</template>

<script>
export default {
    name: 'header-component',
    data: function() {
        return {
            burgerStatus: this.$parent.leftSideMenu,
            loggedIn: false
        };
    },
    methods: {
        burgerMenu: function() {
            this.$emit('nav-menu');
        }
    }
}
</script>