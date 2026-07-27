<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PublicationFile;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function publicationViewer(Publication $publication)
    {
        $files = $publication->files
            ->filter(fn (PublicationFile $file): bool => $file->canBeViewedBy(auth()->user()))
            ->values();

        abort_if($files->isEmpty(), 403, 'Anda tidak memiliki akses ke dokumen publikasi ini.');

        $file = $files->firstWhere('id', (int) request('file')) ?? $files->first();

        return view('pdf.viewer', compact('publication', 'files', 'file'));
    }

    public function stream(PublicationFile $file)
    {
        abort_unless($file->canBeViewedBy(auth()->user()), 403, 'Akses dokumen terbatas.');

        if ((bool) request()->query('download')) {
            abort_unless($file->canBeDownloadedBy(auth()->user()), 403, 'Download tidak diizinkan.');

            if (! Storage::disk('local')->exists($file->file_path)) {
                abort(404, 'File PDF tidak ditemukan di server.');
            }

            return response()->download(Storage::disk('local')->path($file->file_path), $file->title . '.pdf');
        }

        if (! Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        $filename = $file->title ? ($file->title . '.pdf') : basename($file->file_path);

        return response()->stream(function () use ($file) {
            $stream = Storage::disk('local')->readStream($file->file_path);
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}