<?php

namespace App\Http\Controllers;

use App\Models\PublicationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileAccessController extends Controller
{
    public function show(Request $request, PublicationFile $file)
    {
        // Rute ini diperlakukan sebagai akses/unduh berkas legacy: periksa izin download terlebih dahulu
        if (! $file->canBeDownloadedBy(auth()->user())) {
            abort(403, 'Akses/unduh file ditolak.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $path = Storage::disk('local')->path($file->file_path);

        $download = $request->query('download');
        if ($download) {
            return response()->download($path, $file->title . '.pdf');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file->title . '.pdf"',
        ]);
    }
}
