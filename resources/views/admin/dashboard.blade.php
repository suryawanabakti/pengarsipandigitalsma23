@extends('layouts.admin')

@section('title', 'Dashboard ' . $role)

@push('styles')
<style>
    .stats-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--primary);
    }
    .stats-card:active {
        transform: translateY(-2px);
    }
    .status-item:hover {
        background: rgba(0, 0, 0, 0.04);
        transform: translateX(5px);
    }
</style>
@endpush

@section('content')
    <!-- Search Bar -->
    <div class="card" style="margin-bottom: 24px; overflow: visible;">
        <div style="padding: 24px;">
            <form action="{{ route('admin.dashboard') }}" method="GET" style="display: flex; gap: 12px; align-items: center;">
                <div style="position: relative; flex: 1;">
                    <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
                    <input type="text" name="search" class="form-control" 
                        placeholder="Cari dokumen berdasarkan judul, nomor surat, atau ditujukan..." 
                        value="{{ $searchQuery ?? '' }}"
                        style="padding-left: 44px; padding-right: 16px; height: 48px; font-size: 15px; border-radius: 12px; border: 2px solid var(--border); transition: all 0.3s;">
                </div>
                <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 24px; border-radius: 12px; font-size: 15px;">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(!empty($searchQuery))
                    <a href="{{ route('admin.dashboard') }}" class="btn" style="height: 48px; padding: 0 18px; border-radius: 12px; background: var(--secondary); color: white; font-size: 14px;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Search Results -->
    @if(!empty($searchQuery) && $searchResults !== null)
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-search" style="color: var(--primary); margin-right: 8px;"></i>
                    Hasil Pencarian: "{{ $searchQuery }}"
                    <span class="badge badge-info" style="margin-left: 8px;">{{ $searchResults->count() }} ditemukan</span>
                </h2>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul Dokumen</th>
                            <th>Nomor</th>
                            <th>Ditujukan Kepada</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($searchResults as $doc)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $doc->title }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        {{ $doc->uploader->name ?? '-' }} · {{ $doc->created_at->format('d M Y') }}
                                    </div>
                                </td>
                                <td>{{ $doc->document_number ?? '-' }}</td>
                                <td>{{ $doc->ditujukan_kepada ?? '-' }}</td>
                                <td>{{ $doc->category->name ?? '-' }}</td>
                                <td>
                                    <span class="badge 
                                        @if($doc->status == 'disetujui') badge-success 
                                        @elseif($doc->status == 'diajukan') badge-info 
                                        @elseif($doc->status == 'ditolak') badge-danger 
                                        @else badge-warning @endif">
                                        {{ $doc->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('documents.show', $doc) }}" class="btn" style="padding: 6px 10px; font-size: 12px; background: var(--primary); color: white;">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    <i class="fas fa-search" style="font-size: 40px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                                    Tidak ada dokumen ditemukan untuk pencarian "{{ $searchQuery }}".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="stats-grid">
        @if($role == 'Admin')
            <div class="stats-card" onclick="window.location.href='{{ route('documents.index') }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Total Dokumen</h3>
                    <p>{{ $stats['total_documents'] }}</p>
                </div>
                <div class="stats-icon bg-primary-light">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>

            <div class="stats-card" onclick="window.location.href='{{ route('categories.index') }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Kategori Dokumen</h3>
                    <p>{{ $stats['total_categories'] }}</p>
                </div>
                <div class="stats-icon bg-success-light">
                    <i class="fas fa-folder"></i>
                </div>
            </div>

            <div class="stats-card" onclick="window.location.href='{{ route('users.index') }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Pengguna Sistem</h3>
                    <p>{{ $stats['total_users'] }}</p>
                </div>
                <div class="stats-icon bg-warning-light">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        @elseif($role == 'Tata Usaha')
            <div class="stats-card" onclick="window.location.href='{{ route('documents.index', ['unit_id' => auth()->user()->unit_id]) }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Dokumen Unit Kerja</h3>
                    <p>{{ $stats['unit_documents'] }}</p>
                </div>
                <div class="stats-icon bg-primary-light">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="stats-card" onclick="window.location.href='{{ route('documents.index', ['uploaded_by' => auth()->id()]) }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Unggahan Saya</h3>
                    <p>{{ $stats['my_uploads'] }}</p>
                </div>
                <div class="stats-icon bg-success-light">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
            </div>
            <div class="stats-card" onclick="window.location.href='{{ route('documents.index', ['status' => 'draft', 'unit_id' => auth()->user()->unit_id]) }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Dokumen Draft</h3>
                    <p>{{ $stats['document_by_status']['draft'] }}</p>
                </div>
                <div class="stats-icon bg-warning-light">
                    <i class="fas fa-edit"></i>
                </div>
            </div>
        @elseif($role == 'Kepala Sekolah')
            <div class="stats-card" onclick="window.location.href='{{ route('approvals.index') }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Menunggu Persetujuan</h3>
                    <p>{{ $stats['pending_approvals'] }}</p>
                </div>
                <div class="stats-icon bg-danger-light" style="background-color: #fee2e2; color: #ef4444;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stats-card" onclick="window.location.href='{{ route('documents.index') }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Total Dokumen</h3>
                    <p>{{ $stats['total_documents'] }}</p>
                </div>
                <div class="stats-icon bg-primary-light">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stats-card" onclick="window.location.href='{{ route('approvals.index') }}'" style="cursor: pointer;">
                <div class="stats-info">
                    <h3>Disetujui Hari Ini</h3>
                    <p>{{ $stats['approved_by_me_count'] }}</p>
                </div>
                <div class="stats-icon bg-success-light">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        @endif
        
        <div class="stats-card" onclick="window.location.href='{{ route('documents.index', $role == 'Tata Usaha' ? ['unit_id' => auth()->user()->unit_id] : []) }}'" style="cursor: pointer;">
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
                        <div onclick="window.location.href='{{ route('documents.index', ['status' => $status] + ($role == 'Tata Usaha' ? ['unit_id' => auth()->user()->unit_id] : [])) }}'" 
                             style="cursor: pointer; padding: 8px; border-radius: 8px; transition: all 0.2s;"
                             class="status-item">
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
