<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

//    public function render($request, Throwable $exception)
//    {
//        // Если запрос начинается с /api и это 404 ошибка
//        if ($request->is('api/*') || $request->is('*')) {
//            if ($exception instanceof NotFoundHttpException) {
//                return response()->json([
//                    'message' => 'Resource not found',
//                    'status' => 404,
//                ], 404);
//            }
//        }
//
//        return parent::render($request, $exception);
//    }
}
