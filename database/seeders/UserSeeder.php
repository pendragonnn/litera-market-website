<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin LiteraMarket',
            'email' => 'admin@literamarket.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Jl. Buku Indah No. 42, Jakarta',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password'),
            'phone' => '089999999999',
            'address' => 'Jl. Pelanggan Setia No. 5, Bandung',
            'role' => 'customer',
        ]);
    }
}
