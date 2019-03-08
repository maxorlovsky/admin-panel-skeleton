<template>
<section :class="{ 'collapsed': menuCollapsed }"
    class="left-side"
>
    <nav @click="triggerClick()">
        <ul>
            <li v-for="link in menu"
                :key="link.url"
                class="nav-link"
            >
                <router-link v-if="checkUrl(link.url)"
                    :to="link.url"
                    :class="{ 'collapsed': menuCollapsed }"
                >
                    <i :class="link.iconClasses" />
                    <span>{{ link.title }}</span>
                </router-link>
                <a v-else
                    :class="{ 'collapsed': menuCollapsed }"
                    href="javascript:;"
                >
                    <i :class="link.iconClasses" />
                    <span>{{ link.title }}</span>
                </a>

                <ul v-if="link.sublinks"
                    class="nav-sub"
                >
                    <li v-for="sublink in link.sublinks"
                        :key="sublink.url"
                        class="nav-link"
                    >
                        <router-link :to="sublink.url">
                            <i class="fa fa-angle-right" />
                            <i :class="sublink.iconClasses" />
                            <span>{{ sublink.title }}</span>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-link collapser">
                <a href="javascript:;"
                    @click="menuCollapserClick()"
                >
                    <i :class="{ 'fa-angle-double-right': menuCollapsed, 'fa-angle-double-left': !menuCollapsed }"
                        class="fa"
                    />
                    <span v-if="!menuCollapsed">Collapse menu</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="side-menu-cover"
        @click="triggerClick()"
    >
        <i class="fa fa-times burger-closer" />
    </div>
</section>
</template>

<script>
// Globals functions
import { functions } from '../../functions.js';

export default {
    name: 'left-side',
    props: {
        menu: {
            type: Object,
            default: () => []
        }
    },
    data() {
        return {
            menuCollapsed: false
        };
    },
    created() {
        const getMenuCollapseState = functions.storage('get', 'menu-collapse');

        this.menuCollapsed = getMenuCollapseState.state;
    },
    methods: {
        menuCollapserClick() {
            if (this.menuCollapsed) {
                this.menuCollapsed = false;
                functions.storage('set', 'menu-collapse', {
                    state: false
                });
            } else {
                this.menuCollapsed = true;
                functions.storage('set', 'menu-collapse', {
                    state: true
                });
            }
        },
        triggerClick() {
            this.$emit('nav-menu');
        },
        checkUrl(url) {
            if (url.indexOf('nourl-') > 0) {
                return false;
            }

            return true;
        }
    }
}
</script>