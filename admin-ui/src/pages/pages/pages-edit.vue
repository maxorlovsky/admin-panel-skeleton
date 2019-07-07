<template>
    <section class="pages">
        <v-card-actions>
            <h2>
                <span v-if="add">Add</span>
                <span v-else>Edit</span>
                page
            </h2>
            <v-spacer />
            <v-btn round
                depressed
                class="button blue"
                to="/pages"
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
                        <v-text-field v-model="form.title"
                            :error="errorClasses.title"
                            :rules="titleRules"
                            outline
                            counter="100"
                            name="title"
                            label="Title"
                            type="text"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-text-field v-model="form.metaTitle"
                            :error="errorClasses.metaTitle"
                            outline
                            counter="70"
                            name="metaTitle"
                            label="Meta Title"
                            type="text"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-text-field v-model="form.metaDescription"
                            :error="errorClasses.metaDescription"
                            outline
                            counter="230"
                            name="metaDescription"
                            label="Meta Description"
                            type="text"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-text-field v-model="form.link"
                            :error="errorClasses.link"
                            :rules="linkRules"
                            outline
                            counter="300"
                            name="link"
                            label="Link"
                            type="text"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <tinymce id="text-field"
                            v-model="form.text"
                            :class="{ error: errorClasses.text }"
                            :no-paragraph="true"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-checkbox v-model="form.enabled"
                            hide-details
                            label="Enabled"
                        />
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
// Components
import loading from '../../components/loading/loading.vue';
import tinymce from '../../components/tinymce/tinymce.vue';

// Mixins
import formMixin from '../../mixins/form-mixin.js';

// 3rd party libs
import axios from 'axios';

const pagesEditPage = {
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
            submitDisabled: true,
            form: {
                title: '',
                metaTitle: '',
                metaDescription: '',
                link: '',
                text: '',
                enabled: false
            },
            errorClasses: {},
            validationRules: {
                title: {
                    minLength: 1,
                    maxLength: 100
                }
            },
            titleRules: [
                (value) => this.formMixinIsRequired(value) || 'Required',
                (value) => this.formMixinIsRangeValid(value.length, this.validationRules.title.minLength, this.validationRules.title.maxLength) || `Should be between ${this.validationRules.title.minLength} and ${this.validationRules.title.maxLength} characters long`
            ],
            linkRules: [
                (value) => this.formMixinIsRequired(value) || 'Required',
                (value) => value.indexOf(' ') === -1 || 'Must not include spaces, use _ or -'
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
    methods: {
        async fetchEditData(id) {
            try {
                const response = await axios.get(`${mo.apiUrl}/page/${id}`);

                this.form.title = response.data.data.title;
                this.form.metaTitle = response.data.data.metaTitle;
                this.form.metaDescription = response.data.data.metaDescription;
                this.form.link = response.data.data.link;
                this.form.text = response.data.data.text;
                this.form.enabled = response.data.data.enabled;
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
                const response = await axios.post(`${mo.apiUrl}/page`, {
                    title: this.form.title,
                    metaTitle: this.form.metaTitle,
                    metaDescription: this.form.metaDescription,
                    link: this.form.link,
                    text: this.form.text,
                    enabled: this.form.enabled,
                    siteId: this.multiSiteId
                });

                this.displayMessage(response.data.message, { type: 'success' });

                // Redirect back to the list on success
                this.$router.push('/pages');
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
                const response = await axios.put(`${mo.apiUrl}/page`, {
                    id: this.$route.params.id,
                    title: this.form.title,
                    metaTitle: this.form.metaTitle,
                    metaDescription: this.form.metaDescription,
                    link: this.form.link,
                    text: this.form.text,
                    enabled: this.form.enabled
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
        },
        disableSubmit() {
            this.submitDisabled = !this.form.title || !this.form.link;
        }
    }
};

// Routing
mo.routes.push(
    {
        path: '/pages/add',
        component: pagesEditPage,
        meta: {
            title: 'Add page',
            loggedIn: true
        }
    },
    {
        path: '/pages/edit/:id',
        component: pagesEditPage,
        meta: {
            title: 'Edit page',
            loggedIn: true
        }
    }
);

export default pagesEditPage;
</script>