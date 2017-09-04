<template>
<header class="header-component">
    <div class="fa fa-bars burger"
        v-on:click="burgerMenu()"
        :class="{ 'active': burgerStatus }"></div>

    <router-link to="/dashboard" class="logo"></router-link>

    <select class="multi-brand" v-if="enableBrands">
        <option v-for="brand in multisite"
            :value="brand.id"
            v-bind:key="brand.id"
        >{{brand.name}}</option>
    </select>
</header>
</template>

<script>
// Website custom config
import websiteConfig from '../../../../../../../mocms/config.json';

// 3rd party libs
import axios from 'axios';

export default {
    name: 'header-component',
    data: function() {
        return {
            burgerStatus: this.$parent.leftSideMenu,
            loggedIn: false,
            enableBrands: websiteConfig.multiBrands,
            multisite: []
        };
    },
    created: function() {
        return this.fetchData();
    },
    methods: {
        fetchData: function() {
            const self = this;

            axios.get('/api/multisite')
            .then(function (response) {
                self.multisite = response.data;
            });
        },
        burgerMenu: function() {
            this.$emit('nav-menu');
        }
    }
}
</script>