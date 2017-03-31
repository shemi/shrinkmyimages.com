<template>
    <div class="gauge-container"></div>
</template>

<script>
    import Gauge from 'svg-gauge';

    export default {
        props: ['max', 'value'],

        data() {
            return {
                gauge: null,
            }
        },

        mounted() {
            this.init();
        },

        methods: {
            init() {
                this.gauge = new Gauge(this.$el, {
                    max: this.max,
                    label: function(value) {
                        return Math.round(value) + "/" + this.max;
                    },
                    value: this.value,
                });
            },

            updateValue(value) {
                this.gauge.setValueAnimated(value, 1);
            }
        },

        watch: {
            max() {

            },
            value(newVal, oldVal) {
                this.updateValue(newVal);
            }
        }

    }

</script>