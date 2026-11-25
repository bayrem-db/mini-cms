<?php

use App\Models\Post;
use App\Models\Tag;

it('lists tags with their published post count', function () {
    $tag = Tag::factory()->create(['name' => 'Astuce', 'slug' => 'astuce']);
    $post = Post::factory()->published()->create();
    $post->tags()->attach($tag);

    $this->getJson('/api/tags')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'slug', 'posts_count'],
            ],
        ])
        ->assertJsonPath('data.0.slug', 'astuce')
        ->assertJsonPath('data.0.posts_count', 1);
});

it('filters posts by tag slug', function () {
    $tag = Tag::factory()->create(['name' => 'Astuce', 'slug' => 'astuce']);

    $matching = Post::factory()->published()->create(['title' => 'Avec le tag']);
    Post::factory()->published()->create(['title' => 'Sans le tag']);
    $matching->tags()->attach($tag);

    $this->getJson('/api/posts?tag=astuce')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matching->id);
});

it('returns a JSON 404 for an unknown tag', function () {
    $this->getJson('/api/tags/999999')
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});
