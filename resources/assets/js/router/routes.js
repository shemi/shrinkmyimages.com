import NotFound from '../views/NotFound';
import Home from '../views/home/Home.vue';

const routes = [
    {
        path      : '/',
        component : Home
    },
    {
        path      : '*',
        component : NotFound
    }
];

export default routes;
