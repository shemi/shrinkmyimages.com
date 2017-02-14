
export default {

    props: ['ad'],

    data() {
        return {
        }
    },

    mounted() {
        setTimeout(function () {
            this.nextAd();
        }.bind(this), (this.ad.timeout || 10) * 1000)
    },

    methods: {
        nextAd() {
            this.$emit('next');
        }
    },

    computed: {
        src() {
            return this.ad.source;
        }
    }

}