<template>
    <section class="admins">
        <v-card-actions>
            <h2>Admins</h2>
            <v-spacer />
            <v-btn round
                depressed
                class="button green"
                to="/admins/add"
            >Add new admin</v-btn>
        </v-card-actions>

        <v-data-table :headers="headers"
            :items="admins"
            :loading="loading"
            no-data-text="No admins found"
            hide-actions
        >
            <v-progress-linear slot="progress"
                color="purple"
                indeterminate
            />

            <template slot="items"
                slot-scope="props"
            >
                <tr>
                    <td>{{ props.item.id }}</td>
                    <td>{{ props.item.login }}</td>
                    <td>{{ props.item.level !== 0 ? props.item.level : 'Custom' }}</td>
                    <td>{{ props.item.lastLogin || '--' }}</td>
                    <td>{{ props.item.lastIp || '--' }}</td>
                    <td>
                        <router-link :to="'/admins/edit/' + props.item.id">
                            <v-icon>edit</v-icon>
                        </router-link>
                        <button :disabled="loading"
                            @click="deleteAdmin(props.item.id)"
                        >
                            <v-icon>delete</v-icon>
                        </button>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const adminsPage = {
    components: {
        loading
    },
    data() {
        return {
            headers: [
                {
                    text: 'ID',
                    sortable: true,
                    value: 'id'
                },
                {
                    text: 'Login',
                    sortable: true,
                    value: 'login'
                },
                {
                    text: 'Access Level',
                    sortable: true,
                    value: 'level'
                },
                {
                    text: 'Last Login',
                    sortable: true,
                    value: 'lastLogin'
                },
                {
                    text: 'Last IP',
                    sortable: true,
                    value: 'lastIp'
                },
                {
                    text: 'Actions',
                    sortable: false,
                    value: null
                }
            ],
            admins: [],
            loading: true
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        async fetchData() {
            try {
                const response = await axios.get(`${mo.apiUrl}/admins`);

                this.admins = response.data.data;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        async deleteAdmin(id) {
            if (!confirm('Are you sure you want to delete admin?')) {
                return false;
            }

            this.loading = true;

            try {
                const response = await axios.delete(`${mo.apiUrl}/admin/${id}`);

                this.displayMessage(response.data.message, { type: 'success' });

                // Re-fetch admin list
                this.fetchData();
            } catch (error) {
                this.displayMessage(error.response.data.message, { type: 'error' });
            } finally {
                this.loading = false;
            }
        }
    }
};

// Routing
mo.routes.push({
    path: '/admins',
    component: adminsPage,
    meta: {
        title: 'Admins',
        loggedIn: true
    }
});

export default adminsPage;
</script>