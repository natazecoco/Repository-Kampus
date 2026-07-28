<?php

namespace App\Services;

use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class TextPreprocessor
{
    private $stemmer;
    private $stopword;

    public function __construct()
    {
        $stemmerFactory = new StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $stopWordFactory = new StopWordRemoverFactory();
        $this->stopword = $stopWordFactory->createStopWordRemover();
    }

    /**
     * Membersihkan teks melalui pipeline NLP terpusat.
     */
    public function process(?string $text): string
    {
        if (blank($text)) {
            return '';
        }

        // 1. Case folding (Unicode Safe)
        $cleaned = mb_strtolower(trim($text));
        
        // 2. Hapus tanda baca/simbol. Ganti dengan spasi.
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleaned);
        
        // 3. Normalisasi spasi ganda menjadi satu spasi saja
        $cleaned = preg_replace('/\s+/u', ' ', $cleaned);
        $cleaned = trim($cleaned);
        
        // 4. Hapus stopword (kata hubung)
        $cleaned = $this->stopword->remove($cleaned);

        // 5. Optimasi: Hentikan jika teks menjadi kosong setelah stopword dihapus
        if ($cleaned === '') {
            return '';
        }

        // 6. Stemming (kembalikan ke kata dasar)
        return $this->stemmer->stem($cleaned);
    }
}