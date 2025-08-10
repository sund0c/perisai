<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof QueryException) {
            // $e->errorInfo[1] = 1451 → FK restrict delete/update
            if ((int) data_get($e->errorInfo, 1) === 1451) {
                return response()->view('errors.403', [
                    'message' => 'Aksi ini tidak diperbolehkan karena data sedang digunakan.',
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return parent::render($request, $e);
    }
}
