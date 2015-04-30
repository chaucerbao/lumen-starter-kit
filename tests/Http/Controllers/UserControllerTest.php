<?php

use App\User;
use League\FactoryMuffin\Facade as FactoryMuffin;

class UserControllerTest extends TestCase
{
    /**
     * Test the index page.
     */
    public function testIndex()
    {
        FactoryMuffin::seed(3, 'App\User');

        $response = $this->call('GET', '/users');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $view['users']);
        $this->assertInstanceOf('App\User', $view['users']->first());
        $this->assertCount(3, $view['users']);
    }

    /**
     * Test the show user page.
     */
    public function testShow()
    {
        $user = FactoryMuffin::create('App\User');

        $response = $this->call('GET', '/user/1');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\User', $view['user']);
        $this->assertEquals(1, $view['user']->id);
    }

    /**
     * Test storing a new user.
     */
    public function testStore()
    {
        $user = FactoryMuffin::instance('App\User');
        $this->assertEquals(0, User::count());

        $response = $this->call('POST', '/users', $this->csrf($user->getAttributes()));

        $this->assertEquals(1, User::count());
        $this->assertRedirectedTo('users');
    }

    /**
     * Test updating an existing user.
     */
    public function testUpdate()
    {
        $user = FactoryMuffin::create('App\User');
        $this->assertEquals(1, User::count());
        $this->assertNotEquals('a@b.cd', $user->email);

        $response = $this->call('PUT', '/user/1', $this->csrf(['email' => 'a@b.cd']));

        $user = $user->fresh();
        $this->assertEquals(1, User::count());
        $this->assertEquals('a@b.cd', $user->email);
        $this->assertRedirectedTo('users');
    }

    /**
     * Test deleting an existing user.
     */
    public function testDestroy()
    {
        $user = FactoryMuffin::create('App\User');
        $this->assertEquals(1, User::count());

        $response = $this->call('DELETE', '/user/1', $this->csrf());

        $this->assertEquals(0, User::count());
        $this->assertRedirectedTo('users');
    }

    /**
     * Test the create user page.
     */
    public function testCreate()
    {
        $response = $this->call('GET', '/register');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\User', $view['user']);
        $this->assertFalse($view['user']->exists);
    }

    /**
     * Test the edit user page.
     */
    public function testEdit()
    {
        $user = FactoryMuffin::create('App\User');

        $response = $this->call('GET', '/user/1/edit');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\User', $view['user']);
        $this->assertEquals(1, $view['user']->id);
    }
}
