<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'city' => 'required|string',
        ]);

        $user = auth()->user();

        $checkPhone = User::where('phone', $request->phone)
                ->where('id', '!=', $user->id)
                ->first();

        if ($checkPhone) {
            throw new \Exception('Phone number already exists');
        }

        $user->update($request->all());

        return (new UserResource($user))->additional([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }
}
