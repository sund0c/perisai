<?php

namespace App\Http\Controllers;

use App\Models\PtkkaStatusLog;
use Illuminate\Support\Str;
use App\Models\Aset;
use App\Models\PtkkaSession;
use Illuminate\Http\Request;
use App\Models\FungsiStandar;
use App\Models\StandarIndikator;
use App\Models\PtkkaJawaban;
use Barryvdh\DomPDF\Facade\Pdf;

class PtkkaController extends Controller
{
    public function indexPtkka()
    {
        $opdId = auth()->user()->opd_id;

        $asets = Aset::where('opd_id', $opdId)
            ->with(['ptkkaTerakhir.jawabans'])
            ->get();

        return view('opd.ptkka.index', compact('asets'));
    }
    public function riwayat(Aset $aset)
    {
        // batasi hanya ke OPD yang sedang login
        if ($aset->opd_id !== auth()->user()->opd_id) {
            abort(403);
        }

        $riwayat = $aset->ptkkaSessions()->withCount(['jawabans'])->latest()->get();

        return view('opd.ptkka.riwayat', compact('aset', 'riwayat'));
    }


    public function store(Aset $aset, Request $request)
    {
        if ($aset->opd_id !== auth()->user()->opd_id) {
            abort(403);
        }

        $masihAktif = $aset->ptkkaSessions()->whereIn('status', [0, 1, 2, 3])->exists();


        if ($masihAktif) {
            return back()->with('error', 'Anda masih punya PTKKA yang masih berlangsung untuk aset ini.');
        }

        $kategoriId = $request->standar_kategori_id;

        if (!in_array($kategoriId, [2, 3])) {
            return back()->with('error', 'Kategori standar tidak valid.');
        }

        $session = PtkkaSession::create([
            'user_id' => auth()->id(),
            'aset_id' => $aset->id,
            'standar_kategori_id' => $kategoriId,
            'status' => 0,
            'uid' => Str::uuid(),
        ]);

        PtkkaStatusLog::create([
            'ptkka_session_id' => $session->id,
            'from_status' => null,
            'to_status' => 0,
            'user_id' => auth()->id(),
            'catatan' => 'Membuat sesi PTKKA',
            'changed_at' => now(),
        ]);

        return redirect()->route('ptkka.riwayat', $aset->id)->with('success', 'Form PTKKA berhasil dibuat.');
    }


    public function destroy(PtkkaSession $session)
    {
        // Pastikan hanya OPD pemilik yang bisa hapus
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // Hanya bisa hapus saat status masih PENGISIAN (0)
        if ($session->status !== 0) {
            return back()->with('error', 'Hanya PTKKA yang berstatus Pengisian yang dapat dihapus.');
        }

        $session->delete(); // akan menghapus juga status_logs karena onDelete('cascade')

        return back()->with('success', 'Pengajuan PTKKA berhasil dihapus.');
    }


    public function ajukan(PtkkaSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        if ($session->status !== 0) {
            return back()->with('error', 'Status tidak dapat diajukan lagi.');
        }

        // Simpan log status sebelum update
        PtkkaStatusLog::create([
            'ptkka_session_id' => $session->id,
            'from_status' => 0,
            'to_status' => 1,
            'user_id' => auth()->id(),
            'catatan' => 'Pengajuan awal oleh OPD',
            'changed_at' => now(),
        ]);

        // Update status jadi Pengajuan
        $session->update(['status' => 1]);

        return back()->with('success', 'PTKKA berhasil diajukan ke Diskominfos.');
    }

