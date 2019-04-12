import { getLocalUser } from "./helpers/auth";

const user = getLocalUser();

export default {
    state: {
        currentUser: user,
        isLoggedIn: !!user,
        loading: false,
        auth_error: null,
        reg_error: null,
        customers: [],
        tasks: [],
        projects: []
    },
    getters: {
        isLoading(state) {
            return state.loading;
        },
        isLoggedIn(state) {
            return state.isLoggedIn;
        },
        currentUser(state) {
            return state.currentUser;
        },
        authError(state) {
            return state.auth_error;
        },
        regError(state) {
            return state.reg_error;
        },
        customers(state) {
            return state.customers;
        },
        tasks(state) {
            return state.tasks;
        },
        // projects(state) {
        //     return state.projects;
        // }
    },
    mutations: {
        login(state) {
            state.loading = true;
            state.auth_error = null;
        },
        loginSuccess(state, payload) {
            state.auth_error = null;
            state.isLoggedIn = true;
            state.loading = false;
            state.currentUser = Object.assign({}, payload.user, {token: payload.access_token});

            localStorage.setItem("user", JSON.stringify(state.currentUser));
        },
        loginFailed(state, payload) {
            state.loading = false;
            state.auth_error = payload.error;
        },
        logout(state) {
            localStorage.removeItem("user");
            state.isLoggedIn = false;
            state.currentUser = null;
        },
        loading(state) {
            state.loading = true;
        },
        stopLoading(state) {
            state.loading = false;
        },
        updateCustomers(state, payload) {
            state.customers = payload;
        },
        updateTasks(state, payload) {
            state.tasks = payload;
            state.loading = false;
        },
        // updateProjects(state, payload) {
        //     state.projects = payload;
        // },
        signup(state) {
            state.loading = true;
            state.reg_error = null;
        },
        regSuccess(state, payload) {
            state.reg_error = null;
            state.loading = false;
        },
        regFailed(state, payload) {
            state.loading = false;
            state.reg_error = payload;
        },
    },
    actions: {
        login(context) {
            context.commit('login');
        },
        getCustomers(context) {
            axios.get('/api/customers')
            .then((response) => {
                context.commit('updateCustomers', response.data.customers);
            })
        },
        getTasks(context) {
            context.commit('loading');
            axios.get('/api/tasks')
            .then((response) => {
                // console.log(response.data);
                context.commit('updateTasks', response.data);
                // context.commit('updateProjects', response.data.projects);
            })
            .catch((error) => {
                context.commit('stopLoading');
                console.log(error);
            })
        },
        signup(context) {
            context.commit("signup");
        }
    }
};