<?php

namespace App\Http\Middleware\Api\V1\Admin;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use App\Services\Api\V1\Admin\UserApiService;

class AdminRoleApiMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {

        if (! Auth::check())
            return response()->json(['errors' => 'Unauthenticated'])->setStatusCode(Response::HTTP_UNAUTHORIZED);

        $user = Auth::user();

        $userApiService = new UserApiService;

        if(! $userApiService->isAdminRole($user) )
            return response()->json(['errors'=>'Unauthorized'])->setStatusCode(Response::HTTP_FORBIDDEN);


        return $next($request);
    }
}
