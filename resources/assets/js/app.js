import './bootstrap';

import './filters/fileSize';

import router from './router';
import store from './store';

import auth from './mixins/auth';

import AdManager from './components/ad-manager/AdManager.vue';
import UploadCard from './components/upload-card/UploadCard.vue';
import SubscribeForm from './components/subscribe-form/SubscribeForm.vue';

import { mixin as focusMixin }  from 'vue-focus';

Vue.component('subscribe-form', SubscribeForm);

const app = new Vue({
    el: '#app',
    router,
    store,

    mixins: [auth, focusMixin],

    created() {
        if(window.SMI.state.user) {
            this.setUser(window.SMI.state.user);
        }
    },

    components: {
        UploadCard,
        AdManager
    }
});
