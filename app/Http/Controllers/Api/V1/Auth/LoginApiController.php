<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Requests\Api\V1\Auth\LoginApiRequest;


class LoginApiController extends Controller
{

    public function __invoke(LoginApiRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'],$user->password)) {

            return response()->json(['errors' => 'Credentials incorrect'])->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        session()->regenerate();

        $device = substr($request->userAgent() ?? '', 0, 255);

        $token  = $user->createToken($device)->plainTextToken;

        return response()->json([
            'access_token' => $token,
        ]);
    }

}