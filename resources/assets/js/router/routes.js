import NotFound from '../views/NotFound';
import Home from '../views/home/Home.vue';
import About from '../views/About.vue';

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
        path      : '*',
        component : NotFound
    }
];

export default routes;
