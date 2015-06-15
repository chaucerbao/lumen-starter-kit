<?php

use App\Post;
use Carbon\Carbon;
use League\FactoryMuffin\Facade as FactoryMuffin;

class PostTest extends TestCase
{
    /**
     * Test that the model can be instantiated.
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('App\Post', new Post());
    }

    /**
     * Test that the author relationship exists.
     */
    public function testAuthorRelationship()
    {
        $user = FactoryMuffin::create('App\User');
        $post = FactoryMuffin::create('App\Post');

        $post->author()->associate($user);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $post->author());
        $this->assertInstanceOf('App\User', $post->author);
    }

    /**
     * Test that the tags relationship exists.
     */
    public function testTagsRelationship()
    {
        $post = FactoryMuffin::create('App\Post');
        $tag = FactoryMuffin::create('App\Tag');

        $post->tags()->attach($tag);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\MorphToMany', $post->tags());
        $this->assertInstanceOf('App\Tag', $post->tags[0]);
    }

    /**
     * Test that the published scope filters successfully.
     */
    public function testScopePublished()
    {
        $future = Carbon::now()->addMinute();
        $past = Carbon::now()->subMinute();
        FactoryMuffin::create('App\Post', ['is_active' => true, 'published_at' => $future]);
        FactoryMuffin::create('App\Post', ['is_active' => true, 'published_at' => $past]);
        FactoryMuffin::create('App\Post', ['is_active' => false, 'published_at' => $future]);
        FactoryMuffin::create('App\Post', ['is_active' => false, 'published_at' => $past]);

        $published = Post::published()->get();

        $this->assertCount(4, Post::all());
        $this->assertCount(1, $published);
        $this->assertTrue($published[0]->is_active);
        $this->assertTrue($published[0]->published_at->lt(Carbon::now()));
    }

    /**
     * Test that the drafts scope filters successfully.
     */
    public function testScopeDrafts()
    {
        $future = Carbon::now()->addMinute();
        $past = Carbon::now()->subMinute();
        FactoryMuffin::create('App\Post', ['is_active' => true, 'published_at' => $future]);
        FactoryMuffin::create('App\Post', ['is_active' => true, 'published_at' => $past]);
        FactoryMuffin::create('App\Post', ['is_active' => false, 'published_at' => $future]);
        FactoryMuffin::create('App\Post', ['is_active' => false, 'published_at' => $past]);

        $drafts = Post::drafts()->get();

        $this->assertCount(4, Post::all());
        $this->assertCount(2, $drafts);
        $this->assertFalse($drafts[0]->is_active);
        $this->assertFalse($drafts[1]->is_active);
    }
}
