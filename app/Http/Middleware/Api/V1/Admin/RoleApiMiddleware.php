<?php

namespace App\Http\Middleware\Api\V1\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleApiMiddleware
{

    public function handle(Request $request, Closure $next, string $role): Response
    {

        if (!auth()->check())
            return response()->json(['errors' => 'Unauthenticated'], 401);


        if (! auth()->user()->roles()->where('name', $role)->exists())
            return response()->json(['errors'=>'Unauthorized'], Response::HTTP_FORBIDDEN);


        return $next($request);
    }
}
