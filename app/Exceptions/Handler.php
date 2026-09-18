<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'confirm_password',
        'mpin',
        'otp',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            if ($request->hasSession()) {
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Your session was refreshed. Please submit once again.',
                    'csrf_token' => csrf_token(),
                    'refresh_url' => url('csrf-token/refresh'),
                ], 419);
            }

            $safeInput = $request->except([
                '_token', 'password', 'password_confirmation', 'confirm_password',
                'current_password', 'mpin', 'otp',
            ]);

            $fallback = $request->is('login', 'mpin-login', 'is-login')
                ? url('login')
                : url()->previous();

            return redirect($fallback)
                ->withInput($safeInput)
                ->with('error', 'Your session was refreshed. Please submit the form once again.');
        }

        return parent::render($request, $exception);
    }
}
