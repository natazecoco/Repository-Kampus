<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kamus Metode Riset & Pemodelan ULTIMATE (Multi-Disiplin Ilmu)
    |--------------------------------------------------------------------------
    | Urutan sangat penting! Sistem akan mendeteksi dari ATAS ke BAWAH.
    | Hirarki dirancang agar tidak terjadi false-positive pada abstrak umum.
    |
    */
    'methods' => [
        // ====================================================================
        // 1. SPESIFIK: SDLC, REKAYASA PERANGKAT LUNAK & AGILE
        // ====================================================================
        'sdlc waterfall'                   => 'SDLC Waterfall',
        'metode waterfall'                 => 'SDLC Waterfall',
        'model waterfall'                  => 'SDLC Waterfall',
        'waterfall'                        => 'SDLC Waterfall',

        'agile scrum'                      => 'Agile Scrum',
        'metode scrum'                     => 'Agile Scrum',
        'scrum'                            => 'Agile Scrum',
        'extreme programming'              => 'Extreme Programming (XP)',
        'metode xp'                        => 'Extreme Programming (XP)',
        'agile development'                => 'Agile Development',
        'agile'                            => 'Agile Development',

        'rapid application development'    => 'Rapid Application Development (RAD)',
        'metode rad'                       => 'Rapid Application Development (RAD)',
        'rad'                              => 'Rapid Application Development (RAD)',
        
        'rational unified process'         => 'Rational Unified Process (RUP)',
        'metode rup'                       => 'Rational Unified Process (RUP)',
        'rup'                              => 'Rational Unified Process (RUP)',

        'prototyping'                      => 'Prototyping',
        'metode prototype'                 => 'Prototyping',
        'prototype'                        => 'Prototyping',
        
        'model spiral'                     => 'SDLC Spiral',
        'metode spiral'                    => 'SDLC Spiral',
        'spiral'                           => 'SDLC Spiral',
        'devops'                           => 'DevOps',

        // ====================================================================
        // 2. AI, DATA SCIENCE, MACHINE LEARNING & SPK
        // ====================================================================
        'convolutional neural network'     => 'Convolutional Neural Network (CNN)',
        'cnn'                              => 'Convolutional Neural Network (CNN)',
        'recurrent neural network'         => 'Recurrent Neural Network (RNN)',
        'rnn'                              => 'Recurrent Neural Network (RNN)',
        'long short-term memory'           => 'Long Short-Term Memory (LSTM)',
        'lstm'                             => 'Long Short-Term Memory (LSTM)',
        'support vector machine'           => 'Support Vector Machine (SVM)',
        'svm'                              => 'Support Vector Machine (SVM)',
        'k-nearest neighbor'               => 'K-Nearest Neighbor (KNN)',
        'k-nearest neighbours'             => 'K-Nearest Neighbor (KNN)',
        'knn'                              => 'K-Nearest Neighbor (KNN)',
        'naive bayes'                      => 'Naive Bayes',
        'random forest'                    => 'Random Forest',
        'decision tree'                    => 'Decision Tree',
        'k-means clustering'               => 'K-Means Clustering',
        'k-means'                          => 'K-Means Clustering',
        'collaborative filtering'          => 'Collaborative Filtering',
        'content-based filtering'          => 'Content-Based Filtering',
        'data mining'                      => 'Data Mining',
        'machine learning'                 => 'Machine Learning',
        'deep learning'                    => 'Deep Learning',

        'analytical hierarchy process'     => 'Analytical Hierarchy Process (AHP)',
        'metode ahp'                       => 'Analytical Hierarchy Process (AHP)',
        'ahp'                              => 'Analytical Hierarchy Process (AHP)',
        'technique for others reference by similarity to ideal solution' => 'TOPSIS',
        'metode topsis'                    => 'TOPSIS',
        'topsis'                           => 'TOPSIS',
        'simple additive weighting'        => 'Simple Additive Weighting (SAW)',
        'metode saw'                       => 'Simple Additive Weighting (SAW)',
        'profile matching'                 => 'Profile Matching',
        'weighted product'                 => 'Weighted Product (WP)',

        // ====================================================================
        // 3. STATISTIK LANJUTAN, EKONOMI & BISNIS
        // ====================================================================
        'structural equation modeling'     => 'Structural Equation Modeling (SEM)',
        'metode sem'                       => 'Structural Equation Modeling (SEM)',
        'partial least squares'            => 'Partial Least Squares (PLS-SEM)',
        'pls-sem'                          => 'Partial Least Squares (PLS-SEM)',
        'smartpls'                         => 'Partial Least Squares (PLS-SEM)',
        'regresi linear berganda'          => 'Regresi Linear Berganda',
        'regresi linier berganda'          => 'Regresi Linear Berganda',
        'regresi linear sederhana'         => 'Regresi Linear Sederhana',
        'regresi linier sederhana'         => 'Regresi Linear Sederhana',
        'regresi logistik'                 => 'Regresi Logistik',
        'data panel'                       => 'Analisis Data Panel',
        'error correction model'           => 'Error Correction Model (ECM)',
        'vector autoregression'            => 'Vector Autoregression (VAR)',
        'analisis swot'                    => 'Analisis SWOT',
        'swot'                             => 'Analisis SWOT',
        'balanced scorecard'               => 'Balanced Scorecard',
        'kelayakan bisnis'                 => 'Studi Kelayakan Bisnis',

        // ====================================================================
        // 4. METODOLOGI UTAMA (DIPRIORITASKAN SEBELUM METODE PENGUJIAN!)
        // ====================================================================
        'classroom action research'        => 'Penelitian Tindakan Kelas (PTK)',
        'penelitian tindakan kelas'        => 'Penelitian Tindakan Kelas (PTK)',
        'ptk'                              => 'Penelitian Tindakan Kelas (PTK)',
        'penelitian tindakan'              => 'Penelitian Tindakan (Action Research)',
        'action research'                  => 'Penelitian Tindakan (Action Research)',
        
        'research and development'         => 'Research and Development (R&D)',
        'penelitian dan pengembangan'      => 'Research and Development (R&D)',
        'r&d'                              => 'Research and Development (R&D)',
        'model 4d'                         => 'R&D (Model 4D)',
        'model addie'                      => 'R&D (Model ADDIE)',
        'model borg and gall'              => 'R&D (Borg & Gall)',
        
        'randomized controlled trial'      => 'Randomized Controlled Trial (RCT)',
        'rct'                              => 'Randomized Controlled Trial (RCT)',
        'cross sectional'                  => 'Cross-Sectional',
        'case control'                     => 'Case-Control',
        'kohort'                           => 'Cohort Study',
        'cohort'                           => 'Cohort Study',
        'quasi eksperimen'                 => 'Quasi-Eksperimen',
        'kuasi eksperimen'                 => 'Quasi-Eksperimen',
        'eksperimen laboratorium'          => 'Eksperimen Laboratorium',
        'eksperimental'                    => 'Eksperimental',
        'eksperimen'                       => 'Eksperimental',
        'finite element method'            => 'Finite Element Method (FEM)',
        'computational fluid dynamics'     => 'Computational Fluid Dynamics (CFD)',

        'grounded theory'                  => 'Grounded Theory',
        'fenomenologi'                     => 'Fenomenologi',
        'etnografi'                        => 'Etnografi',
        'studi kasus'                      => 'Studi Kasus',
        'analisis wacana'                  => 'Analisis Wacana (Discourse Analysis)',
        'analisis isi'                     => 'Analisis Isi (Content Analysis)',
        'analisis framing'                 => 'Analisis Framing',
        'analisis semiotika'               => 'Analisis Semiotika',
        'semiotika'                        => 'Analisis Semiotika',
        
        'yuridis normatif'                 => 'Yuridis Normatif',
        'yuridis empiris'                  => 'Yuridis Empiris',
        'sosio legal'                      => 'Socio-Legal Research',

        'mixed methods'                    => 'Mixed Methods (Kuantitatif & Kualitatif)',
        'metode campuran'                  => 'Mixed Methods (Kuantitatif & Kualitatif)',
        'kuantitatif'                      => 'Kuantitatif',
        'kualitatif'                       => 'Kualitatif',

        // ====================================================================
        // 5. METODE PENGUJIAN SISTEM (DITARUH DI BAWAH METODOLOGI UTAMA)
        // ====================================================================
        'black box testing'                => 'Black Box Testing',
        'black box'                        => 'Black Box Testing',
        'blackbox'                         => 'Black Box Testing',
        'white box testing'                => 'White Box Testing',
        'white box'                        => 'White Box Testing',
        'whitebox'                         => 'White Box Testing',
        'user acceptance test'             => 'User Acceptance Testing (UAT)',
        'user acceptance testing'          => 'User Acceptance Testing (UAT)',
        'uat'                              => 'User Acceptance Testing (UAT)',
        'system usability scale'           => 'System Usability Scale (SUS)',
        'metode sus'                       => 'System Usability Scale (SUS)',

        // ====================================================================
        // 6. FALLBACK & LITERATURE REVIEW (PALING BAWAH)
        // ====================================================================
        'systematic literature review'     => 'Systematic Literature Review (SLR)',
        'slr'                              => 'Systematic Literature Review (SLR)',
        'bibliometrik'                     => 'Analisis Bibliometrik',
        'meta analisis'                    => 'Meta-Analisis',
        'meta-analysis'                    => 'Meta-Analisis',
        'literature review'                => 'Literature Review',
        'tinjauan pustaka'                 => 'Literature Review',
        'studi pustaka'                    => 'Literature Review',
        'sdlc'                             => 'SDLC (General)',
        'deskriptif'                       => 'Deskriptif',
    ],
];