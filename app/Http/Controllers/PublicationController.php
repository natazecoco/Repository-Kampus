<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication; 
use App\Models\Recommendation; // Panggil model Recommendation di sini

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Publication::with('container')->latest();

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('keywords', 'like', '%' . $search . '%');
        }

        $publications = $query->get();

        // TAMBAHAN: Proses injeksi tag HTML span untuk highlight kata kunci pada abstrak
        foreach ($publications as $pub) {
            $pub->highlighted_abstract = $this->highlightKeyword($pub->abstract, $search);
        }

        // UPDATE: Penambahan variabel $search pada compact untuk keperluan passing data ke view
        return view('index', compact('publications', 'search'));
    }

    public function show(Publication $publication)
    {
        $publication->load('container');

        // AMBIL DARI DATABASE, BUKAN MENGHITUNG DARI NOL LAGI!
        $recommendations = Recommendation::where('publication_id', $publication->id)
                            ->with('recommendedPublication') 
                            ->orderByDesc('similarity_score')
                            ->get();

        return view('show', compact('publication', 'recommendations'));
    }

    // TAMBAHAN: Method private untuk membungkus kata kunci dengan tag highlight Tailwind
    private function highlightKeyword($text, $keyword) 
    {
        if (empty($keyword) || empty($text)) {
            return $text;
        }

        // preg_quote digunakan untuk mengamankan karakter khusus dalam input pencarian
        $safeKeyword = preg_quote($keyword, '/');
        
        // Modifier 'i' pada regex digunakan untuk pencarian case-insensitive
        $pattern = "/($safeKeyword)/i"; 
        
        $replacement = '<span class="bg-yellow-100/80 text-yellow-900 px-1 rounded-sm font-medium">$1</span>';

        return preg_replace($pattern, $replacement, $text);
    }
}