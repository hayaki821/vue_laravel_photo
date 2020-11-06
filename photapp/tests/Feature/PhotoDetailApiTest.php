<?php

namespace Tests\Feature;

use App\Comment;
use App\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function should_正しい構造のJSONを返却する()
    {
        //factorの作成
        //eachでリレーション
        factory(Photo::class)->create()->each(function($photo){
            $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        });
        $photo = Photo::first();

        $response = $this->json('GET', route('photo.show', [
            'id' => $photo->id,
        ]));
        //assertJsonFragment()メソッドを使用すると、引数に渡したJSONが、レスポンスに含まれているか検証します。
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name,
                ],
                'comments' => $photo->comments
                ->sortByDesc('id')
                ->map(function ($comment) {
                    return [
                        'author' => [
                            'name' => $comment->author->name,
                        ],
                        'content' => $comment->content,
                    ];
                })
                ->all(),
                'liked_by_user' => false,
                'likes_count' => 0,

            ]);
    }
}
