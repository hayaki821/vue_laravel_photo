<template>
<div>
    <header>
        <Navbar />
    </header>
    <main>
        <div class="container">
            <Message />
            <RouterView />
        </div>
    </main>
    <Footer />
</div>
</template>

<script>
import Message from "./components/Message.vue";
import Navbar from "./components/Navbar.vue";
import Footer from "./components/Footer.vue";
import {
    INTERNAL_SERVER_ERROR
} from "./util";
export default {
    name: "App",
    components: {
        Message,
        Navbar,
        Footer,
    },
};
</script>

<style>
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
}

#nav {
    padding: 30px;
}

#nav a {
    font-weight: bold;
    color: #2c3e50;
}

#nav a.router-link-exact-active {
    color: #42b983;
}
</style>
