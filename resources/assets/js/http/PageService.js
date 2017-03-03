import HttpService from './HttpService';

export default class PageService extends HttpService
{
    get actionUrl() { return "page" };

    constructor ()
    {
        super();
    }

    fetch(slug)
    {
        return this.get(slug);
    }

}