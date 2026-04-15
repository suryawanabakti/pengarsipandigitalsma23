@extends('layouts.admin')

@section('title', 'Detail Dokumen')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin:0; font-size: 24px; font-weight: 700;">{{ $document->title }}</h1>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('documents.index') }}" class="btn" style="background: var(--secondary); color: white;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Unduh File
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Preview -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Pratinjau Dokumen</h2>
            </div>
            <div style="padding: 0; background: #525659; height: 700px; border-radius: 0 0 12px 12px; overflow: hidden;">
                @php
                    $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                    $officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
                    $fileUrl = asset('storage/' . $document->file_path);
                @endphp

                @if($extension === 'pdf')
                    <iframe src="{{ $fileUrl }}" width="100%" height="100%" style="border: none;"></iframe>
                @elseif(in_array($extension, $officeExtensions))
                    @if(!str_contains(request()->getHost(), 'localhost') && !str_contains(request()->getHost(), '127.0.0.1'))
                        <iframe src="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode($fileUrl) }}" width="100%" height="100%" style="border: none;"></iframe>
                    @else
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: white; text-align: center; padding: 40px;">
                            <i class="fas fa-file-word" style="font-size: 80px; margin-bottom: 20px; opacity: 0.5;"></i>
                            <h3 style="margin-bottom: 10px;">Pratinjau Dokumen Office</h3>
                            <p style="margin-bottom: 5px; opacity: 0.8;">Fitur pratinjau ini akan aktif otomatis setelah website <strong>Online</strong>.</p>
                            <p style="font-size: 13px; opacity: 0.6;">Di localhost, Microsoft tidak dapat mengakses file lokal Anda.</p>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-primary" style="margin-top: 25px;">
                                <i class="fas fa-download"></i> Unduh File
                            </a>
                        </div>
                    @endif
                @else
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: white; text-align: center; padding: 40px;">
                        <i class="fas fa-file-alt" style="font-size: 80px; margin-bottom: 20px; opacity: 0.5;"></i>
                        <h3>Pratinjau tidak tersedia</h3>
                        <p>Format file: <strong>{{ strtoupper($extension) }}</strong></p>
                        <p>Silakan unduh dokumen untuk melihat isi lengkapnya.</p>
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary" style="margin-top: 20px;">
                            <i class="fas fa-download"></i> Unduh Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Metadata & History -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Metadata -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Informasi Dokumen</h2>
                </div>
                <div style="padding: 24px;">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Status Terkini</label>
                        <span class="badge 
                            @if($document->status == 'disetujui') badge-success @elseif($document->status == 'diajukan') badge-info @elseif($document->status == 'ditolak') badge-danger @else badge-warning @endif" style="font-size: 14px; padding: 6px 15px;">
                            {{ strtoupper($document->status) }}
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Nomor Dokumen</label>
                            <div style="font-weight: 600;">{{ $document->document_number ?? '-' }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Kategori</label>
                            <div style="font-weight: 600;">{{ $document->category->name }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Unit Kerja</label>
                            <div style="font-weight: 600;">{{ $document->unit->name }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Jenis Arsip</label>
                            <div style="font-weight: 600;">{{ ucfirst($document->archive_type) }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Tanggal Dokumen</label>
                            <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($document->document_date)->translatedFormat('d F Y') }}</div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Kata Kunci</label>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
                                @foreach($document->tags as $tag)
                                    <span style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Version History -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Riwayat Versi</h2>
                </div>
                <div style="max-height: 300px; overflow-y: auto;">
                    @foreach($document->versions->sortByDesc('version_number') as $version)
                        <div style="padding: 15px; border-bottom: 1px solid #edf2f7; {{ $version->file_path == $document->file_path ? 'background: #f0f9ff;' : '' }}">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <span style="font-weight: 700; color: var(--primary);">Versi {{ $version->version_number }}</span>
                                <span style="font-size: 11px; color: var(--text-muted);">{{ $version->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="font-size: 12px; margin-bottom: 5px;">{{ $version->change_notes }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">Oleh: {{ $version->uploader->name }}</div>
                            @if($version->file_path != $document->file_path)
                                <a href="{{ asset('storage/' . $version->file_path) }}" target="_blank" style="font-size: 11px; color: var(--primary); text-decoration: none; display: inline-block; margin-top: 8px;">
                                    <i class="fas fa-external-link-alt"></i> Lihat File Lama
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
