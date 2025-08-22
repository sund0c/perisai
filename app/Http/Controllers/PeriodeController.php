<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    // Menampilkan semua periode
    public function index()
    {
        $periodes = Periode::orderBy('tahun', 'desc')->get();
        return view('admin.periodes.index', compact('periodes'));
    }

    // Tampilkan form tambah
    public function create()
    {
        return view('admin.periodes.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|digits:4|unique:periodes,tahun',
            'status' => 'required|in:open,closed',
            'kunci' => 'required|in:open,locked',
        ]);

        try {
            if ($request->status === 'open') {
                // Tutup semua periode lain
                Periode::query()->update(['status' => 'closed']);
            }

            Periode::create($request->all());

            return redirect()
                ->route('periodes.index')
                ->with('success', 'Periode berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Bisa juga pakai \Throwable kalau ingin lebih luas
            \Log::error('Gagal menyimpan Periode: ' . $e->getMessage());

            return redirect()
                ->route('periodes.index')
                ->with('error', 'Terjadi kesalahan saat menambahkan periode.');
        }
    }


    // Tampilkan form edit
    public function edit(Periode $periode)
    {
        return view('admin.periodes.edit', compact('periode'));
    }

    // Simpan update
    public function update(Request $request, Periode $periode)
    {
        $validated = $request->validate([
            'tahun'  => 'required|digits:4|unique:periodes,tahun,' . $periode->id,
            'status' => 'required|in:open,closed',
            'kunci'  => 'required|in:open,locked',
        ]);

        $currentYear = now('Asia/Makassar')->year;

        // Siapkan payload update (akan dioverride jika tahun lampau)
        $payload = [
            'tahun'  => (int) $validated['tahun'],
            'status' => $validated['status'],
            'kunci'  => $validated['kunci'],
        ];

        // Jika tahun lampau, paksa kunci=locked (apapun status/kunci yang diminta)
        if ($payload['tahun'] < $currentYear) {
            $payload['kunci'] = 'locked';
        }

        DB::transaction(function () use ($validated, $payload, $periode) {
            // Jika status dibuka, tutup & kunci semua periode lain
            if ($validated['status'] === 'open') {
                Periode::where('id', '!=', $periode->id)
                    ->update(['status' => 'closed', 'kunci' => 'locked']);
            }

            // Update periode yang diedit
            $periode->update($payload);
        });

        // Pesan flash yang jelas saat tahun lampau dipaksa locked
        if ($payload['tahun'] < $currentYear && $validated['kunci'] === 'open') {
            return redirect()
                ->route('periodes.index')
                ->with('warning', 'Periode tahun sebelumnya dibuka (status open), tetapi kunci tetap locked.');
        }

        return redirect()->route('periodes.index')->with('success', 'Periode berhasil diperbarui.');
    }


    // Hapus periode
    public function destroy(Periode $periode)
    {
        // $periode->delete();
        // return redirect()->route('admin.periodes.index')->with('success', 'Periode berhasil dihapus.');



        try {
            $periode->delete();
            $status = 'success';
            $pesan = 'Penghapusan Tahun Berhasil';
        } catch (\Illuminate\Database\QueryException $e) {
            // Biasanya error 1451 untuk foreign key constraint
            $status = 'error';
            $pesan = 'Penghapusan Tahun Gagal karena data sudah terpakai';
        } catch (\Throwable $e) {
            // Penanganan error lain
            $status = 'error';
            $pesan = 'Penghapusan Tahun Gagal karena data sudah terpakai';
        }

        return redirect()
            ->route('periodes.index')
            ->with($status, $pesan);
    }

    public function activate(Periode $periode)
    {
        $currentYear = now('Asia/Makassar')->year;

        DB::transaction(function () use ($periode, $currentYear) {
            // kunci & tutup semua selain yang dipilih
            Periode::where('id', '!=', $periode->id)
                ->update(['status' => 'closed', 'kunci' => 'locked']);

            // tentukan nilai kunci untuk periode terpilih
            $kunciTerpilih = ($periode->tahun < $currentYear) ? 'locked' : 'open';

            // jika tahun lampau dan kunci tetap locked, kita juga bisa kasih pesan nanti
            $periode->update([
                'status' => 'open',
                'kunci'  => $kunciTerpilih,
            ]);
        });

        // jika tahun lampau, tampilkan peringatan
        if ($periode->tahun < $currentYear) {
            return redirect()
                ->route('periodes.index')
                ->with('warning', 'Periode tahun sebelumnya dibuka untuk tampilan, tetapi kunci tetap terkunci (locked).');
        }

        return redirect()->route('periodes.index')->with('success', 'Periode diaktifkan.');
    }
}
