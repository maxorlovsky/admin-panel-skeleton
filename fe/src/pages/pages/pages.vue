<template>
<section class="pages">
    <div class="heading">
        <h2>Pages</h2>

        <router-link to="/pages/add">
            <button class="btn btn-success">
                <span class="fa fa-file-text"/> Add new page
            </button>
        </router-link>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Meta title</th>
                <th>Page link</th>
                <th class="text-center">Enabled</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="4">
                    <loading/>
                </td>
            </tr>
            <tr v-for="page in pages"
                :key="page.id"
            >
                <td>{{ page.title }}</td>
                <td>{{ page.link }}</td>
                <td class="text-center">
                    <i :class="{ 'fa-check': page.enabled == 1, 'fa-times': page.enabled != 1 }"
                        class="fa"
                    />
                </td>
                <td class="text-center">
                    <router-link :to="'/pages/edit/' + page.id">
                        <button class="btn btn-success">
                            <span class="fa fa-pencil"/>
                        </button>
                    </router-link>

                    <button :disabled="formLoading"
                        class="btn btn-danger"
                        @click="deletePage(page.id)"
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

const pagesPage = {
    components: {
        loading
    },
    props: {
        multiSiteId: Number
    },
    data() {
        return {
            pages: {},
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
            axios.get('/api/pages')
            .then((response) => {
                this.pages = response.data.pages;
                this.loading = false;
            })
            .catch((error) => {
                this.$parent.authRequiredState(error);
                this.loading = false;
            });
        },
        deletePage(id) {
            this.formLoading = true;

            axios.delete(`/api/pages/delete/${id}`)
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
    path: '/pages',
    component: pagesPage,
    meta: {
        title: 'Pages',
        loggedIn: true
    }
});

export default pagesPage;
</script>