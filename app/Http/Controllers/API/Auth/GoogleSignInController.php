<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\GoogleService;
use Illuminate\Http\Request;

class GoogleSignInController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'is_google' => 'required|boolean'
        ]);

        $googleService = new GoogleService($request->email, $request->name);

        if (!$googleService->validateUser()) {
            $googleService->registerUser();
        }

        $user = $googleService->getUser();

        $sanctumToken = config('app.sanctum_token');
        $token = $user->createToken($sanctumToken, ['*'], now()->addMonths(12))->plainTextToken;

        return (new UserResource($user))->additional([
            'success' => (bool) $user,
            'message' => 'Berhasil masuk dengan Google',
            'token' => $token,
        ]);
    }
}
