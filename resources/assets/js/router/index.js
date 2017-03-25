import Vue from 'vue';
import Router from 'vue-router';
import routes from './routes';
import store from '../store/index';

Vue.use(Router);

const router = new Router({
    mode : 'history',
    base : '/',
    routes
});

router.beforeEach((to, from, next) => {
    let user = store.getters.user;

    if(! user.id) {
        user = SMI.state.user;
    }

    if(to.matched.some(record => record.meta.middleware == 'auth')) {
        if(! user || ! user.id) {
            next({
                path: '/auth',
                query: { redirect: to.fullPath }
            });

            return;
        }
    }

    if(to.matched.some(record => record.meta.middleware == 'guest')) {
        if(user && user.id) {
            next({
                path: '/'
            });

            return;
        }
    }

    next();
});

export default router;
