<?php

namespace App\Http\Middleware;

use App\Enums\ModelChangeSource;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetModelChangeContext
{
    /**
     * Set the "model change" context for the request so that the
     * RecordsModelChangeNotifications trait can create notifications
     * only when changes come from API (or other non-admin sources).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        app()->instance('model_change.source', ModelChangeSource::API);
        app()->instance('model_change.actor', $user);

        return $next($request);
    }
}
