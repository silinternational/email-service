#!/usr/bin/env bash

# Exit script if any command fails
set -e

# Echo commands as they are executed
set -x

# Try to install composer dev dependencies
cd /data
composer install --no-interaction --no-scripts

# Try to run database migrations
whenavail db 3306 100 ./yii migrate --interactive=0

# start apache
apachectl start

# Run the feature tests
./vendor/bin/codecept run unit
