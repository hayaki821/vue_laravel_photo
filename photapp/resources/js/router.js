import Vue from "vue";
import VueRouter from "vue-router";

// ページコンポーネントをインポートする
import PhotoList from "./pages/PhotoList.vue";
import Login from "./pages/Login.vue";
import SystemError from "./pages/errors/System.vue";
import PhotoDetail from './pages/PhotoDetail.vue'

import store from "./store";
// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter);

// パスとコンポーネントのマッピング
const routes = [{
        path: "/",
        component: PhotoList,
        props: route => {
            const page = route.query.page
            return {
                page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1
            }
        }
    },
    {
        path: '/photos/:id',
        component: PhotoDetail,
        //props: true //props: true はその変数部分（写真IDの値）を props として受け取る
        props: route => ({
            id: route.params.id,
        })
    },
    {
        path: "/login",
        component: Login,
        beforeEnter(to, from, next) {
            //ログイン状態でログインページへアクセスした際はトップページに移動させる
            if (store.getters["auth/check"]) {
                next("/");
            } else {
                next();
            }
        }
    },
    {
        path: '*',
        redirect: '/'
    },
    {
        path: "/500",
        component: SystemError
    }
];

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: 'hash',
    base: process.env.BASE_URL,
    scrollBehavior() {
        return {
            x: 0,
            y: 0
        }
    },
    routes
});

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router;
