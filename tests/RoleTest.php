<?php

use App\Role;
use League\FactoryMuffin\Facade as FactoryMuffin;

class RoleTest extends TestCase
{
    /**
     * Test that the model can be instantiated.
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('App\Role', new Role());
    }

    /**
     * Test that the users relationship exists.
     */
    public function testUsersRelationship()
    {
        $role = FactoryMuffin::create('App\Role');
        $user = FactoryMuffin::create('App\User');

        $role->users()->attach($user);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany', $role->users());
        $this->assertInstanceOf('App\User', $role->users[0]);
    }
}
