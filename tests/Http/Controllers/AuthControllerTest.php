<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

class AuthControllerTest extends TestCase
{
    /**
     * Test the registration form page.
     */
    public function testCreateUser()
    {
        $response = $this->call('GET', '/register');
        $view = $response->original;

        $this->assertResponseOk();
    }

    /**
     * Test a successful registration.
     */
    public function testStoreUserSuccess()
    {
        $user = FactoryMuffin::instance('App\User');

        $response = $this->call('POST', '/register', $this->csrf($user->getAttributes()));

        $this->assertRedirectedTo('register/confirmation');
    }

    /**
     * Test a failed registration.
     */
    public function testStoreUserFail()
    {
        $this->markTestIncomplete();
        $user = FactoryMuffin::instance('App\User', [
            'first_name' => 'George',
            'last_name' => 'Washington',
            'email' => 'a@b.cd',
            'password' => 'secret',
        ]);

        $response = $this->call('POST', '/register', $this->csrf($user->getAttributes()));

        $this->assertRedirectedTo('register');
        $this->assertEquals('George', old('first_name'));
        $this->assertEquals('Washington', old('last_name'));
        $this->assertEquals('a@b.cd', old('email'));
        $this->assertEmpty(old('password'));
    }

    /**
     * Test that an e-mail address is confirmed.
     */
    public function testEmailConfirmed()
    {
        $user = FactoryMuffin::create('App\User');

        $user = $user->fresh();
        $this->assertFalse((bool) $user->is_confirmed);

        $pending = FactoryMuffin::instance('App\PendingUpdate', ['model' => 'App\User', 'id' => 1]);
        $pending->update = ['is_confirmed' => true];
        $pending->save();

        $response = $this->call('GET', '/email/confirmed/'.$pending->token);

        $user = $user->fresh();
        $this->assertTrue((bool) $user->is_confirmed);

        $this->assertResponseOk();
    }

    /**
     * Test the login page.
     */
    public function testCreateSession()
    {
        $response = $this->call('GET', '/login');

        $this->assertResponseOk();
    }

    /**
     * Test a successful authentication.
     */
    public function testStoreSessionSuccess()
    {
        $user = FactoryMuffin::create('App\User', ['email' => 'a@b.cd', 'password' => 'secret']);

        $response = $this->call('POST', '/login', $this->csrf(['email' => 'a@b.cd', 'password' => 'secret']));

        $this->assertRedirectedTo('dashboard');
    }

    /**
     * Test a failed authentication.
     */
    public function testStoreSessionFail()
    {
        $user = FactoryMuffin::create('App\User', ['email' => 'a@b.cd', 'password' => 'secret']);

        $response = $this->call('POST', '/login', $this->csrf(['email' => 'a@b.cd', 'password' => 'wrongPassword']));

        $this->assertEquals('a@b.cd', old('email'));
        $this->assertRedirectedTo('login');
    }

    /**
     * Test the password recovery page.
     */
    public function testCreateRecoveryToken()
    {
        $response = $this->call('GET', '/account/recover');

        $this->assertResponseOk();
    }

    /**
     * Test a successful attempt to create a password recovery token.
     */
    public function testStoreRecoveryTokenSuccess()
    {
        $user = FactoryMuffin::create('App\User', ['email' => 'a@b.cd']);

        $response = $this->call('POST', '/account/recover', $this->csrf(['email' => 'a@b.cd']));

        $this->assertRedirectedTo('account/recover/instructions');
    }

    /**
     * Test a failed attempt to create a password recovery token.
     */
    public function testStoreRecoveryTokenFail()
    {
        $user = FactoryMuffin::create('App\User', ['email' => 'a@b.cd']);

        $response = $this->call('POST', '/account/recover', $this->csrf(['email' => 'w@x.yz']));

        $this->assertEquals('w@x.yz', old('email'));
        $this->assertRedirectedTo('account/recover');
    }

    /**
     * Test the password reset page.
     */
    public function testEditPassword()
    {
        $token = 'a1b2c3d4e5';
        $response = $this->call('GET', '/account/reset/'.$token);

        $this->assertResponseOk();
    }

    /**
     * Test a successful attempt to update a password.
     */
    public function testUpdatePasswordSuccess()
    {
        $user = FactoryMuffin::create('App\User', ['email' => 'a@b.cd']);

        $pending = FactoryMuffin::instance('App\PendingUpdate', ['model' => 'App\User', 'id' => 1]);
        $pending->update = ['password' => null];
        $pending->save();

        $this->assertFalse(Auth::attempt(['email' => 'a@b.cd', 'password' => 'new-secret']));

        $response = $this->call('PUT', '/account/reset/'.$pending->token, $this->csrf(['password' => 'new-secret']));

        $this->assertTrue(Auth::attempt(['email' => 'a@b.cd', 'password' => 'new-secret']));
        $this->assertRedirectedTo('account/reset');
    }

    /**
     * Test a failed attempt to update a password.
     */
    public function testUpdatePasswordFail()
    {
        $token = 'bad-token';
        $user = FactoryMuffin::create('App\User', ['email' => 'a@b.cd']);

        $pending = FactoryMuffin::instance('App\PendingUpdate', ['model' => 'App\User', 'id' => 1]);
        $pending->update = ['password' => null];
        $pending->save();

        $this->assertFalse(Auth::attempt(['email' => 'a@b.cd', 'password' => 'new-secret']));

        $response = $this->call('PUT', '/account/reset/'.$token, $this->csrf(['password' => 'new-secret']));

        $this->assertFalse(Auth::attempt(['email' => 'a@b.cd', 'password' => 'new-secret']));
        $this->assertRedirectedTo('account/reset/'.$token);
    }
}
