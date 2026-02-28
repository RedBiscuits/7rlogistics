<?php

namespace App\Http\Middleware;

use App\Enums\ModelChangeSource;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetModelChangeContextForOperations
{
    /**
     * Set the "model change" context when a write action is performed from the
     * Operations panel so that admin notifications are created with source
     * OPERATIONS_PANEL and the current user as actor.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('operations') || $request->is('operations/*')) {
            app()->instance('model_change.source', ModelChangeSource::OPERATIONS_PANEL);
            app()->instance('model_change.actor', $request->user());
        }

        return $next($request);
    }
}
