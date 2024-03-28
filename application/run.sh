#!/usr/bin/env bash

# Run apache in foreground
if [[ -z "${APP_ID}" ]]; then
  apache2ctl -k start -D FOREGROUND
else
  config-shim --app $APP_ID --config $CONFIG_ID --env $ENV_ID apache2ctl -k start -D FOREGROUND
fi
