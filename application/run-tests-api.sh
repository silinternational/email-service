#!/usr/bin/env bash

# Exit script if any command fails
set -e

# Echo commands as they are executed
set -x

# Install composer dev dependencies
cd /data
composer install --prefer-dist --no-interaction --optimize-autoloader

mkdir -p /data/runtime/mail

# Run database migrations
whenavail ${MYSQL_HOST} 3306 100 /data/yii migrate --interactive=0

# Start apache
apache2ctl start

# Run codeception tests
/data/vendor/bin/codecept run api -d
