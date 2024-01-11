#!/usr/bin/env bash

# establish a signal handler to catch the SIGTERM from a 'docker stop'
# reference: https://medium.com/@gchudnov/trapping-signals-in-docker-containers-7a57fdda7d86
term_handler() {
  apache2ctl stop
  killall cron
  exit 143; # 128 + 15 -- SIGTERM
}
trap 'kill ${!}; term_handler' SIGTERM

# fix folder permissions
chown -R www-data:www-data \
  /data/console/runtime/

# Run database migrations
/data/yii migrate --interactive=0
rc=$?;
if [[ $rc != 0 ]]; then
  echo "FAILED to run database migrations. Exit code ${rc}."
  exit $rc
fi

# Dump env to a file to make available to cron
env >> /etc/environment

# Start cron daemon in the background
service cron start

# If cron failed, exit.
rc=$?;
if [[ $rc != 0 ]]; then
  echo "FAILED to start cron daemon. Exit code ${rc}."
  exit $rc;
fi

# Run apache in foreground
apache2ctl -D FOREGROUND

