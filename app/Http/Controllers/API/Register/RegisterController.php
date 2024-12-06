<?php

namespace App\Http\Controllers\API\Register;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Jobs\SendMailJob;
use App\Mail\RegisterVerifyMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request): JsonResponse|RegisterResource
    {
        $request->validated();

        $data = [
          ...$request->except('password'),
            'password' => bcrypt($request->password),
            'role_id' => 3,
            'is_active' => 0
        ];

        $user = User::query()->create($data);
        $sanctumToken = config('app.sanctum_token');
        $token = $user->createToken($sanctumToken, ['*'], now()->addMonths(6))->plainTextToken;

        $verifyToken = $user->createToken('email-verification')->plainTextToken;
        SendMailJob::dispatch($user->email, new RegisterVerifyMail($user, $verifyToken))->onQueue('mail');

        return (new RegisterResource($user))->additional([
            'success' => (bool) $user,
            'message' => 'User registered successfully',
            'token' => $token,
        ]);
    }
}
