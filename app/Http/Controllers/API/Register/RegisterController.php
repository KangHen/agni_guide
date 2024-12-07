<?php

namespace App\Http\Controllers\API\Register;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterResource;
use App\Jobs\SendMailJob;
use App\Mail\RegisterVerifyMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

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
        $token = $user->createToken($sanctumToken, ['*'], now()->addMonth(1))->plainTextToken;

        $tokenInformation = collect([
            'email' => $user->email,
            'name' => $user->name,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'created_at' => $user->created_at,
        ])->toJson();

        $encryptionKey = config('app.defuse_encryption_key');
        $verifyToken = Crypto::encrypt($tokenInformation, Key::loadFromAsciiSafeString($encryptionKey));

        EmailVerification::query()->create([
            'email' => $user->email,
            'token' => $verifyToken,
            'expired_at' => now()->addMonth()
        ]);

        SendMailJob::dispatch($user->email, new RegisterVerifyMail($user, $verifyToken))->onQueue('email');

        return (new RegisterResource($user))->additional([
            'success' => (bool) $user,
            'message' => 'Berhasil mendaftar, silahkan cek email Anda untuk verifikasi',
            'token' => $token,
        ]);
    }
}
