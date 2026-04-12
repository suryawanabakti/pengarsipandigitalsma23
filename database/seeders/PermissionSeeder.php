<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-documents',
            'create-documents',
            'edit-documents',
            'delete-documents',
            'download-documents',
            'approve-documents',
            'manage-users',
            'manage-settings',
            'view-logs',
            'manage-backups'
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::create(['name' => $permission]);
        }
    }
}
