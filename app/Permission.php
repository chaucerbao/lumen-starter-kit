<?php

namespace App;

class Permission
{
    /**
     * Indicates if the permission have been loaded.
     *
     * @var bool
     */
    protected static $isLoaded = false;

    /**
     * Load the application's permission rules.
     */
    public static function load()
    {
        if (!static::$isLoaded) {
            static::$isLoaded = true;
        }
    }
}
