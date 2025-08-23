<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\TestMiddleware;
use App\Http\Middleware\SSOBrokerMiddleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware as SpatieRoleMiddleware;

// Tambahan IMPORT untuk handler
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class, // sekarang bisa pakai 'role:bidang' di routes
            'SSOBrokerMiddleware' => \App\Http\Middleware\SSOBrokerMiddleware::class,
            'spatie_role' => SpatieRoleMiddleware::class,
            'spatie_permission' => PermissionMiddleware::class,
            'spatie_role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        // (opsional) kalau mau tambahkan global middleware atau group, taruh di sini
        // $middleware->web(append: [ \Illuminate\Session\Middleware\StartSession::class ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Not found / method salah → paksa 403
        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException|MethodNotAllowedHttpException $e, $request) {
            Log::debug('custom-403 (notfound/method) hit', [
                'ex' => get_class($e),
                'path' => $request->path(),
            ]);

            return $request->expectsJson()
                ? response()->json(['message' => 'Forbidden'], 403)
                : response()->view('errors.403', ['forced' => true], 403);
        });

        // Auth / policy → 403 (tetap 403, tapi kita pastikan view konsisten & log)
        $exceptions->render(function (AuthorizationException|AuthenticationException $e, $request) {
            Log::debug('custom-403 (auth/policy) hit', [
                'ex' => get_class($e),
                'path' => $request->path(),
            ]);

            return $request->expectsJson()
                ? response()->json(['message' => 'Forbidden'], 403)
                : response()->view('errors.403', ['forced' => true], 403);
        });
    })->create();
