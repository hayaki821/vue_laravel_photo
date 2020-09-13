<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Photo extends Model
{
    /** プライマリキーの型 */
    protected $keyType = "string";//プライマリキーの値を初期設定（int）から変更したい場合は $keyType を上書きする。

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
}
