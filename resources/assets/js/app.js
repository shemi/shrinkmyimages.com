import './bootstrap';

import './filters/fileSize';

import router from './router';
import store from './store';

import AdManager from './components/ad-manager/AdManager.vue';
import UploadCard from './components/upload-card/UploadCard.vue';

const app = new Vue({
    el: '#app',
    router,
    store,
    components: {
        UploadCard,
        AdManager
    }
});
