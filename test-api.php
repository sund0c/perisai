<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\DataPribadiMaster;

// Bootstrap Laravel tanpa HTTP request
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test query untuk mendapatkan kode saja
echo "Testing DataPribadiMaster API functionality:\n\n";

try {
    // Test 1: Get kode only
    echo "1. Get kode only:\n";
    $kodeList = DataPribadiMaster::select('kode')
        ->orderBy('kode', 'asc')
        ->pluck('kode');
    
    echo json_encode($kodeList, JSON_PRETTY_PRINT) . "\n\n";

    // Test 2: Get kode with id
    echo "2. Get kode with id:\n";
    $dataWithId = DataPribadiMaster::select('id', 'kode')
        ->orderBy('kode', 'asc')
        ->get();
    
    echo json_encode($dataWithId, JSON_PRETTY_PRINT) . "\n\n";

    // Test 3: Get all data
    echo "3. Get all data:\n";
    $allData = DataPribadiMaster::select('id', 'tipe', 'kode', 'deskripsi')
        ->orderBy('tipe', 'asc')
        ->orderBy('kode', 'asc')
        ->get();
    
    echo json_encode($allData, JSON_PRETTY_PRINT) . "\n\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}