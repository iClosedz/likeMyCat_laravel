#!/bin/sh

echo 'Put site in maintenance mode'
php artisan down

## see http://stackoverflow.com/questions/1125968/force-git-to-overwrite-local-files-on-pull
echo 'grabbing latest code from github'
time git fetch --all
time git reset --hard origin/master

echo 'updating permissins'
chown -R :www-data . 2>/dev/null
chmod -R 775 uploads 2>/dev/null
chmod -R 775 app/storage 2>/dev/null

echo 'composer install'
composer install

echo 'migrate database changes'
php artisan migrate

echo 'Bring site back up'
php artisan up

echo 'done'