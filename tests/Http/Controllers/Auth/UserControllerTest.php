<?php

use App\Role;
use App\User;

class UserControllerTest extends TestCase
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
        factory(User::class, 2)->create();

        $response = $this->call('GET', '/auth/users');
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
        $response = $this->call('GET', '/auth/user/1');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\User', $view['user']);
        $this->assertEquals(1, $view['user']->id);
    }

    /**
     * Test successfully storing a new user.
     */
    public function testStoreSuccess()
    {
        $user = factory(User::class)->make();
        $this->assertEquals(1, User::count());

        $response = $this->call('POST', '/auth/users', $this->csrf($user->getAttributes()));

        $this->assertEquals(2, User::count());
        $this->assertRedirectedTo('auth/users');
    }

    /**
     * Test failing to store a new user.
     */
    public function testStoreFail()
    {
        $user = factory(User::class)->make(['email' => '']);
        $this->assertEquals(1, User::count());

        session()->setPreviousUrl('http://localhost/auth/user/create');
        $response = $this->call('POST', '/auth/users', $this->csrf($user->getAttributes()));

        $this->assertEquals(1, User::count());
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('auth/user/create');
    }

    /**
     * Test successfully updating an existing user.
     */
    public function testUpdateSuccess()
    {
        $user = factory(User::class)->create();
        $this->assertEquals(2, User::count());
        $this->assertNotEquals('a@b.cd', $user->email);

        $response = $this->call('PUT', '/auth/user/2', $this->csrf(['email' => 'a@b.cd'] + $user->getAttributes()));

        $user = $user->fresh();
        $this->assertEquals(2, User::count());
        $this->assertEquals('a@b.cd', $user->email);
        $this->assertRedirectedTo('auth/users');
    }

    /**
     * Test failing to update an existing user.
     */
    public function testUpdateFail()
    {
        $user = factory(User::class)->create(['email' => 'a@b.cd']);
        $this->assertEquals(2, User::count());

        session()->setPreviousUrl('http://localhost/auth/user/2/edit');
        $response = $this->call('PUT', '/auth/user/2', $this->csrf(['email' => ''] + $user->getAttributes()));

        $user = $user->fresh();
        $this->assertEquals(2, User::count());
        $this->assertEquals('a@b.cd', $user->email);
        $this->assertSessionHasErrors();
        $this->assertRedirectedTo('auth/user/2/edit');
    }

    /**
     * Test deleting an existing user.
     */
    public function testDestroy()
    {
        factory(User::class)->create();
        $this->assertEquals(2, User::count());

        $response = $this->call('DELETE', '/auth/user/2', $this->csrf());

        $this->assertEquals(1, User::count());
        $this->assertRedirectedTo('auth/users');
    }

    /**
     * Test the create user page.
     */
    public function testCreate()
    {
        $response = $this->call('GET', '/auth/user/create');
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
        factory(Role::class)->create();

        $response = $this->call('GET', '/auth/user/1/edit');
        $view = $response->original;

        $this->assertResponseOk();
        $this->assertInstanceOf('App\User', $view['user']);
        $this->assertEquals(1, $view['user']->id);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $view['roles']);
        $this->assertInstanceOf('App\Role', $view['roles']->first());
    }
}
