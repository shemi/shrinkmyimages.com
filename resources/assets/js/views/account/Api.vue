<template>
    <div class="web-page">
        <api-usege-gauge></api-usege-gauge>
        <personal-access-tokens></personal-access-tokens>
    </div>
</template>

<script>
    import ApiUsegeGauge from '../../components/ApiUsageGauge.vue';
    import PersonalAccessTokens from '../../components/passport/PersonalAccessTokens.vue';

    export default {

        data() {
            return {

            }
        },

        components: {
            ApiUsegeGauge,
            PersonalAccessTokens
        }

    }

</script>