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
<<<<<<< HEAD
        // Periksa hak akses tampilan terlebih dahulu agar tidak membocorkan keberadaan file
        abort_unless($file->canBeViewedBy(auth()->user()), 403, 'Akses dokumen terbatas.');

        // Jika pemanggil meminta download eksplisit, pastikan izin download
        if ((bool) request()->query('download')) {
            abort_unless($file->canBeDownloadedBy(auth()->user()), 403, 'Download tidak diizinkan.');

            if (! Storage::disk('local')->exists($file->file_path)) {
                abort(404, 'File PDF tidak ditemukan di server.');
            }

            return response()->download(Storage::disk('local')->path($file->file_path), $file->title . '.pdf');
        }
=======
        abort_unless($file->canBeViewedBy(auth()->user()), 403, 'Akses dokumen terbatas.');
>>>>>>> 5d6bb8c (Add merge audit metadata (merged_by, merged_at) and surface in admin\n\n- Add migrations to store merged_by and merged_at in topic_merge_backups and topics\n- Record current user and timestamp when merging topics\n- Clear audit fields on undo merge\n- Show merged metadata in Topics table UI\n- Add safe route fallbacks for student.login/student.register for test environments\n\nCo-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>)

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
<<<<<<< HEAD
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
=======
            'Content-Disposition' => 'inline; filename="dokumen.pdf"',
>>>>>>> 5d6bb8c (Add merge audit metadata (merged_by, merged_at) and surface in admin\n\n- Add migrations to store merged_by and merged_at in topic_merge_backups and topics\n- Record current user and timestamp when merging topics\n- Clear audit fields on undo merge\n- Show merged metadata in Topics table UI\n- Add safe route fallbacks for student.login/student.register for test environments\n\nCo-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>)
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
