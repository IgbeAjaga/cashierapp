<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->is_approved) {
            auth()->logout();
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is pending approval by Super Admin.']);
        }

        return $next($request);
    }
}
