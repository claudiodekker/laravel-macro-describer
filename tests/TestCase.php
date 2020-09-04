<?php

namespace ClaudioDekker\MacroDescriber\Tests;

use ClaudioDekker\MacroDescriber\MacroDescriberServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MacroDescriberServiceProvider::class,
        ];
    }

    protected function package_path(string $path = '')
    {
        return realpath(__DIR__.'/../').'/'.ltrim($path, '/');
    }
}
