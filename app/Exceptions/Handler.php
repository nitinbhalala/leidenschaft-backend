<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\ErrorLog;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks.
     */
    public function register(): void
    {
        /**
         * Authorization Exception (403)
         */
        $this->renderable(function (AuthorizationException $e, $request) {

            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.'
            ], 403);
        });

        /**
         * Log all exceptions to error_logs table
         */
        $this->reportable(function (Throwable $e) {

            try {

                ErrorLog::create([
                    'source' => request()->method() . ' ' . request()->path(),
                    'user' => auth()->user()->name ?? 'system',
                    'message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString(),
                    'status' => 'open'
                ]);
            } catch (\Exception $ex) {
                // Prevent infinite loop if logging fails
            }
        });
    }

    /**
     * Custom render for API errors
     */
    public function render($request, Throwable $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'
        ], 500);
    }
}
