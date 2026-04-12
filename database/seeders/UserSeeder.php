<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = \App\Models\Role::all();

        $userData = [
            'Admin' => [
                'name' => 'Super Admin',
                'email' => 'admin@sma23.sch.id',
            ],
            'Tata Usaha' => [
                'name' => 'Budi Santoso',
                'email' => 'budi.tu@sma23.sch.id',
            ],
            'Kepala Sekolah' => [
                'name' => 'Dr. H. Ahmad Fauzi',
                'email' => 'ahmad.ks@sma23.sch.id',
            ],

        ];

        $units = \App\Models\Unit::all();

        foreach ($userData as $roleName => $data) {
            $role = $roles->where('name', $roleName)->first();
            if ($role) {
                // Assign unit based on role
                $unitId = null;
                if ($roleName == 'Tata Usaha') {
                    $unitId = $units->where('name', 'Bagian Tata Usaha')->first()?->id;
                } elseif ($roleName == 'Kurikulum') {
                    $unitId = $units->where('name', 'Unit Kurikulum')->first()?->id;
                }

                \App\Models\User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role_id' => $role->id,
                    'unit_id' => $unitId,
                ]);
            }
        }
    }
}
