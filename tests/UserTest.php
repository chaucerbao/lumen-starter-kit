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
     * Test that the roles relationship exists.
     */
    public function testRolesRelationship()
    {
        $user = FactoryMuffin::create('App\User');
        $role = FactoryMuffin::create('App\Role');

        $user->roles()->attach($role);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $user->roles());
        $this->assertInstanceOf('App\Role', $user->roles[0]);
    }

    /**
     * Test that a new password is hashed when saved.
     */
    public function testNewPasswordHashedWhenSaved()
    {
        $user = FactoryMuffin::instance('App\User', [
            'email' => 'a@b.cd',
            'password' => 'secret',
        ]);

        $user->save();

        $this->assertNotEquals('secret', $user->password);
        $this->assertTrue(Auth::validate(['email' => 'a@b.cd', 'password' => 'secret']));
    }

    /**
     * Test that a hashed password is not re-hashed when saved.
     */
    public function testHashedPasswordNotRehashedWhenSaved()
    {
        $user = FactoryMuffin::create('App\User', [
            'email' => 'a@b.cd',
            'password' => 'secret',
        ]);
        $hashed_password = $user->password;

        $faker = Faker::create();
        $user->first_name = $faker->firstName;
        $user->save();

        $this->assertEquals($hashed_password, $user->password);
        $this->assertTrue(Auth::validate(['email' => 'a@b.cd', 'password' => 'secret']));
    }

    /**
     * Test that a hashed password is not re-hashed when empty during save.
     */
    public function testHashedPasswordNotRehashedWhenEmptyDuringSave()
    {
        $user = FactoryMuffin::create('App\User', [
            'email' => 'a@b.cd',
            'password' => 'secret',
        ]);
        $hashed_password = $user->password;

        $user->password = '';
        $user->save();

        $this->assertEquals($hashed_password, $user->password);
        $this->assertTrue(Auth::validate(['email' => 'a@b.cd', 'password' => 'secret']));
    }
}
