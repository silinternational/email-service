#!/usr/bin/env bash

if [[ "x" == "x$LOGENTRIES_KEY" ]]; then
    echo "Missing LOGENTRIES_KEY environment variable";
else
    # Set logentries key based on environment variable
    sed -i /etc/rsyslog.conf -e "s/LOGENTRIESKEY/${LOGENTRIES_KEY}/"
    # Start syslog
    rsyslogd
    sleep 10
fi

# fix folder permissions
chown -R www-data:www-data \
  /data/console/runtime/

# Run database migrations
runny /data/yii migrate --interactive=0

# Dump env to a file
env >> /etc/environment

# Start cron daemon
cron -f
