import UploadForm from '../../components/upload-form/UploadForm.vue';
import ProgressButton from '../../components/progress-button/ProgressButton.vue';
import SmiLoader from '../../components/loader/Loader.vue';

import {ShrinkService} from '../../http';


export default {
    data() {
        return {
            service: new ShrinkService(),
            settings: {
                mode: 'best',
                width: null,
                height: null
            },
            shrink: {},
            inUploadMode: false,
            loading: false,
            totalProgress: null,
            status: null
        }
    },

    methods: {
        shrinkThem() {
            this.loading = true;
            this.inUploadMode = true;

            this.service.create(this.settings)
                .then(res => {
                    this.shrink = res.data;
                    Vue.nextTick(function () {
                        this.uploadFiles();
                    }.bind(this));
                })
                .catch(err => {
                    this.loading = false;
                    this.status = 'error';
                });
        },

        uploadFiles() {
            this.$refs.uploadForm.processQueue(this.shrink.id);
        },

        updateProgress(progress) {
            this.totalProgress = progress[0];
        },

        uploadFinish() {
            Vue.nextTick(function () {
                this.totalProgress = null;
                this.getShrinkInfo();
            }.bind(this));
        },

        doFormAction() {
            if(this.shrink.downloadLink) {
                window.open(this.shrink.downloadLink);

                return;
            }

            this.shrinkThem();
        },

        getShrinkInfo() {
            this.service.get(this.shrink.id + "")
                .then(res => {
                    this.shrink = res.data;
                    this.status = 'success';

                    setTimeout(function () {
                        this.loading = false;
                        this.status = null;
                    }.bind(this), 1500);
                })
                .catch(err => {
                    this.status = 'error';
                });
        }

    },

    computed: {
        files() {
            return this.$store.getters.files;
        },

        haveFiles() {
            return this.files.length > 0;
        }

    },

    components: {
        UploadForm,
        ProgressButton,
        SmiLoader
    }

}