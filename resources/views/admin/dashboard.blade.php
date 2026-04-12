@extends('layouts.admin')

@section('title', 'Dashboard ' . $role)

@section('content')
    <!-- Stats Grid -->
    <div class="stats-grid">
        @if($role == 'Admin')
            <div class="stats-card">
                <div class="stats-info">
                    <h3>Total Dokumen</h3>
                    <p>{{ $stats['total_documents'] }}</p>
                </div>
                <div class="stats-icon bg-primary-light">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-info">
                    <h3>Kategori Dokumen</h3>
                    <p>{{ $stats['total_categories'] }}</p>
                </div>
                <div class="stats-icon bg-success-light">
                    <i class="fas fa-folder"></i>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-info">
                    <h3>Pengguna Sistem</h3>
                    <p>{{ $stats['total_users'] }}</p>
                </div>
                <div class="stats-icon bg-warning-light">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        @elseif($role == 'Tata Usaha')
            <div class="stats-card">
                <div class="stats-info">
                    <h3>Dokumen Unit Kerja</h3>
                    <p>{{ $stats['unit_documents'] }}</p>
                </div>
                <div class="stats-icon bg-primary-light">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-info">
                    <h3>Unggahan Saya</h3>
                    <p>{{ $stats['my_uploads'] }}</p>
                </div>
                <div class="stats-icon bg-success-light">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-info">
                    <h3>Dokumen Draft</h3>
                    <p>{{ $stats['document_by_status']['draft'] }}</p>
                </div>
                <div class="stats-icon bg-warning-light">
                    <i class="fas fa-edit"></i>
                </div>
            </div>
        @elseif($role == 'Kepala Sekolah')
            <div class="stats-card">
                <div class="stats-info">
                    <h3>Menunggu Persetujuan</h3>
                    <p>{{ $stats['pending_approvals'] }}</p>
                </div>
                <div class="stats-icon bg-danger-light" style="background-color: #fee2e2; color: #ef4444;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-info">
                    <h3>Total Dokumen</h3>
                    <p>{{ $stats['total_documents'] }}</p>
                </div>
                <div class="stats-icon bg-primary-light">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-info">
                    <h3>Disetujui Hari Ini</h3>
                    <p>{{ $stats['approved_by_me_count'] }}</p>
                </div>
                <div class="stats-icon bg-success-light">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        @endif
        
        <div class="stats-card">
            <div class="stats-info">
                <h3>Total Arsip</h3>
                <p>{{ $stats['total_documents'] }}</p>
            </div>
            <div class="stats-icon bg-info-light">
                <i class="fas fa-archive"></i>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left Table Section -->
        <div class="card">
            <div class="card-header">
                @if($role == 'Admin')
                    <h2 class="card-title">Aktivitas Sistem Terbaru</h2>
                @elseif($role == 'Tata Usaha')
                    <h2 class="card-title">Aktivitas Unggahan Saya</h2>
                @elseif($role == 'Kepala Sekolah')
                    <h2 class="card-title">Dokumen Baru Menunggu Persetujuan</h2>
                @endif
                
                @if($role == 'Admin')
                    <a href="{{ route('logs.index') }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Lihat Semua</a>
                @endif
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    @if($role == 'Admin')
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Aktivitas</th>
                                <th>Dokumen</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_logs'] as $log)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="user-avatar" style="width: 24px; height: 24px; font-size: 10px; background-color: var(--secondary);">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $log->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $log->activity }}</td>
                                    <td>{{ $log->document->title ?? '-' }}</td>
                                    <td>{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    @elseif($role == 'Tata Usaha')
                        <thead>
                            <tr>
                                <th>Dokumen</th>
                                <th>Aktivitas</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_my_activities'] as $log)
                                <tr>
                                    <td>{{ $log->document->title ?? '-' }}</td>
                                    <td>{{ $log->activity }}</td>
                                    <td>
                                        <span class="badge {{ $log->document && $log->document->status == 'disetujui' ? 'badge-success' : 'badge-warning' }}">
                                            {{ $log->document->status ?? 'unknown' }}
                                        </span>
                                    </td>
                                    <td>{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    @elseif($role == 'Kepala Sekolah')
                         <thead>
                            <tr>
                                <th>Judul Dokumen</th>
                                <th>Unit Kerja</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $pendingDocs = \App\Models\Document::where('status', 'diajukan')->latest()->take(5)->get();
                            @endphp
                            @foreach($pendingDocs as $doc)
                                <tr>
                                    <td>{{ $doc->title }}</td>
                                    <td>{{ $doc->unit->name ?? '-' }}</td>
                                    <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('approvals.index') }}" class="btn btn-primary" style="padding: 4px 8px; font-size: 11px;">Tinjau</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
        </div>

        <!-- Right Charts/Stats Section -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Status Dokumen {{ $role == 'Tata Usaha' ? 'Unit' : '' }}</h2>
            </div>
            <div style="padding: 24px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach(['disetujui' => 'success', 'diajukan' => 'info', 'draft' => 'warning', 'ditolak' => 'danger'] as $status => $color)
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
                                <span style="text-transform: capitalize;">{{ $status }}</span>
                                <span class="badge badge-{{ $color }}">{{ $stats['document_by_status'][$status] }}</span>
                            </div>
                            <div style="height: 8px; background-color: #f1f5f9; border-radius: 4px; overflow: hidden;">
                                @php
                                    $totalRelevant = array_sum($stats['document_by_status']);
                                    $percentage = $totalRelevant > 0 ? ($stats['document_by_status'][$status] / $totalRelevant * 100) : 0;
                                @endphp
                                <div style="height: 100%; background-color: var(--{{ $color }}); width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($role == 'Tata Usaha' || $role == 'Admin')
                <div style="margin-top: 30px;">
                    <a href="{{ route('documents.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> Unggah Dokumen Baru
                    </a>
                </div>
                @endif
                
                @if($role == 'Kepala Sekolah')
                <div style="margin-top: 30px;">
                    <a href="{{ route('approvals.index') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-check-circle"></i> Buka Antrian Persetujuan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
