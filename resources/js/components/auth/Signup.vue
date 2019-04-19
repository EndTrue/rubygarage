<template>
    <div class="container text-center">
        <div class="row">
            <div class="col-xs-1 col-md-4"></div>
            <div class="col-xs-10 col-md-4">
                <form class="form-signin" @submit.prevent="registrate">
                    <img class="mb-4" src="../../../logo_icon.svg" alt="" width="72" height="72">
                    <h1 class="h3 mb-3 font-weight-normal">Please register</h1>

                    <div class="form-group">
                        <label for="inputName" class="sr-only">Name</label>
                        <input type="text" id="inputName" class="form-control" placeholder="Name" required autofocus  v-model="form.name">
                    </div>


                    <div class="form-group">
                        <label for="inputEmail" class="sr-only">Email address</label>
                        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus  v-model="form.email">
                    </div>

                    <div class="form-group">
                        <label for="inputPassword" class="sr-only">Password</label>
                        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required  v-model="form.password">
                    </div>

                    <div class="form-group">
                        <label for="inputPassword" class="sr-only">Confirm Password</label>
                        <input type="password" id="inputRePassword" class="form-control" placeholder="Confirm Password" required  v-model="form.repassword">
                    </div>
                    <button class="btn btn-primary btn-block  btn-add" type="button" disabled v-if="isLoading">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <button class="btn btn-primary btn-block btn-add" type="submit" v-else>Sign Up</button>
                    
                    <div class="alert alert-warning mt-3" v-if="regError">
                            <ul>
                                <li v-for="value in validationErrors">{{ value }}</li>
                            </ul>
                    </div>
                </form>
            </div>
            <div class="col-xs-1 col-md-4"></div>
        </div>
    </div>
</template>

<script>
    import {registration} from '../../helpers/auth';

    export default {
        name: "signup",
        data() {
            return {
                form: {
                    name:'',
                    email: '',
                    password: '',
                    repassword:'',
                },
                error: null
            };
        },
        methods: {
            registrate() {
                this.$store.dispatch('signup');

                registration(this.$data.form)
                    .then((res) => {
                        if (res.error){
                            this.$store.commit("regFailed", res.error);
                        } else{
                            this.$store.commit("regSuccess", res);
                            this.$router.push({path: '/login'});   
                        }
                        
                    })
                    .catch((error) => {
                        this.$store.commit("regFailed", {error});
                    });
            }
        },
        computed: {
            regError() {
                return this.$store.getters.regError;
            },
            isLoading() {
                return this.$store.getters.isLoading;
            },
            validationErrors(){
                let errors = Object.values(this.regError);
                errors = errors.flat();
                return errors;
            }
            
        }
    }
</script>

<style>
    .btn-add{
        background-image: linear-gradient(#517EBD, #3A609C);
        border: rgb(48, 82, 138);
        font-weight: bold;
    }
</style>