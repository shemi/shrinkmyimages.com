import {PageService} from '../http';

export default {

    data() {
        return {
            isFetchingContent: false,
            contentComponentName: null,
            content: null,
            contentComponentReady: false,
            pageHttpService: new PageService
        }
    },

    mounted() {
        this.fetchPageContent();
    },

    methods: {
        fetchPageContent() {
            let cachedContent = this.$store.getters.getPageByName(this.pageSlug);

            if (cachedContent) {
                this.content = cachedContent.content;

                return;
            }

            this.isFetchingContent = true;

            this.pageHttpService.fetch(this.pageSlug)
                .then(res => {
                    this.$store.commit('addPage', {
                        slug: this.pageSlug,
                        content: res.data.content
                    });

                    this.content = res.data.content;
                })
                .catch((err) => {
                    let code = err.response.data.code;
                    let message = err.response.data.message;

                    if (code == 404) {
                        return this.$router.push('404');
                    }

                    if (code == 500) {
                        return this.$router.push('500');
                    }
                });
        },

        createElement() {
            this.contentComponentName = `${this.pageSlug}-page-content`;

            Vue.component(this.contentComponentName, (resolve, reject) => {
                if(! this.content) {
                    reject('no content');

                    return;
                }

                resolve({
                    template: `<div>${this.content}</div>`
                });
            });
        }
    },

    watch: {
        content(newVal, oldVal) {
            if(newVal === oldVal) {
                return;
            }

            if(newVal) {
                this.createElement();
                this.contentComponentReady = true;
            }
        }
    },

    components: {}

}