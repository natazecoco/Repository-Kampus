<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ambang Batas (Threshold) Auto-Tagging
    |--------------------------------------------------------------------------
    | Skor minimal yang dibutuhkan agar sistem secara otomatis menempelkan
    | topik ke publikasi. 
    | Pembobotan default: Judul (10), Keywords (7), Abstrak (3).
    |
    */
    'threshold' => 10,

    /*
    |--------------------------------------------------------------------------
    | Kamus Sinonim (Topic Mappings)
    |--------------------------------------------------------------------------
    | Digunakan bersama oleh Fitur Pencarian (Query Expansion) dan Auto-Tagging.
    | Pastikan key (sebelah kiri) adalah nama topik dalam huruf kecil.
    |
    */
    // 'mappings' => [
    //     'artificial intelligence' => [
    //         'ai', 
    //         'kecerdasan buatan'
    //     ],
        
    //     'machine learning' => [
    //         'ml', 
    //         'supervised learning', 
    //         'unsupervised learning',
    //         'klasifikasi',
    //         'regresi'
    //     ],
        
    //     'deep learning' => [
    //         'neural network', 
    //         'cnn', 
    //         'rnn', 
    //         'jaringan saraf tiruan'
    //     ],
        
    //     'recommendation systems' => [
    //         'recommendation', 
    //         'recommender', 
    //         'sistem rekomendasi', 
    //         'collaborative filtering', 
    //         'content based'
    //     ],
        
    //     'web development' => [
    //         'web', 
    //         'website', 
    //         'php', 
    //         'laravel', 
    //         'filament', 
    //         'frontend', 
    //         'backend'
    //     ],
    // ],

    /*
    |--------------------------------------------------------------------------
    | Kamus Format Judul (Title Case)
    |--------------------------------------------------------------------------
    | Digunakan untuk memastikan singkatan dan istilah teknis tetap menggunakan
    | huruf kapital yang benar, serta kata hubung tetap menggunakan huruf kecil.
    */
    'title_cases' => [
        'mysql' => 'MySQL',
        'php' => 'PHP',
        'laravel' => 'Laravel',
        'filament' => 'Filament',
        'react' => 'React',
        'api' => 'API',
        'ui/ux' => 'UI/UX',
        'ui' => 'UI',
        'ux' => 'UX',
        'nlp' => 'NLP',
        'tf-idf' => 'TF-IDF',
        'ai' => 'AI',
        'html' => 'HTML',
        'css' => 'CSS',
        'spmb' => 'SPMB',
        'ueq' => 'UEQ',
        'sus' => 'SUS',
        
        // Kata Hubung & Depan
        'dan' => 'dan',
        'atau' => 'atau',
        'di' => 'di',
        'ke' => 'ke',
        'dari' => 'dari',
        'yang' => 'yang',
        'pada' => 'pada',
        'untuk' => 'untuk',
        'dengan' => 'dengan',
        'dalam' => 'dalam',
        'terhadap' => 'terhadap',
        'sebagai' => 'sebagai',
    ],
];