@extends('layouts.admin')

@section('title', 'Persetujuan Dokumen')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Antrean Persetujuan Arsip</h2>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Diajukan</th>
                        <th>Judul Dokumen</th>
                        <th>Kategori</th>
                        <th>Unit Pengirim</th>
                        <th>Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>{{ $doc->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $doc->title }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">{{ $doc->document_number ?? '-' }}</div>
                            </td>
                            <td>{{ $doc->category->name }}</td>
                            <td>{{ $doc->unit->name }}</td>
                            <td>{{ $doc->uploader->name }}</td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('documents.show', $doc) }}" class="btn" style="background: var(--primary); color: white; padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> Review
                                    </a>
                                    
                                    <button onclick="openApprovalModal('{{ $doc->id }}', '{{ $doc->title }}')" class="btn" style="background: var(--success); color: white; padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-check"></i> Disetujui
                                    </button>

                                    <button onclick="openRejectModal('{{ $doc->id }}', '{{ $doc->title }}')" class="btn" style="background: var(--danger); color: white; padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 60px; color: var(--text-muted);">
                                <i class="fas fa-check-circle" style="font-size: 40px; margin-bottom: 15px; display: block; opacity: 0.3;"></i>
                                Tidak ada dokumen yang menunggu persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
            <div style="padding: 15px;">{{ $documents->links() }}</div>
        @endif
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div style="background: white; width: 400px; margin: 10% auto; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="padding: 20px; background: var(--success); color: white; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin:0;">Setujui Dokumen</h3>
                <span onclick="closeModals()" style="cursor:pointer;"><i class="fas fa-times"></i></span>
            </div>
            <form id="approvalForm" method="POST" style="padding:20px;">
                @csrf
                <p id="approvalText" style="margin-bottom: 20px; font-size: 14px;"></p>
                <div class="form-group">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="closeModals()" class="btn btn-secondary" style="flex:1;">Batal</button>
                    <button type="submit" class="btn btn-success" style="flex:2;">Confirm Setujui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div style="background: white; width: 400px; margin: 10% auto; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <div style="padding: 20px; background: var(--danger); color: white; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin:0;">Tolak Dokumen</h3>
                <span onclick="closeModals()" style="cursor:pointer;"><i class="fas fa-times"></i></span>
            </div>
            <form id="rejectForm" method="POST" style="padding:20px;">
                @csrf
                <p id="rejectText" style="margin-bottom: 20px; font-size: 14px;"></p>
                <div class="form-group">
                    <label class="form-label">Alasan Penolakan <span style="color:red;">*</span></label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Sebutkan alasan penolakan..." required></textarea>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="closeModals()" class="btn btn-secondary" style="flex:1;">Batal</button>
                    <button type="submit" class="btn btn-danger" style="flex:2;">Confirm Tolak</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openApprovalModal(id, title) {
            document.getElementById('approvalForm').action = `/admin/approvals/${id}/approve`;
            document.getElementById('approvalText').innerText = `Apakah Anda yakin ingin menyetujui dokumen "${title}"?`;
            document.getElementById('approvalModal').style.display = 'block';
        }

        function openRejectModal(id, title) {
            document.getElementById('rejectForm').action = `/admin/approvals/${id}/reject`;
            document.getElementById('rejectText').innerText = `Apakah Anda yakin ingin menolak dokumen "${title}"?`;
            document.getElementById('rejectModal').style.display = 'block';
        }

        function closeModals() {
            document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModals();
            }
        }
    </script>
    @endpush
@endsection
