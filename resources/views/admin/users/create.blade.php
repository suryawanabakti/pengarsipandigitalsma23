@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">Form Tambah Pengguna</h2>
            <a href="{{ route('users.index') }}" class="btn" style="background: var(--secondary); color: white;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div style="padding: 30px;">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                    @error('name') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email <span style="color: var(--danger);">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="contoh@sma23.sch.id" value="{{ old('email') }}" required>
                    @error('email') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="role_id" class="form-label">Role Akses <span style="color: var(--danger);">*</span></label>
                    <select name="role_id" id="role_id" class="form-control" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required>
                    @error('password') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                </div>

                <div style="margin-top: 25px; padding: 15px; background: #f8fafc; border-radius: 8px;">
                    <label class="form-label" style="font-weight: 700; color: var(--primary); display: block; margin-bottom: 12px;">Hak Akses Dokumen</label>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="can_view" value="1" checked> Lihat Dokumen
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="can_upload" value="1"> Unggah Dokumen
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="can_edit" value="1"> Edit Dokumen
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="can_delete" value="1"> Hapus Dokumen
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; grid-column: span 2;">
                            <input type="checkbox" name="can_download" value="1" checked> Unduh Dokumen
                        </label>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary btn-block" style="padding: 15px;">
                        <i class="fas fa-save"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
