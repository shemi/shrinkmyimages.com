import Axios from 'axios';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

export default class HttpService
{

    get actionUrl() { return "" };

    constructor ()
    {
        this.instance = Axios.create();

        this.instance.defaults.baseURL = this.getBaseURL();
        this.instance.defaults.headers.common = {
            'X-CSRF-TOKEN': window.SMI.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        };

        this.instance.interceptors.request.use(this.requestInterceptors);
        this.instance.interceptors.response.use(
            this.successResponseInterceptors,
            this.errorResponseInterceptors
        );
    }

    getBaseURL()
    {
        return '/' + this.actionUrl;
    }

    requestInterceptors(request)
    {
        return request;
    }

    successResponseInterceptors(response)
    {
        if(response.data.data) {
            response.data = response.data.data;
        }

        return response;
    }

    errorResponseInterceptors(error)
    {
        if(error.response.data.data) {
            error.response.data = error.response.data.data;
        }

        return Promise.reject(error);
    }

    get(url = "", data = {}, config = {})
    {
        config = Object.assign({}, {
            'params': data
        }, config);

        return this.instance.get(url, config);
    }

    post(url = "", data = {}, config = {})
    {
        return this.instance.post(url, data, config);
    }

    put(url = "", data = {}, config = {})
    {
        return this.instance.put(url, data, config);
    }

    patch(url = "", data = {}, config = {})
    {
        return this.instance.patch(url, data, config);
    }

    delete(url = "", data = {}, config = {})
    {
        config = Object.assign({}, {
            'params': data
        }, config);

        return this.instance.delete(url, config);
    }

}
