<template>
<section class="dashboard">
    <form method="post"
        @submit.prevent="submitForm()"
    >
        <div class="update-password col-6 block">
            <div class="form-group row">
                <label for="currentPass-field"
                    class="col-6 col-form-label"
                >Current password</label>

                <div class="col-6">
                    <input id="currentPass-field"
                        v-model="form.currentPass"
                        :class="{ error: errorClasses.currentPass }"
                        class="form-control"
                        type="password"
                    >
                </div>
            </div>

            <div class="form-group row">
                <label for="newPass-field"
                    class="col-6 col-form-label"
                >New password</label>
                <div class="col-6">
                    <input id="newPass-field"
                        v-model="form.newPass"
                        :class="{ error: errorClasses.newPass }"
                        class="form-control"
                        type="password"
                    >
                </div>
            </div>

            <div class="form-group row">
                <label for="repeatPass-field"
                    class="col-6 col-form-label"
                >Repeat new password</label>
                <div class="col-6">
                    <input id="repeatPass-field"
                        v-model="form.repeatPass"
                        :class="{ error: errorClasses.repeatPass }"
                        class="form-control"
                        type="password"
                    >
                </div>
            </div>

            <button :disabled="formLoading"
                class="btn btn-primary"
            >Change password</button>
        </div>
    </form>
</section>
</template>

<script>
// 3rd party libs
import axios from 'axios';

const dashboardPage = {
    data() {
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
    methods: {
        submitForm() {
            this.formLoading = true;

            this.errorClasses = {};

            // Frontend check
            if (!this.form.currentPass || !this.form.newPass || !this.form.repeatPass) {
                // Generic error message
                this.$parent.displayMessage('Please fill in the form', 'error');
                this.formLoading = false;
                // Mark specific fields as empty ones
                this.errorClasses = {
                    currentPass: !this.form.currentPass,
                    newPass: !this.form.newPass,
                    repeatPass: !this.form.repeatPass
                };

                return false;
            }

            axios.put('/api/user-data/change-password', {
                currentPass: this.form.currentPass,
                newPass: this.form.newPass,
                repeatPass: this.form.repeatPass
            })
            .then((response) => {
                this.$parent.displayMessage(response.data.message, 'success');
                this.formLoading = false;
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