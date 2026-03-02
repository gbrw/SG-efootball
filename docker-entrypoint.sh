#!/bin/bash
set -e

# Railway provides $PORT — default to 80 if not set
APP_PORT="${PORT:-80}"

# Update Apache to listen on the correct port
sed -i "s/Listen 80/Listen ${APP_PORT}/" /etc/apache2/ports.conf
sed -i "s/\${PORT}/${APP_PORT}/g" /etc/apache2/sites-available/000-default.conf

exec apache2ctl -D FOREGROUND
