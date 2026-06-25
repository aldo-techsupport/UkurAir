<?php

namespace App\Http\Controllers;

use App\Models\WaterLevel;
use Illuminate\Http\Request;

class WaterLevelController extends Controller
{
    public function index()
    {
        $latest = WaterLevel::latest()->first();
        $history = WaterLevel::latest()->limit(50)->get();

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
                'device_id' => '-',
                'tinggi' => 0,
                'status' => 'Tidak Ada Data',
                'relay' => false,
                'mode' => '-',
                'waktu' => '-',
            ]);
        }

        return response()->json([
            'device_id' => $latest->device_id,
            'tinggi' => $latest->tinggi,
            'status' => $latest->status,
            'relay' => $latest->relay,
            'mode' => $latest->mode,
            'waktu' => $latest->updated_at->format('d M Y H:i:s'),
            'last_seen' => $latest->updated_at->toISOString(),
        ]);
    }

    public function apiHistory(Request $request)
    {
        $limit = $request->input('limit', 50);
        $data = WaterLevel::latest()->limit($limit)->get();

        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'id' => $item->id,
                'device_id' => $item->device_id,
                'tinggi' => $item->tinggi,
                'status' => $item->status,
                'relay' => $item->relay,
                'mode' => $item->mode,
                'waktu' => $item->updated_at->format('H:i:s'),
                'waktu_full' => $item->updated_at->format('d M Y H:i:s'),
            ];
        }

        return response()->json($result);
    }
}
