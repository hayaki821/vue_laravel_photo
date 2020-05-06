<template>
  <div id="app">
    <label v-for="label in options" v-bind:key="label.value">
      <input type="radio" v-model="current" v-bind:value="label.value" />
      {{ label.label }}
    </label>
    <table>
      <!-- テーブルヘッダー -->
      <thead>
        <tr>
          <th class="id">ID</th>
          <th class="comment">コメント</th>
          <th class="state">状態</th>
          <th class="button">-</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in computedTodos" v-bind:key="item.id">
          <th>{{ item.id }}</th>
          <td>{{ item.comment }}</td>
          <td class="state">
            <!-- 状態変更ボタンのモック -->
            <button v-on:click="doChangeState(item)">{{ labels[item.state] }}</button>
          </td>
          <td class="button">
            <!-- 削除ボタンのモック -->
            <button v-on:click="doRemove(item)">削除</button>
          </td>
        </tr>
      </tbody>
      {{ computedTodos.length }} 件を表示中
      <button>
        <router-link :to="{ name: 'add_todo' }">タスクの新規追加</router-link>
      </button>
    </table>
  </div>
</template>
<script>
var STORAGE_KEY = "todos-vuejs";
var todoStorage = {
  fetch: function() {
    var todos = JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    todos.forEach(function(todo, index) {
      todo.id = index + 1;
    });
    todoStorage.uid = todos.length + 1;
    return todos;
  },
  save: function(todos) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(todos));
  }
};
export default {
  data() {
    return {
      todos: [],
      options: [
        { value: -1, label: "すべて" },
        { value: 0, label: "作業中" },
        { value: 1, label: "完了" }
      ],
      // 選択している options の value を記憶するためのデータ
      // 初期値を「-1」つまり「すべて」にする
      current: -1
    };
  },
  computed: {
    computedTodos: function() {
      // データ current が -1 ならすべて
      // それ以外なら current と state が一致するものだけに絞り込む
      return this.todos.filter(function(el) {
        return this.current < 0 ? true : this.current === el.state;
      }, this);
    },
    labels: function() {
      return this.options.reduce(function(a, b) {
        console.log(a);
        return Object.assign(a, { [b.value]: b.label });
      }, {});
      // {0: '作業中', 1: '完了', -1: 'すべて'}
    }
  },
  created() {
    // インスタンス作成時に自動的に fetch() する
    this.todos = todoStorage.fetch();
  },
  watch: {
    // オプションを使う場合はオブジェクト形式にする
    todos: {
      // 引数はウォッチしているプロパティの変更後の値
      handler: function(todos) {
        todoStorage.save(todos);
      },
      // deep オプションでネストしているデータも監視できる
      deep: true
    }
  },
  methods: {
    doChangeState: function(item) {
      item.state = item.state ? 0 : 1;
    },
    // 削除の処理
    doRemove: function(item) {
      if (window.confirm("削除します。よろしいですか。")) {
        var index = this.todos.indexOf(item);
        console.log(item);
        console.log(index);
        this.todos.splice(index, 1);
      }
    }
  }
};
</script>
