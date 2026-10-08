<?php

namespace App\Http\Middleware\Concerns;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait DeniesAccess
{
    /**
     * JSON for API clients (future Next.js), a normal HTTP error page otherwise.
     */
    protected function deny(Request $request, string $code, string $message, int $status = 403): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['code' => $code, 'message' => $message], $status);
        }

        abort($status, $message);
    }
}