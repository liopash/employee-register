#!/usr/bin/env bash
composer require
yarn
yarn dev
chown -R www-data:www-data /var/www
chmod -R 755 /var/www/public 
#chmod -R 0666 /var/www
/entrypoint.sh
