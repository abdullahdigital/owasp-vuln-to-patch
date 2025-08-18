<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'name' => 'Dental Check-up',
            'description' => 'Comprehensive dental examination and cleaning.',
            'price' => 75.00,
        ]);

        Service::create([
            'name' => 'Teeth Whitening',
            'description' => 'Professional teeth whitening for a brighter smile.',
            'price' => 250.00,
        ]);

        Service::create([
            'name' => 'Root Canal Therapy',
            'description' => 'Treatment for infected tooth pulp.',
            'price' => 800.00,
        ]);

        Service::create([
            'name' => 'Orthodontic Consultation',
            'description' => 'Initial consultation for braces or aligners.',
            'price' => 50.00,
        ]);
    }
}
