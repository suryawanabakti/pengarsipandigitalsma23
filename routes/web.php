<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:Admin,Tata Usaha,Kepala Sekolah'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Documents (Common Access)
    // Documents (Common Access)
    Route::get('documents/bulk', [DocumentController::class, 'bulkCreate'])->name('documents.bulk');
    Route::post('documents/bulk', [DocumentController::class, 'bulkStore'])->name('documents.bulk.store');
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('documents/versions/{version}/download', [DocumentController::class, 'downloadVersion'])->name('documents.versions.download');
    Route::post('documents/versions/{version}/restore', [DocumentController::class, 'restoreVersion'])->name('documents.versions.restore');

    // Reports (Admin & TU)
    Route::middleware(['role:Admin,Tata Usaha'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');
    });

    // Approvals (Admin & Kepsek)
    Route::middleware(['role:Admin,Kepala Sekolah,Tata Usaha'])->group(function () {
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/{document}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{document}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });

    // ADMIN ONLY MANAGEMENT
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', UserController::class);
        
        // Master Data
        Route::resource('categories', DocumentCategoryController::class);
        Route::resource('units', UnitController::class);
        Route::resource('tags', TagController::class);

        // Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');

        // Backup & Restore
        Route::get('backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [\App\Http\Controllers\BackupController::class, 'store'])->name('backups.store');
        Route::get('backups/{backup}/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{backup}', [\App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('backups/{backup}/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
        Route::post('backups/settings', [\App\Http\Controllers\BackupController::class, 'updateSettings'])->name('backups.settings');
    });
});
