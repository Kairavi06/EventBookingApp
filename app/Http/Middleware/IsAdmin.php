<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminEmails = ['admin@admin.com']; // Replace/add more admin emails as needed

        if (!in_array($request->user()->email, $adminEmails)) {
            return response()->json(['message' => 'Access denied. Admins only.'], 403);
        }

        return $next($request);
    }
}

