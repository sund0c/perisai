<?php

namespace App\Http\Controllers\Bidang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Aset;
use App\Models\VitalitasSe;
use App\Models\IndikatorVitalitasSe;
use PDF;
use App\Services\PdfFooter;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\QueryException;

use App\Models\KlasifikasiAset;
use App\Models\Periode;
use Illuminate\Support\Facades\Log;

class BidangVitalitasSeController extends Controller
{

    // public function __construct()
    // {
    //     $this->authorizeResource(Aset::class, 'aset');
    // }


    public function index()
    {
        $userOpdId = auth()->user()->opd_id;
        $namaOpd = auth()->user()->opd->namaopd ?? '-';

        // Ambil periode aktif
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            // bebas: bisa redirect balik dengan flash message juga
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $asetPL = Aset::whereHas('subklasifikasiaset', function ($q) {
            $q->whereIn('subklasifikasiaset', [
                'Aplikasi berbasis Website',
                'Aplikasi berbasis Mobile',
                'Aplikasi berbasis Desktop',
            ]);
        })
            ->where('periode_id', $periodeAktifId)   // filter periode aktif
            ->with(['vitalitasSe', 'subklasifikasiaset']) // sekalian eager load subklas
            ->get();


        // Inisialisasi penghitung kategori
        $kategoriCount = [
            'VITAL' => 0,
            'Tidak Vital' => 0,
            'BELUM' => 0,
            'TOTAL' => $asetPL->count()
        ];



        foreach ($asetPL as $aset) {
            $skor = $aset->vitalitasSe->skor_total ?? null;

            if ($skor === null) {
                $kategoriCount['BELUM']++;
                continue;
            }

            $namaKategori = ($skor >= 15) ? 'VITAL' : 'Tidak Vital';
            $kategoriCount[$namaKategori] = ($kategoriCount[$namaKategori] ?? 0) + 1;
        }


        return view('bidang.vitalitasse.index', [
            'vital' => $kategoriCount['VITAL'] ?? 0,
            'novital' => $kategoriCount['Tidak Vital'] ?? 0,
            'belum' => $kategoriCount['BELUM'],
            'total' => $kategoriCount['TOTAL'],
            'namaOpd' => $namaOpd,
        ]);
    }

