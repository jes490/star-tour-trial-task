# INSTALLATION
Install composer dependencies
```
composer install
```
Install and build front-end dependencies
```
npm install
npm run dev
```
Run laravel migrations
```
php artisan migrate
```

### PhantomJS Installation
To have composer automatically install PhantomJS binary you must have bz-2 php extension enabled.

In case you don't have it enabled you can download PhantomJS binary manually and place it to `vendor/bin` folder.

# TESTING
Run `phpunit` to start feature testing.

