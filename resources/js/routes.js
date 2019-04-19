import Login from './components/auth/Login.vue';
import Signup from './components/auth/Signup.vue';
import Tasks from './components/tasks/View.vue';
import Notfound from './components/Notfound.vue';

export const routes = [
    {
        path: '/', redirect: '/tasks'
    },
    {
        path: '/login',
        component: Login
    },
    {
        path: '/signup',
        component: Signup
    },
    {
        path: '/tasks',
        component: Tasks,
        meta: {
            requiresAuth: true
        }
    },
    {
        path: '*',
        component: Notfound
    }
];