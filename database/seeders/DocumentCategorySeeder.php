<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Surat Keputusan (SK)',
            'Surat Masuk',
            'Surat Keluar',
            'Ijazah',
            'Rapor',
            'Sertifikat',
            'Laporan Keuangan',
            'Dokumen Akreditasi',
            'Kurikulum / Silabus',
            'Data Personalia'
        ];

        foreach ($categories as $category) {
            \App\Models\DocumentCategory::create(['name' => $category]);
        }
    }
}
