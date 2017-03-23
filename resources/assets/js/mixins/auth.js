import {Http} from '../http/index';

export default {

    methods: {
        setUser(user) {
            this.$store.commit('setUser', user);
        },

        redirectIfAuthenticated() {
            if(this.isLoggedIn) {
                this.redirectHome();
            }
        },

        redirectHome(to = '/') {
            this.redirect();
        },

        redirect(to = '/') {
            this.$router.push(to);
        },

        logout() {
            let http = new Http();
            http.post('/logout')
                .then(() => {
                    window.location.href = window.location.href + '';
                });
        }

    },

    computed: {
        user() {
            return this.$store.getters.user;
        },

        isLoggedIn() {
            return (this.user && this.user.id);
        }
    }

}