#!/usr/bin/env bash

# fix folder permissions
chown -R www-data:www-data \
  /data/console/runtime/

# Run database migrations
runny /data/yii migrate --interactive=0

# Dump env to a file
touch /etc/cron.d/email
env | while read line ; do
  echo "$line" >> /etc/cron.d/email
done

# Add env vars to idp-cron to make available to scripts
cat /etc/cron.d/email-cron >> /etc/cron.d/email

# Remove original cron file without env vars
rm -f /etc/cron.d/email-cron

# Start cron daemon
cron -f
