import {AdsService} from '../../http';

import ImageAd from './image-ad/ImageAd.vue';

export default {

    data() {
        return {
            service: new AdsService(),
            ads: [],
            displayed: 0
        }
    },

    mounted() {
        this.fetchAdsList();
    },

    methods: {
        fetchAdsList() {
            this.service.get()
                .then(res => {
                    this.ads = res.data;
                });
        },
        nextAd() {
            this.displayed = (this.displayed + 1) > (this.ads.length - 1) ? 0 : (this.displayed + 1);
        }
    },

    components: {
        ImageAd
    }

}