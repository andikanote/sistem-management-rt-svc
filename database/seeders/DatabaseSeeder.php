<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::create([
            'name' => 'Pak RT (Admin)',
            'email' => 'admin@rt.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'no_hp' => '081234567890',
        ]);

        // 2. Sekretaris
        User::create([
            'name' => 'Bu Sekretaris',
            'email' => 'sekretaris@rt.com',
            'password' => Hash::make('password'),
            'role' => 'sekretaris',
            'no_hp' => '081234567891',
        ]);

        // 3. Warga (Buat 5 dummy warga)
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Warga $i",
                'email' => "warga$i@rt.com",
                'password' => Hash::make('password'),
                'role' => 'warga',
                'no_rumah' => "A-$i",
                'no_hp' => '08120000000'.$i,
                'alamat' => 'Jl. Mawar No. '.$i,
            ]);
        }
    }
}
