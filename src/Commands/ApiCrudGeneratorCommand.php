<?php

namespace Krimt\ApiFirstCrudPackage\Commands;

use Illuminate\Console\Command;
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
    protected function generateModel($name)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/Models/{$name}.php"), $modelTemplate);

    }

    protected function generateMigration($name)
    {
        $tableName = Str::plural(Str::snake($name));

        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";

        $migrationTemplate = str_replace(
            ['{{tableName}}'],
            [$tableName],
            $this->getStub('Migration')
        );

        file_put_contents(database_path("/migrations/{$migrationName}"), $migrationTemplate);
    }

    protected function generateController($name)
    {
        $controllerTemplate = str_replace(
            ['{{modelName}}', '{{modelNamePlural}}'],
            [$name, Str::plural($name)],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
    }

    protected function generateRequests($name)
    {
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
    }

    protected function generateRoutes($name)
    {
        $routesTemplate = str_replace(
            ['{{modelNamePluralLowerCase}}', '{{modelName}}'],
            [strtolower(Str::plural($name)), $name],
            $this->getStub('Routes')
        );

        file_put_contents(base_path("routes/api.php"), $routesTemplate, FILE_APPEND);
    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../stubs/$type.stub");
    }
}
