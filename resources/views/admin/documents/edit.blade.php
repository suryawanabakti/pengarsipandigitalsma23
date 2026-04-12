@extends('layouts.admin')

@section('title', 'Edit Dokumen')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">Form Update Dokumen</h2>
            <a href="{{ route('documents.index') }}" class="btn" style="background: var(--secondary); color: white;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div style="padding: 30px;">
            <form action="{{ route('documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title" class="form-label">Judul Dokumen <span
                            style="color: var(--danger);">*</span></label>
                    <input type="text" name="title" id="title" class="form-control"
                        placeholder="Masukkan judul dokumen" value="{{ old('title', $document->title) }}" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="document_number" class="form-label">Nomor Surat / Dokumen</label>
                        <input type="text" name="document_number" id="document_number" class="form-control"
                            placeholder="Contoh: SK/001/2024"
                            value="{{ old('document_number', $document->document_number) }}">
                        @error('document_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="document_date" class="form-label">Tanggal Dokumen</label>
                        <input type="date" name="document_date" id="document_date" class="form-control"
                            value="{{ old('document_date', $document->document_date ? \Carbon\Carbon::parse($document->document_date)->format('Y-m-d') : date('Y-m-d')) }}">
                        @error('document_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori <span
                                style="color: var(--danger);">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $document->category_id) == $cat->id ? 'selected' : '' }}>
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
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id', $document->unit_id) == $unit->id ? 'selected' : '' }}>
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
                            <option value="draft" {{ old('stage', $document->stage) == 'draft' ? 'selected' : '' }}>Draft
                            </option>
                            <option value="final" {{ old('stage', $document->stage) == 'final' ? 'selected' : '' }}>Final
                            </option>
                            <option value="arsip" {{ old('stage', $document->stage) == 'arsip' ? 'selected' : '' }}>Arsip
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status <span style="color: var(--danger);">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="draft" {{ old('status', $document->status) == 'draft' ? 'selected' : '' }}>
                                Draft</option>
                            <option value="diajukan"
                                {{ old('status', $document->status) == 'diajukan' ? 'selected' : '' }}>Diajukan</option>

                        </select>
                    </div>

                    <div class="form-group">
                        <label for="archive_type" class="form-label">Jenis Arsip <span
                                style="color: var(--danger);">*</span></label>
                        <select name="archive_type" id="archive_type" class="form-control" required>
                            <option value="dinamis"
                                {{ old('archive_type', $document->archive_type) == 'dinamis' ? 'selected' : '' }}>Dinamis
                            </option>
                            <option value="statis"
                                {{ old('archive_type', $document->archive_type) == 'statis' ? 'selected' : '' }}>Statis
                            </option>
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
                                    {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="file" class="form-label">File Dokumen (Kosongkan jika tidak ingin ganti file)</label>
                    <input type="file" name="file" id="file" class="form-control">
                    <div style="margin-top: 8px; font-size: 13px; color: var(--text-muted);">
                        File saat ini: <a href="{{ route('documents.download', $document) }}" target="_blank"
                            style="color: var(--primary); font-weight: 600;">{{ $document->file_name }}</a>
                    </div>
                    @error('file')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="change_notes" class="form-label">Catatan Perubahan (Opsional)</label>
                    <textarea name="change_notes" id="change_notes" class="form-control" rows="3"
                        placeholder="Apa yang Anda ubah dalam versi ini?"></textarea>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary btn-block" style="padding: 15px;">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Versions History -->
        <div style="padding: 0 30px 30px;">
            <h3 style="font-size: 16px; margin-bottom: 15px;">Riwayat Versi</h3>
            <div class="table-responsive" style="border: 1px solid var(--border); border-radius: 8px;">
                <table class="table" style="margin-bottom: 0;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th>Versi</th>
                            <th>Waktu</th>
                            <th>Oleh</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($document->versions()->latest()->get() as $version)
                            <tr>
                                <td>v{{ $version->version_number }}</td>
                                <td>{{ $version->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $version->uploader->name ?? '-' }}</td>
                                <td>{{ $version->change_notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
