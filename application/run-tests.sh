#!/usr/bin/env bash

# Try to install composer dev dependencies
cd /data
runny composer install --no-interaction --no-scripts

# Try to run database migrations
runny whenavail db 3306 100 ./yii migrate --interactive=0

# start apache
runny apachectl start

# Run the feature tests
./vendor/bin/codecept run unit
