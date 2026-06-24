<?php

namespace App\Http\Controllers;

use App\Models\WaterLevel;
use Illuminate\Http\Request;

class WaterLevelController extends Controller
{
    public function index()
    {
        $latest = WaterLevel::latest()->first();
        $history = WaterLevel::latest()->limit(50)->get()->reverse();

        return view('pages.monitoring.dashboard', compact('latest', 'history'));
    }

    public function riwayat()
    {
        $data = WaterLevel::latest()->paginate(20);
        return view('pages.monitoring.riwayat', compact('data'));
    }

    public function apiLatest()
    {
        $latest = WaterLevel::latest()->first();

        if (!$latest) {
            return response()->json([
                'tinggi_air' => 0,
                'persen' => 0,
                'status' => 'Tidak Ada Data',
                'waktu' => '-',
            ]);
        }

        return response()->json([
            'tinggi_air' => $latest->tinggi_air,
            'persen' => WaterLevel::hitungPersen($latest->tinggi_air),
            'status' => $latest->status,
            'waktu' => $latest->updated_at->format('d M Y H:i:s'),
        ]);
    }

    public function apiHistory(Request $request)
    {
        $limit = $request->input('limit', 50);
        $data = WaterLevel::latest()->limit($limit)->get()->reverse();

        return response()->json($data->map(function ($item) {
            return [
                'id' => $item->id,
                'tinggi_air' => $item->tinggi_air,
                'persen' => WaterLevel::hitungPersen($item->tinggi_air),
                'status' => $item->status,
                'waktu' => $item->updated_at->format('H:i:s'),
                'waktu_full' => $item->updated_at->format('d M Y H:i:s'),
            ];
        }));
    }
}
