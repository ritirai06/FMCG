<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            // Redirect to the correct panel login based on URL prefix
            if ($request->is('delivery-panel/*') || $request->is('delivery-panel')) {
                return redirect()->route('delivery.panel.login');
            }
            if ($request->is('sale/*') || $request->is('sale')) {
                return redirect()->route('sale.login');
            }
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Wrong role — redirect to their own dashboard
        return match ($userRole) {
            'admin'    => redirect()->route('dashboard')->with('error', 'Access denied.'),
            'sales'    => redirect()->route('sale.panel.dashboard')->with('error', 'Access denied.'),
            'delivery' => redirect()->route('delivery.panel.dashboard')->with('error', 'Access denied.'),
            default    => redirect()->route('login'),
        };
    }
}
