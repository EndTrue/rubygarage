<template>
    <div class="container text-center">
        <div class="row">
            <div class="col-xs-1 col-md-4"></div>
            <div class="col-xs-10 col-md-4">
                <form class="form-signin" @submit.prevent="authenticate">
                    <img class="mb-4" src="../../../logo_icon.svg" alt="" width="72" height="72">
                    <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
                    <div class="form-group">
                        <label for="inputEmail" class="sr-only">Email address</label>
                        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus  v-model="form.email">
                    </div>

                    <div class="form-group">
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required  v-model="form.password">
                    </div>
                        <button class="btn btn-primary btn-block" type="button" disabled v-if="isLoading">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <button class="btn btn-primary btn-block" type="submit" v-else>Sign in</button>
                    
                    <div class="alert alert-warning" v-if="authError">
                            {{ authError }}
                    </div>
                    
                </form>
            </div>
            <div class="col-xs-1 col-md-4"></div>
        </div>
    </div>
</template>

<script>
    import {login} from '../../helpers/auth';

    export default {
        name: "login",
        data() {
            return {
                form: {
                    email: '',
                    password: ''
                },
                error: null
            };
        },
        methods: {
            authenticate() {
                this.$store.dispatch('login');

                login(this.$data.form)
                    .then((res) => {
                        this.$store.commit("loginSuccess", res);
                        this.$router.push({path: '/tasks'});
                    })
                    .catch((error) => {
                        this.$store.commit("loginFailed", {error});
                    });
            }
        },
        computed: {
            authError() {
                return this.$store.getters.authError;
            },
            isLoading(){
                return this.$store.getters.isLoading;
            }
            
        }
    }
</script>
