<?php

use App\Post;
use App\User;

class PostControllerTest extends TestCase
{
    /**
     * Run before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $user = factory(User::class)->create();
        $this->be($user);
    }

    /**
     * Test the index page.
     */
    public function testIndex()
    {
        factory(Post::class, 3)->create();

        $response = $this->call('GET', '/auth/posts');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $view['posts']);
        $this->assertInstanceOf('App\Post', $view['posts']->first());
        $this->assertCount(3, $view['posts']);
    }

    /**
     * Test the show post page.
     */
    public function testShow()
    {
        factory(Post::class)->create(['slug' => 'my-post-title']);

        $response = $this->call('GET', '/auth/post/my-post-title');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\Post', $view['post']);
        $this->assertEquals(1, $view['post']->id);
    }

    /**
     * Test successfully storing a new post.
     */
    public function testStoreSuccess()
    {
        $post = factory(Post::class)->make();
        $this->assertEquals(0, Post::count());
        $this->assertNotEquals(1, $post->author_id);

        $response = $this->call('POST', '/auth/posts', $this->csrf($post->getAttributes()));

        $this->assertEquals(1, Post::count());
        $this->assertEquals(1, Post::find(1)->author_id);
        $this->assertRedirectedTo('auth/posts');
    }

    /**
     * Test failing to store a new post.
     */
    public function testStoreFail()
    {
        $post = factory(Post::class)->make(['title' => '']);
        $this->assertEquals(0, Post::count());

        session()->setPreviousUrl('http://localhost/auth/post/create');
        $response = $this->call('POST', '/auth/posts', $this->csrf($post->getAttributes()));

        $this->assertEquals(0, Post::count());
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('auth/post/create');
    }

    /**
     * Test successfully updating an existing post.
     */
    public function testUpdateSuccess()
    {
        $post = factory(Post::class)->create(['slug' => 'my-post-title']);
        $this->assertEquals(1, Post::count());
        $this->assertNotEquals('Updated title', $post->title);

        $response = $this->call('PUT', '/auth/post/my-post-title', $this->csrf(['title' => 'Updated title'] + $post->getAttributes()));

        $post = $post->fresh();
        $this->assertEquals(1, Post::count());
        $this->assertEquals('Updated title', $post->title);
        $this->assertRedirectedTo('auth/posts');
    }

    /**
     * Test failing to update an existing post.
     */
    public function testUpdateFail()
    {
        $post = factory(Post::class)->create(['slug' => 'my-post-title', 'title' => 'My post title']);
        $this->assertEquals(1, Post::count());

        session()->setPreviousUrl('http://localhost/auth/post/my-post-title/edit');
        $response = $this->call('PUT', '/auth/post/my-post-title', $this->csrf(['title' => ''] + $post->getAttributes()));

        $post = $post->fresh();
        $this->assertEquals(1, Post::count());
        $this->assertEquals('My post title', $post->title);
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('auth/post/my-post-title/edit');
    }

    /**
     * Test deleting an existing post.
     */
    public function testDestroy()
    {
        factory(Post::class)->create(['slug' => 'my-post-title']);
        $this->assertEquals(1, Post::count());

        $response = $this->call('DELETE', '/auth/post/my-post-title', $this->csrf());

        $this->assertEquals(0, Post::count());
        $this->assertRedirectedTo('auth/posts');
    }

    /**
     * Test the create post page.
     */
    public function testCreate()
    {
        $response = $this->call('GET', '/auth/post/create');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\Post', $view['post']);
        $this->assertFalse($view['post']->exists);
    }

    /**
     * Test the edit post page.
     */
    public function testEdit()
    {
        factory(Post::class)->create(['slug' => 'my-post-title']);

        $response = $this->call('GET', '/auth/post/my-post-title/edit');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\Post', $view['post']);
        $this->assertEquals(1, $view['post']->id);
    }
}
