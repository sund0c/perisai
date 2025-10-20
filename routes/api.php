<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DataPribadiMasterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Data Pribadi Master API Routes
Route::prefix('data-pribadi-master')->group(function () {
    // Get only kode values
    Route::get('/kode', [DataPribadiMasterController::class, 'getKode']);
    
    // Get kode with id
    Route::get('/kode-with-id', [DataPribadiMasterController::class, 'getKodeWithId']);
    
    // Get all data
    Route::get('/', [DataPribadiMasterController::class, 'index']);
});