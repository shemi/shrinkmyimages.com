<template>
    <page-template>
        <component v-if="contentComponentReady" :is="contentComponentName"></component>
    </page-template>
</template>

<script>
    import pageTemplate from '../components/PageTemplate.vue';
    import getPageContentMixin from '../mixins/getPageContentMixin';

    export default {
        name: 'About',
        data() {
            return {
                pageSlug: 'about'
            }
        },

        components: {
            pageTemplate
        },

        mixins: [
            getPageContentMixin,
        ]
    }
</script>

<style lang="scss">
</style>
