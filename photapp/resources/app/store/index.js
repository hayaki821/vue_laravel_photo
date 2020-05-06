import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

const state = {
  auth: {
    token: null,
    userId: null,
  },
  board: {
    lists: [],
  },
};

export default new Vuex.Store({
  state: state,
  mutations: {},
  actions: {},
  modules: {},
});
