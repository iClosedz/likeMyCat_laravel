#!/bin/sh

## see http://stackoverflow.com/questions/1125968/force-git-to-overwrite-local-files-on-pull

echo 'grabbing latest code from github'
time git fetch --all
time git reset --hard origin/master

echo 'updating permissins'
chown -R :www-data .
chmod -R 775 uploads
chmod -R 775 app/storage

echo 'composer install'
composer install

echo 'done'