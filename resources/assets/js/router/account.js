import {Account, Web, Api} from '../views/account/index';

const routs = [
    {
        path      : '/account',
        component : Account,
        meta: {
            middleware: 'auth'
        },
        children: [
            {
                path      : '/',
                meta: {
                    middleware: 'auth'
                },
                component: Web
            },
            {
                path      : 'api',
                meta: {
                    middleware: 'auth'
                },
                component: Api
            }
        ]
    },
];

export default routs;