## LikeMyCat [link](https://www.likemycat.com) - recreated with Lavarel 4

[GitHub Repository](https://github.com/davidkey/likeMyCat_laravel) for LikeMyCat site. The master branch is deployed to our production VM nightly.

## Deployment instructions (mostly for our own benifit :))

First, see the excellent documentation for a Laravel 4 starter site [here](https://github.com/andrew13/Laravel-4-Bootstrap-Starter-Site).

Our personal workflow
1. git clone [this repository]
2. Create app/config/production (or app/config/(dev|staging|etc)
3. Copy app/config/database.php to app/config/production and make suitable edits
4. from root likemycat_laravel directory: php artisan migrate
5. php artisan db:seed
6. ./deploy.sh (change chown :www-data ... piece if needed)
7. After configuring webserver config appropriately, login as 'admin@admin.com'. Note that image display won't really work correctly until at least 2 image uploads are made.

More info about LikeMyCat [here](https://www.likemycat.com/about) if interested.
