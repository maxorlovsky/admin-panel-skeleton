<template>
<section class="permissions">
    <div class="heading">
        <h2>Permissions</h2>
    </div>
    
    <form class="table-responsive" v-on:submit.prevent="submitForm()">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Page name</th>
                    <th>Icons classes</th>
                    <th>Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loading">
                    <td colspan="5"><loading></loading></td>
                </tr>
                <tr v-for="(permission, index) in permissions">
                    <td v-if="permission.new">
                        <input type="text"
                            v-model="permission.key"
                            :class="{ error: errorClasses[permission.key] }"
                        />
                    </td>
                    <td v-else>{{permission.key}}</td>
                    <td>
                        <input type="text"
                            v-model="permission.name"
                            :class="{ error: errorClasses[permission.key] }"
                        />
                    </td>
                    <td>
                        <input type="text"
                            v-model="permission.icon_classes"
                            :class="{ error: errorClasses[permission.key] }"
                        />
                    </td>
                    <td align="center">
                        <select v-model="permission.level" :disabled="permission.strict">
                            <option v-for="level in maxLevel" v-bind:key="level">{{level}}</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn btn-danger"
                            v-on:click="removeRow(index)"
                            :disabled="permission.strict">-</button>
                    </td>
                </tr>
                <tr v-if="!loading">
                    <td colspan="5" align="center">
                        <button class="btn btn-success" v-on:click="addRow()">+</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button class="btn btn-primary" :disabled="formLoading">Update permissions</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const permissionsPage = {
    components: {
        loading
    },
    data: function() {
        return {
            permissions: {},
            maxLevel: 0,
            formLoading: false,
            loading: true,
            errorClasses: {},
            updateMenu: false
        };
    },
    created: function() {
        this.fetchData();
    },
    methods: {
        fetchData: function() {
            const self = this;

            axios.get('/api/permissions')
            .then(function (response) {
                self.permissions = response.data.permissions;
                self.maxLevel = parseInt(response.data.maxLevel);
                self.loading = false;
            })
            .catch(function (error) {
                self.$parent.authRequiredState(error);
                self.loading = false;
            });
        },
        submitForm: function() {
            const self = this;

            this.formLoading = true;
            this.errorClasses = {};

            for (let item of this.permissions) {
                if (item.new && item.key && item.name) {
                    this.updateMenu = true;
                    delete item.new;
                }
            }

            axios.put('/api/permissions', this.permissions)
            .then(function (response) {
                self.$parent.displayMessage(response.data.message, 'success');
                self.formLoading = false;

                if (self.updateMenu) {
                    self.$parent.fetchLoggedInData();
                    self.updateMenu = false;
                }
            })
            .catch(function (error) {
                self.formLoading = false;
                self.updateMenu = false;

                // Display error message from API
                self.$parent.displayMessage(error.response.data.message, 'danger');

                let errorFields = error.response.data.fields;
                // In some cases slim return array as json, we need to convert it
                if (errorFields.constructor !== Array) {
                    errorFields = Object.keys(errorFields).map(key => errorFields[key]);
                }

                // Mark fields with error class
                for (let i = 0; i < errorFields.length; ++i) {
                    self.errorClasses[errorFields[i]] = true;
                }
            });
        },
        addRow: function() {
            this.permissions.push({
                key: '',
                name: '',
                icon_classes: '',
                strict: false,
                level: 1,
                new: true
            });

            event.preventDefault();

            return true;
        },
        removeRow: function(index) {
            this.permissions.splice(index, 1);
            this.updateMenu = true;

            event.preventDefault();

            return true;
        }
    }
};

// Routing
tm.routes.push({
    path: '/permissions',
    component: permissionsPage,
    meta: {
        title: 'Permissions',
        loggedIn: true
    }
});

export default permissionsPage;
</script>