<template>
<section class="dashboard">
    <form method="post" v-on:submit.prevent="submitForm()">
        <div class="update-password col-6 block">
            <div class="form-group row">
                <label for="currentPass-field" class="col-6 col-form-label">Current password</label>
                <div class="col-6"> 
                    <input v-model="form.currentPass"
                        :class="{ error: errorClasses.currentPass }"
                        class="form-control"
                        type="password"
                        id="currentPass-field" 
                    />
                </div>
            </div>

            <div class="form-group row">
                <label for="newPass-field" class="col-6 col-form-label">New password</label>
                <div class="col-6"> 
                    <input v-model="form.newPass"
                        :class="{ error: errorClasses.newPass }"
                        class="form-control"
                        type="password"
                        id="newPass-field" 
                    />
                </div>
            </div>

            <div class="form-group row">
                <label for="repeatPass-field" class="col-6 col-form-label">Repeat new password</label>
                <div class="col-6"> 
                    <input v-model="form.repeatPass"
                        :class="{ error: errorClasses.repeatPass }"
                        class="form-control"
                        type="password"
                        id="repeatPass-field" 
                    />
                </div>
            </div>

            <button class="btn btn-primary" :disabled="formLoading">Change password</button>
        </div>
    </form>
</section>
</template>

<script>
// 3rd party libs
import axios from 'axios';

const dashboardPage = {
    data: function() {
        return {
            formLoading: false,
            form: {
                currentPass: '',
                newPass: '',
                repeatPass: ''
            },
            errorClasses: {
                currentPass: '',
                newPass: '',
                repeatPass: ''
            }
        };
    },
    created: function() {

    },
    methods: {
        submitForm: function() {
            const self = this;

            this.formLoading = true;

            this.errorClasses = {};

            // Frontend check
            if (!this.form.currentPass || !this.form.newPass || !this.form.repeatPass) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'error');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    currentPass: !this.form.currentPass ? true : false,
                    newPass: !this.form.newPass ? true : false,
                    repeatPass: !this.form.repeatPass ? true : false
                };

                return false;
            }

            axios.put('/api/user-data/change-password', {
                currentPass: this.form.currentPass,
                newPass: this.form.newPass,
                repeatPass: this.form.repeatPass
            })
            .then(function (response) {
                self.$parent.displayMessage(response.data.message, 'success');
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
mo.routes.push({
    path: '/dashboard',
    component: dashboardPage,
    meta: {
        title: 'Dashboard',
        loggedIn: true
    }
});

export default dashboardPage;
</script>