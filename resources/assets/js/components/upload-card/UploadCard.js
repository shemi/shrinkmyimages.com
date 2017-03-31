import UploadForm from '../upload-form/UploadForm.vue';
import ProgressButton from '../progress-button/ProgressButton.vue';
import SmiLoader from '../loader/Loader.vue';

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

        removeFile(file) {
            this.$refs.uploadForm.removeFile(file);
        },

        updateProgress(progress) {
            if(! this.shrink.id) {
                return;
            }

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
                this.download(this.shrink.downloadLink);

                return;
            }

            this.shrinkThem();
        },

        download(url) {
            window.open(url);
        },

        back() {
            this.shrink = {};
            this.status = null;
            this.loading = false;
            this.$refs.uploadForm.reset();
            this.inUploadMode = false;
        },

        getShrinkInfo() {
            this.service.get(this.shrink.id + "")
                .then(res => {
                    this.shrink = res.data;
                    this.status = 'success';

                    setTimeout(function () {
                        this.loading = false;
                        this.status = null;
                    }.bind(this), 1000);
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
        },

        webImagesPerShrink() {
            return this.$store.getters.webImagesPerShrink;
        }

    },

    components: {
        UploadForm,
        ProgressButton,
        SmiLoader
    }

}