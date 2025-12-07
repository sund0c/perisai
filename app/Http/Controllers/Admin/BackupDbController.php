<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class BackupDbController extends Controller
{
    private $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backup_db');
    }


    public function index()
    {
        $files = collect(File::files($this->backupPath))
            ->sortByDesc(fn($file) => $file->getCTime())
            ->take(5)
            ->map(function ($file) {
                return [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024, 2), // KB
                    'date' => date('Y-m-d H:i:s', $file->getCTime()),
                ];
            });

        return view('admin.backup.index', compact('files'));
    }

    public function download($filename)
    {
        // Hindari path traversal
        $filename = basename($filename);

        $filePath = $this->backupPath . '/' . $filename;

        if (! file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath);
    }
}
