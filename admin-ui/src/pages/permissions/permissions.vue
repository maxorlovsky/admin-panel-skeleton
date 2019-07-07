<template>
    <section class="permissions">
        <v-card-actions>
            <h2>Permissions</h2>
            <v-spacer />
        </v-card-actions>

        <v-form class="permissions-table"
            @submit.prevent="submitForm()"
        >
            <div class="row head">
                <div class="column key">Key</div>
                <div class="column page-name">Page name</div>
                <div class="column icon-classes">Icon classes</div>
                <div class="column level">Level</div>
                <div class="column actions">Actions</div>
            </div>

            <div v-if="loading"
                class="row"
            >
                <loading />
            </div>

            <div class="row">
                <div class="column ghost" />
            </div>

            <draggable v-model="permissions"
                v-bind="{ handle: '.parent-sort-handle', group:'nav', ghostClass: 'ghost', animation: 50 }"
                class="permissions-list"
            >
                <div v-for="(permission, index) in permissions"
                    :key="index"
                    class="row"
                >
                    <div class="column key">
                        <v-text-field v-model="permission.key"
                            :error="errorClasses[permission.key]"
                            :disabled="!permission.new"
                            outline
                            name="name"
                            label="Key"
                            type="text"
                        />
                    </div>

                    <div class="column page-name">
                        <v-text-field v-model="permission.name"
                            :error="errorClasses[permission.key]"
                            outline
                            name="name"
                            label="Name"
                            type="text"
                        />
                    </div>

                    <div class="column icon-classes">
                        <v-text-field v-model="permission.iconClasses"
                            :error="errorClasses[permission.key]"
                            outline
                            name="name"
                            label="Icons"
                            type="text"
                        />
                    </div>

                    <div class="column level">
                        <v-select v-model="permission.level"
                            :items="levels"
                            :disabled="permission.strict"
                            outline
                            label="Level"
                        />
                    </div>

                    <div class="column actions">
                        <div v-if="!permission.strict"
                            @click="makeRowSub(index)"
                        >
                            <v-icon>arrow_forward</v-icon>
                        </div>

                        <div v-if="!permission.strict"
                            class="sort-handle parent-sort-handle"
                            @click.stop.prevent
                        >
                            <v-icon>swap_calls</v-icon>
                        </div>

                        <div v-if="!permission.strict"
                            @click="removeRow(index)"
                        ><v-icon>delete</v-icon></div>
                    </div>

                    <draggable v-model="permission.subCategories"
                        v-bind="{ handle: '.sub-sort-handle', group:'sub-nav', animation: 50 }"
                        :class="{ 'drag-on': drag }"
                        class="subpermissions-list"
                        @start="drag=true"
                    >
                        <div v-for="(subpermission, subindex) in permission.subCategories"
                            :key="subindex"
                            class="row sub-row"
                        >
                            <div class="column subkey">
                                <v-icon>arrow_forward</v-icon>
                            </div>

                            <div class="column key">
                                <v-text-field v-model="subpermission.key"
                                    :error="errorClasses[subpermission.key]"
                                    :disabled="!subpermission.new"
                                    outline
                                    name="name"
                                    label="Key"
                                    type="text"
                                />
                            </div>

                            <div class="column page-name">
                                <v-text-field v-model="subpermission.name"
                                    :error="errorClasses[permission.key]"
                                    outline
                                    name="name"
                                    label="Name"
                                    type="text"
                                />
                            </div>

                            <div class="column icon-classes">
                                <v-text-field v-model="subpermission.iconClasses"
                                    :error="errorClasses[permission.key]"
                                    outline
                                    name="name"
                                    label="Icons"
                                    type="text"
                                />
                            </div>

                            <div class="column level">
                                <v-select v-model="subpermission.level"
                                    :items="levels"
                                    :disabled="subpermission.strict"
                                    outline
                                    label="Level"
                                />
                            </div>

                            <div class="column actions">
                                <div v-if="!subpermission.strict"
                                    @click="makeRowParent(index, subindex)"
                                >
                                    <v-icon>arrow_back</v-icon>
                                </div>

                                <div v-if="!subpermission.strict"
                                    class="sort-handle sub-sort-handle"
                                    @click.stop.prevent
                                >
                                    <v-icon>swap_calls</v-icon>
                                </div>

                                <div v-if="!subpermission.strict"
                                    @click="removeSubRow(index, subindex)"
                                ><v-icon>delete</v-icon></div>
                            </div>
                        </div>
                    </draggable>
                </div>
            </draggable>

            <div v-if="!loading"
                class="row center"
            >
                <v-btn :loading="formLoading"
                    color="green"
                    class="button"
                    round
                    depressed
                    @click="addRow()"
                >+</v-btn>
            </div>

            <v-card-actions>
                <v-btn :loading="formLoading"
                    type="submit"
                    color="blue"
                    class="button"
                    round
                    depressed
                >Save</v-btn>
            </v-card-actions>
        </v-form>
    </section>
</template>

<script>
// 3rd party libs
import axios from 'axios';

// Website custom config
import websiteConfig from '../../../config/config.json';

// Components
import loading from '../../components/loading/loading.vue';
import draggable from 'vuedraggable';

// Mixins
import formMixin from '../../mixins/form-mixin.js';

const permissionsPage = {
    components: {
        loading,
        draggable
    },
    mixins: [formMixin],
    data() {
        return {
            permissions: [],
            formLoading: false,
            loading: true,
            errorClasses: {},
            drag: false,
            levels: [],
            maxLevel: websiteConfig.maxLevel
        };
    },
    created() {
        this.levels = this.setUpLevels();

        this.fetchData();
    },
    methods: {
        setUpLevels() {
            const levels = [];

            for (let i = 1; i <= this.maxLevel; ++i) {
                levels.push(i);
            }

            return levels;
        },
        async fetchData() {
            try {
                const response = await axios.get(`${mo.apiUrl}/permissions`);

                this.permissions = response.data.data;

                // Required for drag-n-drop functionality
                this.addEmptySubCategories();
            } catch (error) {
                this.displayMessage(error.response.data.message, { type: 'error' });
            } finally {
                this.loading = false;
            }
        },
        async submitForm() {
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

            try {
                // Submit to API
                const response = await axios.put(`${mo.apiUrl}/permissions`, this.permissions);

                this.displayMessage(response.data.message, { type: 'success' });

                // Triggering menu re-fetcher
                this.$store.dispatch('fetchMenu');
            } catch (error) {
                // Display error message from API
                this.displayMessage(error.response.data.message, { type: 'error' });

                this.errorClasses = this.formMixinHandleErrors(error);
            } finally {
                this.formLoading = false;
            }
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
                this.displayMessage('This category have subcategories, please move all subcategories first', { type: 'warning' });

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