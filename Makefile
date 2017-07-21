init: deps db tables basemodels app test

app: db deps
	docker-compose up -d app phpmyadmin

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

test: app
	make testunit
	make testapi

testunit:
	APP_ENV=test docker-compose run --rm app /data/run-tests.sh

testapi:
	APP_ENV=test docker-compose run --rm app /data/run-tests-api.sh

clean:
	docker-compose kill
	docker system prune -f
