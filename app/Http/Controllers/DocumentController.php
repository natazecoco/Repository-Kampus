<?php

namespace App\Http\Controllers;

use App\Models\PublicationFile; // Kita panggil model anaknya, bukan Publication lagi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // 1. Menampilkan halaman UI/Viewer (HTML)
    public function viewer($id)
    {
        $file = PublicationFile::findOrFail($id);

        if (! $file->canBeViewedBy(auth()->user())) {
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda harus login untuk membuka dokumen ini.');
        }

        return view('pdf.viewer', compact('file'));
    }

    public function stream($id)
    {
        $file = PublicationFile::findOrFail($id);

        if (! $file->canBeViewedBy(auth()->user())) {
            abort(403, 'Akses dokumen terbatas. Silakan login terlebih dahulu.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        return response()->stream(function () use ($file) {
            $stream = Storage::disk('local')->readStream($file->file_path);
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="dokumen-terenkripsi.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}