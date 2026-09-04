<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Seed the demonstration brands.
     */
    public function run(): void
    {
        $brands = ['Bioderma', 'La Roche-Posay', 'SVR', 'Avène', 'CeraVe', 'Nuxe', 'Caudalie'];

        foreach ($brands as $name) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_active' => true,
                ]
            );
        }
    }
}
