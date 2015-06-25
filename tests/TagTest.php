<?php

use App\Post;
use App\Tag;

class TagTest extends TestCase
{
    /**
     * Test that the model can be instantiated.
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('App\Tag', new Tag());
    }

    /**
     * Test that the posts relationship exists.
     */
    public function testPostsRelationship()
    {
        $tag = factory(Tag::class)->create();
        $post = factory(Post::class)->create();

        $tag->posts()->attach($post);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\MorphToMany', $tag->posts());
        $this->assertInstanceOf('App\Post', $tag->posts[0]);
    }

    /**
     * Test that the slug is generated if empty when saved.
     */
    public function testSlugGeneratedIfEmptyWhenSaved()
    {
        $tag = factory(Tag::class)->make(['slug' => '', 'name' => '3 Word TAG']);

        $tag->save();

        $this->assertEquals('3-word-tag', $tag->slug);
    }
}
