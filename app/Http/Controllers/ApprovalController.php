<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentApproval;
use App\Helpers\ActivityLogger;

class ApprovalController extends Controller
{
    public function index()
    {

        // Only allow Kepala Sekolah or Admin to see this
        if (!in_array(auth()->user()->role->name, ['Admin', 'Kepala Sekolah', 'Humas', 'Tata Usaha'])) {
            abort(403);
        }

        $documents = Document::with(['category', 'unit', 'uploader'])
            ->where('status', 'diajukan')
            ->latest()
            ->paginate(10);

        return view('admin.approvals.index', compact('documents'));
    }

    public function approve(Request $request, Document $document)
    {
        $document->update(['status' => 'disetujui']);

        DocumentApproval::create([
            'document_id' => $document->id,
            'approved_by' => auth()->id(),
            'status' => 'approved',
            'notes' => $request->notes,
            'approved_at' => now(),
        ]);

        ActivityLogger::log('approval', "Dokumen disetujui: {$document->title}", $document->id);

        return redirect()->route('approvals.index')->with('success', 'Dokumen berhasil disetujui.');
    }

    public function reject(Request $request, Document $document)
    {
        $request->validate(['notes' => 'required|string']);

        $document->update(['status' => 'ditolak']);

        DocumentApproval::create([
            'document_id' => $document->id,
            'approved_by' => auth()->id(),
            'status' => 'rejected',
            'notes' => $request->notes,
            'approved_at' => now(),
        ]);

        ActivityLogger::log('rejection', "Dokumen ditolak: {$document->title}", $document->id);

        return redirect()->route('approvals.index')->with('success', 'Dokumen telah ditolak.');
    }
}
