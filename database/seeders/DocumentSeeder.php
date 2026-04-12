<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\DocumentCategory::all();
        $units = \App\Models\Unit::all();
        $users = \App\Models\User::all();
        $tags = \App\Models\Tag::all();
        $roles = \App\Models\Role::all();

        $tuUser = $users->where('email', 'budi.tu@sma23.sch.id')->first();
        $adminUser = $users->where('email', 'admin@sma23.sch.id')->first();
        $ksUser = $users->where('email', 'ahmad.ks@sma23.sch.id')->first();

        $documents = [
            [
                'title' => 'SK Pembagian Tugas Mengajar Semester Ganjil 2024/2025',
                'category' => 'Surat Keputusan (SK)',
                'unit' => 'Unit Kurikulum',
                'number' => 'SK/001/SMA23/VII/2024',
                'stage' => 'arsip',
                'status' => 'disetujui',
                'archive_type' => 'dinamis',
            ],
            [
                'title' => 'Laporan Pertanggungjawaban Dana BOS Triwulan II',
                'category' => 'Laporan Keuangan',
                'unit' => 'Bagian Tata Usaha',
                'number' => 'LPJ/BOS/022/2024',
                'stage' => 'final',
                'status' => 'disetujui',
                'archive_type' => 'dinamis',
            ],
            [
                'title' => 'Sertifikat Akreditasi Sekolah 2023',
                'category' => 'Dokumen Akreditasi',
                'unit' => 'Bagian Tata Usaha',
                'number' => 'AKR/2023/X/001',
                'stage' => 'arsip',
                'status' => 'disetujui',
                'archive_type' => 'statis',
            ],
            [
                'title' => 'Undangan Rapat Orang Tua Siswa Kelas XII',
                'category' => 'Surat Keluar',
                'unit' => 'Unit Humas',
                'number' => 'SR/045/SMA23/VIII/2024',
                'stage' => 'final',
                'status' => 'diajukan',
                'archive_type' => 'dinamis',
            ],
            [
                'title' => 'Draft Kurikulum Operasional Satuan Pendidikan (KOSP)',
                'category' => 'Kurikulum / Silabus',
                'unit' => 'Unit Kurikulum',
                'number' => null,
                'stage' => 'draft',
                'status' => 'draft',
                'archive_type' => 'dinamis',
            ]
        ];

        foreach ($documents as $doc) {
            $category = $categories->where('name', $doc['category'])->first();
            $unit = $units->where('name', $doc['unit'])->first();
            
            $document = \App\Models\Document::create([
                'title' => $doc['title'],
                'file_name' => \Illuminate\Support\Str::slug($doc['title']) . '.pdf',
                'file_path' => 'documents/' . \Illuminate\Support\Str::random(40) . '.pdf',
                'file_type' => 'application/pdf',
                'document_number' => $doc['number'],
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'uploaded_by' => $tuUser->id ?? $adminUser->id,
                'stage' => $doc['stage'],
                'status' => $doc['status'],
                'archive_type' => $doc['archive_type'],
                'document_date' => now()->subDays(rand(1, 30)),
            ]);

            // Add Tags
            $document->tags()->attach($tags->random(rand(1, 3))->pluck('id'));

            // Add Versions
            \App\Models\DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => 1,
                'file_path' => $document->file_path,
                'uploaded_by' => $document->uploaded_by,
                'change_notes' => 'Initial upload'
            ]);

            // Add Approvals
            if (in_array($doc['status'], ['disetujui', 'diajukan', 'ditolak'])) {
                \App\Models\DocumentApproval::create([
                    'document_id' => $document->id,
                    'approved_by' => $ksUser->id ?? $adminUser->id,
                    'status' => $doc['status'] == 'diajukan' ? 'pending' : ($doc['status'] == 'disetujui' ? 'approved' : 'rejected'),
                    'notes' => $doc['status'] == 'disetujui' ? 'Dokumen sudah sesuai.' : null,
                    'approved_at' => $doc['status'] == 'disetujui' ? now() : null,
                ]);
            }

            // Add Retention
            \App\Models\DocumentRetention::create([
                'document_id' => $document->id,
                'retention_status' => $doc['archive_type'] == 'statis' ? 'permanen' : 'aktif',
                'expiry_date' => now()->addYears(5),
            ]);

            // Add Permissions - for Guru
            $guruRole = $roles->where('name', 'Guru')->first();
            if ($guruRole) {
                \App\Models\DocumentPermission::create([
                    'document_id' => $document->id,
                    'role_id' => $guruRole->id,
                    'can_view' => true,
                    'can_download' => $doc['status'] == 'disetujui',
                ]);
            }

            // Add Notification
            \App\Models\EmailNotification::create([
                'document_id' => $document->id,
                'user_id' => $adminUser->id,
                'type' => 'document_status_update',
                'message' => "Status dokumen '{$document->title}' telah diperbarui menjadi {$document->status}.",
                'is_sent' => true
            ]);
        }
    }
}
