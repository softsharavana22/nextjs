<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
//use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken; // New
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))

    /*
    |--------------------------------------------------------------------------
    | ROUTES (Laravel 12)
    |--------------------------------------------------------------------------
    */
    ->withRouting(
        web: __DIR__.'/../routes/web.php',

        api: [
            __DIR__.'/../routes/api.php',
            __DIR__.'/../routes/frontendapi.php',
            __DIR__.'/../routes/adminapi.php',
        ],

        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    /*
    |--------------------------------------------------------------------------
    | MIDDLEWARE (NO Kernel.php)
    |--------------------------------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {
        /*
        |--------------------------------------------------------------
        | CORS CONFIGURATION
        |--------------------------------------------------------------
        */
        // $middleware->cors([
        //     'paths' => ['api/*'],
        //     'allowed_methods' => ['*'],
        //     'allowed_origins' => ['http://localhost:3000'],
        //     'allowed_headers' => ['*'],
        //     'exposed_headers' => [],
        //     'max_age' => 0,
        //     'supports_credentials' => false, // IMPORTANT (token auth)
        // ]);
        $middleware->append(HandleCors::class);

        /* (NEW)
        |--------------------------------------------------------------------------
        | Disable CSRF for API routes (IMPORTANT)
        |--------------------------------------------------------------------------
        */
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);    	

        // API middleware group
        $middleware->api([
        	// ❌ REMOVE this if you are NOT using cookie-based auth
            //EnsureFrontendRequestsAreStateful::class,
            SubstituteBindings::class,
        ]);

        // Custom middleware aliases
        $middleware->alias([
            'frontend.auth' => \App\Http\Middleware\FrontendAuth::class,
            'admin.auth'    => \App\Http\Middleware\AdminAuth::class,
        ]);
    })

    /*
    |--------------------------------------------------------------------------
    | EXCEPTION HANDLING (JSON ONLY)
    |--------------------------------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {

        // 401 - Unauthenticated (no or invalid token)
        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ) {
            return response()->json([
                'status'  => 401,
                'message' => 'Unauthenticated'
            ], 401);
        });

        // 403 - Forbidden (wrong token ability)
        $exceptions->render(function (
            AccessDeniedHttpException $e,
            Request $request
        ) {
            return response()->json([
                'status'  => 403,
                'message' => 'Forbidden'
            ], 403);
        });

        // 🔹 Catch-all 500 error
        // $exceptions->render(function (Throwable $e, Request $request) {
        //     return response()->json([
        //         'status' => 500,
        //         'message' => 'Must passs valid authentication bearer token'
        //         //'message' => 'Internal Server Error',
        //         //'error' => $e->getMessage() // optional: show message in dev, hide in prod
        //     ], 500);
        // });        
    })

    ->create();
