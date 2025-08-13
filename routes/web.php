<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SkController;
use App\Http\Controllers\OpdController;

use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RangeSeController;
use App\Http\Controllers\Opd\AsetController;
use App\Http\Middleware\SSOBrokerMiddleware;

use App\Http\Controllers\Opd\PtkkaController;

use App\Http\Controllers\RangeAsetController;

//BIDANG
use App\Http\Controllers\SSOBrokerController;
use App\Http\Controllers\Opd\KategoriseController;
use App\Http\Controllers\KlasifikasiAsetController;

//OPD
use App\Http\Controllers\Bidang\BidangAsetController;
use App\Http\Controllers\Bidang\BidangPtkkaController;
use App\Http\Controllers\SubKlasifikasiAsetController;
use App\Http\Controllers\IndikatorKategoriSeController;
use App\Http\Controllers\Bidang\BidangKategoriSeController;






Route::get('/', function () {
    return redirect('/login');
});

// SSO
Route::get('authenticateToSSO', 'SSOBrokerController@authenticateToSSO');
Route::get('authData/{authData}', 'SSOBrokerController@authenticateToSSO');
Route::get('exit/{sessionId}', 'SSOBrokerController@logout')->name('exit');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return match ($role) {
            'opd' => view('opd.dashboard'),
            'admin' => view('admin.dashboard'),
            'bidang' => view('bidang.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');
});


