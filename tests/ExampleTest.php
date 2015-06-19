<?php

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Lumen.');
    }
}
