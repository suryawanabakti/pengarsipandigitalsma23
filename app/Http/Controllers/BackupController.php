<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Backup;
use App\Models\Setting;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->paginate(10);
        $autoBackup = Setting::get('auto_backup_frequency', 'none');
        return view('admin.backups.index', compact('backups', 'autoBackup'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:db,file,full'
        ]);

        $type = $request->type;

        try {
            $extension = ($type === 'db') ? 'sql' : 'zip';
            $filename = "backup-{$type}-" . date('Y-m-d-His') . ".{$extension}";
            $path = 'backups/' . $filename;
            
            if (!Storage::disk('local')->exists('backups')) {
                Storage::disk('local')->makeDirectory('backups');
            }

            $fullPath = storage_path('app/private/' . $path);
            
            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            if ($type === 'db') {
                $this->createDatabaseBackup($fullPath);
            } elseif ($type === 'file') {
                $this->createFileBackup($fullPath);
            } else {
                $this->createFullBackup($fullPath);
            }

            $backup = Backup::create([
                'file_name' => $filename,
                'file_path' => $path,
                'type' => $type,
                'backup_date' => now()
            ]);

            ActivityLogger::log('backup', "Berhasil membuat backup ({$type}): {$filename}", $backup->id);

            return redirect()->back()->with('success', "Backup ({$type}) berhasil dibuat.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function createDatabaseBackup($outputPath)
    {
        $dbConfig = config('database.connections.mysql');
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password'] ?? ''),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($outputPath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Gagal menjalankan mysqldump.');
        }
    }

    private function createFileBackup($outputPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($outputPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Gagal membuat file ZIP.");
        }

        $sourcePath = storage_path('app/public');
        if (is_dir($sourcePath)) {
            $this->addFolderToZip($sourcePath, $zip, 'storage');
        }

        $zip->close();
    }

    private function createFullBackup($outputPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($outputPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Gagal membuat file ZIP.");
        }

        // 1. Add Database SQL
        $tempSql = storage_path('app/temp_db_' . time() . '.sql');
        $this->createDatabaseBackup($tempSql);
        $zip->addFile($tempSql, 'database.sql');

        // 2. Add Files
        $sourcePath = storage_path('app/public');
        if (is_dir($sourcePath)) {
            $this->addFolderToZip($sourcePath, $zip, 'storage');
        }

        $zip->close();
        unlink($tempSql);
    }

    private function addFolderToZip($dir, $zipArchive, $zipDir = '')
    {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                $zipArchive->addEmptyDir($zipDir);
                while (($file = readdir($dh)) !== false) {
                    if (!is_file($dir . '/' . $file)) {
                        if (($file !== ".") && ($file !== "..")) {
                            $this->addFolderToZip($dir . '/' . $file, $zipArchive, $zipDir . '/' . $file);
                        }
                    } else {
                        $zipArchive->addFile($dir . '/' . $file, $zipDir . '/' . $file);
                    }
                }
            }
        }
    }

    public function download(Backup $backup)
    {
        $fullPath = storage_path('app/private/' . $backup->file_path);
        
        if (!file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $backup->file_path);
        }

        if (!file_exists($fullPath)) {
             return redirect()->back()->with('error', 'File backup tidak ditemukan.');
        }

        ActivityLogger::log('download', "Mengunduh file backup ({$backup->type}): {$backup->file_name}", $backup->id);
        
        return response()->download($fullPath);
    }

    public function destroy(Backup $backup)
    {
        $fullPath = storage_path('app/private/' . $backup->file_path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        } else {
             $fullPath = storage_path('app/' . $backup->file_path);
             if (file_exists($fullPath)) unlink($fullPath);
        }

        ActivityLogger::log('delete', "Menghapus backup ({$backup->type}): {$backup->file_name}");
        
        $backup->delete();
        return redirect()->back()->with('success', 'Backup berhasil dihapus.');
    }

    public function restore(Backup $backup)
    {
        try {
            $fullPath = storage_path('app/private/' . $backup->file_path);
            if (!file_exists($fullPath)) {
                 $fullPath = storage_path('app/' . $backup->file_path);
            }

            if (!file_exists($fullPath)) {
                throw new \Exception('File backup tidak ditemukan.');
            }

            if ($backup->type === 'db') {
                $this->restoreDatabase($fullPath);
            } elseif ($backup->type === 'file') {
                $this->restoreFiles($fullPath);
            } else {
                $this->restoreFull($fullPath);
            }

            ActivityLogger::log('restore', "Berhasil memulihkan data ({$backup->type}) dari: {$backup->file_name}");

            return redirect()->back()->with('success', "Data ({$backup->type}) berhasil dipulihkan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function restoreDatabase($sqlPath)
    {
        $dbConfig = config('database.connections.mysql');
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s %s < %s',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password'] ?? ''),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($sqlPath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('Gagal memulihkan database.');
        }
    }

    private function restoreFiles($zipPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === true) {
            $extractPath = storage_path('app/temp_restore_' . time());
            mkdir($extractPath, 0755, true);
            $zip->extractTo($extractPath);
            $zip->close();

            // Move files from temp_restore/storage to storage/app/public
            $src = $extractPath . '/storage';
            $dest = storage_path('app/public');
            
            if (is_dir($src)) {
                $this->recursiveCopy($src, $dest);
            }

            $this->recursiveDelete($extractPath);
        } else {
            throw new \Exception("Gagal membuka file ZIP.");
        }
    }

    private function restoreFull($zipPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === true) {
            $extractPath = storage_path('app/temp_restore_' . time());
            mkdir($extractPath, 0755, true);
            $zip->extractTo($extractPath);
            $zip->close();

            // 1. Restore Database
            $sqlFile = $extractPath . '/database.sql';
            if (file_exists($sqlFile)) {
                $this->restoreDatabase($sqlFile);
            }

            // 2. Restore Files
            $src = $extractPath . '/storage';
            $dest = storage_path('app/public');
            if (is_dir($src)) {
                $this->recursiveCopy($src, $dest);
            }

            $this->recursiveDelete($extractPath);
        } else {
            throw new \Exception("Gagal membuka file ZIP.");
        }
    }

    private function recursiveCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while (($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function recursiveDelete($dir)
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recursiveDelete("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'auto_backup_frequency' => 'required|in:none,daily,weekly'
        ]);

        Setting::set('auto_backup_frequency', $request->auto_backup_frequency);

        ActivityLogger::log('settings', "Memperbarui frekuensi backup otomatis menjadi: {$request->auto_backup_frequency}");

        return redirect()->back()->with('success', 'Pengaturan backup berhasil diperbarui.');
    }
}
