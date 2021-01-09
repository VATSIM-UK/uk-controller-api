#!/bin/bash

php /var/www/ukcp/artisan websockets:serve > /var/log/websocket/websocket.log &
php-fpm
