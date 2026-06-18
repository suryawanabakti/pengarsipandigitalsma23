<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;
use App\Models\User;
use App\Models\DocumentCategory;
use App\Models\ActivityLog;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role->name;
        
        $stats = [
            'total_documents' => Document::count(),
            'document_by_status' => [
                'draft' => Document::where('status', 'draft')->count(),
                'diajukan' => Document::where('status', 'diajukan')->count(),
                'disetujui' => Document::where('status', 'disetujui')->count(),
                'ditolak' => Document::where('status', 'ditolak')->count(),
            ]
        ];

        if ($role == 'Admin') {
            $stats['total_users'] = User::count();
            $stats['total_categories'] = DocumentCategory::count();
            $stats['recent_logs'] = ActivityLog::with(['user', 'document'])->latest()->take(5)->get();
        } elseif ($role == 'Tata Usaha') {
            $stats['my_uploads'] = Document::where('uploaded_by', $user->id)->count();
            $stats['unit_documents'] = Document::where('unit_id', $user->unit_id)->count();
            $stats['recent_my_activities'] = ActivityLog::where('user_id', $user->id)->with('document')->latest()->take(5)->get();
            
            // Adjust document counts to unit-specific
            $stats['document_by_status'] = [
                'draft' => Document::where('unit_id', $user->unit_id)->where('status', 'draft')->count(),
                'diajukan' => Document::where('unit_id', $user->unit_id)->where('status', 'diajukan')->count(),
                'disetujui' => Document::where('unit_id', $user->unit_id)->where('status', 'disetujui')->count(),
                'ditolak' => Document::where('unit_id', $user->unit_id)->where('status', 'ditolak')->count(),
            ];
        } elseif (in_array($role, ['Kepala Sekolah', 'Kepala Tata Usaha'])) {
            $stats['pending_approvals'] = Document::where('status', 'diajukan')->count();
            $stats['approved_by_me_count'] = ActivityLog::where('user_id', $user->id)->where('activity', 'approve')->count();
            $stats['recent_approvals'] = Document::whereIn('status', ['disetujui', 'ditolak'])->latest()->take(5)->get();
        }

        // Dashboard Search
        $searchResults = null;
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $searchResults = Document::with(['category', 'unit', 'uploader'])
                ->where(function($q) use ($searchQuery) {
                    $q->where('title', 'like', '%' . $searchQuery . '%')
                      ->orWhere('document_number', 'like', '%' . $searchQuery . '%')
                      ->orWhere('ditujukan_kepada', 'like', '%' . $searchQuery . '%');
                })
                ->latest()
                ->take(10)
                ->get();
        }

        return view('admin.dashboard', compact('stats', 'role', 'searchResults', 'searchQuery'));
    }
}
