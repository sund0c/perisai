<?php

// AUTH START
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
// AUTH END

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SkController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\Admin\UserRoleController;

use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RangeSeController;
use App\Http\Controllers\Opd\AsetController;
use App\Http\Middleware\SSOBrokerMiddleware;

use App\Http\Controllers\Opd\PtkkaController;
use App\Http\Controllers\RangeAsetController;
use App\Http\Controllers\KlasifikasiAsetController;

//BIDANG
use App\Http\Controllers\SSOBrokerController;
use App\Http\Controllers\Bidang\BidangKategoriSeController;
use App\Http\Controllers\Bidang\BidangVitalitasSeController;
use App\Http\Controllers\Bidang\BidangAsetController;
use App\Http\Controllers\Bidang\BidangPtkkaController;
use App\Http\Controllers\Bidang\BidangOpdController;

//OPD
use App\Http\Controllers\Opd\KategoriSeController;
use App\Http\Controllers\Opd\VitalitasSeController;
use App\Http\Controllers\SubKlasifikasiAsetController;
use App\Http\Controllers\IndikatorKategoriSeController;


// FOR TESTING PURPOSES
use App\Http\Controllers\ExampleController;

// Auth Routes Start

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('SSOBrokerMiddleware', 'spatie_role_or_permission:admin|opd|bidang', 'prevent-back-history')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Obsolete: moved into GET logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Auth Routes End

// SSO
Route::get('authenticateToSSO', 'App\Http\Controllers\SSOBrokerController@authenticateToSSO');
Route::get('authData/{authData}', 'App\Http\Controllers\SSOBrokerController@authenticateToSSO');
Route::get('exit/{sessionId}', 'App\Http\Controllers\SSOBrokerController@logout')->name('exit');
Route::get('logout', 'App\Http\Controllers\SSOBrokerController@logout')->name('logout.get');

Route::middleware(['SSOBrokerMiddleware', 'spatie_role_or_permission:admin|opd|bidang', 'prevent-back-history'])->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    Route::get('/dashboard', function () {

        $role = auth()->user()->getRoleNames()->first() ?? 'guest';
        $namaOpd = auth()->user()->opd->namaopd ?? '-';
        return match ($role) {
            'opd' => view('opd.dashboard', compact('namaOpd')),
            'admin' => view('admin.dashboard'),
            'bidang' => view('bidang.dashboard'),
            default => abort(403)
        };
    })->name('dashboard');
});

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:admin'])->group(function () {

    Route::prefix('opd')->group(function () {
        Route::get('/', [OpdController::class, 'index'])->name('opd.index');
        Route::resource('opd', OpdController::class);
    });

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
        Route::get('/export/pdf', [IndikatorKategoriSeController::class, 'exportPDF'])->name('indikatorkategorise.export.pdf');
    });

    Route::prefix('indikatorkategorise')->name('admin.indikatorkategorise.')->group(function () {
        Route::resource('/', IndikatorKategoriSeController::class)->parameters(['' => 'indikatorkategorise']);
    });
});


Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:admin|bidang'])->group(function () {
    Route::resource('periodes', PeriodeController::class);
    Route::post('periodes/{periode}/activate', [PeriodeController::class, 'activate'])
        ->name('periodes.activate');
});

