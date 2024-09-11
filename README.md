# API CRUD generator package 

## Installation process

for the technical task package we need to install the package
```bash
composer require krimt/kiurd-crud-generator:dev-master

```
For resource publish run the following command
```bash
php artisan vendor:publish --tag=stubs
```
after that run the following command
```bash
php artisan crud:generate {name}
```
name would be the name of the model, controller, request and migration file

after run this command you will see the following files in the following directories and api routes will be added in the routes/api.php file


