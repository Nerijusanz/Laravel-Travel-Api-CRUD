<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LogoutApiController extends Controller
{

    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        session()->invalidate();

        session()->regenerateToken();

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }
}
