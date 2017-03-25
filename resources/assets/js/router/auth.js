import {Auth, Login, Register, Forgot, Reset} from '../views/auth/index';

const routs = [
    {
        path: '/auth',
        component: Auth,
        redirect: '/auth/login',
        meta: {
            middleware: 'guest'
        },
        children: [
            {
                meta: {
                    middleware: 'guest'
                },
                path: 'login',
                component: Login
            },
            {
                meta: {
                    middleware: 'guest'
                },
                path: 'register',
                component: Register
            },
            {
                meta: {
                    middleware: 'guest'
                },
                path: 'forgot',
                component: Forgot
            },
            {
                meta: {
                    middleware: 'guest'
                },
                path: 'reset/:token',
                component: Reset
            }
        ]
    },
];

export default routs;