<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Unit;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = DocumentCategory::all();
        $units = Unit::all();

        $query = Document::with(['category', 'unit', 'uploader']);

        if ($request->filled('start_date')) {
            $query->whereDate('document_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('document_date', '<=', $request->end_date);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->latest()->get();

        return view('admin.reports.index', compact('documents', 'categories', 'units'));
    }

    public function print(Request $request)
    {
        $query = Document::with(['category', 'unit', 'uploader']);

        if ($request->filled('start_date')) {
            $query->whereDate('document_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('document_date', '<=', $request->end_date);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->latest()->get();
        $filters = $request->only(['start_date', 'end_date', 'category_id', 'unit_id', 'status']);
        
        $categoryName = $request->filled('category_id') ? DocumentCategory::find($request->category_id)->name : 'Semua Kategori';
        $unitName = $request->filled('unit_id') ? Unit::find($request->unit_id)->name : 'Semua Unit';

        return view('admin.reports.print', compact('documents', 'filters', 'categoryName', 'unitName'));
    }
}
