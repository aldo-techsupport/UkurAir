<?php

namespace Database\Seeders;

use App\Models\WaterLevel;
use Illuminate\Database\Seeder;

class WaterLevelSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Penuh', 'Sedang', 'Rendah'];
        $now = now();

        for ($i = 50; $i >= 1; $i--) {
            $tinggi = rand(1500, 9500) / 100;
            WaterLevel::create([
                'tinggi_air' => $tinggi,
                'status' => WaterLevel::hitungStatus($tinggi),
                'created_at' => $now->copy()->subSeconds($i * 5),
                'updated_at' => $now->copy()->subSeconds($i * 5),
            ]);
        }
    }
}