    public function exportRekapPdf()
    {
        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (!$periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }

        $data = Aset::query()
            ->select('asets.*', 'vitalitas_ses.skor_total')
            ->leftJoin('vitalitas_ses', 'vitalitas_ses.aset_id', '=', 'asets.id')
            ->where('asets.periode_id', $periodeAktifId)
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                    'Aplikasi berbasis Desktop',
                ]);
            })
            ->with([
                'vitalitasSe:id,aset_id,skor_total,jawaban',
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id',
                'opd:id,namaopd',
            ])
            ->orderByDesc('vitalitas_ses.skor_total')
            ->orderBy('asets.nama_aset', 'ASC')
            ->get();

        $namaOpd = 'SEMUA OPD';

        $pdf = PDF::loadView('bidang.vitalitasse.export_rekap_pdf', compact('data', 'namaOpd'))
            ->setPaper('A4', 'landscape');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('rekap_vitalitasse_' . date('Ymd_His') . '.pdf');
    }

    public function exportRekapKategoriPdf($kategori)
    {
        $allowed = ['vital', 'novital', 'belum']; // tambah 'total' kalau perlu
        if (! in_array(strtolower($kategori), $allowed, true)) {
            abort(404);
        }

        $user = auth()->user();
        $userOpdId = $user->opd_id;
        $namaOpd   = $user->opd->namaopd ?? '-';

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (! $periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }
        $query = Aset::query()
            ->select('asets.*', 'vitalitas_ses.skor_total')
            ->leftJoin('vitalitas_ses', 'vitalitas_ses.aset_id', '=', 'asets.id')
            ->where('asets.periode_id', $periodeAktifId)
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                    'Aplikasi berbasis Desktop',
                ]);
            })
            ->with([
                'vitalitasSe:id,aset_id,skor_total,jawaban',
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id',
                'opd:id,namaopd',
            ]);

        // filter kategori
        if ($kategori === 'belum') {
            $query->doesntHave('vitalitasSe');
        } else {
            $query->whereHas('vitalitasSe', function ($q) use ($kategori) {
                $q->whereNotNull('skor_total');

                if ($kategori === 'vital') {
                    // Skor >= 15
                    $q->where('skor_total', '>=', 15);
                } else {
                    // Semua skor di bawah 15 dianggap Tidak Vital
                    $q->where('skor_total', '<', 15);
                }
            });
        }

        $data = $query
            ->orderByDesc('vitalitas_ses.skor_total')
            ->orderBy('asets.nama_aset')
            ->get();

        $pdf = PDF::loadView('bidang.vitalitasse.export_rekap_kategori_pdf', compact('data', 'kategori', 'namaOpd'))
            ->setPaper('A4', 'landscape');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->stream('vitalitasse_pernilai_' . date('Ymd_His') . '.pdf');
    }

    public function show($kategori)
    {
        $namaOpd = 'SEMUA OPD';
        $allowed = ['vital', 'novital', 'belum']; // tambah 'total' kalau perlu
        if (! in_array(strtolower($kategori), $allowed, true)) {
            abort(404);
        }

        $periodeAktifId = Periode::where('status', 'open')->value('id');
        if (! $periodeAktifId) {
            abort(409, 'Tidak ada periode yang berstatus open.');
        }
        $query = Aset::query()
            ->where('periode_id', $periodeAktifId)
            ->whereHas('subklasifikasiaset', function ($q) {
                $q->whereIn('subklasifikasiaset', [
                    'Aplikasi berbasis Website',
                    'Aplikasi berbasis Mobile',
                    'Aplikasi berbasis Desktop',
                ]);
            })
            ->with([
                'vitalitasSe:id,aset_id,skor_total,jawaban',
                'subklasifikasiaset:id,subklasifikasiaset,klasifikasi_aset_id',
                'opd'
            ]);


        // filter kategori
        if ($kategori === 'belum') {
            $query->doesntHave('vitalitasSe');
        } else {
            $query->whereHas('vitalitasSe', function ($q) use ($kategori) {
                $q->whereNotNull('skor_total');

                if ($kategori === 'vital') {
                    // Skor >= 15
                    $q->where('skor_total', '>=', 15);
                } else {
                    // Semua skor di bawah 15 dianggap Tidak Vital
                    $q->where('skor_total', '<', 15);
                }
            });
        }


        $data = $query->orderBy('nama_aset')->get();

        return view('bidang.vitalitasse.list', compact('data', 'kategori', 'namaOpd'));
    }


    public function destroy($id)
    {
        $aset = Aset::with('vitalitasSe')->findOrFail($id);

        if (!$aset->vitalitasSe) {
            return back()->with('error', 'Tidak ada data vitalitas SE untuk aset ini.');
        }

        try {
            $aset->vitalitasSe->delete();

            return back()->with('success', 'Vitalitas SE berhasil dihapus.');
        } catch (QueryException $e) {
            Log::warning('Bidang gagal menghapus vitalitas SE', [
                'aset_id'    => $aset->id,
                'mysql_code' => $e->errorInfo[1] ?? null,
                'sql_state'  => $e->errorInfo[0] ?? null,
                'driver_msg' => $e->errorInfo[2] ?? null,
                'route'      => request()->path(),
            ]);

            return back()->with('error', 'Gagal menghapus vitalitas SE. Silakan coba lagi.');
        }
    }


    public function exportPdf($id)
    {
        $aset = \App\Models\Aset::with(['vitalitasSe', 'opd'])->findOrFail($id);
        $namaOpd = $aset->opd->namaopd;
        $vitalitasSe = $aset->vitalitasSe;
        $indikators = IndikatorVitalitasSe::orderBy('urutan')->get();
        //$skor = $vitalitasSe->skor_total ?? 0;

        $skorRaw = $vitalitasSe->skor_total ?? null;

        if (is_null($skorRaw)) {
            $kategoriLabel = 'BELUM DINILAI';
            $warna = 'transparent'; // tanpa warna background
            $warnatext = '#000'; // tanpa warna background
        } else {
            $skor = (int) $skorRaw;

            if ($skor >= 15) {
                $kategoriLabel = 'VITAL';
                $warna = '#dc3545'; // merah
                $warnatext = '#FFF';
            } else {
                $kategoriLabel = 'Tidak Vital';
                $warna = '#28a745'; // hijau
                $warnatext = '#FFF';
            }
        }

        // 7) Render PDF
        $pdf = PDF::loadView('opd.vitalitasse.pdf_detail', compact(
            'aset',
            'vitalitasSe',
            'indikators',
            'kategoriLabel',
            'warna',
            'warnatext',
            'skor',
            'namaOpd',
        ))
            ->setPaper('A4', 'potrait');
        PdfFooter::add_right_corner_footer($pdf);
        return $pdf->download('penilaianvitalitasse_' . date('Ymd_His') . '.pdf');
    }
}
