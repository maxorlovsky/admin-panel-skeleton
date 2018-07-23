<template>
<header class="header-component">
    <div :class="{ 'active': burgerStatus }"
        class="fa fa-bars burger"
        @click="burgerMenu()"
    />

    <router-link to="/dashboard"
        class="logo"
    />

    <select v-if="enableBrands"
        v-model="multiSiteId"
        class="multi-brand"
    >
        <option v-for="brand in multisite"
            :value="brand.id"
            :key="brand.id"
        >{{ brand.name }}</option>
    </select>

    <div class="profile">
        <router-link to="/profile">
            <i class="fa fa-user"/>
        </router-link>
    </div>
</header>
</template>

<script>
// Website custom config
import websiteConfig from '../../../../../../../mocms/config.json';

// 3rd party libs
import axios from 'axios';

export default {
    name: 'header-component',
    data() {
        return {
            burgerStatus: this.$parent.leftSideMenu,
            loggedIn: false,
            enableBrands: websiteConfig.multiBrands,
            multisite: [],
            multiSiteId: null
        };
    },
    watch: {
        'multiSiteId'() {
            this.$emit('update-multisite', this.multiSiteId);
        }
    },
    created() {
        return this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/api/multisite')
            .then((response) => {
                this.multisite = response.data;

                if (this.multisite.length > 0) {
                    this.multiSiteId = this.multisite[0].id;
                } else {
                    this.multiSiteId = 0;
                }
            });
        },
        burgerMenu() {
            this.$emit('nav-menu');
        }
    }
}
</script>