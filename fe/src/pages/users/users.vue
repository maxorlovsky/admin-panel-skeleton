<template>
<section class="users">
    <div class="heading">
        <h2>Users</h2>

        <router-link to="/users/add">
            <button class="btn btn-success">
                <span class="fa fa-user-plus"></span> Add new user
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
                    <td colspan="5"><loading></loading></td>
                </tr>
                <tr v-for="admin in admins" v-bind:key="admin.id">
                    <td>{{admin.login}}</td>
                    <td>{{admin.email}}</td>
                    <td><span v-if="admin.level != 0">{{admin.level}}</span><span v-else>Custom</span></td>
                    <td>{{admin.last_login}}</td>
                    <td>
                        <router-link :to="'/users/edit/' + admin.id"><button class="btn btn-success"><span class="fa fa-pencil"></span></button></router-link>
                        <button class="btn btn-danger"
                            v-on:click="deleteAdmin(admin.id)"
                            :disabled="formLoading"
                        >
                            <span class="fa fa-trash"></span>
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
    data: function() {
        return {
            admins: {},
            formLoading: false,
            loading: true
        };
    },
    created: function() {
        return this.fetchData();
    },
    methods: {
        fetchData: function() {
            const self = this;

            axios.get('/api/users')
            .then(function (response) {
                self.admins = response.data.admins;
                self.loading = false;
            })
            .catch(function (error) {
                self.$parent.authRequiredState(error);
                self.loading = false;
            });
        },
        deleteAdmin: function(id) {
            const self = this;

            if (!confirm('Are you sure to delete?')) {
                return false;
            }

            this.formLoading = true;

            axios.delete(`/api/users/delete/${id}`)
            .then(function (response) {
                self.$parent.displayMessage(response.data.message, 'success');
                self.fetchData();
                self.formLoading = false;
            })
            .catch(function (error) {
                self.$parent.displayMessage(error.response.data.message, 'danger');
                self.formLoading = false;
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