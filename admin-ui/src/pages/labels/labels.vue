<template>
    <section class="labels">
        <v-card-actions>
            <h2>Labels</h2>
            <v-spacer />
            <v-btn round
                depressed
                class="button green"
                to="/labels/add"
            >Add new label</v-btn>
        </v-card-actions>

        <v-data-table :headers="headers"
            :items="labels"
            :loading="loading"
            no-data-text="No labels added"
            hide-actions
        >
            <v-progress-linear slot="progress"
                color="blue"
                indeterminate
            />

            <template slot="items"
                slot-scope="props"
            >
                <tr>
                    <td>{{ props.item.name }}</td>
                    <td>{{ props.item.output }}</td>
                    <td>
                        <router-link :to="'/labels/edit/' + props.item.id">
                            <v-icon>edit</v-icon>
                        </router-link>
                        <button :disabled="loading"
                            @click="deleteLabel(props.item.id)"
                        >
                            <v-icon>delete</v-icon>
                        </button>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </section>
</template>

<script>
// Components
import loading from '../../components/loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const labelsPage = {
    components: {
        loading
    },
    data() {
        return {
            headers: [
                {
                    text: 'Name',
                    sortable: true,
                    value: 'name'
                },
                {
                    text: 'Output',
                    sortable: true,
                    value: 'output'
                },
                {
                    text: 'Actions',
                    sortable: false,
                    value: null
                }
            ],
            labels: [],
            loading: true
        };
    },
    computed: {
        multiSiteId() {
            return this.$store.getters.get('multiSiteId');
        }
    },
    watch: {
        // Triggering watch immediately
        multiSiteId: {
            immediate: true,
            handler() {
                this.fetchData();
            }
        }
    },
    methods: {
        async fetchData() {
            try {
                // Fetch labels data
                const response = await axios.get(`${mo.apiUrl}/labels`);

                this.labels = response.data.data;
            } catch (error) {
                // Display error message in case there is one
                this.displayMessage(error, { type: 'error' });
            } finally {
                this.loading = false;
            }
        },
        async deleteLabel(id) {
            if (!confirm('Are you sure you want to delete label?')) {
                return false;
            }

            // Display loader
            this.loading = true;

            try {
                const response = await axios.delete(`${mo.apiUrl}/label/${id}`);

                // Display success message
                this.displayMessage(response.data.message, { type: 'success' });

                // Re-fetching data
                this.fetchData();
            } catch (error) {
                // Display error message
                this.displayMessage(error.response.data.message, { type: 'error' });
            } finally {
                // Remove loader
                this.loading = false;
            }
        }
    }
};

// Routing
mo.routes.push({
    path: '/labels',
    component: labelsPage,
    meta: {
        title: 'Labels',
        loggedIn: true
    }
});

export default labelsPage;
</script>