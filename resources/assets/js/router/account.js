import {Account, Web, Api} from '../views/account/index';

const routs = [
    {
        path      : '/account',
        component : Account,
        children: [
            {
                path      : '/',
                component: Web
            },
            {
                path      : 'api',
                component: Api
            }
        ]
    },
];

export default routs;