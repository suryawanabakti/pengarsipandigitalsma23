<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'Bagian Tata Usaha',
            'Perpustakaan',
            'Unit Kurikulum',
            'Unit Kesiswaan',
            'Unit Sarana & Prasarana',
            'Unit Humas',
            'Laboratorium Komputer',
            'Bimbingan Konseling (BK)'
        ];

        foreach ($units as $unit) {
            \App\Models\Unit::create(['name' => $unit]);
        }
    }
}
