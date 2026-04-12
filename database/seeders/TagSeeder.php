<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Penting',
            'Rahasia',
            'Sangat Rahasia',
            'Tahun 2024',
            'Tahun 2023',
            'Sertifikasi',
            'Arsip Statis',
            'Arsip Dinamis',
            'Draft',
            'Revisi'
        ];

        foreach ($tags as $tag) {
            \App\Models\Tag::create(['name' => $tag]);
        }
    }
}
