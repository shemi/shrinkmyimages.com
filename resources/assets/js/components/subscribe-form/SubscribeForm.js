import Loader from '../loader/Loader.vue';
import {SubscriptionService} from '../../http';

export default {
    props: ['list'],

    data() {
        return {
            service: new SubscriptionService,
            loading: false,
            success: false,
            email: "",
            error: null
        }
    },

    methods: {
        submit($event) {
            if(! this.email || ! this.validateEmail()) {
                this.error = "Please enter valid Email.";

                return;
            }

            this.loading = true;

            this.service.subscribe(this.email, this.list)
                .then((res) => {
                    this.loading = false;
                    this.success = true;
                })
                .catch(() => {
                    this.loading = false;
                    this.email = "";
                    this.error = 'internal error, please try again later.';
                });

        },

        validateEmail() {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(this.email);
        }
    },

    watch: {
        email(newVal) {
            if(newVal == "") {
                return;
            }

            this.error = null;
        }
    },

    computed: {

    },

    components: {
        Loader
    }

}