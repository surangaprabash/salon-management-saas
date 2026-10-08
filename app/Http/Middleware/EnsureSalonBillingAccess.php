<?php

namespace App\Http\Middleware;

use App\Enums\SalonStatus;
use App\Http\Middleware\Concerns\DeniesAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards ONLY the future billing / payment-proof endpoints.
 * Lets a SALON_ADMIN reach them even when the salon is BLOCKED,
 * so a blocked salon can still upload a payment receipt.
 *
 * Controllers behind this must still load bills/requests through
 * $request->attributes->get('salon'), never from a submitted salon_id.
 */
class EnsureSalonBillingAccess
{
    use DeniesAccess;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->deny($request, 'unauthenticated', 'Unauthenticated.', 401);
        }

        if (! $user->isActive()) {
            return $this->deny($request, 'user_disabled', 'Your account has been disabled.');
        }

        if (! $user->isSalonAdmin()) {
            return $this->deny($request, 'forbidden', 'Only the salon admin can manage billing.');
        }

        $salon = $user->salon;

        if (! $salon || $salon->trashed()
            || ! in_array($salon->status, [SalonStatus::Active, SalonStatus::Blocked], true)) {
            return $this->deny($request, 'no_billing_access', 'Billing is not available for this salon.');
        }

        $request->attributes->set('salon', $salon);

        return $next($request);
    }
}