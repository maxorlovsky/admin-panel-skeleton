<template>
    <section class="pages">
        <v-card-actions>
            <h2>Pages</h2>
            <v-spacer />
            <v-btn round
                depressed
                class="button green"
                to="/pages/add"
            >Add new page</v-btn>
        </v-card-actions>

        <v-data-table :headers="headers"
            :items="pages"
            :loading="loading"
            no-data-text="No pages added"
            hide-actions
        >
            <v-progress-linear slot="progress"
                color="purple"
                indeterminate
            />

            <template slot="items"
                slot-scope="props"
            >
                <tr>
                    <td>{{ props.item.title }}</td>
                    <td>{{ props.item.link }}</td>
                    <td>{{ props.item.enabled ? 'Yes' : 'No' }}</td>
                    <td>
                        <router-link :to="'/pages/edit/' + props.item.id">
                            <v-icon>edit</v-icon>
                        </router-link>
                        <button :disabled="loading"
                            @click="deletePage(props.item.id)"
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

const pagesPage = {
    components: {
        loading
    },
    data() {
        return {
            headers: [
                {
                    text: 'Title',
                    sortable: true,
                    value: 'title'
                },
                {
                    text: 'Link',
                    sortable: true,
                    value: 'link'
                },
                {
                    text: 'Enabled',
                    sortable: false,
                    value: 'enabled'
                },
                {
                    text: 'Actions',
                    sortable: false,
                    value: null
                }
            ],
            pages: [],
            formLoading: false,
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
                const response = await axios.get(`${mo.apiUrl}/pages`);

                this.pages = response.data.data;
            } catch (error) {
                this.displayMessage(error, { type: 'error' });
            } finally {
                this.loading = false;
            }
        },
        async deletePage(id) {
            if (!confirm('Are you sure you want to delete page?')) {
                return false;
            }

            this.loading = true;

            try {
                const response = await axios.delete(`${mo.apiUrl}/page/${id}`);

                // Display success message
                this.displayMessage(response.data.message, { type: 'success' });

                // Re-fetching data
                this.fetchData();
            } catch (error) {
                // Display error message
                this.displayMessage(error.response.data.message, { type: 'error' });
            } finally {
                this.loading = false;
            }
        }
    }
};

// Routing
mo.routes.push({
    path: '/pages',
    component: pagesPage,
    meta: {
        title: 'Pages',
        loggedIn: true
    }
});

export default pagesPage;
</script>