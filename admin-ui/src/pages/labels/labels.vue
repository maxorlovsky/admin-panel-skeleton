<template>
<section class="labels">
    <div class="heading">
        <h2>Labels</h2>

        <router-link to="/labels/add">
            <button class="btn btn-success">
                <span class="fa fa-file-text"/> Add new label
            </button>
        </router-link>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Output</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="3">
                    <loading/>
                </td>
            </tr>
            <tr v-for="label in labels"
                :key="label.id"
            >
                <td>{{ label.name }}</td>
                <td>{{ label.output }}</td>
                <td>
                    <router-link :to="'/labels/edit/' + label.id">
                        <button class="btn btn-success">
                            <span class="fa fa-pencil"/>
                        </button>
                    </router-link>
                    <button :disabled="formLoading"
                        class="btn btn-danger"
                        @click="deleteLabel(label.id)"
                    >
                        <span class="fa fa-trash"/>
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
    props: {
        multiSiteId: Number
    },
    data() {
        return {
            labels: {},
            formLoading: false,
            loading: true
        };
    },
    created() {
        this.fetchData();
    },
    watch: {
        'multiSiteId'() {
            this.fetchData();
        }
    },
    methods: {
        fetchData() {
            axios.get('/api/labels')
            .then((response) => {
                this.labels = response.data.labels;
                this.loading = false;
            })
            .catch((error) => {
                this.$parent.authRequiredState(error);
                this.loading = false;
            });
        },
        deleteLabel(id) {
            if (!confirm('Are you sure to delete?')) {
                return false;
            }

            this.formLoading = true;

            axios.delete(`/api/labels/delete/${id}`)
            .then((response) => {
                this.$parent.displayMessage(response.data.message, 'success');
                this.fetchData();
                this.formLoading = false;
            })
            .catch((error) => {
                this.$parent.displayMessage(error.response.data.message, 'error');
                this.formLoading = false;
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