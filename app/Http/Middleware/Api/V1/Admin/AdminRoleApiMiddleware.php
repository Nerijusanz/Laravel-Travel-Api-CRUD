<?php

namespace App\Http\Middleware\Api\V1\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Role;
use App\Services\Api\V1\Admin\UserApiService;

class AdminRoleApiMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {

        if (!auth()->check())
            return response()->json(['errors' => 'Unauthenticated'], 401);

        $userApiService = new UserApiService;

        $user = auth()->user();

        if(! $userApiService->isAdminRole($user) )
            return response()->json(['errors'=>'Unauthorized'], Response::HTTP_FORBIDDEN);


        return $next($request);
    }
}
