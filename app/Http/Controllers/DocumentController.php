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
        // Cari berdasarkan ID file di tabel anak
        $file = PublicationFile::findOrFail($id); 
        
        // Kita kirim variabel $file ke viewer.blade.php
        return view('pdf.viewer', compact('file'));
    }

    // 2. Mengirimkan data file PDF secara rahasia (Streaming biner)
    public function stream($id)
    {
        $file = PublicationFile::findOrFail($id); 

        // Cek apakah file benar-benar ada di brankas 'local'
        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        // Kirim file sebagai stream murni untuk mengecoh downloader eksternal
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