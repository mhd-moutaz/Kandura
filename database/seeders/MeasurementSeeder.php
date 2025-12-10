<?php

namespace Database\Seeders;



use Illuminate\Database\Seeder;
use App\Models\Measurement;

class MeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        foreach ($sizes as $size) {
            Measurement::create(['size' => $size]);
        }
    }
}
