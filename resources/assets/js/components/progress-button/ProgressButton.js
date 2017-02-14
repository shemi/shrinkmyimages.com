import SVGEl from "./SVGEl";

export default {

    props: ['progress', 'loading', 'isDisabled', 'status'],

    data() {
        return {
            transEndEventNames: {
                'WebkitTransition': 'webkitTransitionEnd',
                'MozTransition': 'transitionend',
                'OTransition': 'oTransitionEnd',
                'msTransition': 'MSTransitionEnd',
                'transition': 'transitionend'
            },
            transEndEventName: null,
            support: {
                transitions: Modernizr.csstransitions
            },

            button: null,
            progressEl: null,
            successEl: null,
            errorEl: null,
            inInfinityMode: false
        }
    },

    mounted() {
        this.init();
    },

    methods: {
        init() {
            // the button
            this.button = this.$el.querySelector( 'button' );
            // progress el
            this.progressEl = new SVGEl( this.$el.querySelector( 'svg.progress-circle' ) );
            // the success/error elems
            this.successEl = new SVGEl( this.$el.querySelector( 'svg.checkmark' ) );
            this.errorEl = new SVGEl( this.$el.querySelector( 'svg.cross' ) );

            this.toggleInfinityMode();
        },

        setProgress(newVal) {
            let progress = null;

            if(newVal != null) {
                progress = newVal / 100;
            }

            this.progressEl.draw(progress);
        },

        buttonClicked(event) {
            if(this.disabled) {
                return;
            }

            this.$emit('click', event);
        },

        toggleInfinityMode() {
            if(this.loading && this.progress == null && this.status == null) {
                this.startInfinityMode();
            } else {
                this.stopInfinityMode();
            }
        },

        startInfinityMode() {
            if(this.inInfinityMode) {
                return;
            }

            this.inInfinityMode = true;
            this.setProgress(95);
        },

        stopInfinityMode() {
            if(! this.inInfinityMode) {
                return;
            }

            this.setProgress(null);
            this.inInfinityMode = false;
        }
    }
    ,

    watch: {
        progress(newVal) {
            this.setProgress(newVal);
            this.toggleInfinityMode();
        },

        loading(newVal) {
            this.toggleInfinityMode();
        },

        status(newVal) {
            if(newVal == 'success') {
                this.successEl.draw(1);
            } else if(newVal == 'error') {
                this.errorEl.draw(1);
            } else {
                this.successEl.draw(0);
                this.errorEl.draw(0);
            }
        }
    },

    computed: {
        classes() {
            return {
                'success': this.status == 'success',
                'error': this.status == 'error',
                'loading': (this.loading || (this.progress != null) && this.status == null),
                'progress': this.progress != null,
                'infinity': this.inInfinityMode

            };
        },

        disabled() {
            return this.isDisabled || this.inInfinityMode || this.progress != null || this.status != null;
        }

    },

    components: {}

}