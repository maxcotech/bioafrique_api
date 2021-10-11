<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */

    protected $middlewarePriority = [
        \App\Http\Middleware\AddAuthHeader::class,
        \App\Http\Middleware\CheckAuthenticationStatus::class,
        \App\Http\Middleware\SetAccessCookie::class,
        \App\Http\Middleware\EnsureCurrencySelected::class,
        \App\Http\Middleware\Authenticate::class
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'auth.apicookie' => [
            \App\Http\Middleware\AddAuthHeader::class,
            \App\Http\Middleware\CheckAuthenticationStatus::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'auth:api',
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        ]

    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'app_access_guard' => \App\Http\Middleware\AppAccessGuard::class,
        'teachers_guard' => \App\Http\Middleware\TeachersGuard::class,
        'ensure_currency_selected' => \App\Http\Middleware\EnsureCurrencySelected::class,
        'set_access_cookie' => \App\Http\Middleware\SetAccessCookie::class,
        'sasom_access_guard' => \App\Http\Middleware\SASOMAccessGuard::class,
        'super_admin_access_guard' => \App\Http\Middleware\SuperAdminAccessGuard::class,
        'store_owner_access_guard' => \App\Http\Middleware\StoreOwnerAccessGuard::class,
        'add_auth_header' => \App\Http\Middleware\AddAuthHeader::class,
        'store_staff_guard' => \App\Http\Middleware\StoreStaffGuard::class,
        'check_authentication_status' => \App\Http\Middleware\CheckAuthenticationStatus::class
    ];
}
