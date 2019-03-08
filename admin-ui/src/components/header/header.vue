<template>
    <header class="header-component">
        <div :class="{ 'active': burgerStatus }"
            class="fa fa-bars burger"
            @click="burgerMenu()"
        />

        <router-link to="/dashboard"
            class="logo"
        />

        <v-select v-if="enableBrands"
            v-model="multiSiteId"
            :items="multisite"
            class="multi-brand"
            outline
        />
            <!-- <option v-for="brand in multisite"
                :key="brand.id"
                :value="brand.id"
            >{{ brand.name }}</option>
        </select> -->

        <div class="profile">
            <router-link to="/profile">
                <i class="fa fa-user" />
            </router-link>
        </div>

        <v-toolbar-items class="toolbar-right">
            <v-btn :ripple="false"
                flat
                @click="logout()"
            >
                <v-icon>icon-logout</v-icon>
            </v-btn>
        </v-toolbar-items>
    </header>
</template>

<script>
// 3rd party libs
import firebase from 'firebase';

// Website custom config
import websiteConfig from '../../../config/config.json';

export default {
    name: 'header-component',
    data() {
        return {
            db: firebase.firestore(),
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
        async fetchData() {
            try {
                const response = await this.db.collection('multisite').orderBy('id')
                    .get();

                const multisite = [];

                response.forEach((doc) => {
                    multisite.push(doc.data());
                });

                this.multisite = multisite;

                if (this.multisite.length > 0) {
                    this.multiSiteId = this.multisite[0].id;
                } else {
                    this.multiSiteId = 0;
                }
            } catch (e) {
                console.error(e);
            }
        },
        burgerMenu() {
            this.$emit('nav-menu');
        }
    }
};
</script>