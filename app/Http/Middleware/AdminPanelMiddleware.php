<?php

namespace App\Http\Middleware;

use App\Http\Models\UserRoles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPanelMiddleware
{
    /**
     * Allow the request through only when the authenticated user holds the
     * `see_admin_panel` permission; otherwise deny with a 403.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (is_null($user) || $user->cannot('see_admin_panel', UserRoles::class)) {
            abort(403);
        }

        return $next($request);
    }
}
