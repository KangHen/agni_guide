<?php
namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth",
     *     tags={"Auth"},
     *     summary="Login",
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="email@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="token"),
     *         ),
     *     ),
     * )
     * */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::query()
                ->where('email', $request->email)
                ->first();

        if (! $user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => 'Email or Password was Incorrect'
            ]);
        }

        $sanctumToken = config('app.sanctum_token');
        $token = $user->createToken($sanctumToken, ['*'], now()->addMonths(6))->plainTextToken;

        return (new UserResource($user))->additional([
            'token' => $token,
        ]);
    }
}
