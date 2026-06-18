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
        // Only allow Kepala Tata Usaha to view approval queue
        if (auth()->user()->role->name !== 'Kepala Tata Usaha') {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $documents = Document::with(['category', 'unit', 'uploader'])
            ->where('status', 'diajukan')
            ->latest()
            ->paginate(10);

        return view('admin.approvals.index', compact('documents'));
    }

    public function approve(Request $request, Document $document)
    {
        // Only Kepala Tata Usaha can approve documents
        if (auth()->user()->role->name !== 'Kepala Tata Usaha') {
            abort(403, 'Hanya Kepala Tata Usaha yang dapat menyetujui dokumen.');
        }

        $document->update(['status' => 'disetujui']);

        // Apply permanent watermark upon approval
        \App\Helpers\DocumentWatermarker::watermark($document);

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
        // Only Kepala Tata Usaha can reject documents
        if (auth()->user()->role->name !== 'Kepala Tata Usaha') {
            abort(403, 'Hanya Kepala Tata Usaha yang dapat menolak dokumen.');
        }

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
