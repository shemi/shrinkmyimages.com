<template>
    <div class="card-container">
        <form @submit.prevent="register" class="auth-main-form">
            <div class="form-group" :class="{'has-error': form.errors.has('name')}">
                <label for="name" class="sr-only">Your name</label>
                <input type="text" id="name" placeholder="Your name" v-model="form.name" v-focus.lazy="true">
                <span class="help-block error" v-if="form.errors.has('name')">
                    {{ form.errors.get('name') }}
                </span>
            </div>
            <div class="form-group" :class="{'has-error': form.errors.has('email')}">
                <label for="email" class="sr-only">Email</label>
                <input type="text" id="email" placeholder="Email" v-model="form.email">
                <span class="help-block error" v-if="form.errors.has('email')">
                    {{ form.errors.get('email') }}
                </span>
            </div>
            <div class="form-group" :class="{'has-error': form.errors.has('password')}">
                <label for="password" class="sr-only">Email</label>
                <input type="password" id="password" placeholder="Password" v-model="form.password">
                <span class="help-block error" v-if="form.errors.has('password')">
                    {{ form.errors.get('password') }}
                </span>
            </div>

            <div class="help-group terms">
                <p>
                    By clicking "REGISTER" you confirm that you have read and agree to Shrink my images general terms and privacy policy.
                </p>
            </div>

            <div class="submit-group" v-show="! form.busy && ! form.successful && ! fail">
                <button type="submit">Register</button>
            </div>

            <div class="loading-container" v-show="form.busy || form.successful || fail">
                <loader :success="form.successful" :fail="fail"></loader>
            </div>
        </form>
    </div>
</template>

<script>
    import auth from '../../mixins/auth';
    import loader from '../../components/loader/Loader.vue';
    import { mixin as focusMixin }  from 'vue-focus';

    export default {
        name: 'register',

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
            this.redirectIfAuthenticated();
        },

        methods: {

            register() {
                let self = this;

                this.form.post("/register")
                        .then(function (state) {
                            self.setAppState(state);

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
            },



        },

        components: {
            loader
        }
    }
</script>