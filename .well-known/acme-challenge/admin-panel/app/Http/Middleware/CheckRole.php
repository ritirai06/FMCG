<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->is('delivery-panel*') || $request->is('delivery_panel*')) {
                return redirect()->route('delivery.panel.login');
            }

            if ($request->is('sale*')) {
                return redirect()->route('sale.login');
            }

            return redirect('/login');
        }

        if (!Schema::hasColumn('users', 'role')) {
            if ($request->is('delivery-panel*') || $request->is('delivery_panel*')) {
                return $next($request);
            }

            return abort(403, 'User role system is not configured.');
        }

        $userRole = auth()->user()->role;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        return abort(403, 'Unauthorized action. Your role does not have access to this resource.');
    }
}