//============= Start of BIDANG ===========================

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:bidang|admin'])->prefix('bidang/opd')->name('bidang.opd.')->group(function () {
    Route::get('/', [BidangOpdController::class, 'index'])->name('index');
    Route::get('/{opd:id}/view', [BidangOpdController::class, 'view'])->name('view');
    Route::get('/{id}/pdf', [BidangOpdController::class, 'pdf'])->name('pdf');
    Route::get('/{aset:uuid}/edit', [BidangOpdController::class, 'edit'])->name('edit');
    Route::put('/{aset:uuid}', [BidangOpdController::class, 'update'])->name('update');
});

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:bidang|admin'])->prefix('bidang/aset')->name('bidang.aset.')->group(function () {
    Route::get('/', [BidangAsetController::class, 'index'])->name('index');
    Route::get('/export/rekap', [BidangAsetController::class, 'exportRekapPdf'])->name('export_rekap');
    Route::get('/klasifikasi/{id}', [BidangAsetController::class, 'showByKlasifikasi'])->name('show_by_klasifikasi');
    Route::get('/export/rekapklas/{id}', [BidangAsetController::class, 'exportRekapKlasPdf'])->name('export_rekap_klas');
    Route::get('/{aset:uuid}/edit', [BidangAsetController::class, 'edit'])->name('edit');
    Route::put('/{aset:uuid}', [BidangAsetController::class, 'update'])->name('update');
    Route::get('/{id}/pdf', [BidangAsetController::class, 'pdf'])->name('pdf');
});

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:bidang|admin'])->prefix('bidang/kategorise')->name('bidang.kategorise.')->group(function () {
    Route::get('/export/rekap/{kategori}', [BidangKategoriSeController::class, 'exportRekapKategoriPdf'])->name('export_rekap_kategori');
    Route::get('/', [BidangKategoriSeController::class, 'index'])->name('index');
    Route::get('/kategori/{kategori}', [BidangKategoriSeController::class, 'show'])->name('show');
    Route::get('/export/rekap', [BidangKategoriSeController::class, 'exportRekapPdf'])->name('export_rekap');
    Route::get('/export/pdf/{id}', [BidangKategoriSeController::class, 'exportPdf'])->name('exportPdf');
    Route::delete('/{id}', [BidangKategoriSeController::class, 'destroy'])->name('destroy');
});

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:bidang|admin'])->prefix('bidang/vitalitasse')->name('bidang.vitalitasse.')->group(function () {
    Route::get('/export/rekap/{kategori}', [BidangVitalitasSEController::class, 'exportRekapKategoriPdf'])->name('export_rekap_kategori');
    Route::get('/', [BidangVitalitasSEController::class, 'index'])->name('index');
    Route::get('/kategori/{kategori}', [BidangVitalitasSEController::class, 'show'])->name('show');
    Route::get('/export/rekap', [BidangVitalitasSEController::class, 'exportRekapPdf'])->name('export_rekap');
    Route::get('/export/pdf/{id}', [BidangVitalitasSEController::class, 'exportPdf'])->name('exportPdf');
});

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:bidang|admin'])->prefix('bidang/ptkka')->name('bidang.ptkka.')->group(function () {
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

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:opd|bidang'])->prefix('bidang/ptkka')->name('bidang.ptkka.')->group(function () {
    Route::get('/export/pdf/{id}', [BidangPtkkaController::class, 'exportPDF'])->name('exportPDF');
});

//============= END of BIDANG ===========================

Route::middleware(['SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:admin'])
    ->prefix('admin/users')
    ->name('admin.users.')
    ->group(function () {
        Route::get('/', [UserRoleController::class, 'index'])->name('index');
        Route::get('/{user}/edit', [UserRoleController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserRoleController::class, 'update'])->name('update');
    });

//============= Start of OPD ===========================
// UPDATED: hide ptkka
// Route::middleware(['SSOBrokerMiddleware', 'spatie_role_or_permission:opd', 'prevent-back-history'])
//     ->prefix('opd/ptkka')
//     ->name('opd.ptkka.')
//     ->group(function () {
//         Route::post('/{session}/fungsi/{fungsi}/simpan', [PtkkaController::class, 'simpanPerFungsi'])->name('simpanPerFungsi');
//         Route::get('/export/pdf/{session:uid}', [PtkkaController::class, 'exportPDF'])
//             ->name('exportPDF');
//         Route::get('/', [PtkkaController::class, 'indexPtkka'])->name('index');
//         Route::get('/riwayat/{aset:uuid}', [PtkkaController::class, 'riwayat'])
//             ->name('riwayat');
//         Route::post('/{aset:uuid}/store', [PtkkaController::class, 'store'])->name('store');
//         Route::get('/{session:uid}/detail', [PtkkaController::class, 'showDetail'])->name('detail');
//         Route::delete('/{session:uid}', [PtkkaController::class, 'destroy'])->name('destroy');
//         Route::post('/jawaban', [PtkkaController::class, 'simpanJawaban'])->name('jawaban.simpan');
//         Route::post('/{session:uid}/simpan', [PtkkaController::class, 'simpan'])->name('simpan');


//         // Route::post('/{id}/simpan', [PtkkaController::class, 'simpan'])->name('simpan');

//         Route::post('/{session}/ajukan-verifikasi', [PtkkaController::class, 'ajukanVerifikasi'])->name('ajukanverifikasi');
//     });

