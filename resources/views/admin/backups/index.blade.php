@extends('layouts.admin')

@section('title', 'Backup & Restore')

@section('content')
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Backup List -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="card-title">Daftar Backup Database & File</h2>
                <form action="{{ route('backups.store') }}" method="POST" style="display: flex; gap: 8px; align-items: center;">
                    @csrf
                    <select name="type" class="form-control" style="padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px;">
                        <option value="full">Full (DB + File)</option>
                        <option value="db">Database Saja</option>
                        <option value="file">File Dokumen Saja</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Backup
                    </button>
                </form>
            </div>
            
            @if (session('error'))
                <div style="padding: 15px; margin: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-size: 14px;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Tipe</th>
                            <th>Tanggal Backup</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="background: var(--primary-light); color: var(--primary); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                            <i class="fas {{ $backup->type == 'db' ? 'fa-database' : ($backup->type == 'file' ? 'fa-file-archive' : 'fa-archive') }}"></i>
                                        </div>
                                        <span>{{ $backup->file_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            'db' => ['Database', 'badge-info'],
                                            'file' => ['File Dokumen', 'badge-warning'],
                                            'full' => ['Full (DB+File)', 'badge-success']
                                        ];
                                        $label = $typeLabels[$backup->type] ?? ['Unknown', 'badge-secondary'];
                                    @endphp
                                    <span class="badge {{ $label[1] }}">{{ $label[0] }}</span>
                                </td>
                                <td>{{ $backup->backup_date->format('d F Y, H:i') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('backups.download', $backup) }}" class="btn btn-info" style="padding: 5px 10px; font-size: 12px;" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        
                                        <form action="{{ route('backups.restore', $backup) }}" method="POST" onsubmit="return confirm('Peringatan: Seluruh data saat ini akan ditimpa oleh data dari backup ini. Tindakan ini tidak dapat dibatalkan. Lanjutkan?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success" style="padding: 5px 10px; font-size: 12px;" title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('backups.destroy', $backup) }}" method="POST" onsubmit="return confirm('Hapus file backup ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    Belum ada data backup.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div style="padding: 20px;">
                {{ $backups->links() }}
            </div>
        </div>

        <!-- Settings -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Pengaturan Backup Otomatis</h2>
            </div>
            <div style="padding: 24px;">
                <form action="{{ route('backups.settings') }}" method="POST">
                    @csrf
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Frekuensi Backup</label>
                        <select name="auto_backup_frequency" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <option value="none" {{ $autoBackup == 'none' ? 'selected' : '' }}>Matikan (Manual Saja)</option>
                            <option value="daily" {{ $autoBackup == 'daily' ? 'selected' : '' }}>Harian (Setiap Hari)</option>
                            <option value="weekly" {{ $autoBackup == 'weekly' ? 'selected' : '' }}>Mingguan (Setiap Minggu)</option>
                        </select>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                            * Backup otomatis akan dijalankan pada jam 00:00 sesuai frekuensi yang dipilih.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Simpan Pengaturan
                    </button>
                </form>

                <hr style="margin: 24px 0; border: none; border-top: 1px solid #e2e8f0;">

                <div style="background: var(--warning-light); padding: 15px; border-radius: 12px; border-left: 4px solid var(--warning);">
                    <h4 style="color: #92400e; margin-bottom: 8px; font-size: 14px;"><i class="fas fa-exclamation-triangle"></i> Catatan Penting</h4>
                    <p style="font-size: 12px; color: #92400e; line-height: 1.6;">
                        Pastikan server memiliki izin (permission) yang cukup untuk menjalankan perintah <code>mysqldump</code> dan menulis file ke folder storage. File restore akan menimpa seluruh data database saat ini.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
