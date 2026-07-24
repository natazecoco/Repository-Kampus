<?php

namespace App\Http\Controllers;

use App\Models\PublicationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileAccessController extends Controller
{
    public function show(PublicationFile $file)
    {
        // 1. Jika file restricted dan user belum login, arahkan ke form login!
        if ($file->access_type === 'restricted' && !auth()->check()) {
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda harus login untuk membaca dokumen ini.');
        }

        // 2. Cek apakah file fisik benar-benar ada di storage server
        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        // 3. Jika aman, ambil path fisiknya dan tampilkan PDF-nya langsung di browser (Preview)
        $path = Storage::disk('public')->path($file->file_path);
        
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file->title . '.pdf"'
        ]);
    }
}