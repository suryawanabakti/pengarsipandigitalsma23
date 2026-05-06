@extends('layouts.admin')

@section('title', 'Unggah Dokumen Baru')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">Form Unggah Dokumen</h2>
            <a href="{{ route('documents.index') }}" class="btn" style="background: var(--secondary); color: white;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div style="padding: 30px;">
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="title" class="form-label">Judul Dokumen <span
                            style="color: var(--danger);">*</span></label>
                    <input type="text" name="title" id="title" class="form-control"
                        placeholder="Masukkan judul dokumen" value="{{ old('title') }}" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="document_number" class="form-label">Nomor Surat / Dokumen</label>
                        <input type="text" name="document_number" id="document_number" class="form-control"
                            placeholder="Contoh: SK/001/2024" value="{{ old('document_number') }}">
                        @error('document_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="document_date" class="form-label">Tanggal Dokumen</label>
                        <input type="date" name="document_date" id="document_date" class="form-control"
                            value="{{ old('document_date', date('Y-m-d')) }}">
                        @error('document_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="ditujukan_kepada" class="form-label">Ditujukan Kepada</label>
                    <input type="text" name="ditujukan_kepada" id="ditujukan_kepada" class="form-control"
                        placeholder="Contoh: Kepala Sekolah, Wakil Kepala Sekolah, Semua Guru" value="{{ old('ditujukan_kepada') }}">
                    @error('ditujukan_kepada')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori <span
                                style="color: var(--danger);">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="unit_id" class="form-label">Unit Kerja <span
                                style="color: var(--danger);">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-control" required>
                            <option value="">Pilih Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="stage" class="form-label">Tahapan <span
                                style="color: var(--danger);">*</span></label>
                        <select name="stage" id="stage" class="form-control" required>
                            <option value="draft" {{ old('stage') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="final" {{ old('stage') == 'final' ? 'selected' : '' }}>Final</option>
                            <option value="arsip" {{ old('stage') == 'arsip' ? 'selected' : '' }}>Arsip</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status <span style="color: var(--danger);">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="diajukan" {{ old('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label for="archive_type" class="form-label">Jenis Arsip <span
                                style="color: var(--danger);">*</span></label>
                        <select name="archive_type" id="archive_type" class="form-control" required>
                            <option value="dinamis" {{ old('archive_type', 'dinamis') == 'dinamis' ? 'selected' : '' }}>
                                Dinamis</option>
                            <option value="statis" {{ old('archive_type') == 'statis' ? 'selected' : '' }}>Statis</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kata Kunci (Tags)</label>
                    <div
                        style="display: flex; flex-wrap: wrap; gap: 10px; padding: 10px; border: 1px solid var(--border); border-radius: 8px;">
                        @foreach ($tags as $tag)
                            <label style="display: flex; align-items: center; gap: 5px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                    {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="file" class="form-label">File Dokumen <span
                            style="color: var(--danger);">*</span></label>
                    <input type="file" name="file" id="file" class="form-control" required>
                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">Format yang didukung: PDF, DOC,
                        DOCX, XLS, XLSX (Maksimal 10MB)</p>
                    @error('file')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary btn-block" style="padding: 15px;">
                        <i class="fas fa-upload"></i> Unggah Dokumen sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
