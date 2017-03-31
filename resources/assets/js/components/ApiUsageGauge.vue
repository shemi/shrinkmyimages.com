<template>
    <div class="api-usage-gauge">
        <div class="panel-heading">
            <span>
                API Usage
            </span>
            <span class="loading" v-if="loading">
                Updating...
            </span>
        </div>

        <gauge :max="total" :value="used"></gauge>

        <div class="panel-massage">
            You used {{ used }} of {{ total }} API shrinks.<br>
            The usage count will reset in <time :title="countResetDate">{{ countResetHumans }}</time>.
        </div>

    </div>
</template>

<script>
    import {Http} from '../http/index';
    import Gauge from './Gauge.vue';

    export default {

        data() {
            return {
                loading: false
            }
        },

        mounted() {
            this.getApiUsageStatus();
        },

        methods: {
            getApiUsageStatus() {
                let http = new Http,
                    self = this;

                self.loading = true;

                http.get('account/status')
                    .then((res) => {
                        self.$store.commit('setAppState', res.data);
                        self.loading = false;
                    });
            }

        },

        computed: {
            total() {
                return this.$store.getters.totalApiPrePaidCredits;
            },

            used() {
                return this.$store.getters.totalApiUsedCredits;
            },

            remaining() {
                return this.totalApiPrePaidCredits - this.totalApiUsedCredits;
            },

            countResetHumans() {
                return this.$store.getters.countResetHumans;
            },

            countResetDate() {
                return this.$store.getters.countResetDate;
            }

        },

        components: {
            Gauge
        }

    }

</script>