<template>
    <section class="logs">
        <v-card-actions>
            <h2>Logs</h2>
            <v-spacer />
        </v-card-actions>

        <v-data-table :headers="headers"
            :items="logs"
            :loading="loading"
            :pagination.sync="pagination"
            :total-items="logsAmount"
            :rows-per-page-items="[pagination.rowsPerPage]"
            :rows-per-page-text="pagination.rowsPerPage.toString()"
            no-data-text="No logs found"
        >
            <v-progress-linear slot="progress"
                color="purple"
                indeterminate
            />

            <template slot="header">
                <td :colspan="headers.length">
                    <strong>This is an extra footer</strong>
                </td>
            </template>

            <template slot="items"
                slot-scope="props"
            >
                <tr>
                    <td>{{ props.item.id }}</td>
                    <td>{{ props.item.module }}</td>
                    <td>{{ props.item.type }}</td>
                    <td>{{ props.item.admin }}</td>
                    <td>{{ props.item.date }}</td>
                    <td>{{ props.item.ip }}</td>
                    <td v-html="props.item.info" />
                </tr>
            </template>

            <template v-slot:footer>
                <td :colspan="headers.length - 2" />
                <td :colspan="2">
                    <div class="filters">
                        <v-select v-model="pickedModule"
                            :items="modules"
                            :disabled="loading"
                            outline
                            clearable
                            hide-details
                            label="Module"
                        />

                        <v-spacer />

                        <v-select v-model="pickedType"
                            :items="types"
                            :disabled="loading"
                            outline
                            clearable
                            hide-details
                            label="Type"
                        />
                    </div>
                </td>
            </template>
        </v-data-table>
    </section>
</template>

<script>
// Website custom config
import websiteConfig from '../../../config/config.json';

// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const logsPage = {
    components: {
        loading
    },
    data() {
        if (!this.$route.params.page) {
            this.$route.params.page = 1;
        }

        return {
            loading: true,
            headers: [
                {
                    text: 'ID',
                    sortable: false,
                    value: 'id'
                },
                {
                    text: 'Module',
                    sortable: false,
                    value: 'module'
                },
                {
                    text: 'Type',
                    sortable: false,
                    value: 'type'
                },
                {
                    text: 'Admin',
                    sortable: false,
                    value: 'admin'
                },
                {
                    text: 'Date',
                    sortable: false,
                    value: 'date'
                },
                {
                    text: 'IP',
                    sortable: false,
                    value: 'ip'
                },
                {
                    text: 'Info',
                    sortable: false,
                    value: 'info'
                }
            ],
            logs: [],
            pagination: {
                rowsPerPage: 20
            },
            page: 1,
            logsAmount: 0,
            pickedModule: null,
            pickedType: null,
            modules: ['admins', 'labels', 'login', 'logout', 'pages', 'permissions', 'users'],
            types: ['add', 'delete', 'edit', 'fail', 'password-change', 'success']
        };
    },
    created() {
        this.modules = this.modules.concat(websiteConfig.logs.modules);
        this.types = this.types.concat(websiteConfig.logs.types);

        this.fetchData();
    },
    watch: {
        $route: 'fetchData',
        pickedModule: 'fetchData',
        pickedType: 'fetchData',
        pagination: {
            deep: true,
            handler () {
                this.$router.push(`/logs/page/${this.pagination.page}`);
                this.loading = true;
            }
        }
    },
    methods: {
        async fetchData() {
            this.page = parseInt(this.$route.params.page);

            let queryUrl = `page=${this.page}`;

            if (this.pickedModule) {
                queryUrl += `&module=${this.pickedModule}`;
            }

            if (this.pickedType) {
                queryUrl += `&type=${this.pickedType}`;
            }

            try {
                const response = await axios.get(`${mo.apiUrl}/logs?${queryUrl}`);

                if (response.data.data) {
                    this.logs = response.data.data;
                } else {
                    console.error('Module or Type not found, or page is set too high, dropping to first page');

                    this.$router.push('/logs/page/1');
                }

                this.logsAmount = response.data.amount;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
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