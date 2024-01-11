start: deps db tables basemodels app

app: db
	docker-compose up -d app cron phpmyadmin

deps:
	docker-compose run --rm cli composer install

depsupdate:
	docker-compose run --rm cli bash -c "composer update && composer show -D > versions.txt"

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

#TODO: tests api won't run unless the access keys are abc-123, need to change that so test will run out-of-box.
# would be best to have an additional container for testing because I don't think the env vars are being honored on the command line the way we think they are.
testapi:
	APP_ENV=test docker-compose run --rm app /data/run-tests-api.sh

cron: db
	docker-compose run --rm cron ./yii send/send-queued-email

clean:
	docker-compose kill
	docker-compose rm -f

psr2:
	docker-compose run --rm cli bash -c "vendor/bin/php-cs-fixer fix ."
