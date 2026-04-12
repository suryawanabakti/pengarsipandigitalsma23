@extends('layouts.admin')

@section('title', 'Manajemen Tag')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        <!-- Form Add -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Tambah Tag Baru</h2>
            </div>
            <div style="padding: 24px;">
                <form action="{{ route('tags.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Tag</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Sangat Penting" required>
                        @error('name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Simpan Tag
                    </button>
                </form>
            </div>
        </div>

        <!-- List Tags -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Daftar Tag</h2>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Tag</th>
                            <th>Penggunaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                            <tr>
                                <td>
                                    <span style="background: #e2e8f0; padding: 4px 10px; border-radius: 6px; font-weight: 600;">
                                        # {{ $tag->name }}
                                    </span>
                                </td>
                                <td>{{ $tag->documents()->count() }} Dokumen</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Hapus tag ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn" style="padding: 6px; font-size: 12px; background: var(--danger); color: white;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($tags->hasPages())
                <div style="padding: 15px;">{{ $tags->links() }}</div>
            @endif
        </div>
    </div>
@endsection
