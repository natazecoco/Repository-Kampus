<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Publication;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        /* 
          ULTIMATE CLEAN & LOGICAL TAXONOMY TREE
          Batasan jelas: 
          - Level 1: Domain / Rumpun Utama (Contoh: Ilmu Komputer & Teknologi)
          - Level 2: Bidang / Program Studi / Fokus (Contoh: Sistem Informasi, Kecerdasan Buatan)
          - Level 3: Kelompok Topik / Sub-Bidang (Contoh: Sistem Rekomendasi, Machine Learning)
          - Level 4: Metode / Algoritma / Spesifik (Contoh: Content-based Filtering, TF-IDF)
        */
        $ultimateTaxonomy = [
            'Ilmu Komputer & Teknologi' => [
                'Sistem Informasi' => [
                    'Sistem Rekomendasi' => [
                        'Content-based Filtering',
                        'Collaborative Filtering',
                        'Hybrid Recommender System',
                    ],
                    'Information Retrieval' => [
                        'TF-IDF',
                        'Cosine Similarity',
                        'Semantic Search Engine',
                        'Information Extraction',
                    ],
                    'Knowledge Management' => [
                        'Knowledge Graph',
                        'Ontology Engineering',
                        'Semantic Web',
                    ],
                    'Business Analytics' => [
                        'Predictive Analytics',
                        'Customer Churn Analysis',
                        'Business Intelligence',
                        'RFM Analysis',
                        'Performance Scoring',
                    ]
                ],
                'Kecerdasan Buatan' => [
                    'Natural Language Processing' => [
                        'Named Entity Recognition',
                        'Sentiment Analysis',
                        'Retrieval-Augmented Generation',
                        'Large Language Models',
                        'OCR',
                        'Chatbot',
                    ],
                    'Machine Learning' => [
                        'Classification',
                        'Clustering',
                        'Anomaly Detection',
                        'Explainable AI',
                        'Autoencoder',
                        'Time Series Forecasting',
                    ],
                    'Computer Vision' => [
                        'Object Detection',
                        'YOLO',
                        'CNN',
                        'Face Recognition',
                        'VGG-16',
                    ],
                    'Advanced AI' => [
                        'Graph Neural Networks',
                        'Graph Embedding',
                        'Reinforcement Learning',
                    ]
                ],
                'Terapan & Studi Kasus' => [
                    'Smart City & Urban' => [
                        'Geospatial Analysis',
                        'Urban Analytics',
                    ],
                    'Healthcare System' => [
                        'Predictive Healthcare',
                        'Clinical Decision Support',
                    ],
                    'Industrial & Supply Chain' => [
                        'Predictive Maintenance',
                        'Supply Chain Optimization',
                        'Vehicle Routing Problem',
                    ],
                    'Education & Agriculture' => [
                        'Educational Data Mining',
                        'Learning Analytics',
                        'Precision Agriculture',
                    ]
                ],
                'Keamanan & Infrastruktur' => [
                    'Cybersecurity' => [
                        'Malware Detection',
                        'Intrusion Detection System',
                    ],
                    'System Optimization' => [
                        'LLM Optimization',
                        'Batch Prompting',
                    ]
                ],
                'Pengembangan Perangkat Lunak' => [
                    'Web Development' => [
                        'Laravel',
                        'React',
                        'Filament',
                        'PHP',
                    ],
                    'Mobile Development' => [
                        'Android',
                        'Flutter',
                    ]
                ]
            ]
        ];

        $sortOrder = 1;

        // Eksekusi Pemetaan Multi-Level ke Database
        foreach ($ultimateTaxonomy as $level1Name => $level2Group) {
            $level1Parent = Topic::updateOrCreate(
                ['slug' => Str::slug($level1Name)], 
                [
                    'name' => $level1Name,
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                    'parent_id' => null,
                ]
            );

            foreach ($level2Group as $level2Name => $level3GroupOrChildren) {
                $level2Parent = Topic::updateOrCreate(
                    ['slug' => Str::slug($level2Name)], 
                    [
                        'name' => $level2Name,
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                        'parent_id' => $level1Parent->id,
                    ]
                );

                foreach ($level3GroupOrChildren as $key => $value) {
                    if (is_array($value)) {
                        $level3Name = $key;
                        $level3Parent = Topic::updateOrCreate(
                            ['slug' => Str::slug($level3Name)], 
                            [
                                'name' => $level3Name,
                                'is_active' => true,
                                'sort_order' => $sortOrder++,
                                'parent_id' => $level2Parent->id,
                            ]
                        );

                        foreach ($value as $level4Name) {
                            Topic::updateOrCreate(
                                ['slug' => Str::slug($level4Name)], 
                                [
                                    'name' => $level4Name,
                                    'is_active' => true,
                                    'sort_order' => $sortOrder++,
                                    'parent_id' => $level3Parent->id,
                                ]
                            );
                        }
                    } else {
                        $level3Name = $value;
                        Topic::updateOrCreate(
                            ['slug' => Str::slug($level3Name)], 
                            [
                                'name' => $level3Name,
                                'is_active' => true,
                                'sort_order' => $sortOrder++,
                                'parent_id' => $level2Parent->id,
                            ]
                        );
                    }
                }
            }
        }

        // Buat Container sampel jika belum ada
        if (Container::count() === 0) {
            $container = Container::create([
                'name' => 'Repository Utama Universitas', 
                'type' => 'university'
            ]);
        } else {
            $container = Container::first();
        }

        // Buat Publikasi sampel untuk pengujian auto-tagging & hierarki
        if (Publication::count() === 0) {
            $p1 = Publication::create([
                'container_id' => $container->id,
                'type' => 'thesis',
                'title' => 'Semantic Search Engine untuk Repositori Skripsi Menggunakan Content-based Filtering',
                'author' => 'Radhwa Radinka',
                'year' => 2026,
                'abstract' => 'Penelitian ini mengembangkan sistem pencarian dan rekomendasi literatur berbasis Content-based filtering, TF-IDF, dan cosine similarity untuk repositori ilmiah.',
                'keywords' => 'content-based filtering, tf-idf, semantic search engine',
            ]);

            $targetTopic = Topic::where('slug', 'content-based-filtering')->first();
            if ($targetTopic) { 
                $p1->topics()->attach($targetTopic->id, ['is_auto' => false]); 
            }
        }
    }
}