<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $u = User::find($user->id);

            $referral_code = Str::of($u->name)
                ->pipe(fn ($name) => $name->slug())
                ->pipe(fn ($name) => $name->replace('-', ''))
                ->upper();

            $user->referral_code = $referral_code;
            $user->save();
        }
    }
}
