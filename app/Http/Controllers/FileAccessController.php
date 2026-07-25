<?php

namespace App\Http\Controllers;

use App\Models\PublicationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileAccessController extends Controller
{
    public function show(PublicationFile $file)
    {
        if ($file->access_type === 'restricted' && ! auth()->check()) {
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda harus login untuk membaca dokumen ini.');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $path = Storage::disk('local')->path($file->file_path);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file->title . '.pdf"',
        ]);
    }
}