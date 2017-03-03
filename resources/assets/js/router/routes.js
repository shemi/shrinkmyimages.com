import NotFound from '../views/NotFound';
import Home from '../views/home/Home.vue';
import About from '../views/About.vue';
import Developers from '../views/Developers.vue';
import Wordpress from '../views/Wordpress.vue';
import ServerError from '../views/ServerError.vue';

const routes = [
    {
        path      : '/',
        component : Home
    },
    {
        path      : '/about',
        component : About
    },
    {
        path      : '/developers',
        component : Developers
    },
    {
        path      : '/wordpress',
        component : Wordpress
    },
    {
        path      : '/500',
        component : ServerError
    },
    {
        path      : '*',
        component : NotFound
    }
];

export default routes;
