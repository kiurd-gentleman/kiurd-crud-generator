<?php

namespace Krimt\ApiFirstCrudPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Krimt\ApiFirstCrudPackage\Commands\ApiCrudGeneratorCommand;

class ApiCrudServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            ApiCrudGeneratorCommand::class,
        ]);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../stubs' => resource_path('stubs/crud-generator'),
        ], 'stubs');
    }
}
