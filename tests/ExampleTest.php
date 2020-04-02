<?php

namespace Alish\ShortMessage\Tests;

use Orchestra\Testbench\TestCase;
use Alish\ShortMessage\ShortMessageServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [ShortMessageServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
