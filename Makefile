init: deps db tables basemodels app phpmyadmin test

app: db
	docker-compose up -d app

deps:
	docker-compose run --rm cli composer install

depsupdate:
	docker-compose run --rm cli composer update

db:
	docker-compose up -d db

tables: db
	docker-compose run --rm cli whenavail db 3306 100 ./yii migrate --interactive=0

basemodels: tables
	docker-compose run --rm cli whenavail db 3306 100 ./rebuildbasemodels.sh

phpmyadmin: db
	docker-compose up -d phpmyadmin

test: app
	APP_ENV=test docker-compose run --rm app /data/run-tests.sh

clean:
	docker-compose kill
	docker system prune -f
