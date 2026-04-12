<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::latest()->paginate(10);
        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:tags']);
        Tag::create($request->all());
        return back()->with('success', 'Tag berhasil ditambahkan.');
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate(['name' => 'required|string|max:255|unique:tags,name,' . $tag->id]);
        $tag->update($request->all());
        return back()->with('success', 'Tag berhasil diperbarui.');
    }

    public function destroy(Tag $tag)
    {
        $tag->documents()->detach();
        $tag->delete();
        return back()->with('success', 'Tag berhasil dihapus.');
    }
}
