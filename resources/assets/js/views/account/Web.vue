<template>
    <div class="web-page">
        You are currently limited to {{ webImagesPerShrink }} images and a maximum file size of 15 MB
    </div>
</template>

<script>

    export default {

        data() {
            return {

            }
        },

        computed: {
            webImagesPerShrink() {
                return this.$store.getters.webImagesPerShrink;
            }
        }

    }

</script>