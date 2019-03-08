<template>
    <div class="pagination">
        <router-link v-for="page in pages"
            :class="page.disabled ? 'disabled' : false"
            :key="page.url+'-'+page.value"
            :to="'/' + pageUrl + '/page/' + page.url"
            class="btn btn-info"
            role="button"
        >{{ page.value }}</router-link>
    </div>
</template>

<script>
export default {
    props: {
        page: {
            type: Number,
            default: 0
        },
        amount: {
            type: Number,
            default: 0
        },
        pageUrl: {
            type: String,
            default: '/'
        },
        amountPerPage: {
            type: Number,
            default: 5
        }
    },
    data() {
        return {
            pages: {}
        };
    },
    watch: {
        page() {
            this.render();
        },
        amount() {
            this.render();
        }
    },
    created() {
        this.render();
    },
    methods: {
        render() {
            // Getting max amount of possible pages
            const maxPages = Math.ceil(this.amount / this.amountPerPage);

            // Creating array
            const pages = [];

            // Defining start page
            let startPage = this.page - 3;
            let endPage = this.page + 3;

            // Checking if numbers are correct
            if (startPage < 1) {
                startPage = 1;
            }
            if (endPage > maxPages) {
                endPage = maxPages;
            }

            // Add backward button
            if (this.page !== 1) {
                pages.push({
                    url: parseInt(this.page) - 1,
                    value: '<<'
                });
            }

            // Looping and creating buttons for pagination
            for (let i = startPage; i <= endPage; ++i) {
                pages.push({
                    url: parseInt(i),
                    value: i,
                    disabled: i === this.page
                });
            }

            // Add forward button at the end
            if (this.page !== maxPages) {
                pages.push({
                    url: parseInt(this.page) + 1,
                    value: '>>'
                });
            }

            this.pages = pages;

            return pages;
        }
    }
}
</script>