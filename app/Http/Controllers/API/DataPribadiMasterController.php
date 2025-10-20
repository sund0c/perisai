<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DataPribadiMaster;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DataPribadiMasterController extends Controller
{
    /**
     * Get all kode from data_pribadi_master
     *
     * @return JsonResponse
     */
    public function getKode(): JsonResponse
    {
        try {
            $kodeList = DataPribadiMaster::select('kode')
                ->orderBy('kode', 'asc')
                ->pluck('kode');

            return response()->json([
                'success' => true,
                'message' => 'Data kode berhasil diambil',
                'data' => $kodeList
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kode',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all data with kode and id only
     *
     * @return JsonResponse
     */
    public function getKodeWithId(): JsonResponse
    {
        try {
            $data = DataPribadiMaster::select('id', 'kode')
                ->orderBy('kode', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kode dengan ID berhasil diambil',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kode dengan ID',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all data with complete information
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $data = DataPribadiMaster::select('id', 'tipe', 'kode', 'deskripsi')
                ->orderBy('tipe', 'asc')
                ->orderBy('kode', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pribadi master berhasil diambil',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pribadi master',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
