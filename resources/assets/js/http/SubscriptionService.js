import HttpService from './HttpService';

export default class SubscriptionService extends HttpService
{
    get actionUrl() { return "subscribe" };

    constructor ()
    {
        super();
    }

    subscribe(email, list)
    {
        return this.post("", {
            email,
            list
        });
    }

}