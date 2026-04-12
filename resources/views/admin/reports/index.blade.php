@extends('layouts.admin')

@section('title', 'Laporan Pengarsipan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Filter Laporan Dokumen</h2>
        </div>
        
        <div style="padding: 24px;">
            <form action="{{ route('reports.index') }}" method="GET">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit Kerja</label>
                        <select name="unit_id" class="form-control">
                            <option value="">Semua Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('reports.index') }}" class="btn" style="background: var(--secondary); color: white;">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('reports.print', request()->all()) }}" target="_blank" class="btn" style="background: var(--dark); color: white;">
                        <i class="fas fa-print"></i> Cetak Laporan
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top: 30px;">
        <div class="card-header">
            <h2 class="card-title">Preview Data Laporan ({{ $documents->count() }} Dokumen)</h2>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Dokumen</th>
                        <th>Judul Dokumen</th>
                        <th>Nomor</th>
                        <th>Kategori</th>
                        <th>Unit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($doc->document_date)->format('d/m/Y') }}</td>
                            <td style="font-weight: 600;">{{ $doc->title }}</td>
                            <td>{{ $doc->document_number ?? '-' }}</td>
                            <td>{{ $doc->category->name }}</td>
                            <td>{{ $doc->unit->name }}</td>
                            <td>
                                <span class="badge 
                                    @if($doc->status == 'disetujui') badge-success 
                                    @elseif($doc->status == 'diajukan') badge-info 
                                    @elseif($doc->status == 'ditolak') badge-danger 
                                    @else badge-warning @endif">
                                    {{ $doc->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Tidak ada data untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
