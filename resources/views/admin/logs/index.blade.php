@extends('layouts.admin')

@section('title', 'Log Aktivitas Sistem')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Riwayat Aktivitas</h2>
        </div>

        <div style="padding: 20px;">
            <form action="{{ route('logs.index') }}" method="GET">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label class="form-label">Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama user / deskripsi..." value="{{ request('search') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Aktivitas</label>
                        <select name="activity" class="form-control">
                            <option value="">Semua</option>
                            <option value="login" {{ request('activity') == 'login' ? 'selected' : '' }}>Login</option>
                            <option value="logout" {{ request('activity') == 'logout' ? 'selected' : '' }}>Logout</option>
                            <option value="create" {{ request('activity') == 'create' ? 'selected' : '' }}>Unggah</option>
                            <option value="update" {{ request('activity') == 'update' ? 'selected' : '' }}>Update</option>
                            <option value="delete" {{ request('activity') == 'delete' ? 'selected' : '' }}>Hapus</option>
                            <option value="view" {{ request('activity') == 'view' ? 'selected' : '' }}>Lihat</option>
                            <option value="download" {{ request('activity') == 'download' ? 'selected' : '' }}>Download</option>
                            <option value="approval" {{ request('activity') == 'approval' ? 'selected' : '' }}>Persetujuan</option>
                            <option value="rejection" {{ request('activity') == 'rejection' ? 'selected' : '' }}>Penolakan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="{{ route('logs.index') }}" class="btn" style="background: var(--secondary); color: white;">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="white-space: nowrap; font-size: 13px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td style="font-weight: 600;">{{ $log->user->name ?? 'System' }}</td>
                            <td>
                                @php
                                    $activityColors = [
                                        'login' => '#22c55e',
                                        'logout' => '#64748b',
                                        'create' => '#3b82f6',
                                        'update' => '#f59e0b',
                                        'delete' => '#ef4444',
                                        'view' => '#8b5cf6',
                                        'download' => '#06b6d4',
                                        'approval' => '#10b981',
                                        'rejection' => '#f43f5e',
                                    ];
                                    $color = $activityColors[$log->activity] ?? '#94a3b8';
                                @endphp
                                <span style="background: {{ $color }}; color: white; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                                    {{ $log->activity }}
                                </span>
                            </td>
                            <td style="font-size: 13px;">{{ $log->description }}</td>
                            <td style="font-size: 12px; color: var(--text-muted);">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Belum ada aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div style="padding: 15px;">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
