<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\Role::where('name', 'Admin')->first();
        $tu = \App\Models\Role::where('name', 'Tata Usaha')->first();
        $ks = \App\Models\Role::where('name', 'Kepala Sekolah')->first();
        $ktu = \App\Models\Role::where('name', 'Kepala Tata Usaha')->first();

        $permissions = \App\Models\Permission::all();

        // Admin gets all
        $admin->permissions()->attach($permissions);

        // Tata Usaha
        $tu->permissions()->attach($permissions->whereIn('name', [
            'view-documents',
            'create-documents',
            'edit-documents',
            'download-documents',
            'view-logs'
        ]));

        // Kepala Sekolah
        $ks->permissions()->attach($permissions->whereIn('name', [
            'view-documents',
            'download-documents',
            'approve-documents',
            'view-logs'
        ]));

        // Kepala Tata Usaha
        $ktu->permissions()->attach($permissions->whereIn('name', [
            'view-documents',
            'download-documents',
            'approve-documents',
            'view-logs'
        ]));
    }
}
