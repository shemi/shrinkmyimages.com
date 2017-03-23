<template>
    <div class="card-container">
        <form @submit.prevent="sendResetLinkEmail" class="auth-main-form">

            <div class="form-error-massage" v-if="form.errors.has('form')">
                {{ form.errors.get('form') }}
            </div>

            <div class="form-success-massage" v-if="massage">
                {{ massage }}
            </div>

            <div class="form-group" :class="{'has-error': form.errors.has('email')}">
                <label for="email" class="sr-only">Email</label>
                <input type="text" id="email" placeholder="Email" v-model="form.email" v-focus.lazy="true">
                <span class="help-block error" v-if="form.errors.has('email')">
                    {{ form.errors.get('email') }}
                </span>
            </div>

            <div class="submit-group" v-show="! form.busy && ! form.successful && ! fail">
                <button type="submit">Send Password Reset Link</button>
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
                    'email': null
                }),
                loading: false,
                success: false,
                fail: false,
                massage: null
            }
        },

        mounted() {
            this.redirectIfAuthenticated();
        },

        methods: {

            sendResetLinkEmail() {
                let self = this;

                this.form.post("/password/email")
                        .then(function (res) {
                            self.massage = res;
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