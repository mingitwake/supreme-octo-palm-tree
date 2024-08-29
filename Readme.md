### Readme.md

#### Prerequisite

 - python ^3.7
 - php ^8.2
 - php-fpm (optional)
 - nginx (optional)
 - composer
 - git
 - 7zip
 - Visual Studio Community 2022 [recommended]
 - mysql-server [recommended]
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

> ``./.venv/Scripts/activate``

#### Activate virtual environment (Linux)

> ``source .venv/Scripts/activate``

#### Install packages

> ``pip install -r requirements.txt``

#### Run Chroma Server

> ``cd ./chroma``
>
> ``docker compose up``

check if the server is running on <http://localhost:8000/api/v1> or <http://localhost:8000/api/v1/collections>  

#### Run and Test Python Service
create a .env under /rest to store your azure credentials.

> ``API_KEY=your-azure-api-key``
>
> ``AZURE_ENDPOINT=your-azure-endpoint``
>
> ``MODEL_DEPLOYMENT=your-model-deployment``
>
> ``EMBEDDING_DEPLOYMENT=your-embedding-deployment``
>
> ``API_VERSION=your-api-version``
>
> ``DI_ENDPOINT=your-document-intelligence-endpoint``
>
> ``DI_API_KEY=your-document-intelligence-api-key``

> ``cd ./rest``
> 
> ``uvicorn main:app --port 5000 --reload``

#### Run Laravel
the laravel folder should be put under /var/www/, artisan serve is for developmental purpose only. 

> ``cd /var/www/laravel``
>
> ``php artisan route:list --path=api``
>
> ``php artisan serve --port 8080``

check if laravel is running

#### Configurating Laravel

if path misconfiguration occurs, the quick way is to create a laravel project and move the files into the project.

> ``composer create-project --prefer-dist laravel/laravel laravel-project``
>
> ``cd laravel-project``
>
> ``php artisan install:api --passport``
>
> ``composer require guzzlehttp/guzzle``
>
> ``php artisan migrate:generate``
>
> ``php artisan config:publish cors``
> 
> ``php artisan make:model <Model> -m``
>
> ``php artisan make:controller <Controller> --resource``
>
> ``php artisan migrate``

#### Modify .env
> ``DB_CONNECTION=mysql``
>
> ``DB_HOST=127.0.0.1``
>
> ``DB_PORT=3306``
>
> ``DB_DATABASE=your-dbname``
>
> ``DB_USERNAME=your-username``
>
> ``DB_PASSWORD=your-password``
>
> ``CACHE_STORE=redis``
>
> ``CACHE_PREFIX=``
>
> ``REDIS_CLIENT=phpredis``
>
> ``REDIS_HOST=127.0.0.1``
>
> ``REDIS_PASSWORD=null``
>
> ``REDIS_PORT=6379``
>
> ``MAIL_MAILER=your-mailer``
>
> ``MAIL_HOST=127.0.0.1``
>
> ``MAIL_PORT=2525``
>
> ``MAIL_USERNAME=null``
>
> ``MAIL_PASSWORD=null``
>
> ``MAIL_ENCRYPTION=null``
>
> ``MAIL_FROM_ADDRESS=your_address``
>
> ``MAIL_FROM_NAME="EstherITWake"``
>
> ``POSTMARK_MESSAGE_STREAM_ID=your-stream``
>
> ``POSTMARK_TOKEN=your-token``
>
> ``PASSPORT_PRIVATE_KEY=your-private-key``
>
> ``PASSPORT_PUBLIC_KEY=your-public-key``

#### Nginx As Reverse Proxy on WSL
> ``wsl -d Ubuntu-22.04``
- https://saaslit.com/blog/laravel/how-to-install-laravel-11-on-linux
- https://www.how2shout.com/how-to/install-nginx-php-mysql-wsl-windows-10.html
- https://www.baeldung.com/linux/nginx-cross-origin-policy-headers
- https://laravel.com/docs/11.x/deployment#server-configuration

note* leave server name as wildcard _</br>
For Windows servers, firewall configuration might be required.</br>
</br>
Get the server ip address with ipconfig (Windows) or ifconfig (Linux)