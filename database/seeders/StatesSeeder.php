<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 
            'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 
            'Ekiti', 'Enugu', 'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 
            'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 'Niger', 
            'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 
            'Taraba', 'Yobe', 'Zamfara', 'Federal Capital Territory'
        ];

        foreach ($states as $state) {
            DB::table('states')->insert([
                'name' => $state,
                'slug' => Str::slug($state),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
