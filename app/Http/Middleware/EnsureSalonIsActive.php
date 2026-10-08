<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\DeniesAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards normal salon features (future API: services, appointments, staff...).
 * The salon is resolved from the authenticated user, never from request input.
 */
class EnsureSalonIsActive
{
    use DeniesAccess;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->deny($request, 'unauthenticated', 'Unauthenticated.', 401);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->isActive()) {
            return $this->deny($request, 'user_disabled', 'Your account has been disabled.');
        }

        $salon = $user->salon;

        if (! $salon || $salon->trashed()) {
            return $this->deny($request, 'no_salon', 'No salon is associated with this account.');
        }

        if (! $salon->isActive()) {
            return $this->deny($request, 'salon_blocked', 'This salon is currently not active.');
        }

        $request->attributes->set('salon', $salon);

        return $next($request);
    }
}