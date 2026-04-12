<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Setting;
use App\Models\Backup;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatic Backup Schedule
Schedule::call(function () {
    $frequency = Setting::get('auto_backup_frequency', 'none');
    
    if ($frequency === 'none') return;

    try {
        $filename = 'auto-backup-' . date('Y-m-d-His') . '.sql';
        $path = 'backups/' . $filename;
        
        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        $fullPath = storage_path('app/private/' . $path);
        
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $dbConfig = config('database.connections.mysql');
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password'] ?? ''),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($fullPath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $backup = Backup::create([
                'file_name' => $filename,
                'file_path' => $path,
                'backup_date' => now()
            ]);
            ActivityLogger::log('backup', "Backup otomatis berhasil: {$filename}", $backup->id);
        }
    } catch (\Exception $e) {
        \Log::error('Auto Backup Failed: ' . $e->getMessage());
    }
})->dailyAt('00:00')->when(function () {
    return Setting::get('auto_backup_frequency') === 'daily';
});

Schedule::call(function () {
    // Shared logic via command or duplicate for simplicity in console.php
    Artisan::call('backups:run-auto'); 
})->weeklyOn(0, '00:00')->when(function () {
    return Setting::get('auto_backup_frequency') === 'weekly';
});
