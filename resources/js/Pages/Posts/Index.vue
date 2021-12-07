<template>
    <b-container>
        <Head title="Welcome" />
        <H1>Welcome</H1>
            <b-table responsive striped hover :items="posts" :fields="fields">
                <template #cell(author)="{item}" >
                    <p>{{ item.author ?     item.author : 'No Author' }}</p>
                </template>
                <template #cell(excerpt)="{item}" rows="3">
                    <p>{{ item.excerpt }}</p>
                </template>
            </b-table>
    </b-container>
</template>

<script>
  import { Head } from '@inertiajs/inertia-vue'

  export default {
    components: {
      Head
    },
    props: {
      posts: Array,
    },
    data: () => {
        return {
            fields: [
                'title',
                'author',
                'excerpt',
                'scraped_date',
                'article_date',
            ]
        }
    }
  }
</script>

<style scoped>
.hideextra {
    white-space: nowrap;
    overflow: hidden;
    text-overflow:ellipsis;
}
</style>