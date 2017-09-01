<template>
<section class="pages">
    <div class="heading">
        <h2>Pages</h2>

        <router-link to="/pages/add">
            <button class="btn btn-success">
                <span class="fa fa-file-text"></span> Add new page
            </button>
        </router-link>
    </div>

    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>Page name</th>
                <th>Page link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="3"><loading></loading></td>
            </tr>
            <tr v-for="page in pages" v-bind:key="page.id">
                <td>{{page.name}}</td>
                <td>{{page.link}}</td>
                <td>
                    <router-link :to="'/pages/edit/' + page.id"><button class="btn btn-success"><span class="fa fa-pencil"></span></button></router-link>
                    <button class="btn btn-danger"
                        v-on:click="deletePage(page.id)"
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
import loading from '../loading/loading.vue';

// 3rd party libs
import axios from 'axios';

const pagesPage = {
    components: {
        loading
    },
    data: function() {
        return {
            pages: {},
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

            axios.get('/api/pages')
            .then(function (response) {
                self.pages = response.data.pages;
                self.loading = false;
            })
            .catch(function (error) {
                self.$parent.authRequiredState(error);
                self.loading = false;
            });
        },
        deletePage: function(id) {
            const self = this;

            this.formLoading = true;

            axios.delete(`/api/pages/delete/${id}`)
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
    path: '/pages',
    component: pagesPage,
    meta: {
        title: 'Pages',
        loggedIn: true
    }
});

export default pagesPage;
</script>