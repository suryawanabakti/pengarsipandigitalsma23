@extends('layouts.admin')

@section('title', 'Manajemen Dokumen')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Dokumen</h2>
            @if(auth()->user()->role->name == 'Admin' || auth()->user()->can_upload)
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('documents.bulk') }}" class="btn" style="background: var(--secondary); color: white;">
                        <i class="fas fa-folder-plus"></i> Unggah Kolektif
                    </a>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Unggah Dokumen
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Filters -->
        <div style="padding: 20px; border-bottom: 1px solid var(--border);">
            <form action="{{ route('documents.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari judul atau nomor surat..." value="{{ request('search') }}">
                </div>
                <div style="width: 180px;">
                    <select name="category_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 150px;">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'status']))
                    <a href="{{ route('documents.index') }}" class="btn" style="background-color: var(--secondary); color: white;">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul Dokumen</th>
                        <th>Nomor</th>
                        <th>Kategori</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Tags</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $doc->title }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    Diunggah oleh: {{ $doc->uploader->name }} ({{ $doc->created_at->format('d M Y') }})
                                </div>
                            </td>
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
                            <td>
                                @foreach($doc->tags as $tag)
                                    <span style="font-size: 10px; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; margin-right: 2px;">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('documents.show', $doc) }}" class="btn" style="padding: 6px; font-size: 12px; background: var(--primary); color: white;" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('documents.download', $doc) }}" class="btn" style="padding: 6px; font-size: 12px; background: var(--info); color: white;" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @if($doc->status !== 'disetujui' && (auth()->user()->role->name == 'Admin' || $doc->uploaded_by == auth()->id()))
                                        <a href="{{ route('documents.edit', $doc) }}" class="btn" style="padding: 6px; font-size: 12px; background: var(--warning); color: white;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn" style="padding: 6px; font-size: 12px; background: var(--danger); color: white;" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fas fa-file-excel" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                                Tidak ada dokumen ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div style="padding: 20px; border-top: 1px solid var(--border);">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
@endsection
