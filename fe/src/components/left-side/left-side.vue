<template>
<section class="left-side" :class="{ 'collapsed': menuCollapsed }">
    <nav v-on:click="triggerClick()">
        <ul>
            <li class="nav-link"
                v-for="link in menu"
                :key="link.url"
            >
                <router-link :to="link.url" :class="{ 'collapsed': menuCollapsed }">
                    <span :class="link.icon_classes"></span>
                    <label v-if="!menuCollapsed">{{link.title}}</label>
                </router-link>

                <!-- <ul class="nav-sub" v-if="link.sublinks">
                    <li :class="'nav-sublink ' + sublink.css_classes" v-for="(sublink, subkey) in link.sublinks">
                        <a v-if="sublink.target" :href="sublink.url" :target="sublink.target">{{sublink.title}}</a>
                        <router-link v-else :to="sublink.url" :target="sublink.target">{{sublink.title}}</router-link>
                    </li>
                </ul> -->
            </li>
        </ul>
    </nav>
    
    <div class="collapser" v-on:click="menuCollapserClick()">
        <i class="fa"
            :class="{ 'fa-angle-double-right': menuCollapsed, 'fa-angle-double-left': !menuCollapsed }"
        ></i>
    </div>

    <div class="side-menu-cover" v-on:click="triggerClick()"><i class="fa fa-times burger-closer"></i></div>
</section>
</template>

<script>
// Globals functions
import { functions } from '../../functions.js';

export default {
    name: 'left-side',
    props: {
        menu: {}
    },
    data: function() {
        return {
            menuCollapsed: false
        };
    },
    created: function() {
        const getMenuCollapseState = functions.storage('get', 'menu-collapse');
        this.menuCollapsed = getMenuCollapseState.state;
    },
    methods: {
        menuCollapserClick: function() {
            if (this.menuCollapsed) {
                this.menuCollapsed = false;
                functions.storage('set', 'menu-collapse', { state: false });
            } else {
                this.menuCollapsed = true;
                functions.storage('set', 'menu-collapse', { state: true });
            }
        },
        triggerClick: function() {
            this.$emit('nav-menu');
        }
    }
}
</script>