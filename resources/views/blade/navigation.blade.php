<nav class="navigation">
    <ul class="menu">
        <router-link to="/about" tag="li">
            <a>About</a>
        </router-link>
        <router-link to="/developers" tag="li">
            <a>Developers</a>
        </router-link>
        <router-link to="/wordpress" tag="li">
            <a>WordPress</a>
        </router-link>
        <router-link to="/auth" tag="li" v-if="! isLoggedIn">
            <a>Login/Register</a>
        </router-link>
        <router-link to="/account" tag="li" v-if="isLoggedIn">
            <a>@{{ user.name }}</a>
        </router-link >
    </ul>
</nav>