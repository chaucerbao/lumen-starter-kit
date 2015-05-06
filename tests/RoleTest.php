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

    /**
     * Test the inclusion of a boolean rule.
     */
    public function testAllowBoolean()
    {
        Role::$rules = [];

        Role::allow('A Role', 'An Action', 'An Object');
        Role::allow('A Role', 'An Action', 'Another Object', false);

        $this->assertEquals(Role::$rules, ['A Role' => ['An Object' => ['An Action' => true], 'Another Object' => ['An Action' => false]]]);
    }

    /**
     * Test the inclusion of a callback rule.
     */
    public function testAllowCallback()
    {
        Role::$rules = [];

        Role::allow('A Role', 'An Action', 'An Object', function ($n) { return 2 * $n; });
        Role::allow('A Role', 'Another Action', 'An Object', function ($n) { return 4 * $n; });

        $this->assertTrue(is_callable(Role::$rules['A Role']['An Object']['An Action']));
        $this->assertEquals(12, call_user_func(Role::$rules['A Role']['An Object']['An Action'], 6));
        $this->assertTrue(is_callable(Role::$rules['A Role']['An Object']['Another Action']));
        $this->assertEquals(16, call_user_func(Role::$rules['A Role']['An Object']['Another Action'], 4));
    }
}