Route::middleware(['auth', RoleMiddleware::class . ':admin', 'SSOBrokerMiddleware'])->group(function () {

    Route::prefix('sk')->group(function () {
        Route::get('/', [SkController::class, 'indexKategori'])->name('sk.index');
        Route::get('/create', [SkController::class, 'createKategori'])->name('sk.create');
        Route::post('/', [SkController::class, 'storeKategori'])->name('sk.store');
        Route::get('/{id}/edit', [SkController::class, 'editKategori'])->name('sk.edit');
        Route::put('/{id}', [SkController::class, 'updateKategori'])->name('sk.update');
        Route::delete('/{id}', [SkController::class, 'destroyKategori'])->name('sk.destroy');
        Route::get('/kategoripdf', [SkController::class, 'kategoriPDF'])->name('sk.kategoripdf');
        Route::get('/fungsistandarpdf/{id}', [SkController::class, 'fungsiPDF'])->name('sk.fungsistandarpdf');
        Route::get('/indikatorpdf/{id}', [SkController::class, 'indikatorPDF'])->name('sk.indikatorpdf');
        Route::get('/rekomendasipdf/{id}', [SkController::class, 'rekomendasiPDF'])->name('sk.rekomendasipdf');
        Route::get('/{kategori}/fungsi-standar', [SkController::class, 'indexFungsi'])->name('sk.fungsistandar.index');
        Route::get('/{kategori}/fungsi-standar/create', [SkController::class, 'createFungsi'])->name('sk.fungsistandar.create');
        Route::post('/{kategori}/fungsi-standar', [SkController::class, 'storeFungsi'])->name('sk.fungsistandar.store');
        Route::get('/fungsi-standar/{id}/edit', [SkController::class, 'editFungsi'])->name('sk.fungsistandar.edit');
        Route::put('/fungsi-standar/{id}', [SkController::class, 'updateFungsi'])->name('sk.fungsistandar.update');
        Route::delete('/fungsi-standar/{id}', [SkController::class, 'destroyFungsi'])->name('sk.fungsistandar.destroy');
        Route::get('/fungsi-standar/{fungsi}/indikator', [SkController::class, 'indexIndikator'])->name('sk.indikator.index');
        Route::get('/fungsi-standar/{fungsi}/indikator/create', [SkController::class, 'createIndikator'])->name('sk.indikator.create');
        Route::post('/fungsi-standar/{fungsi}/indikator', [SkController::class, 'storeIndikator'])->name('sk.indikator.store');
        Route::get('/indikator/{id}/edit', [SkController::class, 'editIndikator'])->name('sk.indikator.edit');
        Route::put('/indikator/{id}', [SkController::class, 'updateIndikator'])->name('sk.indikator.update');
        Route::delete('/indikator/{id}', [SkController::class, 'destroyIndikator'])->name('sk.indikator.destroy');
        Route::get('/indikator/{id}/rekomendasi', [SkController::class, 'indexRekomendasi'])->name('sk.rekomendasi.index');
        Route::get('/indikator/{id}/rekomendasi/create', [SkController::class, 'createRekomendasi'])->name('sk.rekomendasi.create');
        Route::post('/indikator/{id}/rekomendasi', [SkController::class, 'storeRekomendasi'])->name('sk.rekomendasi.store');
        Route::get('/rekomendasi/{id}/edit', [SkController::class, 'editRekomendasi'])->name('sk.rekomendasi.edit');
        Route::put('/rekomendasi/{id}', [SkController::class, 'updateRekomendasi'])->name('sk.rekomendasi.update');
        Route::delete('/rekomendasi/{id}', [SkController::class, 'destroyRekomendasi'])->name('sk.rekomendasi.destroy');
    });

    Route::prefix('opd')->group(function () {
        Route::get('/', [OpdController::class, 'index'])->name('opd.index');
        Route::resource('opd', OpdController::class);
    });

    // Route::prefix('klasifikasiaset')->group(function () {
    //     Route::resource('', KlasifikasiAsetController::class);
    //     Route::get('/{id}/sub', [SubKlasifikasiAsetController::class, 'index'])->name('subklasifikasiaset.index');
    //     Route::get('/{id}/sub/create', [SubKlasifikasiAsetController::class, 'create'])->name('subklasifikasiaset.create');
    //     Route::get('/export/pdf', [KlasifikasiAsetController::class, 'exportPDF'])->name('klasifikasiaset.export.pdf');
    //     Route::get('/{id}/field', [\App\Http\Controllers\KlasifikasiAsetController::class, 'aturField'])->name('klasifikasiaset.field');
    //     Route::post('/{id}/field', [\App\Http\Controllers\KlasifikasiAsetController::class, 'simpanField']);
    // });

    Route::resource('klasifikasiaset', KlasifikasiAsetController::class);
    Route::prefix('klasifikasiaset')->name('klasifikasiaset.')->group(function () {
        Route::get('export/pdf', [KlasifikasiAsetController::class, 'exportPDF'])->name('export.pdf');
        Route::get('{klasifikasiaset}/field', [KlasifikasiAsetController::class, 'aturField'])->name('field');
        Route::post('{klasifikasiaset}/field', [KlasifikasiAsetController::class, 'simpanField']);
        Route::get('{klasifikasiaset}/sub', [SubKlasifikasiAsetController::class, 'index'])->name('subklasifikaset.index');
        Route::get('{klasifikasiaset}/sub/create', [SubKlasifikasiAsetController::class, 'create'])->name('subklasifikasaset.create');
    });

    Route::prefix('subklasifikasiaset')->group(function () {
        Route::get('/', [SubKlasifikasiAsetController::class, 'index'])->name('subklasifikasiaset.index');
        Route::get('/{id}/edit', [SubKlasifikasiAsetController::class, 'edit'])->name('subklasifikasiaset.edit');
        Route::post('/', [SubKlasifikasiAsetController::class, 'store'])->name('subklasifikasiaset.store');
        Route::put('/{id}', [SubKlasifikasiAsetController::class, 'update'])->name('subklasifikasiaset.update');
        Route::delete('/{id}', [SubKlasifikasiAsetController::class, 'destroy'])->name('subklasifikasiaset.destroy');
        Route::get('/export/pdf/{id}', [SubKlasifikasiAsetController::class, 'exportPDF'])->name('subklasifikasiaset.export.pdf');
    });

    Route::resource('rangeaset', RangeAsetController::class);
    Route::get('/range_aset/export/pdf', [RangeAsetController::class, 'exportPDF'])->name('rangeaset.export.pdf');
    Route::resource('rangese', RangeSeController::class);
    Route::get('/range_se/export/pdf', [RangeSeController::class, 'exportPDF'])->name('rangese.export.pdf');

    // Route::prefix('periodes')->group(function () {
    //     Route::resource('', PeriodeController::class);
    //     Route::post('/{periode}/activate', [PeriodeController::class, 'activate'])->name('periodes.activate');
    // });





    Route::prefix('indikatorkategorise')->group(function () {
        Route::resource('/', IndikatorKategoriSeController::class)->names('admin.indikatorkategorise');
        Route::get('/export/pdf', [IndikatorKategoriSeController::class, 'exportPDF'])->name('indikatorkategorise.export.pdf');
    });
});


