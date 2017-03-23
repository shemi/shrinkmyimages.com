<template>
    <div class="auth-page">
        <div class="auth-card">
            <div class="card-nav">
                <nav class="tab-navigation">
                    <ul class="tabs-menu">
                        <router-link to="/auth/login" tag="li">
                            <a>Login</a>
                        </router-link>
                        <router-link to="/auth/register" tag="li">
                            <a>Register</a>
                        </router-link>
                    </ul>
                </nav>
            </div>
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'auth'
    }
</script>