#!/usr/bin/env bash

# Run apache in foreground
if [[ $PARAMETER_STORE_PATH ]]; then
  config-shim --path $PARAMETER_STORE_PATH apache2ctl -k start -D FOREGROUND
elif [[ $APP_ID ]]; then
  config-shim --app $APP_ID --config $CONFIG_ID --env $ENV_ID apache2ctl -k start -D FOREGROUND
else
  apache2ctl -k start -D FOREGROUND
fi
