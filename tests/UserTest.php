<?php

use App\Role;
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
     * Test the generation of the full name.
     */
    public function testGetFullName()
    {
        $user = FactoryMuffin::instance('App\User', [
            'first_name' => 'George',
            'last_name' => 'Washington',
        ]);

        $this->assertEquals('George Washington', $user->full_name);
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

    /**
     * Test a boolean permission.
     */
    public function testCanBoolean()
    {
        $user = FactoryMuffin::create('App\User');
        $role = FactoryMuffin::create('App\Role', ['name' => 'My Role']);
        $user->roles()->attach($role);

        Role::allow('My Role', 'write', 'Model');
        Role::allow('My Role', 'write', 'Restricted', false);

        $this->assertTrue($user->can('write', 'Model'));
        $this->assertFalse($user->can('write', 'Restricted'));
    }

    /**
     * Test a permission determined by a callback function.
     */
    public function testCanCallback()
    {
        $userA = FactoryMuffin::create('App\User');
        $userB = FactoryMuffin::create('App\User');
        $role = FactoryMuffin::create('App\Role', ['name' => 'My Role']);
        $userA->roles()->attach($role);

        Role::allow('My Role', 'write', 'App\User', function ($user, $target) {
            return $user->id === $target->id;
        });

        $this->assertTrue($userA->can('write', $userA));
        $this->assertFalse($userA->can('write', $userB));
    }
}
