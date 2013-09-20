rsync -av --exclude 'composer.json' --exclude 'composer.lock' --exclude '/uploads' --exclude '.git' --exclude='app/storage' . root@192.168.0.113:/var/likemycat_laravel/
rsync -av --exclude '/uploads' --exclude '.git' --exclude='app/storage' root@192.168.0.113:/var/likemycat_laravel/ .
echo 'sync complete'
