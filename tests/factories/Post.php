<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

FactoryMuffin::define('App\Post', [
    'author_id' => function () { return FactoryMuffin::create('App\User')->id; },
    'slug' => 'unique:word',
    'title' => 'sentence',
    'body' => 'paragraph',
    'is_active' => 'boolean',
    'published_at' => 'dateTime',
]);
