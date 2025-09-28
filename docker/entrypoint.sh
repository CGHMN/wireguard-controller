#!/bin/sh
set -e

echo "Running migration steps"
php artisan migrate

echo "Starting supervisord"
supervisord -c /etc/supervisor.conf