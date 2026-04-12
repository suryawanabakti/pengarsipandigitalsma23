<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Unit;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(10);
        return view('admin.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:units']);
        Unit::create($request->all());
        return back()->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate(['name' => 'required|string|max:255|unique:units,name,' . $unit->id]);
        $unit->update($request->all());
        return back()->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->documents()->count() > 0 || $unit->users()->count() > 0) {
            return back()->with('error', 'Unit tidak dapat dihapus karena masih terkait dengan dokumen atau user.');
        }
        $unit->delete();
        return back()->with('success', 'Unit kerja berhasil dihapus.');
    }
}
