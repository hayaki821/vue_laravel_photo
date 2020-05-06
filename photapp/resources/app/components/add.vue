<template>
  <div>
    <h2>新しい作業の追加</h2>
    <form class="add-form" v-on:submit.prevent="doAdd">
      <!-- コメント入力フォーム -->
      コメント
      <input type="text" ref="comment" />
      <!-- 追加ボタンのモック -->
      <button type="submit">追加</button>
    </form>
    <button>
      <router-link :to="{ name: 'Home' }">ホームに戻る</router-link>
    </button>
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
      todos: []
    };
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
    // ToDo 追加の処理
    doAdd: function() {
      // ref で名前を付けておいた要素を参照
      var comment = this.$refs.comment;
      // 入力がなければ何もしないで return
      if (!comment.value.length) {
        return;
      }
      // { 新しいID, コメント, 作業状態 }
      // というオブジェクトを現在の todos リストへ push
      // 作業状態「state」はデフォルト「作業中=0」で作成
      this.todos.push({
        id: todoStorage.uid++,
        comment: comment.value,
        state: 0
      });
      console.log(localStorage.getItem(STORAGE_KEY));
      // フォーム要素を空にする
      comment.value = "";
    }
  }
};
</script>