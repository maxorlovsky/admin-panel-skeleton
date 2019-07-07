<template>
    <aside class="left-sidebar">
        <v-navigation-drawer :value="drawer"
            fixed
            dark
        >
            <v-list dense>
                <template v-for="item in menu">
                    <v-list-tile v-if="!item.sublinks"
                        :key="item.url"
                        :to="item.url"
                    >
                        <v-list-tile-action>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-tile-action>

                        <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                    </v-list-tile>

                    <v-list-group v-else
                        :key="item.url"
                        v-model="item.active"
                        :prepend-icon="item.icon"
                    >
                        <template v-slot:activator>
                            <v-list-tile>
                                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                            </v-list-tile>
                        </template>

                        <v-list-tile v-for="subitem in item.sublinks"
                            :key="subitem.url"
                            :to="subitem.url"
                        >
                            <v-list-tile-action>
                                <v-icon>{{ subitem.icon }}</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-title>{{ subitem.title }}</v-list-tile-title>
                        </v-list-tile>
                    </v-list-group>
                </template>
            </v-list>
        </v-navigation-drawer>
    </aside>
</template>

<script>
export default {
    name: 'left-side',
    data() {
        return {};
    },
    computed: {
        drawer() {
            return this.$store.getters.get('drawer');
        },
        menu() {
            return this.$store.getters.get('menu');
        }
    },
    watch: {
        menu() {
            if (this.menu) {
                const path = `/${this.$route.path.split('/')[1]}`;

                // Only trigger this when menu is available
                for (const item of this.menu) {
                    if (item.sublinks) {
                        const findPath = item.sublinks.find((sublink) => sublink.url === path);

                        if (findPath) {
                            item.active = true;
                        }
                    }
                }
            }
        }
    },
    created() {
        this.$store.dispatch('fetchMenu');
    }
};
</script>