Route::middleware(['SSOBrokerMiddleware', 'spatie_role_or_permission:opd', 'prevent-back-history'])
    ->prefix('opd/aset')
    ->name('opd.aset.')
    ->group(function () {
        Route::get('/', [AsetController::class, 'index'])->name('index');
        Route::get('/export/rekap', [AsetController::class, 'exportRekapPdf'])->name('export_rekap');
        Route::get('/export/rekapklas/{klasifikasiaset}', [AsetController::class, 'exportRekapKlasPdf'])
            ->name('export_rekap_klas');
        Route::get('/klasifikasi/{klasifikasiaset}', [AsetController::class, 'showByKlasifikasi'])
            ->name('show_by_klasifikasi');
        Route::get('/export/klasifikasi/{klasifikasiaset}', [AsetController::class, 'exportKlasifikasiPdf'])
            ->name('export_klasifikasi');
        Route::get('/klasifikasi/{klasifikasiaset}/create', [AsetController::class, 'create'])
            ->name('create');
        Route::post('/klasifikasi/{klasifikasiaset}', [AsetController::class, 'store'])
            ->name('store');
        Route::get('/{aset:uuid}/pdf', [AsetController::class, 'pdf'])
            ->middleware('can:view,aset')
            ->name('pdf');
        Route::get('/{aset:uuid}/edit', [AsetController::class, 'edit'])
            ->middleware('can:update,aset')
            ->name('edit');
        Route::put('/{aset:uuid}', [AsetController::class, 'update'])
            ->middleware('can:update,aset')
            ->name('update');
        Route::delete('/{aset:uuid}', [AsetController::class, 'destroy'])
            ->middleware('can:delete,aset')
            ->name('destroy');
    });

Route::middleware(['SSOBrokerMiddleware', 'spatie_role_or_permission:opd', 'prevent-back-history'])
    ->prefix('opd/kategorise')
    ->name('opd.kategorise.')
    ->group(function () {
        Route::get('/', [KategoriSeController::class, 'index'])->name('index');
        Route::get('/export/rekap', [KategoriSeController::class, 'exportRekapPdf'])->name('export_rekap');
        Route::get('/kategori/{kategori}', [KategoriSeController::class, 'showByKategori'])
            ->whereIn('kategori', ['strategis', 'tinggi', 'rendah', 'belum', 'total'])
            ->name('show_by_kategori');
        Route::get('/export/rekap/{kategori}', [KategoriSeController::class, 'exportRekapKategoriPdf'])
            ->whereIn('kategori', ['strategis', 'tinggi', 'rendah', 'belum', 'total'])
            ->name('export_rekap_kategori');
        Route::get('/export/pdf/{aset:uuid}', [KategoriSeController::class, 'exportPdf'])
            ->name('exportPdf');
        Route::get('/{aset}/edit', [KategoriSeController::class, 'edit'])
            ->where('aset', '[0-9a-fA-F-]+')
            ->name('edit');
        Route::put('/{aset}', [KategoriSeController::class, 'update'])
            ->where('aset', '[0-9a-fA-F-]+')
            ->name('update');
        Route::post('/sync-previous', [KategoriSeController::class, 'syncFromPrevious'])->name('sync_previous');
    });

Route::middleware(['SSOBrokerMiddleware', 'spatie_role_or_permission:opd', 'prevent-back-history'])
    ->prefix('opd/vitalitasse')
    ->name('opd.vitalitasse.')
    ->group(function () {
        Route::get('/', [VitalitasSeController::class, 'index'])->name('index');
        Route::get('/export/rekap', [VitalitasSeController::class, 'exportRekapPdf'])->name('export_rekap');
        Route::get('/kategori/{kategori}', [VitalitasSeController::class, 'showByKategori'])
            ->whereIn('kategori', ['vital', 'novital', 'belum', 'total'])
            ->name('show_by_kategori');
        Route::get('/export/rekap/{kategori}', [VitalitasSeController::class, 'exportRekapKategoriPdf'])
            ->whereIn('kategori', ['vital', 'novital', 'belum', 'total'])
            ->name('export_rekap_kategori');
        Route::get('/export/pdf/{aset:uuid}', [VitalitasSeController::class, 'exportPdf'])
            ->name('exportPdf');
        Route::get('/{aset}/edit', [VitalitasSeController::class, 'edit'])
            ->where('aset', '[0-9a-fA-F-]+')
            ->name('edit');
        Route::put('/{aset}', [VitalitasSeController::class, 'update'])
            ->where('aset', '[0-9a-fA-F-]+')
            ->name('update');
        Route::post('/sync-previous', [VitalitasSeController::class, 'syncFromPrevious'])->name('sync_previous');
    });

//============= End of OPD ===========================

Route::middleware('SSOBrokerMiddleware', 'prevent-back-history', 'spatie_role_or_permission:admin|opd|bidang')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// require __DIR__ . '/auth.php';
Route::fallback(function () {
    abort(403);
});