    public function showDetail($id)
    {
        $session = PtkkaSession::with('kategori')->findOrFail($id);
        $kategoriId = $session->standar_kategori_id;
        $fungsiStandars = FungsiStandar::with([
            'indikators.rekomendasis.jawabans'
        ])
            ->where('kategori_id', $session->standar_kategori_id)
            ->orderBy('urutan')
            ->get();


        $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)
            ->get()
            ->keyBy('standar_indikator_id'); // agar mudah diakses di view
        return view('opd.ptkka.detail', compact('session', 'fungsiStandars', 'jawabans'));
    }

    public function simpan(Request $request, $id)
    {
        $session = PtkkaSession::findOrFail($id);

        $jawabans = $request->input('jawaban', []);
        $penjelasanOpd = $request->input('penjelasanopd', []);
        $linkBukti = $request->input('linkbuktidukung', []);

        foreach ($jawabans as $rekomendasiId => $jawabanNilai) {
            $penjelasan = $penjelasanOpd[$rekomendasiId] ?? null;
            $link = $linkBukti[$rekomendasiId] ?? null;

            // Validasi: jika penjelasan atau link kosong
            if (empty($penjelasan) || empty($link)) {
                return back()->with('error', 'Semua jawaban harus disertai penjelasan dan link bukti dukung.');
            }

            \App\Models\PtkkaJawaban::updateOrCreate(
                [
                    'ptkka_session_id' => $session->id,
                    'rekomendasi_standard_id' => $rekomendasiId,
                ],
                [
                    'jawaban' => $jawabanNilai,
                    'penjelasanopd' => $penjelasan,
                    'linkbuktidukung' => $link,
                ]
            );
        }

        return redirect()->back()->with('success', 'Jawaban berhasil disimpan.');
    }

    public function simpanPerFungsi(Request $request, $sessionId, $fungsiId)
    {
        $session = PtkkaSession::findOrFail($sessionId);

        $jawabans = $request->input('jawaban', []);
        $penjelasanOpd = $request->input('penjelasanopd', []);
        $linkBukti = $request->input('linkbuktidukung', []);

        foreach ($jawabans as $rekomendasiId => $jawabanNilai) {
            $penjelasan = $penjelasanOpd[$rekomendasiId] ?? null;
            $link = $linkBukti[$rekomendasiId] ?? null;

            if (empty($penjelasan) || empty($link)) {
                return back()->with('error', 'Semua jawaban harus disertai Penjelasan dan Link Bukti Dukung.');
            }

            \App\Models\PtkkaJawaban::updateOrCreate(
                [
                    'ptkka_session_id' => $session->id,
                    'rekomendasi_standard_id' => $rekomendasiId,
                ],
                [
                    'jawaban' => $jawabanNilai,
                    'penjelasanopd' => $penjelasan,
                    'linkbuktidukung' => $link,
                ]
            );
        }

        return back()->with('success', 'Self-Asssessment PTKKA Berhasil Diupdate');
    }

    public function ajukanVerifikasi(Request $request, $sessionId)
    {
        $session = PtkkaSession::findOrFail($sessionId);

        // Ambil semua rekomendasi dari fungsi standar session ini
        $fungsiStandars = FungsiStandar::with('indikators.rekomendasis')->where('kategori_id', $session->standar_kategori_id)->get();

        $incomplete = [];

        foreach ($fungsiStandars as $fungsi) {
            foreach ($fungsi->indikators as $indikator) {
                foreach ($indikator->rekomendasis as $rek) {
                    $jawaban = PtkkaJawaban::where('ptkka_session_id', $session->id)
                        ->where('rekomendasi_standard_id', $rek->id)
                        ->first();

                    if (!$jawaban || empty($jawaban->penjelasanopd) || empty($jawaban->linkbuktidukung)) {
                        $incomplete[] = $rek->id;
                    }
                }
            }
        }

        if (count($incomplete) > 0) {
            return back()->with('error', 'Masih ada isian yang belum lengkap. Silakan lengkapi semua jawaban sebelum mengajukan verifikasi.');
        }

        // Ubah status session
        $oldStatus = $session->status;
        $session->status = 1; // 1 = Pengajuan
        $session->save();

        // Tambahkan ke status log
        \App\Models\PtkkaStatusLog::create([
            'ptkka_session_id' => $session->id,
            'from_status' => $oldStatus,
            'to_status' => 1,
            'user_id' => auth()->id(),
            'catatan' => 'Mengajukan verifikasi oleh OPD',
            'changed_at' => now(),
        ]);

        return redirect()->route('ptkka.riwayat', $session->aset_id)
            ->with('success', 'Pengajuan verifikasi berhasil dikirim. Hubungi Dinas Kominfos Prov Bali untuk jadwal Verifikasi.');
    }


    public function simpanJawaban(Request $request)
    {
        $data = $request->validate([
            'ptkka_session_id' => 'required|exists:ptkka_sessions,id',
            'standar_indikator_id' => 'required|exists:standar_indikator,id',
            'jawaban' => 'required|in:0,1,2',
            'rekomendasi' => 'nullable|string',
        ]);

        PtkkaJawaban::updateOrCreate(
            [
                'ptkka_session_id' => $data['ptkka_session_id'],
                'standar_indikator_id' => $data['standar_indikator_id'],
            ],
            [
                'jawaban' => $data['jawaban'],
                'rekomendasi' => $data['rekomendasi'],
            ]
        );

        return back()->with('success', 'Jawaban berhasil disimpan.');
    }

    public function exportPDF($id)
    {
        // $session = PtkkaSession::with('kategori', 'aset')->findOrFail($id);
        $session = PtkkaSession::with(['kategori', 'aset.opd'])->findOrFail($id);


        $fungsiStandars = FungsiStandar::with([
            'indikators.rekomendasis' // ini aman kalau rekomendasis tidak punya relasi aneh
        ])
            ->where('kategori_id', $session->standar_kategori_id)
            ->orderBy('urutan')
            ->get();



        $jawabans = PtkkaJawaban::where('ptkka_session_id', $session->id)
            ->get()
            ->keyBy('rekomendasi_standard_id');

        $jumlahJawaban = $jawabans->count();
        $skorMaksimal = $jumlahJawaban * 2;
        $totalSkor = $jawabans->sum('jawaban');

        $kategoriKepatuhan = 'TIDAK TERDEFINISI';
        if ($skorMaksimal > 0) {
            $persentase = ($totalSkor / $skorMaksimal) * 100;

            if ($persentase >= 66.7) {
                $kategoriKepatuhan = 'TINGGI';
            } elseif ($persentase >= 33.4) {
                $kategoriKepatuhan = 'SEDANG';
            } else {
                $kategoriKepatuhan = 'RENDAH';
            }
        }

        // Load PDF View
        $pdf = PDF::loadView('opd.ptkka.export_pdf', compact(
            'session',
            'fungsiStandars',
            'jawabans',
            'jumlahJawaban',
            'skorMaksimal',
            'totalSkor',
            'kategoriKepatuhan',
            'persentase'
        ))
            ->setPaper([0, 0, 595.28, 841.89], 'portrait'); // A4 in points

        // Call render first to make sure page count is available
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();

        // Footer and page script
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $text = "PERISAI :: Halaman $pageNumber dari $pageCount";
            $font = $fontMetrics->getFont('Helvetica', 'normal');
            $size = 9;
            $width = $canvas->get_width();
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
            $x = ($width - $textWidth) / 2;
            $y = 820; // posisi bawah halaman A4
            $canvas->text($x, $y, $text, $font, $size);
        });

        return $pdf->download('ptkka-' . $session->uid . '.pdf');
    }
}
