<?php

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('user can follow and unfollow another user', function () {
    $follower = User::factory()->create();
    $followed = User::factory()->create();

    $follower->following()->attach($followed->id);
    $this->assertTrue($follower->following->contains($followed->id));

    $follower->following()->detach($followed->id);
    $follower->refresh();
    $this->assertFalse($follower->following->contains($followed->id));
});

test('user can create a post and comment', function () {
    $user = User::factory()->create();
    
    $post = Post::create([
        'user_id' => $user->id,
        'content' => 'Check out my new STH!',
    ]);

    $this->assertDatabaseHas('posts', ['content' => 'Check out my new STH!']);

    Comment::create([
        'user_id' => $user->id,
        'post_id' => $post->id,
        'content' => 'Wow, nice find.',
    ]);

    $this->assertDatabaseHas('comments', ['content' => 'Wow, nice find.']);
});
