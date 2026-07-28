<?php

namespace App\Observers;

use App\Models\Publication;

class PublicationObserver
{
    public function created(Publication $publication): void
    {
        // Sengaja dikosongkan karena tagging sudah dipindah ke CreatePublication
    }

    public function updated(Publication $publication): void
    {
        // Sengaja dikosongkan karena tagging sudah dipindah ke EditPublication
    }
}