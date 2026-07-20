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
        return view('index', compact('publications'));
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
}