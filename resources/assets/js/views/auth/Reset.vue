<template>
    <div class="card-container">

        <form @submit.prevent="reset" class="auth-main-form">

            <div class="form-error-massage" v-if="form.errors.has('form')">
                {{ form.errors.get('form') }}
            </div>

            <div class="form-success-massage" v-if="massage">
                {{ massage }}
            </div>

            <div class="form-group" :class="{'has-error': form.errors.has('email')}">
                <label for="email" class="sr-only">Email</label>
                <input type="text" id="email" placeholder="Email" v-model="form.email">
                <span class="help-block error" v-if="form.errors.has('email')">
                    {{ form.errors.get('email') }}
                </span>
            </div>

            <div class="form-group" :class="{'has-error': form.errors.has('password')}">
                <label for="password" class="sr-only">Password</label>
                <input type="password" id="password" placeholder="Password" v-model="form.password" v-focus.lazy="true">
                <span class="help-block error" v-if="form.errors.has('password')">
                    {{ form.errors.get('password') }}
                </span>
            </div>


            <div class="form-group" :class="{'has-error': form.errors.has('password_confirmation')}">
                <label for="password_confirmation" class="sr-only">Confirm Password</label>
                <input type="password" id="password_confirmation" placeholder="Confirm Password" v-model="form.password_confirmation">
                <span class="help-block error" v-if="form.errors.has('password_confirmation')">
                    {{ form.errors.get('password_confirmation') }}
                </span>
            </div>

            <div class="submit-group" v-show="! form.busy && ! form.successful && ! fail">
                <button type="submit">Reset Password</button>
            </div>

            <div class="loading-container" v-show="form.busy || form.successful || fail">
                <loader :success="form.successful" :fail="fail"></loader>
            </div>
        </form>
    </div>
</template>

<script>
    import {Http} from '../../http/index';
    import auth from '../../mixins/auth';
    import loader from '../../components/loader/Loader.vue';
    import { mixin as focusMixin }  from 'vue-focus';

    export default {
        name: 'reset',

        mixins: [auth, focusMixin],

        data() {
            return {
                form: new SmiForm({
                    'token': null,
                    'password': null,
                    'password_confirmation': null,
                    'email': null
                }),
                loading: false,
                fail: false,
                success: false,
                massage: null
            }
        },

        mounted() {
            this.redirectIfAuthenticated();

            if(! this.$route.params.token) {
                this.redirectHome();
            } else {
                this.form.token = this.$route.params.token;
            }

            if(this.$route.query.email) {
                this.form.email = this.$route.query.email;
            }

        },

        methods: {

            reset() {
                let self = this;

                this.form.post("/password/reset")
                        .then(function (state) {
                            self.setAppState(state);

                            self.$nextTick(function () {
                                self.redirectIfAuthenticated();
                            });
                        })
                        .catch(function () {
                            self.fail = true;

                            setTimeout(function () {
                                self.fail = false;
                            }, 1500);
                        });
            },

        },

        components: {
            loader
        }
    }
</script>