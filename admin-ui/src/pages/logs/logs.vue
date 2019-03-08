<template>
<section class="logs">
    <div class="heading">
        <h2>Logs</h2>
    </div>

    <div class="filters">
        <div class="filter-wrapper">
            <label for="module">Module</label>

            <select id="module"
                v-model="pickedModule"
                class="module"
            >
                <option v-for="mod in modules"
                    :value="mod"
                    :key="mod"
                >{{ mod }}</option>
            </select>
        </div>

        <div class="filter-wrapper">
            <label for="type">Type</label>

            <select id="type"
                v-model="pickedType"
                class="type"
            >
                <option v-for="typ in types"
                    :value="typ"
                    :key="typ"
                >{{ typ }}</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Module</th>
                    <th>Type</th>
                    <th>User</th>
                    <th>Data</th>
                    <th>IP</th>
                    <th>Info</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loading">
                    <td colspan="7"><loading/></td>
                </tr>

                <tr v-if="!logs">
                    <td colspan="7"
                        align="center"
                    >There are no data in logs</td>
                </tr>
                <tr v-for="log in logs"
                    v-else
                    :key="log.id"
                >
                    <td>{{ log.id }}</td>
                    <td>{{ log.module }}</td>
                    <td>{{ log.type }}</td>
                    <td>
                        <span v-if="log.login">{{ log.login }}</span>
                        <span v-else>N/A</span>
                    </td>
                    <td>{{ log.date }}</td>
                    <td>{{ log.ip }}</td>
                    <td class="logs-info"
                        v-html="log.info"
                    />
                </tr>
            </tbody>
        </table>

        <pagination :page="page"
            :amount="logsAmount"
            :amount-per-page="amountPerPage"
            page-url="logs"
        />
    </div>
</section>
</template>

<script>
// Website custom config
import websiteConfig from '../../../../../../../mocms/config.json';

// Components
import loading from '../../components/loading/loading.vue';
import pagination from '../../components/pagination/pagination.vue';

// 3rd party libs
import axios from 'axios';

const logsPage = {
    components: {
        loading,
        pagination
    },
    data() {
        if (!this.$route.params.page) {
            this.$route.params.page = 1;
        }

        return {
            logs: null,
            formLoading: false,
            loading: true,
            offset: 0,
            empty: null,
            page: 1,
            amountPerPage: 20,
            logsAmount: 0,
            pickedModule: '',
            pickedType: '',
            modules: ['', 'labels', 'login', 'logout', 'pages', 'permissions', 'users'],
            types: ['', 'add', 'delete', 'edit', 'fail', 'password-change', 'success']
        };
    },
    created() {
        this.modules = this.modules.concat(websiteConfig.logs.modules);
        this.types = this.types.concat(websiteConfig.logs.types);

        return this.fetchData();
    },
    watch: {
        $route: 'fetchData',
        pickedModule: 'fetchData',
        pickedType: 'fetchData'
    },
    methods: {
        fetchData() {
            this.page = parseInt(this.$route.params.page);

            axios.post('/api/logs', {
                module: this.pickedModule,
                type: this.pickedType,
                offset: this.offset,
                page: this.page
            })
            .then((response) => {
                if (response.data.logs) {
                    this.logs = response.data.logs;
                } else {
                    console.log('Module or Type not found, or page is set too high, dropping to first page');
                    this.$router.push('/logs/page/1');
                }

                this.logsAmount = response.data.maxAmount;
                this.loading = false;
            })
            .catch((error) => {
                this.$parent.authRequiredState(error);
                this.loading = false;
            });
        }
    }
};

// Routing
mo.routes.push({
    path: '/logs',
    component: logsPage,
    meta: {
        title: 'Logs',
        loggedIn: true
    },
    children: [
        {
            path: 'page/:page',
            meta: {
                title: 'Logs',
                loggedIn: true
            }
        }
    ]
});

export default logsPage;
</script>