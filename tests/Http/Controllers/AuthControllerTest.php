<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

class AuthControllerTest extends TestCase
{
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

        $this->assertRedirectedTo('account/reset_requested');
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
        $response = $this->call('GET', '/password/reset/'.$token);

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

        $response = $this->call('PUT', '/password/reset/'.$pending->token, $this->csrf(['password' => 'new-secret']));

        $this->assertTrue(Auth::attempt(['email' => 'a@b.cd', 'password' => 'new-secret']));
        $this->assertRedirectedTo('account/password_set');
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

        $response = $this->call('PUT', '/password/reset/'.$token, $this->csrf(['password' => 'new-secret']));

        $this->assertFalse(Auth::attempt(['email' => 'a@b.cd', 'password' => 'new-secret']));
        $this->assertRedirectedTo('password/reset/'.$token);
    }
}
