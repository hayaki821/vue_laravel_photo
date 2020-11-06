<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // ★ 追記


class Photo extends Model
{
    /** プライマリキーの型 */
    protected $keyType = "string";//プライマリキーの値を初期設定（int）から変更したい場合は $keyType を上書きする。

    protected $perPage = 5; 

    /** IDの桁数 */
    const ID_LENGTH = 12;

    public function __construct(array $attribute = [])
    {
        parent::__construct($attribute);//親クラスのコンストラクタ呼び出し

        if (!Arr::get($this->attributes,"id")){//Arr::getメソッドは指定された値を「ドット」記法で指定された値を深くネストされた配列から取得する
            $this->setId();
        }
    }

    /**
     * ランダムなID値をid属性に代入する
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    /**
     * ランダムなID値を生成する
     * @return string
     */
    private function getRandomId(){
        $characters = array_merge(//array_merge — ひとつまたは複数の配列をマージする
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );
        $length = count($characters);

        $id = "";

        for ($i=0;$i<self::ID_LENGTH;$i++){
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;

    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        //モデルクラスがコントローラーからレスポンスされて JSON に変換されるとき、このリレーション名 "owner" が反映されます。
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    /**
     * アクセサ - url
     * @return string
     */
    public function getUrlAttribute()
    {
        //クラウドストレージの url メソッドは S3 上のファイルの公開 URL を返却します。
        //具体的には .env で定義した AWS_URL と引数のファイル名を結合した値
        return Storage::cloud()->url($this->attributes['filename']);
    }

    //アクセサは定義しただけではモデルの JSON 表現には現れません。
    //ユーザー定義のアクセサを JSON 表現に含めるためには、明示的に $appends プロパティに登録する必要があり
    /** JSONに含める属性 */
    protected $appends = [
        'url','likes_count', 'liked_by_user',
    ];
    // /** JSONに含めない属性 */
    // protected $hidden = [
    //     'user_id', 'filename',
    //     self::CREATED_AT, self::UPDATED_AT,
    // ];
    /** JSONに含める属性 */
    protected $visible = [
        'id', 'owner', 'url','comments',
        'likes_count', 'liked_by_user',
    ];

    /**
     * リレーションシップ - commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('id', 'desc');
    }
    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        //これは likes テーブルを中間テーブルとした、photos テーブルと users テーブルの多対多の関連性を表しています。
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    /**
     * アクセサ - likes_count
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    /**
     * アクセサ - liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }
        //ログインユーザーのIDと合致するいいねが含まれるか調べています
        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }
}
