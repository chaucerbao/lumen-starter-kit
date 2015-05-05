<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

FactoryMuffin::define('App\PendingUpdate', [
    'model' => 'MyModel',
    'id' => 'randomNumber',
]);
