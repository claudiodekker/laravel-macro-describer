<?php

namespace ClaudioDekker\MacroDescriber;

use ClaudioDekker\MacroDescriber\Commands\Generator;
use Illuminate\Support\ServiceProvider;

class MacroDescriberServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../stubs', 'macro-describer');
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Generator::class,
            ]);
        }
    }
}
