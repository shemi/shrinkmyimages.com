import './bootstrap';

import './filters/fileSize';

import router from './router';
import store from './store';

import auth from './mixins/auth';

import AdManager from './components/ad-manager/AdManager.vue';
import UploadCard from './components/upload-card/UploadCard.vue';
import SubscribeForm from './components/subscribe-form/SubscribeForm.vue';

import VueClipboards from 'vue-clipboards';

import { mixin as focusMixin }  from 'vue-focus';

Vue.component('subscribe-form', SubscribeForm);

Vue.use(VueClipboards);

const app = new Vue({
    el: '#app',
    router,
    store,

    mixins: [auth, focusMixin],

    created() {
        this.setAppState(window.SMI.state);
    },

    components: {
        UploadCard,
        AdManager
    }
});
