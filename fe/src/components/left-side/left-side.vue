<template>
<section class="left-side" :class="{ 'collapsed': menuCollapsed }">
    <nav v-on:click="triggerClick()">
        <ul>
            <li class="nav-link"
                v-for="link in menu"
                :key="link.url"
            >
                <router-link :to="link.url"
                    :class="{ 'collapsed': menuCollapsed }"
                    v-if="checkUrl(link.url)"
                >
                    <i :class="link.icon_classes"></i>
                    <span>{{link.title}}</span>
                </router-link>
                <a href="javascript:;" :class="{ 'collapsed': menuCollapsed }" v-else>
                    <i :class="link.icon_classes"></i>
                    <span>{{link.title}}</span>
                </a>

                <ul class="nav-sub" v-if="link.sublinks">
                    <li class="nav-link"
                        v-for="sublink in link.sublinks"
                        v-bind:key="sublink.url"
                    >
                        <router-link :to="sublink.url">
                            <i class="fa fa-angle-right"></i>
                            <i :class="sublink.icon_classes"></i>
                            <span>{{sublink.title}}</span>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-link collapser">
                <a href="javascript:;" v-on:click="menuCollapserClick()">
                    <i class="fa"
                        :class="{ 'fa-angle-double-right': menuCollapsed, 'fa-angle-double-left': !menuCollapsed }"
                    ></i>
                    <span v-if="!menuCollapsed">Collapse menu</span>
                </a>
            </li>
        </ul>
    </nav>

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
        },
        checkUrl: function(url) {
            if (url.indexOf('nourl-') > 0) {
                return false;
            }

            return true;
        }
    }
}
</script>