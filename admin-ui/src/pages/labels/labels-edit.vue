<template>
    <section class="labels">
        <v-card-actions>
            <h2>
                <span v-if="add">Add</span>
                <span v-else>Edit</span>
                label
            </h2>
            <v-spacer />
            <v-btn round
                depressed
                class="button blue"
                to="/labels"
            >Back to list</v-btn>
        </v-card-actions>

        <loading v-if="loading" />

        <v-form v-else
            class="block"
            @submit.prevent="submitForm()"
        >
            <v-container grid-list-xl
                fluid
                text-xs-center
            >
                <v-layout row
                    wrap
                >
                    <v-flex xs12>
                        <v-text-field v-model="form.name"
                            :error="errorClasses.name"
                            :rules="nameRules"
                            outline
                            counter="100"
                            hint="No spaces or dashes, use underscore '_'"
                            name="name"
                            label="Name"
                            type="text"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <tinymce id="output-field"
                            v-model="form.output"
                            :class="{ error: errorClasses.output }"
                            :no-paragraph="true"
                        />

                        <div class="output-hint-message v-messages theme--light">
                            <div class="v-messages__wrapper">
                                <div class="v-message__message">Unlike in other places, this text won't be wrapper in "paragraph" tag</div>
                            </div>
                        </div>
                    </v-flex>
                </v-layout>
            </v-container>

            <v-card-actions>
                <v-btn :loading="formLoading"
                    :disabled="submitDisabled"
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

// Components
import loading from '../../components/loading/loading.vue';
import tinymce from '../../components/tinymce/tinymce.vue';

// Mixins
import formMixin from '../../mixins/form-mixin.js';

const labelsEditPage = {
    components: {
        loading,
        tinymce
    },
    mixins: [formMixin],
    data() {
        return {
            add: false,
            edit: false,
            loading: true,
            formLoading: false,
            form: {
                name: '',
                output: ''
            },
            errorClasses: {},
            submitDisabled: true,
            validationRules: {
                name: {
                    minLength: 1,
                    maxLength: 100
                }
            },
            nameRules: [
                (value) => this.formMixinIsRequired(value) || 'Required',
                (value) => this.formMixinIsRangeValid(value.length, this.validationRules.name.minLength, this.validationRules.name.maxLength) || `Should be between ${this.validationRules.name.minLength} and ${this.validationRules.name.maxLength} characters long`
            ]
        };
    },
    watch: {
        multiSiteId: {
            // Triggering watch immediately
            immediate: true,
            handler() {
                // Trigger only on edit
                if (this.$route.params.id) {
                    this.fetchEditData(this.$route.params.id);
                }
            }
        },
        form: {
            deep: true,
            handler() {
                this.disableSubmit();
            }
        }
    },
    created() {
        // Define if we add or edit
        if (this.$route.params.id) {
            this.edit = true;
            this.fetchEditData(this.$route.params.id);
        } else {
            this.add = true;
            this.loading = false;
        }
    },
    computed: {
        multiSiteId() {
            return this.$store.getters.get('multiSiteId');
        }
    },
    methods: {
        disableSubmit() {
            this.submitDisabled = !this.form.name || !this.form.output;
        },
        async fetchEditData(id) {
            try {
                const response = await axios.get(`${mo.apiUrl}/label/${id}`);

                this.form.name = response.data.data.name;
                this.form.output = response.data.data.output;
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
        submitForm() {
            this.formLoading = true;
            this.errorClasses = {};

            // Check in case if it's an edit form
            if (this.edit) {
                this.submitEditForm();

                return true;
            }

            this.submitAddForm();

            return true;
        },
        async submitAddForm() {
            try {
                // Send request to API to create a label
                const response = await axios.post(`${mo.apiUrl}/label`, {
                    name: this.form.name,
                    output: this.form.output,
                    siteId: this.multiSiteId
                });

                this.displayMessage(response.data.message, { type: 'success' });

                // Redirect back to the list on success
                this.$router.push('/labels');
            } catch (error) {
                // Display error message from API
                this.displayMessage(error.response.data.message, { type: 'error' });

                this.errorClasses = this.formMixinHandleErrors(error);
            } finally {
                // Unblock the form
                this.formLoading = false;
            }
        },
        async submitEditForm() {
            try {
                const response = await axios.put(`${mo.apiUrl}/label`, {
                    id: this.$route.params.id,
                    name: this.form.name,
                    output: this.form.output
                });

                this.displayMessage(response.data.message, { type: 'success' });
            } catch (error) {
                // Display error message from API
                this.displayMessage(error.response.data.message, { type: 'error' });

                this.errorClasses = this.formMixinHandleErrors(error);
            } finally {
                // Unblock the form
                this.formLoading = false;
            }
        }
    }
};

// Routing
mo.routes.push(
    {
        path: '/labels/add',
        component: labelsEditPage,
        meta: {
            title: 'Add label',
            loggedIn: true
        }
    },
    {
        path: '/labels/edit/:id',
        component: labelsEditPage,
        meta: {
            title: 'Edit label',
            loggedIn: true
        }
    }
);

export default labelsEditPage;
</script>