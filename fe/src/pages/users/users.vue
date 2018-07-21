<template>
<section class="users">
    <div class="heading">
        <h2>Users</h2>

        <router-link to="/users/add">
            <button class="btn btn-success">
                <span class="fa fa-user-plus"/> Add new user
            </button>
        </router-link>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Login</th>
                    <th>Email</th>
                    <th>Access level</th>
                    <th>Last entrance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loading">
                    <td colspan="5">
                        <loading/>
                    </td>
                </tr>
                <tr v-for="admin in admins"
                    :key="admin.id"
                >
                    <td>{{ admin.login }}</td>
                    <td>{{ admin.email }}</td>
                    <td>
                        <span v-if="admin.level != 0">{{ admin.level }}</span>
                        <span v-else>Custom</span>
                    </td>
                    <td>{{ admin.last_login }}</td>
                    <td>
                        <router-link :to="'/users/edit/' + admin.id">
                            <button class="btn btn-success">
                                <span class="fa fa-pencil"/>
                            </button>
                        </router-link>
                        <button :disabled="formLoading"
                            class="btn btn-danger"
                            @click="deleteAdmin(admin.id)"
                        >
                            <span class="fa fa-trash"/>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const usersPage = {
    components: {
        loading
    },
    data() {
        return {
            admins: {},
            formLoading: false,
            loading: true
        };
    },
    created() {
        return this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/api/users')
            .then((response) => {
                this.admins = response.data.admins;
                this.loading = false;
            })
            .catch((error) => {
                this.$parent.authRequiredState(error);
                this.loading = false;
            });
        },
        deleteAdmin(id) {
            if (!confirm('Are you sure to delete?')) {
                return false;
            }

            this.formLoading = true;

            axios.delete(`/api/users/delete/${id}`)
            .then((response) => {
                this.$parent.displayMessage(response.data.message, 'success');
                this.fetchData();
                this.formLoading = false;
            })
            .catch((error) => {
                this.$parent.displayMessage(error.response.data.message, 'error');
                this.formLoading = false;
            });
        }
    }
};

// Routing
mo.routes.push({
    path: '/users',
    component: usersPage,
    meta: {
        title: 'Users',
        loggedIn: true
    }
});

export default usersPage;
</script>