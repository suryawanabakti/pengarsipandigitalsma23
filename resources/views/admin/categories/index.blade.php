@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        <!-- Form Add -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Tambah Kategori</h2>
            </div>
            <div style="padding: 24px;">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Surat Keputusan" required>
                        @error('name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Simpan Kategori
                    </button>
                </form>
            </div>
        </div>

        <!-- List Categories -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Daftar Kategori</h2>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Dokumen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                            <tr>
                                <td>{{ $cat->name }}</td>
                                <td>{{ $cat->documents()->count() }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        {{-- Simple inline edit could be here, but for now just delete --}}
                                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
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
            @if($categories->hasPages())
                <div style="padding: 15px;">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
@endsection
