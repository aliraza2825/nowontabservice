<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuWidgetAdminAuthController extends Controller
{
    public function showLogin(Request $request): RedirectResponse|View
    {
        if ($request->session()->get('menu_widget_admin_authenticated')) {
            return redirect()->route('menu-widget.admin.edit');
        }

        return view('menu-widget-admin-login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $validUsername = hash_equals(config('toast_menu.admin.username'), $credentials['username']);
        $validPassword = hash_equals(config('toast_menu.admin.password'), $credentials['password']);

        if (! $validUsername || ! $validPassword) {
            return back()
                ->withErrors(['username' => 'Invalid username or password.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('menu_widget_admin_authenticated', true);

        return redirect()->intended(route('menu-widget.admin.edit'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('menu_widget_admin_authenticated');
        $request->session()->regenerateToken();

        return redirect()->route('menu-widget.admin.login');
    }
}
