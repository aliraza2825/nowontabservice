<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMenuWidgetAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (! $request->session()->get('menu_widget_admin_authenticated')) {
            return redirect()->guest(route('menu-widget.admin.login'));
        }

        return $next($request);
    }
}
