# Create virtual environment
python -m venv .venv

# Activate virtual environment
# Windows
Set-ExecutionPolicy -ExecutionPolicy Unrestricted

./.venv/scripts/activate
set FLASK_ENV=development

# Install packages
pip install -r requirements.txt

# Run Chroma Server
cd ./chroma
docker compose up
http://localhost:8000/api/v1
http://localhost:8000/api/v1/collections

# References
https://docs.docker.com/compose/compose-application-model/

# Run Restful API
curl -X POST -H "Content-Type: application/json" --json "{\"document\":\"https://engg.hku.hk/Admissions/MSc/Fees\"}" http://localhost:5000/upload

# Run Laravel
cd ./laravel-app
php artisan serve --port 8080
http://localhost:8080

###
composer create-project --prefer-dist laravel/laravel laravel-app
cd laravel-app
npm -v
npm install axios
php artisan install:api
composer require guzzlehttp/guzzle
php artisan make:controller Controller