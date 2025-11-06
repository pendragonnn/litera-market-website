<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::create([
            'name' => 'Admin LiteraMarket',
            'email' => 'admin@literamarket.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'address' => 'Jl. Buku Indah No. 42, Jakarta',
            'role' => 'admin',
        ]);

        // Customer accounts
        User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password'),
            'phone' => '089999999999',
            'address' => 'Jl. Pelanggan Setia No. 5, Bandung',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'janesmith@example.com',
            'password' => Hash::make('password'),
            'phone' => '081212345678',
            'address' => 'Jl. Mawar No. 10, Surabaya',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Michael Tan',
            'email' => 'michaeltan@example.com',
            'password' => Hash::make('password'),
            'phone' => '082134567890',
            'address' => 'Jl. Melati No. 8, Medan',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarahjohnson@example.com',
            'password' => Hash::make('password'),
            'phone' => '081345678912',
            'address' => 'Jl. Cendana No. 22, Yogyakarta',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'David Lee',
            'email' => 'davidlee@example.com',
            'password' => Hash::make('password'),
            'phone' => '083123456789',
            'address' => 'Jl. Anggrek No. 3, Semarang',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Linda Park',
            'email' => 'lindapark@example.com',
            'password' => Hash::make('password'),
            'phone' => '085612345678',
            'address' => 'Jl. Dahlia No. 14, Makassar',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Robert Wilson',
            'email' => 'robertwilson@example.com',
            'password' => Hash::make('password'),
            'phone' => '081998877665',
            'address' => 'Jl. Kenanga No. 2, Bali',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Emily Davis',
            'email' => 'emilydavis@example.com',
            'password' => Hash::make('password'),
            'phone' => '087712345678',
            'address' => 'Jl. Sakura No. 18, Malang',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Kevin Hartono',
            'email' => 'kevinhartono@example.com',
            'password' => Hash::make('password'),
            'phone' => '081276543210',
            'address' => 'Jl. Merpati No. 6, Palembang',
            'role' => 'customer',
        ]);
    }
}
