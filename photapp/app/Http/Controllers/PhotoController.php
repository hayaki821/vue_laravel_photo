<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Photo;
use App\Comment;
use App\Http\Requests\StoreComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証が必要
        //写真一覧取得 API は認証していなくてもアクセスできる仕様
        $this->middleware('auth')->except(['index','download','show']);
    }

    /**
     * 写真一覧
     */
    public function index()
    {
        //with メソッドを使うと、引数で渡したリレーションが定義されたテーブルの情報を先にまとめて取得する
        //複数行のデータについてさらにリレーションを参照するような場合にはこの with メソッドを活用
        $photos = Photo::with(['owner','likes'])//with メソッドは、リレーションを事前にロードしておくメソッド
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();
        //paginate はページ送り機能を実現します。get の代わりに paginate を使うことで、
        //JSON レスポンスでも示した total（総ページ数）や current_page（現在のページ）といった情報が自動的に追加

        return $photos;
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        // 投稿写真の拡張子を取得する
        $extension = $request->photo->extension();

        $photo = new Photo();

        // インスタンス生成時に割り振られたランダムなID値と
        // 本来の拡張子を組み合わせてファイル名とする
        $photo->filename = $photo->id.".".$extension;
        
        // S3にファイルを保存する
        // 第三引数の'public'はファイルを公開状態で保存するため    
        //Storageファザードを使用
        Storage::cloud()
            ->putFileAs('', $request->photo, $photo->filename, 'public');

        // データベースエラー時にファイル削除を行うため
        // トランザクションを利用する
        DB::beginTransaction();

        try{
            Auth::user()->photos()->save($photo);
            DB::commit();
        }catch (\Exception $extension){
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            Storage::cloud()->delete($photo->filename);
            throw $exception;         
        }

        // リソースの新規作成なので
        // レスポンスコードは201(CREATED)を返却する
        return response($photo, 201);
    }

    /**
     * 写真ダウンロード
     * @param Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function download(Photo $photo)
    {
         // 写真の存在チェック
    if (! Storage::cloud()->exists($photo->filename)) {
        abort(404);
    }

    //レスポンスヘッダ Content-Disposition に attachment および filename を指定することで、レスポンスの内容（S3 から取得した画像ファイル）を
    // Web ページとして表示するのではなく、ダウンロードさせるために保存ダイアログを開くようにブラウザに指示
    $disposition = 'attachment; filename="' . $photo->filename . '"';
    $headers = [
        'Content-Type' => 'application/octet-stream',
        'Content-Disposition' => $disposition,
    ];

    return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

     /**
     * 写真詳細
     * @param string $id
     * @return Photo
     */
    public function show(string $id)
    {

        //with メソッドは階層化されたリレーションもロードできます
        $photo = Photo::where('id', $id)->with(['owner', 'comments.author','likes'])->first();
        // SELECT * FROM `photos` WHERE `id` = "abcd1234EFGH";
        // SELECT * FROM `users` WHERE `id` IN (1); -- ownerリレーションを解決する
        // SELECT * FROM `comments` WHERE `photo_id` = "abcd1234EFGH"; -- commentsリレーションを解決する
        // SELECT * FROM `users` WHERE `id` IN (2, 3, 4); -- comments.authorリレーションを解決する
        
        return $photo ?? abort(404);
    }

        /**
     * コメント投稿
     * @param Photo $photo
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        // authorリレーションをロードするためにコメントを取得しなおす
        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }

    /**
     * いいね
     * @param string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }
        //何回実行しても1個しかいいねが付かないように、
        //まず特定の写真およびログインユーザーに紐づくいいねを削除して（detach）から、新たに追加（attach）しています。
        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ["photo_id" => $id];
    }

    /**
     * いいね解除
     * @param string $id
     * @return array
     */
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id" => $id];
    }

}
