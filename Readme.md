### Readme

#### Prerequisite

 - python ^3.1
 - php ^7.2
 - composer
 - git
 - 7zip
 - docker [recommended]
 - helm [recommended]
 - nodejs [recommended]

#### Resources

 - https://www.geeksforgeeks.org/how-to-install-php-in-windows-10/
 - https://getcomposer.org/download/

extensions fileinfo, gettext, mysqli, pdo_mysql, zip, openssl are required
uncomment the according lines in php.ini and check the .dll files in /ext if required
uncomment ';extension_dir = "ext"' in php.ini if required
if php.ini is not found, rename php.ini-development to php.ini

#### Create virtual environment

> ``python -m venv .venv``

#### Activate virtual environment (Windows)

> ``Set-ExecutionPolicy -ExecutionPolicy Unrestricted``
> ``./.venv/scripts/activate``

#### Install packages

> ``pip install -r requirements.txt``

#### Run Chroma Server

> ``cd ./chroma``
> ``docker compose up``
[http://localhost:8000/api/v1]
[http://localhost:8000/api/v1/collections]

#### Run and Test Python Service

> ``python ./restapi/app.py``
> ``curl -X POST -H "Content-Type: application/json" --json "{\"query\":\"Hi\"}" http://localhost:5000/chat``

#### Run Laravel

> ``cd ./laravel-project``
> ``php artisan serve --port 8080``
[http://localhost:8080]

#### Resources

> ``composer create-project --prefer-dist laravel/laravel laravel-project``
> ``cd laravel-project``
> ``npm -v``
> ``npm install axios``
> ``php artisan install:api``
> ``composer require guzzlehttp/guzzle``
> ``php artisan make:controller Controller``