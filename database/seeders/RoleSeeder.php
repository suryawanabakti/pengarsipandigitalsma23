<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Admin',
            'Tata Usaha',
            'Kepala Sekolah',
            'Kepala Tata Usaha',
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role], // kondisi pencarian
                ['name' => $role]  // data yang diupdate/dibuat
            );
        }
    }
}
