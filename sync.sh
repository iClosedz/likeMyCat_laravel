rsync -av --exclude '.git' --exclude='app/storage' --exclude='vendor' . root@192.168.1.133:/var/likemycat_laravel/
rsync -av --exclude '.git' --exclude='app/storage' root@192.168.1.133:/var/likemycat_laravel/ .
