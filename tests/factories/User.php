<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

FactoryMuffin::define('App\User', [
    'first_name' => 'firstName',
    'last_name' => 'lastName',
    'email' => 'unique:email',
    'password' => 'password',
]);
