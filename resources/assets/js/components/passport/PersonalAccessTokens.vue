<template>
    <div class="access-tokens-card">
        <div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div>
                        <span>
                            Access Tokens
                        </span>

                        <a class="action-link" @click="showCreateTokenForm">
                            Create New Token
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <!-- No Tokens Notice -->
                    <p class="m-b-none empty-message" v-if="!loading && tokens.length === 0">
                        You have not created any access tokens.
                    </p>

                    <p class="m-b-none loading-message" v-if="loading">
                        Loading...
                    </p>

                    <!-- Personal Access Tokens -->
                    <table class="table table-borderless" v-if="tokens.length > 0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="token in tokens">
                                <!-- Client Name -->
                                <td style="vertical-align: middle;">
                                    {{ token.name }}
                                </td>

                                <!-- Delete Button -->
                                <td style="vertical-align: middle;">
                                    <a class="action-link text-danger" @click="revoke(token)">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Token Modal -->
        <div class="modal" tabindex="-1" role="dialog" v-if="showCreateModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close ignore-global" @click="showCreateModal = false">&times;</button>

                        <h4 class="modal-title">
                            Create Token
                        </h4>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in form.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Create Token Form -->
                        <form role="form" @submit.prevent="store">
                            <!-- Name -->
                            <div class="form-group">
                                <label class="control-label">Name</label>

                                <div>
                                    <input id="create-token-name"
                                           type="text"
                                           class="form-control"
                                           name="name"
                                           placeholder="e.g. my-website.com"
                                           v-model="form.name">
                                </div>
                            </div>

                            <!-- Scopes -->
                            <div class="form-group" v-if="scopes.length > 0">
                                <label class="col-md-4 control-label">Scopes</label>

                                <div class="col-md-6">
                                    <div v-for="scope in scopes">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                    @click="toggleScope(scope.id)"
                                                    :checked="scopeIsAssigned(scope.id)">

                                                    {{ scope.id }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="default" @click="showCreateModal = false">
                            Close
                        </button>

                        <button type="button" @click="store">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Access Token Modal -->
        <div class="modal access-token-copy-modal" tabindex="-1" role="dialog" v-if="showTokenModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close ignore-global" @click="showTokenModal = false" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">
                            Personal Access Token
                        </h4>
                    </div>

                    <div class="modal-body">
                        <p>
                            Here is your new access token. This is the only time it will be shown so don't lose it!
                            You may now use this token to make API requests.
                        </p>

                        <p class="copy-massage" v-if="copyMassage">
                            {{ copyMassage }}
                        </p>

                        <div class="access-token-field-container">
                            <input type="text" class="access-token-field" v-model="accessToken" readonly>
                            <button v-clipboard="accessToken"
                                    @success="handleSuccessCopy"
                                    @error="handleErrorCopy">
                                Copy
                            </button>
                        </div>

                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="default" @click="showTokenModal = false">Close</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import {Http} from '../../http/index';

    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                loading: true,
                accessToken: null,
                http: new Http,
                copyMassage: null,
                copyMassageClock: null,
                tokens: [],
                scopes: [],

                showCreateModal: false,
                showTokenModal: false,

                form: {
                    name: '',
                    scopes: [],
                    errors: []
                }
            };
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getTokens();
                this.getScopes();

//                $('#modal-create-token').on('shown.bs.modal', () => {
//                    $('#create-token-name').focus();
//                });
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getTokens() {
                this.http.get('/oauth/personal-access-tokens')
                        .then(response => {
                            this.loading = false;
                            this.tokens = response.data;
                        });
            },

            /**
             * Get all of the available scopes.
             */
            getScopes() {
                this.http.get('/oauth/scopes')
                        .then(response => {
                            this.scopes = response.data;
                        });
            },

            /**
             * Show the form for creating new tokens.
             */
            showCreateTokenForm() {
                this.showCreateModal = true;
            },

            /**
             * Create a new personal access token.
             */
            store() {


                this.accessToken = null;

                this.form.errors = [];

                this.http.post('/oauth/personal-access-tokens', this.form)
                        .then(response => {
                            this.form.name = '';
                            this.form.scopes = [];
                            this.form.errors = [];

                            this.tokens.push(response.data.token);

                            this.showAccessToken(response.data.accessToken);
                        })
                        .catch(error => {
                            if (typeof error.response.data === 'object') {
                                this.form.errors = _.flatten(_.toArray(error.response.data));
                            } else {
                                this.form.errors = ['Something went wrong. Please try again.'];
                            }
                        });
            },

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {
                this.showCreateModal = false;

                this.accessToken = accessToken;

                this.showTokenModal = true;
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                this.http.delete('/oauth/personal-access-tokens/' + token.id)
                        .then(response => {
                            this.getTokens();
                        });
            },

            handleSuccessCopy() {
                if(this.copyMassage) {
                    this.removeCopyMassage(true);
                }

                this.copyMassage = "the token successfully copied to clipboard";
                this.removeCopyMassage();
            },

            handleErrorCopy() {
                if(this.copyMassage) {
                    this.removeCopyMassage(true);
                }

                this.copyMassage = "We could not copy the token in to the clipboard :(";
                this.removeCopyMassage();
            },

            removeCopyMassage(fast = false) {
                if(this.copyMassageClock) {
                    clearTimeout(this.copyMassageClock);
                    this.copyMassageClock = null;
                }

                this.copyMassageClock = setTimeout(function () {
                    this.copyMassage = null;
                    this.copyMassageClock = null;
                }.bind(this), fast ? 0 : 8000);
            }
        }
    }
</script>
