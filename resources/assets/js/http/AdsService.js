import HttpService from './HttpService';

export default class AdsService extends HttpService
{
    get actionUrl() { return "ads" };

    constructor ()
    {
        super();
    }

}