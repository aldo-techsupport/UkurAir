<?php

namespace Database\Seeders;

use App\Models\WaterLevel;
use Illuminate\Database\Seeder;

class WaterLevelSeeder extends Seeder
{
    public function run(): void
    {
        $modes = ['AUTO', 'MANUAL'];
        $now = now();

        for ($i = 50; $i >= 1; $i--) {
            $tinggi = rand(15, 95);
            WaterLevel::create([
                'device_id' => '001',
                'tinggi'    => $tinggi,
                'status'    => WaterLevel::hitungStatus($tinggi),
                'relay'     => (bool) rand(0, 1),
                'mode'      => $modes[array_rand($modes)],
                'created_at' => $now->copy()->subSeconds($i * 5),
                'updated_at' => $now->copy()->subSeconds($i * 5),
            ]);
        }
    }
}
