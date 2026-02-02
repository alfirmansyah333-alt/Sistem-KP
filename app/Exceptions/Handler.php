<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Redirect 419 (Session Expired) to login
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 419) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
        }

        return parent::render($request, $exception);
    }
}
