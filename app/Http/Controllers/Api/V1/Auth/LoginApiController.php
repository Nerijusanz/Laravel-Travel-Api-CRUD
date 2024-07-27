<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use App\Http\Requests\Api\V1\Auth\LoginApiRequest;


class LoginApiController extends Controller
{

    public function __invoke(LoginApiRequest $request)
    {

        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {

            throw ValidationException::withMessages([
                'errors' => ['The provided credentials are incorrect.'],
            ]);
        }

        session()->regenerate();

        $device = substr($request->userAgent() ?? '', 0, 255);

        return response()->json([
            'access_token' => $user->createToken($device)->plainTextToken,
        ]);
    }

}
