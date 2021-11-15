<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Install laravel8 and use jetstream with livewire.
user profile, email verification, password change & everything & also admin login process with seed 
& advance authentication system. user & admin same dashboard.

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

1, Install Laravel8

2,composer require laravel/jetstream

3,php artisan jetstream:install livewire

4,npm install && npm run dev 
or we can do follow above 2,3,4 step from this link:>https://jetstream.laravel.com/2.x/installation.html

5,setup .env file & database (phpmyadmin on xampp)

6,php artisan storage:link (for profile img)

7,update .env file http://127.0.0.1:8000 (for profile img)

8,we must be enable config>jetstream>Features::profilePhotos(),

After-that if I want to email verification system add so follow this step:
1,implements MustVerifyEmail, on model or follow this docmentation link>laravel.com/docs/8.x/verification.html

2,Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');

3,Must be enable here >config>fortify>Features::emailVerification(),

4,Signup on https://mailtrap.io/

5,Update .env file>
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=

After-that if I want to multiauth user/admin add so follow this step:
Since I already created user controller,model,table so I need create for admin.(Setup Admin Table and Seed data)
1,php artisan make:model -mcr or (I create individually controller,model,table)

2,Update database>migrations>admins_table (copy from users_table)

3,Update Models>Admin (copy from User)

4,php artisan migrate

5,database>factories>AdminFactory 
(php artisan make:factory AdminFactory or follow this link> https://laravel.com/docs/8.x/database-testing#generating-factories)

6,update AdminFactory>defination>return>
'name' => 'admin',
'email' => 'admin@gmail.com',
'email_verified_at' => now(),
'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
'remember_token' => Str::random(10),
[copy from UserFactory & password defualt]

7,Update database>seeders>DatabaseSeeder>\App\Models\Admin::factory()->create();

8,database>factories>AdminFactory>use Illuminate\Support\Str;

9,php artisan migrate --seed

#Create Guards for admin

10,(Laravel bydefault web guard for user):Add another-one guard: config>auth>
'guards' =>[
	'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
],
then:
'providers' => [
  'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
],
then:
'passwords' => [
'admins' => [
            'provider' => 'admins',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
],


11,app>Providers>FortifyServiceProvider>public function register()
    {
        $this->app->when([AdminController::class, AttemptToAuthenticate::class,
        RedirectIfTwoFactorAuthenticatable::class])->needs(StatefulGuard::class)
        ->give(function(){
            return Auth::guard('admin');
        });
    }

12,app>Providers>FortifyServiceProvider>
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Actions\Fortify\AttemptToAuthenticate;
use App\Actions\Fortify\RedirectIfTwoFactorAuthenticatable;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

13,Create new Guard under app: app>Guard>AdminStatefulGuard.php
& copy everything from>vendor>laravel>framework>src>Illuminate>Contracts>Auth>StatefulGuard.php 
& past here app>Guard>AdminStatefulGuard.php & update: namespace App\Guard;


14,routes>web.php>
Route::group(['prefix' => 'admin', 'middleware' => ['admin:admin']], function(){
    Route::get('/login', [AdminController::class, 'loginForm']);
    Route::post('/login', [AdminController::class, 'store'])->name('admin.login');
});

Route::middleware(['auth:sanctum,admin', 'verified'])->get('/admin/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum,web', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

15,Copy everything from>vendor>laravel>fortify>src>Http>Controllers>AuthenticatedSessionController
and past my AdminController: app>Http>Controllers>AdminController.
and update> use App\Http\Responses\LoginResponse;
also add:   
public function loginForm(){
       return view('auth.login', ['guard' => 'admin']);
   }

16,Update: resources>views>auth>login.php>action="{{ isset($guard) ? url($guard.'/login') : route('login') }}"

17,Copy two(2) both of file from: vendor>laravel>fortify>src>Actions>AttemptToAuthenticate.php & RedirectIfTwoFactorAuthenticatable.php
& past both of this file here: app>Actions>Fortify and update namespace: namespace App\Actions\Fortify;

18,app>Providers>RouteServiceProvider>    
public static function redirectTo($guard){
        return $guard.'/dashboard';
    }

19,app>Providers>FortifyServiceProvider>
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Actions\Fortify\AttemptToAuthenticate;
use App\Actions\Fortify\RedirectIfTwoFactorAuthenticatable;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
    public function register()
    {
        $this->app->when([AdminController::class, AttemptToAuthenticate::class,
        RedirectIfTwoFactorAuthenticatable::class])->needs(StatefulGuard::class)
        ->give(function(){
            return Auth::guard('admin');
        });
    }


20,Update: app>Http>Middleware>RedirectIfAuthenticated.php>return>redirect($guard.'/dashboard');

21,Add: app>Http>Middleware>AdminRedirectIfAuthenticated.php

22,Add: inside routeMiddleware: app>Http>Kernel.php>'admin' => \App\Http\Middleware\AdminRedirectIfAuthenticated::class,

23,Add: app>Http>Responses> then copy file from vendor>laravel>fortify>src>Http>Responses>LoginResponse.php
and update: namespace App\Http\Responses; & redirect()->intended('admin/dashboard');

24,run project >http://localhost:8000, http://localhost:8000/login, http://localhost:8000/admin/login

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[CMS Max](https://www.cmsmax.com/)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# newportal
