<?php

namespace Krimt\ApiFirstCrudPackage\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ApiCrudGeneratorCommand extends Command
{
    protected $signature = 'crud:generate {name}';
    protected $description = 'Generate CRUD operations';

    public function handle()
    {
        $name = $this->argument('name');
        $folder = explode('/', $name);
        $name = end($folder);

        $this->generateModel($name);
        $this->generateMigration($name);
        $this->generateController($name);
        $this->generateRequests($name);
        $this->generateRoutes($name);

        $this->info('CRUD operations generated successfully.');
    }

    protected function fileExists($path, $type = null)
    {
        // if model, controller, migration, request, route already exists
        if (file_exists($path)) {
            $this->error("{$type} already exists!");
            return true;
        }
        return false;

    }

    protected function generateModel($name)
    {
        if ($this->fileExists(app_path("/Models/{$name}.php"), 'Model')) {
            return;
        }
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/Models/{$name}.php"), $modelTemplate);

        $this->info('Model generated successfully.');
    }

    protected function generateMigration($name)
    {
        $name = Str::studly($name);
        $tableName = Str::plural(Str::snake($name));

        // Get all migration file names
        $migrationFiles = scandir(database_path('migrations'));
        $migrationFiles = array_diff($migrationFiles, array('.', '..'));

        // Check if a migration file for the table already exists
        foreach ($migrationFiles as $file) {
            if (strpos($file, "create_{$tableName}_table") !== false) {
                $this->error('Migration already exists!');
                return;
            }
        }

        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";

        $migrationTemplate = str_replace(
            ['{{tableName}}'],
            [$tableName],
            $this->getStub('Migration')
        );

        file_put_contents(database_path("/migrations/{$migrationName}"), $migrationTemplate);

        $this->info('Migration generated successfully.');
    }

    protected function generateController($name)
    {
        if ($this->fileExists(app_path("/Http/Controllers/{$name}Controller.php"), 'Controller')) {
            return;
        }

        $controllerTemplate = str_replace(
            ['{{modelName}}', '{{modelNamePlural}}'],
            [$name, Str::plural($name)],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);

        $this->info('Controller generated successfully.');
    }

    protected function generateRequests($name)
    {
        // if request already exists
        if ($this->fileExists(app_path("/Http/Requests/Store{$name}Request.php"), 'Request')) {
            return;
        }

        $StoreRequestTemplate = str_replace(
            ['{{modelName}}'],
            ['Store'.$name],
            $this->getStub('Request')
        );

        $UpdateRequestTemplate = str_replace(
            ['{{modelName}}'],
            ['Update'.$name],
            $this->getStub('Request')
        );

        if (!file_exists($dir = app_path("/Http/Requests"))) {
            mkdir($dir, 0777, true);
        }

        file_put_contents(app_path("/Http/Requests/Store{$name}Request.php"), $StoreRequestTemplate);
        file_put_contents(app_path("/Http/Requests/Update{$name}Request.php"), $UpdateRequestTemplate);

        $this->info('Requests generated successfully.');
    }

    protected function generateRoutes($name)
    {
        $routeName = strtolower(Str::plural($name)) . '.index';

        // Check if the route already exists
        if (Route::has($routeName)) {
            $this->error('Route already exists!');
            return;
        }

        $routesTemplate = str_replace(
            ['{{modelNamePluralLowerCase}}', '{{modelName}}'],
            [strtolower(Str::plural($name)), $name],
            $this->getStub('Routes')
        );

        file_put_contents(base_path("routes/api.php"), $routesTemplate, FILE_APPEND);

        $this->info('Routes generated successfully.');
    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../stubs/$type.stub");
    }
}
