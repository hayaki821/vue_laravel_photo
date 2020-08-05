import Vue from "vue";
import VueRouter from "vue-router";
import PhotoList from "../components/PhotoList.vue";
import Login from "../components/Login.vue";

Vue.use(VueRouter);

const routes = [
    {
        path: "/",
        component: PhotoList
    },
    {
        path: "/login",
        component: Login
    }
];

const router = new VueRouter({
    mode: "history",
    base: process.env.BASE_URL,
    routes
});

export default router;
