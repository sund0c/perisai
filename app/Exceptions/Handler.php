<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        // Tulis jejak ke log agar kita tahu Handler terpanggil
        Log::debug('Handler::render hit', [
            'exception' => get_class($e),
            'path'      => $request->path(),
            'status'    => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : null,
        ]);

        // Semuanya dipaksa 403 (kalau mau batasi ke area tertentu, lihat bagian #3)
        if (
            $e instanceof ModelNotFoundException ||
            $e instanceof NotFoundHttpException ||
            $e instanceof MethodNotAllowedHttpException ||
            $e instanceof AuthorizationException ||
            $e instanceof AuthenticationException ||
            ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 403)
        ) {
            return $this->to403($request);
        }

        return parent::render($request, $e);
    }

    protected function to403($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden (forced by Handler)'], 403);
        }
        // Buat view ini agar keliatan jelas saat aktif
        return response()->view('errors.403', ['forced' => true], 403);
    }
}
