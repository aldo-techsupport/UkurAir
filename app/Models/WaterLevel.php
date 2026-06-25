<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterLevel extends Model
{
    protected $table = 'tb_ketinggian_air';

    protected $guarded = ['id'];

    protected $casts = [
        'relay' => 'boolean',
    ];

    public static function hitungStatus(float $persen): string
    {
        if ($persen >= 80) return 'Penuh';
        if ($persen >= 30) return 'Sedang';
        return 'Rendah';
    }
}
