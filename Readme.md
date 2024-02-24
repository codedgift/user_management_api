# Senior Laravel Developer Interview Task - User Management System API

### **Task Overview**

Develop a **User Management System API** using Laravel. This API will be responsible for handling user profiles within an application, including operations such as creating, updating, viewing, and deleting users.

## Installation and Setup
**1. Clone the repository on your machine**
```sh
git clone github.com/repository_url
```
**2. Install Composer**
CD into your project directory then run this composer command to install all the dependency needed for your applicaiton to run
```sh
composer install
```
**3. Create a database on your machine**
Create a database on your machine and update the `.env` file with the details of the newly created database

**4. Install Passport**
Install passport using the command below
```sh
composer require laravel/passport
```
**5. Run Migration**
Run the in-built laravel artisan command to run the migrations, run
```sh
php artisan migrate
```
**6. Execute Passport install**
This command will create the encryption keys needed to generate secure access tokens. In addition, the command will create "personal access" and "password grant" clients which will be used to generate access tokens, with a tag of uuids because the id is uuid as oppose the normal integer value.
```sh
php artisan passport:install --uuids
```
**7. Seed Admin Data**
Run artisan command to seed an already generated default admin data into the users table, run:
```sh
php artisan db:seed --class=AdminSeeder
```
**7. Seed Users Data**
Run artisan command to seed newly generated faker users that has been setup in the UserFactory.php file, run
```sh
php artisan db:seed --class=UserSeeder
```
Alternatively, you can run one command to seed both `Amdin` and `Users` seeder together, run
```sh
php artisan db:seed --class=UserAdminSeeder
```
**8. Define passport authentication guard inside the `config/auth.php` file**
Inside the `config/auth.php` file, locate the guards section and add this if not already present:
```sh
'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
```
**9. Finally, update your `Http\Kernel.php` file**
Add this so as to make passport accessible globally on your application:
```sh
protected $routeMiddleware = [
'passport.auth' => \App\Http\Middleware\PassportAuthMiddleware::class,
'admin.role.check' => \App\Http\Middleware\CheckAdminRole::class,
];
```

## Tests
Both `Feature` and `Unit` test has been written. To run all the test in the application, run the artisan command below:
```sh
php artisan test
```
Alternatively, if you choose to run the test individually, let's say you want to run just the `LoginUnitTest` which can be found in the `tests\Unit\Auth` folder, run:
```sh
php artisan test --filter LoginUnitTest
```
Do this for the remaining individual tests.

## Run Application
To run the application so as to test on postman, run the command below to run the laravel application:
```sh 
php artisan serve --port=8008
```
Note: you can make use of your defined port number, not necessary you use this port number.

Made with ❤️ by Gift Amah
