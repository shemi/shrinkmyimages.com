import Dropzone from 'dropzone';

Dropzone.prototype.getActiveFiles = function() {
    var file, _i, _len, _ref, _results;
    _ref = this.files;
    _results = [];

    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        file = _ref[_i];
        _results.push(file);
    }

    return _results;
};

export default {

    props: ['sessionId'],

    data() {
        return {
            dropzone: null,
            shrinkId: null,
            errorMessage: "",
            dropzoneEvents: [
                'added_file',
                'removed_file',
                'thumbnail',
                'error',
                'processing',
                'upload_progress',
                'sending',
                'complete',
                'canceled',
                'max_files_reached',
                'max_files_exceeded',
                'processing_multiple',
                'sending_multiple',
                'success_multiple',
                'complete_multiple',
                'canceled_multiple',
                'total_upload_progress',
                'reset',
                'queue_complete'
            ]
        }
    },

    mounted() {
        this.init();
    },

    methods: {
        init() {
            this.mountDropzone();
            this.setupListeners();
        },

        mountDropzone() {
            this.dropzone = new Dropzone(document.body, {
                url: "/shrink/{id}/upload",
                previewsContainer: false,
                clickable: "#upload-button",
                acceptedFiles: 'image/*',
                previewTemplate: '',
                paramName: 'image',
                parallelUploads: 2,
                createImageThumbnails: false,
                uploadMultiple: false,
                autoProcessQueue: false,
                maxFilesize: 15,
                headers: {
                    'X-CSRF-TOKEN': window.SMI.csrfToken,
                },
                accept: this.isAccepted.bind(this)
            });
        },

        setupListeners() {
            let self = this;

            for(let eventIndex in this.dropzoneEvents) {
                let eventName = this.dropzoneEvents[eventIndex],
                    methodName = this.eventNameToMethod(eventName),
                    dropzoneEventName = eventName.replace(/_/g, '');

                this.dropzone.on(dropzoneEventName, function () {
                    let eventArguments = arguments;

                    setTimeout(function () {
                        self[methodName].apply(self, eventArguments);
                        self.$emit(methodName, eventArguments);
                    }, 0);
                });
            }
        },

        reset() {
            this.dropzone.removeAllFiles(true);
            this.dropzone.setupEventListeners();
            this.dropzone.options.url = this.dropzone.options.url.replace(this.shrinkId, '{id}');
            this.shrinkId = null;
        },

        removeFile(file) {
            this.dropzone.removeFile(file);
        },

        isAccepted(file, done) {
            let exist = this.$store.getters.getFileByName(file.name);

            if(exist) {
                return done('We can\'t process multiple files with the same name.');
            }

            done();
        },

        eventNameToMethod(eventName) {
            eventName = `on_${eventName}`;
            let eventSplit = eventName.split('_');

            eventSplit = eventSplit.map(function(segment, index) {
                if(index == 0) {
                    return segment;
                }

                return segment.charAt(0).toUpperCase() + segment.slice(1);
            });

            return eventSplit.join('');
        },

        processQueue(shrinkId) {
            if(shrinkId) {
                this.shrinkId = shrinkId;
                this.dropzone.removeEventListeners();
                this.dropzone.options.url = this.dropzone.options.url.replace('{id}', shrinkId);
            } else if(! this.shrinkId) {
                return;
            }

            this.dropzone.updateTotalUploadProgress();
            this.dropzone.processQueue();
        },


        onAddedFile(file) {
            if(! file.accepted) {
                return;
            }

            this.$store.commit('addFile', file);
        },

        onRemovedFile(file) {
            this.$store.commit('removeFile', file);
        },

        onThumbnail(file, dataUrl) {

        },

        onError(file, errorMessage) {
            this.errorMessage = errorMessage;

            setTimeout(function () {
                this.errorMessage = "";
            }.bind(this), 15000);
        },

        onProcessing(file) {
            this.$store.dispatch('updateFile', file);
        },

        onUploadProgress(file, progress, bytesSent) {
            this.$store.dispatch('updateFile', file);
        },

        onSending(file, xhr, formData) {
            this.$store.dispatch('updateFile', file);
        },

        onSuccess(file) {
            this.$store.dispatch('updateFile', file);
        },

        onComplete(file) {
            this.$store.dispatch('updateFile', file);

            if(this.dropzone.getQueuedFiles().length > 0) {
                this.processQueue();
            }
        },

        onCanceled(file) {
        },

        onMaxFilesReached(file) {

        },

        onMaxFilesExceeded(file) {

        },

        onProcessingMultiple(files) {

        },

        onSendingMultiple(files) {

        },

        onSuccessMultiple(files) {

        },

        onCompleteMultiple(files) {

        },

        onCanceledMultiple(files) {

        },

        onTotalUploadProgress(uploadProgress, totalBytes, totalBytesSent) {

        },

        onReset() {

        },

        onQueueComplete() {
            console.log('queueComplete');
        }

    },

    computed: {
        files() {
            console.log(this.$store.getters.files);
            return this.$store.getters.files;
        },

        addButtonText() {
            return this.files.length >= 1 ? "Add more images" : "Add your images";
        }

    }


}