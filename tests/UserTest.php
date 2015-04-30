<?php

use App\User;
use Faker\Factory as Faker;
use League\FactoryMuffin\Facade as FactoryMuffin;

class UserTest extends TestCase
{
    /**
     * Test that the model can be instantiated.
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('App\User', new User());
    }

    /**
     * Test that a new password is hashed when saved.
     */
    public function testNewPasswordHashedWhenSaved()
    {
        $user = FactoryMuffin::instance('App\User', [
            'email' => 'a@b.cd',
            'password' => 'aTestPassword',
        ]);

        $user->save();

        $this->assertNotEquals('aTestPassword', $user->password);
        $this->assertTrue(Auth::validate(['email' => 'a@b.cd', 'password' => 'aTestPassword']));
    }

    /**
     * Test that a hashed password is not re-hashed when saved.
     */
    public function testHashedPasswordNotRehashedWhenSaved()
    {
        $user = FactoryMuffin::create('App\User', [
            'email' => 'a@b.cd',
            'password' => 'aTestPassword',
        ]);
        $hashed_password = $user->password;

        $faker = Faker::create();
        $user->first_name = $faker->firstName;
        $user->save();

        $this->assertEquals($hashed_password, $user->password);
        $this->assertTrue(Auth::validate(['email' => 'a@b.cd', 'password' => 'aTestPassword']));
    }
}
