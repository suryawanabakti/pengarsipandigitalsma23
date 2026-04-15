@extends('layouts.admin')

@section('title', 'Unggah Dokumen Sekaligus (Bulk)')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h2 class="card-title">Unggah Dokumen Kolektif</h2>
            <a href="{{ route('documents.index') }}" class="btn" style="background: var(--secondary); color: white;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div style="padding: 30px;">
            <div class="alert alert-info" style="margin-bottom: 25px; background: #e0f2fe; color: #0369a1; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd;">
                <i class="fas fa-info-circle"></i> <strong>Tips:</strong> Nama file akan otomatis digunakan sebagai Judul Dokumen. Anda bisa mengubahnya nanti setelah berhasil diunggah.
            </div>

            <form action="{{ route('documents.bulk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="files" class="form-label">Pilih Satu atau Banyak File <span style="color: var(--danger);">*</span></label>
                    <div style="border: 2px dashed var(--border); padding: 30px; text-align: center; border-radius: 12px; cursor: pointer; transition: 0.3s;" id="dropzone" onclick="document.getElementById('files').click()">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 40px; color: var(--primary); margin-bottom: 10px;"></i>
                        <p style="margin: 0;">Seret & lepas file di sini atau <strong>klik untuk memilih</strong></p>
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">PDF, DOC, DOCX, XLS, XLSX (Maksimal 10MB per file)</p>
                        <input type="file" name="files[]" id="files" class="form-control" style="display: none;" multiple required onchange="showSelectedFiles(this)">
                    </div>
                    <div id="file-list" style="margin-top: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;"></div>
                    @error('files')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 25px;">
                    <h3 style="font-size: 14px; margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Metadata Umum untuk Semua File</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="category_id" class="form-label">Kategori <span style="color: var(--danger);">*</span></label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="unit_id" class="form-label">Unit Kerja <span style="color: var(--danger);">*</span></label>
                            <select name="unit_id" id="unit_id" class="form-control" required>
                                <option value="">Pilih Unit</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="stage" class="form-label">Tahapan <span style="color: var(--danger);">*</span></label>
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
                            <label for="archive_type" class="form-label">Jenis Arsip <span style="color: var(--danger);">*</span></label>
                            <select name="archive_type" id="archive_type" class="form-control" required>
                                <option value="dinamis" {{ old('archive_type', 'dinamis') == 'dinamis' ? 'selected' : '' }}>Dinamis</option>
                                <option value="statis" {{ old('archive_type') == 'statis' ? 'selected' : '' }}>Statis</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kata Kunci (Tags)</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; padding: 10px; border: 1px solid var(--border); border-radius: 8px; background: white;">
                            @foreach ($tags as $tag)
                                <label style="display: flex; align-items: center; gap: 5px; font-size: 13px; cursor: pointer;">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}">
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary btn-block" style="padding: 15px;">
                        <i class="fas fa-upload"></i> Unggah Sekarang (<span id="file-count">0</span> file)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showSelectedFiles(input) {
            const list = document.getElementById('file-list');
            const countLabel = document.getElementById('file-count');
            list.innerHTML = '';
            
            if (input.files) {
                countLabel.innerText = input.files.length;
                for (let i = 0; i < input.files.length; i++) {
                    const file = input.files[i];
                    const item = document.createElement('div');
                    item.style.padding = '8px 12px';
                    item.style.background = '#f1f5f9';
                    item.style.borderRadius = '6px';
                    item.style.fontSize = '12px';
                    item.style.display = 'flex';
                    item.style.alignItems = 'center';
                    item.style.gap = '8px';
                    item.innerHTML = `<i class="far fa-file"></i> <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${file.name}</span>`;
                    list.appendChild(item);
                }
            }
        }

        const dropzone = document.getElementById('dropzone');
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = 'var(--primary)';
            dropzone.style.background = '#f0f9ff';
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.style.borderColor = 'var(--border)';
            dropzone.style.background = 'transparent';
        });
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = 'var(--border)';
            dropzone.style.background = 'transparent';
            const files = e.dataTransfer.files;
            document.getElementById('files').files = files;
            showSelectedFiles(document.getElementById('files'));
        });
    </script>
@endsection
