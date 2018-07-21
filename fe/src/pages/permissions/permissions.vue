<template>
<section class="permissions">
    <div class="heading">
        <h2>Permissions</h2>
    </div>

    <form class="table-responsive"
        @submit.prevent="submitForm()"
    >
        <div class="row head">
            <div class="col-2 column">Key</div>
            <div class="col-4 column">Page name</div>
            <div class="col-3 column">Icon classes</div>
            <div class="col-1 column centered">Level</div>
            <div class="col-2 column centered">Actions</div>
        </div>

        <div v-if="loading"
            class="row"
        >
            <div class="col-12 column"><loading/></div>
        </div>

        <div class="row">
            <div class="col-12 column ghost"/>
        </div>

        <draggable v-model="permissions"
            :options="{ handle: '.parent-sort-handle', group:'nav', ghostClass: 'ghost', animation: 50 }"
            class="permissions-list"
        >
            <div v-for="(permission, index) in permissions"
                :key="index"
                class="row"
            >
                <div v-if="permission.new"
                    class="col-2 column"
                >
                    <input v-model="permission.key"
                        :class="{ error: errorClasses[permission.key] }"
                        type="text"
                    >
                </div>
                <div v-else
                    class="col-2 column"
                >{{ permission.key }}</div>
                <div class="col-4 column">
                    <input v-model="permission.name"
                        :class="{ error: errorClasses[permission.key] }"
                        type="text"
                    >
                </div>
                <div class="col-3 column">
                    <input v-model="permission.iconClasses"
                        :class="{ error: errorClasses[permission.key] }"
                        type="text"
                    >
                </div>
                <div class="col-1 column centered">
                    <select v-model="permission.level"
                        :disabled="permission.strict"
                    >
                        <option v-for="level in maxLevel"
                            :key="level"
                        >{{ level }}</option>
                    </select>
                </div>
                <div class="col-2 column centered">
                    <div :disabled="permission.strict"
                        class="btn btn-info"
                        @click="makeRowSub(index)"
                    ><i class="fa fa-arrow-right"/></div>

                    <div :disabled="permission.strict"
                        class="btn btn-info sort-handle parent-sort-handle"
                        @click.stop.prevent
                    ><i class="fa fa-sort"/></div>

                    <div :disabled="permission.strict"
                        class="btn btn-danger"
                        @click="removeRow(index)"
                    >-</div>
                </div>

                <draggable v-model="permission.subCategories"
                    :options="{ handle: '.sub-sort-handle', group:'sub-nav', animation: 50 }"
                    :class="{ 'drag-on': drag }"
                    class="subpermissions-list"
                    @start="drag=true"
                >
                    <div v-for="(subpermission, subindex) in permission.subCategories"
                        :key="subindex"
                        class="row sub-row"
                    >
                        <div class="col-1 column">
                            <i class="fa fa-arrow-right"/>
                        </div>

                        <div v-if="subpermission.new"
                            class="col-1 column"
                        >
                            <input v-model="subpermission.key"
                                :class="{ error: errorClasses[subpermission.key] }"
                                type="text"
                            >
                        </div>
                        <div v-else
                            class="col-2 column"
                        >{{ subpermission.key }}</div>

                        <div class="col-3 column">
                            <input v-model="subpermission.name"
                                :class="{ error: errorClasses[subpermission.key] }"
                                type="text"
                            >
                        </div>
                        <div class="col-3 column">
                            <input v-model="subpermission.iconClasses"
                                :class="{ error: errorClasses[subpermission.key] }"
                                type="text"
                            >
                        </div>
                        <div class="col-1 column centered">
                            <select v-model="subpermission.level"
                                :disabled="subpermission.strict"
                            >
                                <option v-for="level in maxLevel"
                                    :key="level"
                                >{{ level }}</option>
                            </select>
                        </div>
                        <div class="col-2 column centered">
                            <div :disabled="subpermission.strict"
                                class="btn btn-info"
                                @click="makeRowParent(index, subindex)"
                            ><i class="fa fa-arrow-left"/></div>

                            <div :disabled="subpermission.strict"
                                class="btn btn-info sort-handle sub-sort-handle"
                                @click.stop.prevent
                            ><i class="fa fa-sort"/></div>

                            <div :disabled="subpermission.strict"
                                class="btn btn-danger"
                                @click="removeSubRow(index, subindex)"
                            >-</div>
                        </div>
                    </div>
                </draggable>
            </div>
        </draggable>

        <div v-if="!loading"
            class="row"
        >
            <div class="col-12 column centered">
                <div class="btn btn-success"
                    @click="addRow()"
                >+</div>
            </div>
        </div>

        <button :disabled="formLoading"
            class="btn btn-primary"
        >Update permissions</button>
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
    data() {
        return {
            permissions: [],
            formLoading: false,
            loading: true,
            errorClasses: {},
            drag: false,
            maxLevel: websiteConfig.maxLevel
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/api/permissions')
            .then((response) => {
                this.permissions = response.data.permissions;

                // Required for drag-n-drop functionality
                this.addEmptySubCategories();

                this.loading = false;
            })
            .catch((error) => {
                this.$parent.authRequiredState(error);
                this.loading = false;
            });
        },
        submitForm() {
            this.formLoading = true;
            this.errorClasses = {};

            // On submit, remove "new" parameter from all
            for (const item of this.permissions) {
                if (item.new && item.key && item.name) {
                    delete item.new;
                }

                for (const subitem of item.subCategories) {
                    if (subitem.new && subitem.key && subitem.name) {
                        delete subitem.new;
                    }
                }
            }

            axios.put('/api/permissions', this.permissions)
            .then((response) => {
                this.$parent.displayMessage(response.data.message, 'success');
                this.formLoading = false;
                this.$parent.fetchLoggedInData();
            })
            .catch((error) => {
                this.formLoading = false;

                // Display error message from API
                this.$parent.displayMessage(error.response.data.message, 'error');

                let errorFields = error.response.data.fields;

                // In some cases slim return array as json, we need to convert it
                if (errorFields.constructor !== Array) {
                    errorFields = Object.keys(errorFields).map((key) => errorFields[key]);
                }

                // Mark fields with error class
                for (let i = 0; i < errorFields.length; ++i) {
                    this.errorClasses[errorFields[i]] = true;
                }
            });
        },
        addRow() {
            this.permissions.push({
                key: '',
                name: '',
                iconClasses: '',
                strict: false,
                level: 1,
                new: true,
                subCategories: []
            });

            return true;
        },
        removeRow(index) {
            if (!confirm('Are you sure to delete?')) {
                return false;
            }

            // Remove row from menu
            this.permissions.splice(index, 1);

            return true;
        },
        makeRowSub(index) {
            // In case it's already a parent with subs, forbid to move make it sub as well
            if (this.permissions[index].subCategories.length !== 0) {
                this.$parent.displayMessage('This category have subcategories, please move all subcategories first', 'error');
                return false;
            }

            // If subCategories parameter for some reason is empty, define it
            if (!this.permissions[index - 1].subCategories) {
                this.permissions[index - 1].subCategories = [];
            }

            // Add to subs
            this.permissions[index - 1].subCategories.push(this.permissions[index]);

            // Remove from parent menu
            this.permissions.splice(index, 1);

            return true;
        },
        makeRowParent(index, subindex) {
            // Saving row, as after permission removal indexing will be messed up
            const row = this.permissions[index].subCategories[subindex];

            // Remove row from subs
            this.permissions[index].subCategories.splice(subindex, 1);

            // Add to parent menu
            this.permissions.splice(index + 1, 0, row);

            return true;
        },
        removeSubRow(index, subindex) {
            if (!confirm('Are you sure to delete?')) {
                return false;
            }

            // Remove from subs
            this.permissions[index].subCategories.splice(subindex, 1);

            return true;
        },
        addEmptySubCategories() {
            // Add empty subcategories for drag-n-drop porpouses
            for (let i = 0; i < this.permissions.length; ++i) {
                if (!this.permissions[i].subCategories) {
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