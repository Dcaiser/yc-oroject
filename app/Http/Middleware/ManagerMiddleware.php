<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || (!Auth::user()->isManager() && !Auth::user()->isAdmin())) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Akses ditolak. Halaman ini hanya untuk Manager atau Admin.');
        }

        return $next($request);
    }
}
