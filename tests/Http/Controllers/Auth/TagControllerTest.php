<?php

use App\Tag;
use League\FactoryMuffin\Facade as FactoryMuffin;

class TagControllerTest extends TestCase
{
    /**
     * Run before each test.
     */
    public function setUp()
    {
        parent::setUp();

        $user = FactoryMuffin::create('App\User');
        $this->be($user);
    }

    /**
     * Test the index page.
     */
    public function testIndex()
    {
        FactoryMuffin::seed(3, 'App\Tag');

        $response = $this->call('GET', '/auth/tags');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $view['tags']);
        $this->assertInstanceOf('App\Tag', $view['tags']->first());
        $this->assertCount(3, $view['tags']);
    }

    /**
     * Test successfully storing a new tag.
     */
    public function testStoreSuccess()
    {
        $tag = FactoryMuffin::instance('App\Tag');
        $this->assertEquals(0, Tag::count());

        $response = $this->call('POST', '/auth/tags', $this->csrf($tag->getAttributes()));

        $this->assertEquals(1, Tag::count());
        $this->assertRedirectedTo('auth/tags');
    }

    /**
     * Test failing to store a new tag.
     */
    public function testStoreFail()
    {
        $tag = FactoryMuffin::instance('App\Tag', ['name' => '']);
        $this->assertEquals(0, Tag::count());

        session()->setPreviousUrl('http://localhost/auth/tag/create');
        $response = $this->call('POST', '/auth/tags', $this->csrf($tag->getAttributes()));

        $this->assertEquals(0, Tag::count());
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('auth/tag/create');
    }

    /**
     * Test successfully updating an existing tag.
     */
    public function testUpdateSuccess()
    {
        $tag = FactoryMuffin::create('App\Tag');
        $this->assertEquals(1, Tag::count());
        $this->assertNotEquals('The 2nd TAG', $tag->name);

        $response = $this->call('PUT', '/auth/tag/1', $this->csrf(['name' => 'The 2nd TAG'] + $tag->getAttributes()));

        $tag = $tag->fresh();
        $this->assertEquals(1, Tag::count());
        $this->assertEquals('The 2nd TAG', $tag->name);
        $this->assertRedirectedTo('auth/tags');
    }

    /**
     * Test failing to update an existing tag.
     */
    public function testUpdateFail()
    {
        $tag = FactoryMuffin::create('App\Tag', ['name' => 'The 1 TAG']);
        $this->assertEquals(1, Tag::count());

        session()->setPreviousUrl('http://localhost/auth/tag/1/edit');
        $response = $this->call('PUT', '/auth/tag/1', $this->csrf(['name' => '']));

        $tag = $tag->fresh();
        $this->assertEquals(1, Tag::count());
        $this->assertEquals('The 1 TAG', $tag->name);
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('auth/tag/1/edit');
    }

    /**
     * Test deleting an existing tag.
     */
    public function testDestroy()
    {
        FactoryMuffin::create('App\Tag');
        $this->assertEquals(1, Tag::count());

        $response = $this->call('DELETE', '/auth/tag/1', $this->csrf());

        $this->assertEquals(0, Tag::count());
        $this->assertRedirectedTo('auth/tags');
    }

    /**
     * Test the create tag page.
     */
    public function testCreate()
    {
        $response = $this->call('GET', '/auth/tag/create');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\Tag', $view['tag']);
        $this->assertFalse($view['tag']->exists);
    }

    /**
     * Test the edit tag page.
     */
    public function testEdit()
    {
        FactoryMuffin::create('App\Tag');

        $response = $this->call('GET', '/auth/tag/1/edit');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\Tag', $view['tag']);
        $this->assertEquals(1, $view['tag']->id);
    }
}
