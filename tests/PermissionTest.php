<?php

use App\Permission;

class PermissionTest extends TestCase
{
    /**
     * Run before each test class.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        Permission::load();
    }

    /**
     * Test a permission rule.
     */
    public function testRule()
    {
    }
}
