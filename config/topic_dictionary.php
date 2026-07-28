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
    'mappings' => [
        'artificial intelligence' => [
            'ai', 
            'kecerdasan buatan'
        ],
        
        'machine learning' => [
            'ml', 
            'supervised learning', 
            'unsupervised learning',
            'klasifikasi',
            'regresi'
        ],
        
        'deep learning' => [
            'neural network', 
            'cnn', 
            'rnn', 
            'jaringan saraf tiruan'
        ],
        
        'recommendation systems' => [
            'recommendation', 
            'recommender', 
            'sistem rekomendasi', 
            'collaborative filtering', 
            'content based'
        ],
        
        'web development' => [
            'web', 
            'website', 
            'php', 
            'laravel', 
            'filament', 
            'frontend', 
            'backend'
        ],
        
        // Kamu bisa menambahkan lebih banyak topik dan sinonimnya di sini nanti
        // seiring dengan berkembangnya skripsimu.
    ],
];