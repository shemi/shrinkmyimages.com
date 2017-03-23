<template>
    <div class="card-container">
        <form @submit.prevent="login" class="auth-main-form">
            <div class="form-group">
                <label for="email" class="sr-only" :class="{'has-error': form.errors.has('email')}">Email</label>
                <input type="text" id="email" placeholder="Email" v-model="form.email" v-focus.lazy="true">
                <span class="help-block error" v-if="form.errors.has('email')">
                    {{ form.errors.get('email') }}
                </span>
            </div>
            <div class="form-group" :class="{'has-error': form.errors.has('password')}">
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" placeholder="Password" v-model="form.password">
                <span class="help-block error" v-if="form.errors.has('password')">
                    {{ form.errors.get('password') }}
                </span>
            </div>

            <div class="submit-group" v-show="! form.busy && ! form.successful && ! fail">
                <button type="submit">Log Me In</button>
            </div>

            <div class="loading-container" v-show="form.busy || form.successful || fail">
                <loader :success="form.successful" :fail="fail"></loader>
            </div>

            <div class="help-group">
                <router-link to="/auth/forgot">Forgot your password?</router-link>
                <router-link to="/auth/register">Don't have an account? register now</router-link>
            </div>

        </form>
    </div>
</template>

<script>
    import auth from '../../mixins/auth';
    import loader from '../../components/loader/Loader.vue';
    import { mixin as focusMixin }  from 'vue-focus';

    export default {
        name: 'login',

        mixins: [auth, focusMixin],

        data() {
            return {
                form: new SmiForm({
                    'email': null,
                    'password': null,
                    'name': null
                }),
                loading: false,
                success: false,
                fail: false
            }
        },

        mounted() {
            this.$nextTick(function () {
                this.redirectIfAuthenticated();
            });
        },

        methods: {

            login() {
                let self = this;

                this.form.post("/login")
                        .then(function (user) {
                            self.setUser(user);

                            self.$nextTick(function () {
                                self.redirectIfAuthenticated();
                            });
                        })
                        .catch(function (err) {
                            self.fail = true;

                            setTimeout(function () {
                                self.fail = false;
                            }, 1500);
                        });
            }

        },

        components: {
            loader
        }
    }
</script>