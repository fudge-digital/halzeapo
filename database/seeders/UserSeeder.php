<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('2025selalusukses_11M#'),
            'role' => 'SUPERADMIN',
        ]);

        User::create([
            'name' => 'Marketing Admin',
            'email' => 'marketing@halzpo.com',
            'password' => Hash::make('Bdg_2025##'),
            'role' => 'MARKETING',
        ]);

        User::create([
            'name' => 'Finance Admin',
            'email' => 'finance@halzpo.com',
            'password' => Hash::make('Bdg_2025##'),
            'role' => 'FINANCE',
        ]);

        User::create([
            'name' => 'Produksi Admin',
            'email' => 'produksi@halzpo.com',
            'password' => Hash::make('Bdg_2025##'),
            'role' => 'PRODUKSI',
        ]);

        User::create([
            'name' => 'Shipper Admin',
            'email' => 'shipper@halzpo.com',
            'password' => Hash::make('Bdg_2025##'),
            'role' => 'SHIPPER',
        ]);
    }
}
