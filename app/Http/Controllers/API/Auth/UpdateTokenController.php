<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
        ]);

        $user =User::query()
                ->where('email', $request->email)
                ->first();

        $sanctumToken = config('app.sanctum_token');
        $token = $user->createToken($sanctumToken, ['*'], now()->addMonths(6))->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
}
