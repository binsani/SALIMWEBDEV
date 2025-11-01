<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Nigerian E-Commerce'],
            ['key' => 'contact_email', 'value' => 'support@example.com'],
            ['key' => 'contact_phone', 'value' => '08012345678'],
            ['key' => 'delivery_fee_lagos', 'value' => '2000'],
            ['key' => 'delivery_fee_nationwide', 'value' => '4000'],
            ['key' => 'delivery_fee_pickup', 'value' => '0'],
            ['key' => 'low_stock_threshold', 'value' => '5'],
            ['key' => 'logo_path', 'value' => null],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
