<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => App\Http\Middleware\CheckPermissionMiddleware::class,
            'auth' => App\Http\Middleware\AuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                $previous = $e->getPrevious();

                if ($previous instanceof ModelNotFoundException) {
                    $model = class_basename($previous->getModel());
                    $resource = Str::snake($model);

                    return response()->json([
                        'message' => "$model not found.",
                        'code' => "$resource.errors.notFound",
                    ], 404);
                }

                return response()->json([
                    'message' => 'Resource not found.',
                    'code' => 'resource.errors.notFound',
                ], 404);
            }
        });
    })->create();
