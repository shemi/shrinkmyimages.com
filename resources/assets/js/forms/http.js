import {Http} from '../http/index';

export default {
    /**
     * Helper method for making POST HTTP requests.
     */
    post(uri, form) {
        return SMI.sendForm('post', uri, form);
    },


    /**
     * Helper method for making PUT HTTP requests.
     */
    put(uri, form) {
        return SMI.sendForm('put', uri, form);
    },


    /**
     * Helper method for making PATCH HTTP requests.
     */
    patch(uri, form) {
        return SMI.sendForm('patch', uri, form);
    },


    /**
     * Helper method for making DELETE HTTP requests.
     */
    delete(uri, form) {
        return SMI.sendForm('delete', uri, form);
    },


    /**
     * Send the form to the back-end server.
     *
     * This function will clear old errors, update "busy" status, etc.
     */
    sendForm(method, uri, form) {
        let http = new Http();

        return new Promise((resolve, reject) => {
            form.startProcessing();

            http[method](uri, JSON.parse(JSON.stringify(form)))
                .then(response => {
                    form.finishProcessing();

                    resolve(response.data);
                })
                .catch(function(errors) {
                    form.errors.set(errors.response.data);
                    form.busy = false;

                    reject(errors.response.data);
                });
        });
    }
};