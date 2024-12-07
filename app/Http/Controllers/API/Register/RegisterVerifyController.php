<?php

namespace App\Http\Controllers\API\Register;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\User;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Illuminate\Http\Request;

class RegisterVerifyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        try {
            $token = $request->token;
            $encryptionKey = config('app.defuse_encryption_key');
            $tokenInformation = Crypto::decrypt($token, Key::loadFromAsciiSafeString($encryptionKey));
            $user = json_decode($tokenInformation);

            $emailVerification = EmailVerification::query()
                ->where('email', $user->email)
                ->where('token', $token)
                ->where('expired_at', '>', now())
                ->first();

            if (!$emailVerification) {
                return view('register.verify', [
                    'success' => false,
                    'message' => 'Token verifikasi tidak valid'
                ]);
            }

            $user = User::query()->where('email', $user->email)->first();
            $user->is_active = 1;
            $user->email_verified_at = now();
            $user->save();

            $emailVerification->is_verified = 1;
            $emailVerification->save();

            return view('register.verify', [
                'success' => true,
                'message' => 'Berhasil verifikasi email, silahkan login'
            ]);

        } catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
            return view('register.verify', [
                'success' => false,
                'message' => 'Token verifikasi tidak valid'
            ]);
        }
    }
}
