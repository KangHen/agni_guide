<?php

namespace App\Services;

use App\Models\User;

class GoogleService
{
    private User $user;
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected string $email,
        protected string $name
    )
    {}

    public function validateUser(): ?User
    {
        $user = User::query()
                ->where('email', $this->email)
                ->first();

        if (!$user) {
            return null;
        }

        return $this->user = $user;
    }

    public function registerUser(): User
    {
        $save = User::query()
            ->create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => '',
                'address' => '',
                'city' => '',
                'password' => '',
                'role_id' => 3,
                'email_verified_at' => now(),
                'is_active' => 1,
                'is_google_login' => 1
            ]);

        if ($save) {
            $this->user = User::find($save->id);
        }

        return $this->user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
