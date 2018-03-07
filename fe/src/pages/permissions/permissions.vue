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
            :options="{ handle: '.parent-sort-handle', group:'nav', ghostClass: 'ghost', animation: 50 }"
        >
            <div v-for="(permission, index) in permissions" class="row">
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
                    <button class="btn btn-info"
                        v-on:click="makeRowSub(index)"
                        :disabled="permission.strict"><i class="fa fa-arrow-right"></i></button>
                        
                    <button class="btn btn-info sort-handle parent-sort-handle"
                        :disabled="permission.strict"
                        v-on:click.stop.prevent><i class="fa fa-sort"></i></button>

                    <button class="btn btn-danger"
                        v-on:click="removeRow(index)"
                        :disabled="permission.strict">-</button>
                </div>

                <draggable
                    class="subpermissions-list"
                    v-model="permission.subCategories"
                    :options="{ handle: '.sub-sort-handle', group:'sub-nav', animation: 50 }"
                    :class="{ 'drag-on': drag }"
                    @start="drag=true"
                >
                    <div v-for="(subpermission, subindex) in permission.subCategories" class="row sub-row">
                        <div class="col-1 column">
                            <i class="fa fa-arrow-right"></i>
                        </div>
                        <div v-if="subpermission.new" class="col-1 column">
                            <input type="text"
                                v-model="subpermission.key"
                                :class="{ error: errorClasses[subpermission.key] }"
                            />
                        </div>
                        <div v-else class="col-2 column">{{subpermission.key}}</div>
                        <div class="col-3 column">
                            <input type="text"
                                v-model="subpermission.name"
                                :class="{ error: errorClasses[subpermission.key] }"
                            />
                        </div>
                        <div class="col-3 column">
                            <input type="text"
                                v-model="subpermission.icon_classes"
                                :class="{ error: errorClasses[subpermission.key] }"
                            />
                        </div>
                        <div class="col-1 column centered">
                            <select v-model="subpermission.level" :disabled="subpermission.strict">
                                <option v-for="level in maxLevel" v-bind:key="level">{{level}}</option>
                            </select>
                        </div>
                        <div class="col-2 column centered">
                            <button class="btn btn-info"
                                v-on:click="makeRowParent(index, subindex)"
                                :disabled="subpermission.strict"><i class="fa fa-arrow-left"></i></button>

                            <button class="btn btn-info sort-handle sub-sort-handle"
                                :disabled="subpermission.strict"
                                v-on:click.stop.prevent><i class="fa fa-sort"></i></button>

                            <button class="btn btn-danger"
                                v-on:click="removeSubRow(index, subindex)"
                                :disabled="subpermission.strict">-</button>
                        </div>
                    </div>
                </draggable>
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
import loading from '../../components/loading/loading.vue';
import draggable from 'vuedraggable';

// 3rd party libs
import axios from 'axios';

// Website custom config
import websiteConfig from '../../../../../../../mocms/config.json';

const permissionsPage = {
    components: {
        loading,
        draggable
    },
    data: function() {
        return {
            permissions: [],
            formLoading: false,
            loading: true,
            errorClasses: {},
            drag: false,
            maxLevel: websiteConfig.maxLevel
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

                // Required for drag-n-drop functionality
                self.addEmptySubCategories();

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

            // On submit, remove "new" parameter from all
            for (let item of this.permissions) {
                if (item.new && item.key && item.name) {
                    delete item.new;
                }

                for (let subitem of item.subCategories) {
                    if (subitem.new && subitem.key && subitem.name) {
                        delete subitem.new;
                    }
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
                self.$parent.displayMessage(error.response.data.message, 'error');

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
            event.preventDefault();

            this.permissions.push({
                key: '',
                name: '',
                icon_classes: '',
                strict: false,
                level: 1,
                new: true,
                subCategories: []
            });

            return true;
        },
        removeRow: function(index) {
            event.preventDefault();

            if (!confirm('Are you sure to delete?')) {
                return false;
            }
            
            // Remove row from menu
            this.permissions.splice(index, 1);

            return true;
        },
        makeRowSub: function(index) {
            event.preventDefault();

            // In case it's already a parent with subs, forbid to move make it sub as well
            if (this.permissions[index].subCategories.length !== 0) {
                this.$parent.displayMessage('This category have subcategories, please move all subcategories first', 'error');
                return false;
            }

            // If subCategories parameter for some reason is empty, define it
            if (this.permissions[index - 1].subCategories === undefined) {
                this.permissions[index - 1].subCategories = [];
            }

            // Add to subs
            this.permissions[index - 1].subCategories.push(this.permissions[index]);

            // Remove from parent menu
            this.permissions.splice(index, 1);

            return true;
        },
        makeRowParent: function(index, subindex) {
            // Make row from parent to Sub
            event.preventDefault();

            // Saving row, as after permission removal indexing will be messed up
            const row = this.permissions[index].subCategories[subindex];

            // Remove row from subs
            this.permissions[index].subCategories.splice(subindex, 1);

            // Add to parent menu
            this.permissions.splice(index + 1, 0, row);

            return true;
        },
        removeSubRow: function(index, subindex) {
            // Sub row removal required different indexes
            event.preventDefault();

            if (!confirm('Are you sure to delete?')) {
                return false;
            }
            
            // Remove from subs
            this.permissions[index].subCategories.splice(subindex, 1);

            return true;
        },
        addEmptySubCategories: function() {
            // Add empty subcategories for drag-n-drop porpouses
            for (let i = 0; i < this.permissions.length; ++i) {
                if (this.permissions[i].subCategories === undefined) {
                    this.permissions[i].subCategories = [];
                }
            }

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