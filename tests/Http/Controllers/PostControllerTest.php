<?php

use App\Post;
use League\FactoryMuffin\Facade as FactoryMuffin;

class PostControllerTest extends TestCase
{
    /**
     * Test the index page.
     */
    public function testIndex()
    {
        FactoryMuffin::seed(3, 'App\Post');

        $response = $this->call('GET', '/posts');
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
        FactoryMuffin::create('App\Post', ['slug' => 'my-post-title']);

        $response = $this->call('GET', '/post/my-post-title');
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
        $user = FactoryMuffin::create('App\User');
        $this->be($user);

        $post = FactoryMuffin::instance('App\Post');
        $this->assertEquals(0, Post::count());
        $this->assertNotEquals(1, $post->author_id);

        $response = $this->call('POST', '/posts', $this->csrf($post->getAttributes()));

        $this->assertEquals(1, Post::count());
        $this->assertEquals(1, Post::find(1)->author_id);
        $this->assertRedirectedTo('posts');
    }

    /**
     * Test failing to store a new post.
     */
    public function testStoreFail()
    {
        $user = FactoryMuffin::create('App\User');
        $this->be($user);

        $post = FactoryMuffin::instance('App\Post', ['title' => '']);
        $this->assertEquals(0, Post::count());

        session()->setPreviousUrl('http://localhost/post/create');
        $response = $this->call('POST', '/posts', $this->csrf($post->getAttributes()));

        $this->assertEquals(0, Post::count());
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('post/create');
    }

    /**
     * Test successfully updating an existing post.
     */
    public function testUpdateSuccess()
    {
        $post = FactoryMuffin::create('App\Post', ['slug' => 'my-post-title']);
        $this->assertEquals(1, Post::count());
        $this->assertNotEquals('Updated title', $post->title);

        $response = $this->call('PUT', '/post/my-post-title', $this->csrf(['title' => 'Updated title'] + $post->getAttributes()));

        $post = $post->fresh();
        $this->assertEquals(1, Post::count());
        $this->assertEquals('Updated title', $post->title);
        $this->assertRedirectedTo('posts');
    }

    /**
     * Test failing to update an existing post.
     */
    public function testUpdateFail()
    {
        $post = FactoryMuffin::create('App\Post', ['slug' => 'my-post-title', 'title' => 'My post title']);
        $this->assertEquals(1, Post::count());

        session()->setPreviousUrl('http://localhost/post/my-post-title/edit');
        $response = $this->call('PUT', '/post/my-post-title', $this->csrf(['title' => ''] + $post->getAttributes()));

        $post = $post->fresh();
        $this->assertEquals(1, Post::count());
        $this->assertEquals('My post title', $post->title);
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('post/my-post-title/edit');
    }

    /**
     * Test deleting an existing post.
     */
    public function testDestroy()
    {
        FactoryMuffin::create('App\Post', ['slug' => 'my-post-title']);
        $this->assertEquals(1, Post::count());

        $response = $this->call('DELETE', '/post/my-post-title', $this->csrf());

        $this->assertEquals(0, Post::count());
        $this->assertRedirectedTo('posts');
    }

    /**
     * Test the create post page.
     */
    public function testCreate()
    {
        $response = $this->call('GET', '/post/create');
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
        FactoryMuffin::create('App\Post', ['slug' => 'my-post-title']);

        $response = $this->call('GET', '/post/my-post-title/edit');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\Post', $view['post']);
        $this->assertEquals(1, $view['post']->id);
    }
}
