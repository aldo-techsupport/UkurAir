<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterLevel extends Model
{
    protected $table = 'tb_ketinggian_air';

    protected $guarded = ['id'];

    const TINGGI_TANDON = 100.0;

    public function getStatusAttribute($value)
    {
        return $value;
    }

    public function getPersenAttribute(): float
    {
        return round(($this->tinggi_air / self::TINGGI_TANDON) * 100, 1);
    }

    public static function hitungStatus(float $tinggi): string
    {
        $persen = ($tinggi / self::TINGGI_TANDON) * 100;
        if ($persen >= 80) return 'Penuh';
        if ($persen >= 30) return 'Sedang';
        return 'Rendah';
    }

    public static function hitungPersen(float $tinggi): float
    {
        return round(($tinggi / self::TINGGI_TANDON) * 100, 1);
    }
}