Route::middleware(['auth', 'role:admin,bidang', 'SSOBrokerMiddleware'])->group(function () {
    Route::resource('periodes', PeriodeController::class);
    Route::post('periodes/{periode}/activate', [PeriodeController::class, 'activate'])
        ->name('periodes.activate');
});


//============= Start of BIDANG ===========================

Route::middleware(['auth', 'role:bidang', 'SSOBrokerMiddleware'])->prefix('bidang/aset')->name('bidang.aset.')->group(function () {
    Route::get('/', [BidangAsetController::class, 'index'])->name('index');
    Route::get('/export/rekap', [BidangAsetController::class, 'exportRekapPdf'])->name('export_rekap');
    Route::get('/klasifikasi/{id}', [BidangAsetController::class, 'showByKlasifikasi'])->name('show_by_klasifikasi');
    Route::get('/export/rekapklas/{id}', [BidangAsetController::class, 'exportRekapKlasPdf'])->name('export_rekap_klas');
    Route::get('/{id}/pdf', [BidangAsetController::class, 'pdf'])->name('pdf');
});

Route::middleware(['auth', 'role:bidang', 'SSOBrokerMiddleware'])->prefix('bidang/kategorise')->name('bidang.kategorise.')->group(function () {
    Route::get('/export/rekap/{kategori}', [BidangKategoriSeController::class, 'exportRekapKategoriPdf'])->name('export_rekap_kategori');
    Route::get('/', [BidangKategoriSeController::class, 'index'])->name('index');
    Route::get('/kategori/{kategori}', [BidangKategoriSeController::class, 'show'])->name('show');
    Route::get('/export/rekap', [BidangKategoriSeController::class, 'exportRekapPdf'])->name('export_rekap');
    Route::get('/export/pdf/{id}', [BidangKategoriSeController::class, 'exportPdf'])->name('exportPdf');
});

Route::middleware(['auth', 'role:bidang', 'SSOBrokerMiddleware'])->prefix('bidang/ptkka')->name('bidang.ptkka.')->group(function () {
    Route::get('/', [BidangPtkkaController::class, 'indexPtkkaBidang'])->name('index');
    Route::post('/{session}/ajukan-verifikasi', [BidangPtkkaController::class, 'ajukanVerifikasi'])->name('ajukanverifikasi');
    Route::get('/export/pdfpengajuan', [BidangPtkkaController::class, 'pengajuanPDF'])->name('pengajuanPDF');
    Route::get('/export/pdfprogress', [BidangPtkkaController::class, 'progressPDF'])->name('progressPDF');
    Route::get('/export/pdfclosing', [BidangPtkkaController::class, 'closingPDF'])->name('closingPDF');
    Route::get('/{session}/detail', [BidangPtkkaController::class, 'showDetail'])->name('detail');
    Route::post('/{session}/fungsi/{fungsi}/simpan', [BidangPtkkaController::class, 'simpanCatatan'])->name('simpanCatatan');
    Route::post('/{session}/ajukan-klarifikasi', [BidangPtkkaController::class, 'ajukanKlarifikasi'])->name('ajukanklarifikasi');
    Route::post('/{session}/ajukan-closing', [BidangPtkkaController::class, 'ajukanClosing'])->name('ajukanclosing');
    Route::get('/riwayat/{aset}', [BidangPtkkaController::class, 'riwayat'])->name('riwayat');
});

