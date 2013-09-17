rsync -av --exclude '/uploads' --exclude '.git' --exclude='app/storage' --exclude='vendor' . root@192.168.1.135:/var/likemycat_laravel/
rsync -av --exclude '/uploads' --exclude '.git' --exclude='app/storage' root@192.168.1.135:/var/likemycat_laravel/ .
echo 'sync complete'
