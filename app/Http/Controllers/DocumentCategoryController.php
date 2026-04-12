<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DocumentCategory;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:document_categories']);
        DocumentCategory::create($request->all());
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, DocumentCategory $category)
    {
        $request->validate(['name' => 'required|string|max:255|unique:document_categories,name,' . $category->id]);
        $category->update($request->all());
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(DocumentCategory $category)
    {
        if ($category->documents()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki dokumen.');
        }
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
