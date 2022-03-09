# A Future Corporation Test Task.

## Requirements
- Docker.

## Installation
Open terminal. Navigate to current directory. Run commands below to build app and initial setups:

```
docker-compose build app
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app npm install
docker-compose exec app npm run dev
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Run supervisor to run schedule:work, npm watch and websocket server.
# This can be included in Dockerfile but we need to run composer install 
# for the websocket library before running websocket server.

docker-compose exec app /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

## Run
Navigate to http://localhost:8000.

## Test
Run:
```
docker compose exec app php artisan test
```

## Troubleshooters
- Invalid API key, or exceeded your monthly request limit: You need to use a new Currency Layer API Key. Change key
  in www/.env `CURRENCY_LAYER_KEY`.