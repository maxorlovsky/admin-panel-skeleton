<template>
<section class="labels">
    <div class="heading">
        <h2>Labels</h2>

        <router-link to="/labels/add">
            <button class="btn btn-success">
                <span class="fa fa-file-text"></span> Add new label
            </button>
        </router-link>
    </div>

    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>Name</th>
                <th>Output</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="3"><loading></loading></td>
            </tr>
            <tr v-for="label in labels" v-bind:key="label.id">
                <td>{{label.name}}</td>
                <td>{{label.output}}</td>
                <td>
                    <router-link :to="'/labels/edit/' + label.id"><button class="btn btn-success"><span class="fa fa-pencil"></span></button></router-link>
                    <button class="btn btn-danger"
                        v-on:click="deleteLabel(label.id)"
                        :disabled="formLoading"
                    >
                        <span class="fa fa-trash"></span>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const labelsLabel = {
    components: {
        loading
    },
    data: function() {
        return {
            labels: {},
            formLoading: false,
            loading: true
        };
    },
    created: function() {
        this.fetchData();
    },
    methods: {
        fetchData: function() {
            const self = this;

            axios.get('/api/labels')
            .then(function (response) {
                self.labels = response.data.labels;
                self.loading = false;
            })
            .catch(function (error) {
                self.$parent.authRequiredState(error);
                self.loading = false;
            });
        },
        deleteLabel: function(id) {
            const self = this;

            this.formLoading = true;

            axios.delete(`/api/labels/delete/${id}`)
            .then(function (response) {
                self.$parent.displayMessage(response.data.message, 'success');
                self.fetchData();
                self.formLoading = false;
            })
            .catch(function (error) {
                self.$parent.displayMessage(error.response.data.message, 'danger');
                self.formLoading = false;
            });
        }
    }
};

// Routing
mo.routes.push({
    path: '/labels',
    component: labelsLabel,
    meta: {
        title: 'Labels',
        loggedIn: true
    }
});

export default labelsLabel;
</script>