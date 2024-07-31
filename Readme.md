### Readme.md

#### Prerequisite

 - python ^3.7
 - php ^7.2
 - composer
 - git
 - 7zip
 - xampp [recommended]
 - docker [recommended]
 - helm [recommended]
 - nodejs [recommended]

#### Resources

 - <https://www.geeksforgeeks.org/how-to-install-php-in-windows-10/>
 - <https://getcomposer.org/download/>
 - <https://windows.php.net/download/>

#### Configuring PHP

extensions fileinfo, gettext, mysqli, pdo_mysql, zip, sodium, openssl are required  
uncomment the according lines in php.ini and check the .dll files in /ext if required  
uncomment ';extension_dir = "ext"' in php.ini if required  
if php.ini is not found, rename php.ini-development to php.ini  

#### Create virtual environment

> ``python -m venv .venv``

#### Activate virtual environment (Windows)

> ``Set-ExecutionPolicy -ExecutionPolicy Unrestricted``
>
> ``./.venv/scripts/activate``

#### Install packages

> ``pip install -r requirements.txt``

#### Run Chroma Server

> ``cd ./chroma``
>
> ``docker compose up``

check if the server is running on <http://localhost:8000/api/v1> or <http://localhost:8000/api/v1/collections>  

#### Run and Test Python Service

> ``uvicorn main:app --port 5000 --reload``
>
> ``curl -X <Method> -H "Content-Type: application/json" --json "{\"<key>\":\"<value>\"}" http://localhost:5000/<route>``

#### Run Laravel

> ``cd ./laravel-project``
>
> ``php artisan route:list --path=api``
>
> ``php artisan serve --port 8080``

check if laravel is running on <http://localhost:8080>  

#### Configurating Laravel

if path misconfiguration occurs, create a laravel project and move the files into the project.

> ``composer create-project --prefer-dist laravel/laravel laravel-project``
>
> ``cd laravel-project``
>
> ``php artisan install:api --passport``
>
> ``composer require guzzlehttp/guzzle``
>
> ``composer require --dev "kitloong/laravel-migrations-generator"``
>
> ``php artisan migrate:generate``
>
> ``php artisan config:publish cors``
>
> ``php artisan make:controller <Controller> --resource``
>
> ``php artisan migrate``

directories and files that require modification
- .env
- app/Http/
- app/Models/
- database/migrations/
- resources/views/
- routes/web.php
