@extends('layouts.admin')

@section('title', 'Manajemen Unit Kerja')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        <!-- Form Add -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Tambah Unit Kerja</h2>
            </div>
            <div style="padding: 24px;">
                <form action="{{ route('units.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Unit</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Contoh: Tata Usaha" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Simpan Unit
                    </button>
                </form>
            </div>
        </div>

        <!-- List Units -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Daftar Unit Kerja</h2>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Unit</th>
                            <th>Dokumen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $unit)
                            <tr>
                                <td>{{ $unit->name }}</td>
                                <td>{{ $unit->documents()->count() }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" class="btn"
                                            onclick="editUnit('{{ $unit->id }}', '{{ $unit->name }}')"
                                            style="padding: 6px; font-size: 12px; background: var(--warning); color: white;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('units.destroy', $unit) }}" method="POST"
                                            onsubmit="return confirm('Hapus unit ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn"
                                                style="padding: 6px; font-size: 12px; background: var(--danger); color: white;">
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
            @if ($units->hasPages())
                <div style="padding: 15px;">{{ $units->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Unit Kerja</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name" class="form-label">Nama Unit</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" style="background: var(--secondary); color: white;"
                        onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const modal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');
        const editName = document.getElementById('edit_name');

        function editUnit(id, name) {
            editName.value = name;
            editForm.action = `/admin/units/${id}`;
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
@endpush
