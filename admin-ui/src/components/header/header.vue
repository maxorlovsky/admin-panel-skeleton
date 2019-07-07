<template>
    <v-toolbar class="header-component">
        <v-toolbar-side-icon @click.stop="burgerMenu()" />

        <router-link to="/profile"
            class="logo-wrapper"
        >
            <div class="logo" />
        </router-link>

        <v-select v-if="enableBrands"
            v-model="multiSiteId"
            :items="multiSite"
            item-text="name"
            item-value="id"
            class="multi-brand"
            outline
            dark
            label="Current website/brand"
            @change="updateMultiSiteId()"
        />

        <v-toolbar-items class="toolbar-right">
            <v-btn :ripple="false"
                flat
                @click="logout()"
            >
                <v-icon>power_settings_new</v-icon>
            </v-btn>
        </v-toolbar-items>
    </v-toolbar>
</template>

<script>
// Globals functions
import { functions } from '../../functions.js';

// Website custom config
import websiteConfig from '../../../config/config.json';

// 3rd party libs
import axios from 'axios';

export default {
    name: 'header-component',
    data() {
        return {
            loggedIn: false,
            enableBrands: websiteConfig.multiBrands,
            multiSiteId: 0
        };
    },
    computed: {
        multiSite() {
            return this.$store.getters.get('multiSite');
        }
    },
    created() {
        if (this.enableBrands) {
            this.checkSiteId();
        }
    },
    methods: {
        checkSiteId() {
            let siteId = parseInt(functions.storage('get', 'site-id'));

            if (siteId) {
                siteId = parseInt(siteId);
            } else {
                siteId = 1;
            }

            this.multiSiteId = siteId;

            axios.defaults.headers.common.siteId = siteId;

            this.$store.commit('saveMultiSiteId', siteId);
        },
        updateMultiSiteId() {
            // Storring site ID in localStorage
            functions.storage('set', 'site-id', this.multiSiteId);

            // Update axios calls header
            axios.defaults.headers.common.siteId = this.multiSiteId;

            // Update store data
            this.$store.commit('saveMultiSiteId', this.multiSiteId);
        },
        burgerMenu() {
            this.$store.commit('drawer');
        },
        logout() {
            this.$store.dispatch('logout');

            this.$router.push('/');
        }
    }
};
</script>