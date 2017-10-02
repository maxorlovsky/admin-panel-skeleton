<template>
<section class="logs">
    <div class="heading">
        <h2>Logs</h2>
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
                    <td colspan="7"><loading></loading></td>
                </tr>
                <tr v-if="!logs">
                    <td colspan="7" align="center">There are no data in logs</td>
                </tr>
                <tr v-else v-for="log in logs" v-bind:key="log.id">
                    <td>{{log.id}}</td>
                    <td>{{log.module}}</td>
                    <td>{{log.type}}</td>
                    <td><span v-if="log.login">{{log.login}}</span><span v-else>N/A</span></td>
                    <td>{{log.date}}</td>
                    <td>{{log.ip}}</td>
                    <td v-html="log.info"></td>
                </tr>
            </tbody>
        </table>

        <pagination v-bind:page="page"
            v-bind:amount="logsAmount"
            v-bind:amount-per-page="amountPerPage"
            page-url="logs"
        ></pagination>
    </div>
</section>
</template>

<script>
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
    data: function() {
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
            logsAmount: 0
        };
    },
    created: function() {
        return this.fetchData();
    },
    watch: {
        $route: 'fetchData'
    },
    methods: {
        fetchData: function() {
            const self = this;

            this.page = parseInt(this.$route.params.page);

            axios.post('/api/logs', {
                module: '',
                type: '',
                offset: this.offset,
                page: this.page
            })
            .then(function (response) {
                if (response.data.logs) {
                    self.logs = response.data.logs;
                }
                self.logsAmount = response.data.maxAmount;
                self.loading = false;
            })
            .catch(function (error) {
                self.$parent.authRequiredState(error);
                self.loading = false;
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