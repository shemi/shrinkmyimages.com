<template>
    <page-template class="account-layout">
        <div class="main-page-content">
            <router-view></router-view>
        </div>

        <div class="side-bar">
            <nav class="side-navigation">
                <ul>
                    <router-link to="/account" tag="li" exact>
                        <a>WEB</a>
                    </router-link>
                    <router-link to="/account/api" tag="li">
                        <a>API</a>
                    </router-link>
                    <router-link to="/account/securety" tag="li">
                        <a>Securety</a>
                    </router-link>
                    <router-link to="/account/logout" tag="li">
                        <a>Logout</a>
                    </router-link>

                </ul>
            </nav>
        </div>
    </page-template>
</template>

<script>
    import pageTemplate from '../../components/PageTemplate.vue';

    export default {

        data() {
            return {

            }
        },

        components: {pageTemplate}

    }

</script>