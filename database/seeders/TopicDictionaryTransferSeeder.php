<?php

namespace Database\Seeders;

use App\Models\TopicDictionary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicDictionaryTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil data mappings dari file config lawas
        $mappings = config('topic_dictionary.mappings');

        if (!$mappings) {
            $this->command->error('Gagal mengambil data dari config/topic_dictionary.php. Pastikan filenya ada.');
            return;
        }

        $this->command->info('Memulai transfer data dari config ke database...');
        
        // Gunakan database transaction agar aman, jika error data tidak masuk setengah
        DB::beginTransaction();

        try {
            $count = 0;

            foreach ($mappings as $targetTopic => $keywords) {
                
                // Normalisasi nama topik target (biar seragam lowercase, sesuai contoh config)
                $targetTopic = trim(strtolower($targetTopic));

                // -- STRATEGI A: Daftarkan Topik Utama itu sendiri sebagai Keyword --
                // Kita pakai updateOrCreate biar tidak error kalau datanya sudah ada
                TopicDictionary::updateOrCreate(
                    ['keyword' => $targetTopic], // Kunci pencarian
                    ['target_topic' => $targetTopic] // Data yang diupdate/diinput
                );
                $count++;


                // -- STRATEGI B: Daftarkan semua Sinonim/Array sebagai Keyword --
                foreach ($keywords as $keyword) {
                    $keyword = trim(strtolower($keyword));
                    
                    if (empty($keyword)) continue;

                    TopicDictionary::updateOrCreate(
                        ['keyword' => $keyword],
                        ['target_topic' => $targetTopic]
                    );
                    $count++;
                }
            }

            DB::commit();
            $this->command->info("Berhasil mentransfer sekitar {$count} data kamus ke database.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Terjadi error saat transfer data: ' . $e->getMessage());
        }
    }
}