Route::middleware(['auth', 'role:bidang,opd', 'SSOBrokerMiddleware'])->prefix('bidang/ptkka')->name('bidang.ptkka.')->group(function () {
    Route::get('/export/pdf/{id}', [BidangPtkkaController::class, 'exportPDF'])->name('exportPDF');
});

//============= END of BIDANG ===========================




//============= Start of OPD ===========================

Route::middleware(['auth', 'role:opd', 'SSOBrokerMiddleware'])->prefix('opd/ptkka')->name('opd.ptkka.')->group(function () {
    //Route::middleware(['auth', RoleMiddleware::class . ':opd'])->group(function () {

    //  Route::prefix('ptkka')->group(function () {
    Route::get('/', [PtkkaController::class, 'indexPtkka'])->name('index');
    Route::get('/riwayat/{aset}', [PtkkaController::class, 'riwayat'])->name('riwayat');
    Route::post('/{aset}/store', [PtkkaController::class, 'store'])->name('store');
    Route::delete('/{session}', [PtkkaController::class, 'destroy'])->name('destroy');
    Route::get('/{session}/detail', [PtkkaController::class, 'showDetail'])->name('detail');
    Route::post('/jawaban', [PtkkaController::class, 'simpanJawaban'])->name('jawaban.simpan');
    Route::post('/{id}/simpan', [PtkkaController::class, 'simpan'])->name('simpan');
    Route::post('/{session}/fungsi/{fungsi}/simpan', [PtkkaController::class, 'simpanPerFungsi'])->name('simpanPerFungsi');
    Route::post('/{session}/ajukan-verifikasi', [PtkkaController::class, 'ajukanVerifikasi'])->name('ajukanverifikasi');
    Route::get('/export/pdf/{id}', [PtkkaController::class, 'exportPDF'])->name('exportPDF');
});

Route::middleware(['auth', 'role:opd', 'SSOBrokerMiddleware'])->prefix('opd/aset')->name('opd.aset.')->group(function () {
    Route::get('/', [AsetController::class, 'index'])->name('index');
    Route::get('/export/rekap', [AsetController::class, 'exportRekapPdf'])->name('export_rekap');
    Route::get('/export/rekapklas/{id}', [AsetController::class, 'exportRekapKlasPdf'])->name('export_rekap_klas');
    Route::get('/klasifikasi/{id}', [AsetController::class, 'showByKlasifikasi'])->name('show_by_klasifikasi');
    Route::get('/export/klasifikasi/{id}', [AsetController::class, 'exportKlasifikasiPdf'])->name('export_klasifikasi');
    Route::get('/klasifikasi/{id}/create', [AsetController::class, 'create'])->name('create');
    Route::post('/klasifikasi/{id}', [AsetController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [AsetController::class, 'edit'])->name('edit');
    Route::get('/{id}/pdf', [AsetController::class, 'pdf'])->name('pdf');
    Route::put('/{id}', [AsetController::class, 'update'])->name('update');
    Route::delete('/{id}', [AsetController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'role:opd', 'SSOBrokerMiddleware'])->prefix('opd/kategorise')->name('opd.kategorise.')->group(function () {
    Route::get('/export/rekap/{kategori}', [KategoriSeController::class, 'exportRekapKategoriPdf'])->name('export_rekap_kategori');
    Route::get('/', [KategoriSeController::class, 'index'])->name('index');
    Route::get('/{aset}/edit', [KategoriSeController::class, 'edit'])->name('edit');
    Route::put('/{aset}', [KategoriSeController::class, 'update'])->name('update');
    Route::get('/export/pdf/{id}', [KategoriSeController::class, 'exportPdf'])->name('exportPdf');
    Route::get('/kategori/{kategori}', [KategoriSeController::class, 'show'])->name('show');
    Route::get('/export/rekap', [KategoriSeController::class, 'exportRekapPdf'])->name('export_rekap');
});

//============= End of OPD ===========================



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
