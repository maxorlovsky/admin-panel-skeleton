<template>
    <section class="profile-page">
        <v-form class="block"
            @submit.prevent="submitForm()"
        >
            <v-container grid-list-xl
                fluid
                text-xs-center
            >
                <v-layout row
                    wrap
                >
                    <v-flex xs12
                        md4
                    >
                        <v-text-field label="Admin ID"
                            :value="user.id"
                            disabled
                        />
                    </v-flex>
                    <v-flex xs12
                        md4
                    >
                        <v-text-field label="Admin Name"
                            :value="user.login"
                            disabled
                        />
                    </v-flex>
                    <v-flex xs12
                        md4
                    >
                        <v-text-field label="Admin Level"
                            :value="user.level"
                            disabled
                        />
                    </v-flex>
                </v-layout>

                <v-layout row
                    wrap
                >
                    <v-flex xs12>
                        <v-text-field v-model="form.currentPass"
                            :error="errorClasses.currentPass"
                            :rules="passwordRules"
                            outline
                            name="name"
                            label="Current Password"
                            type="password"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-text-field v-model="form.newPass"
                            :error="errorClasses.newPass"
                            :rules="passwordRules"
                            outline
                            name="newPass"
                            label="New Password"
                            type="password"
                        />
                    </v-flex>

                    <v-flex xs12>
                        <v-text-field v-model="form.repeatPass"
                            :error="errorClasses.repeatPass"
                            :rules="passwordRules"
                            outline
                            name="repeatPass"
                            label="Repeat New Password"
                            type="password"
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
// 3rd party libs
import axios from 'axios';

// Mixins
import formMixin from '../../mixins/form-mixin.js';

const profilePage = {
    mixins: [formMixin],
    data() {
        return {
            formLoading: false,
            submitDisabled: true,
            form: {
                currentPass: '',
                newPass: '',
                repeatPass: ''
            },
            errorClasses: {},
            validationRules: {
                password: {
                    minLength: 6,
                    maxLength: 255
                }
            },
            passwordRules: [
                (value) => this.formMixinIsRequired(value) || 'Required',
                (value) => this.formMixinIsRangeValid(value.length, this.validationRules.password.minLength, this.validationRules.password.maxLength) || `Should be between ${this.validationRules.password.minLength} and ${this.validationRules.password.maxLength} characters long`
            ]
        };
    },
    computed: {
        user() {
            return this.$store.getters.get('user');
        }
    },
    watch: {
        form: {
            deep: true,
            handler() {
                this.disableSubmit();
            }
        }
    },
    methods: {
        disableSubmit() {
            this.submitDisabled = !this.form.currentPass || !this.form.newPass || !this.form.repeatPass;
        },
        async submitForm() {
            this.formLoading = true;
            this.errorClasses = {};

            try {
                // Sending request to API, to update user password
                const response = await axios.put('/api/user-data/change-password', {
                    currentPass: this.form.currentPass,
                    newPass: this.form.newPass,
                    repeatPass: this.form.repeatPass
                });

                // Display success message
                this.displayMessage(response.data.message, { type: 'success' });
            } catch (error) {
                // Display error message from API
                this.displayMessage(error.response.data.message, { type: 'error' });

                this.errorClasses = this.formMixinHandleErrors(error);
            } finally {
                // Unblock form
                this.formLoading = false;
            }
        }
    }
};

// Routing
mo.routes.push({
    path: '/profile',
    component: profilePage,
    meta: {
        title: 'Profile',
        loggedIn: true
    }
});

export default profilePage;
</script>