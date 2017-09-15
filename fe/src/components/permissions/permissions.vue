<template>
<section class="permissions">
    <div class="heading">
        <h2>Permissions</h2>
    </div>
    
    <form class="table-responsive" v-on:submit.prevent="submitForm()">
        <div class="row head">
            <div class="col-2 column">Key</div>
            <div class="col-4 column">Page name</div>
            <div class="col-3 column">Icon classes</div>
            <div class="col-1 column centered">Level</div>
            <div class="col-2 column centered">Actions</div>
        </div>

        <div class="row" v-if="loading">
            <div class="col-12 column"><loading></loading></div>
        </div>

        <div class="row">
            <div class="col-12 column ghost"></div>
        </div>

        <draggable
            class="permissions-list"
            v-model="permissions"
            :options="{ handle: '.sort-handle', group:'nav', ghostClass: 'ghost', animation: 50 }"
        >
            <div v-for="(permission, index) in permissions" :key="permission.key" class="row">
                <div v-if="permission.new" class="col-2 column">
                    <input type="text"
                        v-model="permission.key"
                        :class="{ error: errorClasses[permission.key] }"
                    />
                </div>
                <div v-else class="col-2 column">{{permission.key}}</div>
                <div class="col-4 column">
                    <input type="text"
                        v-model="permission.name"
                        :class="{ error: errorClasses[permission.key] }"
                    />
                </div>
                <div class="col-3 column">
                    <input type="text"
                        v-model="permission.icon_classes"
                        :class="{ error: errorClasses[permission.key] }"
                    />
                </div>
                <div class="col-1 column centered">
                    <select v-model="permission.level" :disabled="permission.strict">
                        <option v-for="level in maxLevel" v-bind:key="level">{{level}}</option>
                    </select>
                </div>
                <div class="col-2 column centered">
                    <button class="btn btn-info sort-handle"><i class="fa fa-sort"></i></button>
                    <button class="btn btn-danger"
                        v-on:click="removeRow(index)"
                        :disabled="permission.strict">-</button>
                </div>
            </div>
        </draggable>

        <div class="row" v-if="!loading">
            <div class="col-12 column centered">
                <button class="btn btn-success" v-on:click="addRow()">+</button>
            </div>
        </div>

        <button class="btn btn-primary" :disabled="formLoading">Update permissions</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../loading/loading.vue';
import draggable from 'vuedraggable';

// 3rd party libs
import axios from 'axios';

const permissionsPage = {
    components: {
        loading,
        draggable
    },
    data: function() {
        return {
            permissions: [],
            maxLevel: 0,
            formLoading: false,
            loading: true,
            errorClasses: {}
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
                    delete item.new;
                }
            }

            axios.put('/api/permissions', this.permissions)
            .then(function (response) {
                self.$parent.displayMessage(response.data.message, 'success');
                self.formLoading = false;
                self.$parent.fetchLoggedInData();
            })
            .catch(function (error) {
                self.formLoading = false;

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

            event.preventDefault();

            return true;
        }
    }
};

// Routing
mo.routes.push({
    path: '/permissions',
    component: permissionsPage,
    meta: {
        title: 'Permissions',
        loggedIn: true
    }
});

export default permissionsPage;
</script>