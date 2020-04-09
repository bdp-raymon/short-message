<?php

namespace Alish\ShortMessage\Tests;

use Alish\ShortMessage\ShortMessageServiceProvider;
use Orchestra\Testbench\TestCase;

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
