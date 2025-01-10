<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()
            ->whereNotIn('id', [1,2])
            ->get();

        foreach ($users as $user) {
            Order::query()
                ->create([
                   'user_id' => $user->id,
                    'product_id' => 5,
                    'quantity' => 1,
                    'total' => 100000,
                    'grand_total' => 100000,
                    'status' => 'pending',
                    'order_code' => 'AG'.Str::random()
                ]);
        }
    }
}
