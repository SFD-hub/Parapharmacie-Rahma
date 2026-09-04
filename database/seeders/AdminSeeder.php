<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's first administrator.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'Proprietaire@demo.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Rahmane#2026Vps'),
            ]
        );
    }
}
