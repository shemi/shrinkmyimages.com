import HttpService from './HttpService';

export default class ShrinkService extends HttpService
{
    get actionUrl() { return "shrink" };

    constructor ()
    {
        super();
    }

    create(settings)
    {
        return this.post("", settings);
    }

}