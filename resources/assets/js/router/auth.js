import {Auth, Login, Register, Forgot, Reset} from '../views/auth/index';

const routs = [
    {
        path      : '/auth',
        component : Auth,
        redirect: '/auth/login',
        children: [
            {
                path      : 'login',
                component: Login
            },
            {
                path      : 'register',
                component: Register
            },
            {
                path      : 'forgot',
                component: Forgot
            },
            {
                path      : 'reset/:token',
                component: Reset
            }
        ]
    },
];

export default routs;