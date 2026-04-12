<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $documents = \App\Models\Document::all();

        foreach ($documents as $doc) {
            $user = $users->random();
            
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'document_id' => $doc->id,
                'activity' => 'view',
                'description' => "User {$user->name} viewed document '{$doc->title}'",
                'ip_address' => '192.168.1.' . rand(1, 254)
            ]);
        }

        // Add some uploads
        foreach ($documents as $doc) {
            \App\Models\ActivityLog::create([
                'user_id' => $doc->uploaded_by,
                'document_id' => $doc->id,
                'activity' => 'upload',
                'description' => "User uploaded a new document: '{$doc->title}'",
                'ip_address' => '192.168.1.' . rand(1, 254)
            ]);
        }
    }
}
