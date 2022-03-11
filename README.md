# A Future Corporation Test Task.

## Requirements
- Docker.

## Installation
Open terminal. Navigate to current directory.

- Copy `api/.env-example` to `api/.env`.
- Copy `app/.env-example` to `app/.env`.

Run commands below to build api and app and initial setups:

```
# Load the environment.
docker-compose build api
docker-compose build app
docker-compose up -d

# Setup API.
docker-compose exec api composer install
docker-compose exec api php artisan key:generate
docker-compose exec api npm install
docker-compose exec api npm run dev
docker-compose exec api php artisan migrate
docker-compose exec api php artisan db:seed

# Create a passport client password grant.
docker-compose exec api php artisan passport:client --password

# What should we name the password grant client? [A Future Corporation API Password Grant Client]:
# Answer: <any name>
# Which user provider should this client use to retrieve users? [users]:
# Answer: users
# Received Client ID: <client id>
# Received Client secret: <client secret>
```

Copy configurations below to the end of `www/.env`. \
Change `<client id>` and `<client secret>` to the generated client id and client secret above. 
```
API_HOST=http://nginx_api
API_CLIENT_SECRET=<client secret>
API_CLIENT_ID=<client id>
API_GRANT_TYPE=password
API_USERNAME=api.user@future.com
API_PASSWORD=AComplexPassword
```

```
# Setup App.
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app npm install
docker-compose exec app npm run dev
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## Run
Navigate to http://localhost:8000 for the app.
Navigate to http://localhost:8001/api/documentation for the API documentation.

## Test
Run:
```
docker compose exec app php artisan test
```
