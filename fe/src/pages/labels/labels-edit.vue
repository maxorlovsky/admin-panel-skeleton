<template>
<section class="labels">
    <div class="heading">
        <h2>
            <span v-if="add">Add</span>
            <span v-else>Edit</span>
            label
        </h2>
        <router-link to="/labels">
            <button class="btn btn-info">
                <span class="fa fa-step-backward"></span> Back to list
            </button>
        </router-link>
    </div>

    <loading v-if="loading"></loading>
    <form method="post" v-on:submit.prevent="submitForm()" v-else>
        <div class="form-group row">
            <label for="name-field" class="col-3 col-form-label">Name <span class="small">No spaces or dashes, use underscore "_"</span></label>
            <div class="col-9">
                <input v-model="form.name"
                    :class="{ error: errorClasses.name }"
                    class="form-control"
                    type="text"
                    id="name-field" 
                />
            </div>
        </div>
        <div class="form-group row">
            <label for="output-field" class="col-3 col-form-label">Text <span class="small">Unlike in other places, this text won't be wrapper in "paragraph" tag</span></label>
            <div class="col-9">
                <tinymce v-model="form.output"
                    :class="{ error: errorClasses.output }"
                    id="output-field"
                    :no-paragraph="true"
                ></tinymce>
            </div>
        </div>

        <button class="btn btn-primary" v-if="add" :disabled="formLoading">Add label</button>
        <button class="btn btn-primary" v-else :disabled="formLoading">Edit label</button>
    </form>
</section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';
import tinymce from '../../components/tinymce/tinymce.vue';

// 3rd party libs
import axios from 'axios';

const labelsEditPage = {
    components: {
        loading,
        tinymce
    },
    props: {
        multiSiteId: Number
	},
    data: function() {
        return {
            add: false,
            edit: false,
            loading: true,
            formLoading: false,
            form: {
                name: '',
                output: ''
            },
            errorClasses: {}
        };
    },
    created: function() {
        const self = this;

        // Define if we add or edit
        if (this.$route.params.id) {
            this.edit = true;
            this.fetchEditData(this.$route.params.id);
        } else {
            this.add = true;
            this.loading = false;
        }
    },
    watch: {
        'multiSiteId': function() {
            if (this.$route.params.id) {
                this.fetchEditData(this.$route.params.id);
            }
        }
    },
    methods: {
        fetchEditData: function(id) {
            const self = this;

            axios.get(`/api/labels/${id}`)
            .then(function (response) {
                self.form.name = response.data.label.name;
                self.form.output = response.data.label.output;
                
                self.loading = false;
            })
            .catch(function (error) {
                self.loading = false;
                console.log(error);
            });
        },
        submitForm: function() {
            const self = this;

            this.formLoading = true;

            this.errorClasses = {};

            // Frontend check
            if (!this.form.name) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'error');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    name: !this.form.name ? true : false
                };

                return false;
            }

            let apiUrl = '/api/labels/add';
            let apiAttributes = {
                name: this.form.name,
                output: this.form.output,
                site_id: this.multiSiteId
            };

            if (this.edit) {
                apiUrl = '/api/labels/edit';
                apiAttributes = {
                    id: this.$route.params.id,
                    name: this.form.name,
                    output: this.form.output
                };
            }

            axios.post(apiUrl, apiAttributes)
            .then(function (response) {
                self.$parent.displayMessage(response.data.message, 'success');
                if (self.add) {
                    self.$router.push('/labels');
                }
                self.formLoading = false;
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