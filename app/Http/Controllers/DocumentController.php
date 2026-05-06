<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Unit;
use App\Models\Tag;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_view) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat dokumen.');
        }

        $query = Document::with(['category', 'unit', 'uploader', 'tags']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('document_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $documents = $query->latest()->paginate(10);
        $categories = DocumentCategory::all();

        return view('admin.documents.index', compact('documents', 'categories'));
    }

    public function create()
    {
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_upload) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunggah dokumen.');
        }

        $categories = DocumentCategory::all();
        $units = Unit::all();
        $tags = Tag::all();
        return view('admin.documents.create', compact('categories', 'units', 'tags'));
    }

    public function bulkCreate()
    {
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_upload) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunggah dokumen.');
        }

        $categories = DocumentCategory::all();
        $units = Unit::all();
        $tags = Tag::all();
        return view('admin.documents.bulk', compact('categories', 'units', 'tags'));
    }

    public function bulkStore(Request $request)
    {
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_upload) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunggah dokumen.');
        }

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'category_id' => 'required|exists:document_categories,id',
            'unit_id' => 'required|exists:units,id',
            'stage' => 'required|in:draft,final,arsip',
            'status' => 'required|in:draft,diajukan,disetujui,ditolak',
            'archive_type' => 'required|in:dinamis,statis',
        ]);

        $count = 0;
        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $title = pathinfo($originalName, PATHINFO_FILENAME);
            $fileName = Str::slug($title) . '_' . time() . '_' . $count . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $document = Document::create([
                'title' => $title,
                'file_name' => $originalName,
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'document_number' => null,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'uploaded_by' => auth()->id(),
                'stage' => $request->stage,
                'status' => $request->status,
                'archive_type' => $request->archive_type,
                'document_date' => now(),
            ]);

            if ($request->tags) {
                $document->tags()->attach($request->tags);
            }

            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => 1,
                'file_path' => $filePath,
                'uploaded_by' => auth()->id(),
                'change_notes' => 'Bulk upload'
            ]);

            ActivityLogger::log('create', "Bulk Upload: Berhasil mengunggah dokumen: {$document->title}", $document->id);
            $count++;
        }

        return redirect()->route('documents.index')->with('success', "{$count} dokumen berhasil diunggah secara kolektif.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'category_id' => 'required|exists:document_categories,id',
            'unit_id' => 'required|exists:units,id',
            'stage' => 'required|in:draft,final,arsip',
            'status' => 'required|in:draft,diajukan,disetujui,ditolak',
            'archive_type' => 'required|in:dinamis,statis',
        ]);

        $file = $request->file('file');
        $fileName = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document = Document::create([
            'title' => $request->title,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getClientMimeType(),
            'document_number' => $request->document_number,
            'ditujukan_kepada' => $request->ditujukan_kepada,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'uploaded_by' => auth()->id(),
            'stage' => $request->stage,
            'status' => $request->status,
            'archive_type' => $request->archive_type,
            'document_date' => $request->document_date ?? now(),
        ]);

        if ($request->tags) {
            $document->tags()->attach($request->tags);
        }

        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => 1,
            'file_path' => $filePath,
            'uploaded_by' => auth()->id(),
            'change_notes' => 'Initial upload'
        ]);

        ActivityLogger::log('create', "Berhasil mengunggah dokumen: {$document->title}", $document->id);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    public function show(Document $document)
    {
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_view) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat dokumen ini.');
        }

        $document->load(['category', 'unit', 'uploader', 'tags', 'versions.uploader']);
        ActivityLogger::log('view', "Melihat detail/pratinjau dokumen: {$document->title}", $document->id);
        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        // Permission check
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_edit) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah dokumen.');
        }

        // Freeze check: Approved documents cannot be edited (Admin bypass optionally removed or kept)
        if ($document->status == 'disetujui' && auth()->user()->role->name !== 'Admin') {
            abort(403, 'Dokumen yang telah disetujui tidak dapat diubah.');
        }

        // Only Owner can edit if not Admin
        if (auth()->user()->role->name !== 'Admin' && $document->uploaded_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengubah dokumen ini.');
        }

        $categories = DocumentCategory::all();
        $units = Unit::all();
        $tags = Tag::all();
        $selectedTags = $document->tags->pluck('id')->toArray();
        return view('admin.documents.edit', compact('document', 'categories', 'units', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Document $document)
    {
        // Permission check
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_edit) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah dokumen.');
        }

        // Freeze check: Approved documents cannot be updated
        if ($document->status == 'disetujui' && auth()->user()->role->name !== 'Admin') {
            abort(403, 'Dokumen yang telah disetujui tidak dapat diperbarui.');
        }

        // Only Owner can update if not Admin
        if (auth()->user()->role->name !== 'Admin' && $document->uploaded_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki wewenang untuk memperbarui dokumen ini.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'category_id' => 'required|exists:document_categories,id',
            'unit_id' => 'required|exists:units,id',
            'stage' => 'required|in:draft,final,arsip',
            'status' => 'required|in:draft,diajukan,disetujui,ditolak',
            'archive_type' => 'required|in:dinamis,statis',
        ]);

        $data = $request->except('file', 'tags');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $filePath;
            $data['file_type'] = $file->getClientMimeType();

            // Create new version
            $latestVersion = $document->versions()->max('version_number') ?? 0;
            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $latestVersion + 1,
                'file_path' => $filePath,
                'uploaded_by' => auth()->id(),
                'change_notes' => $request->change_notes ?? 'File updated'
            ]);
        }

        $document->update($data);

        if ($request->tags) {
            $document->tags()->sync($request->tags);
        }

        ActivityLogger::log('update', "Memperbarui dokumen: {$document->title}", $document->id);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document)
    {
        // Permission check
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_delete) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus dokumen.');
        }

        // Freeze check: Approved documents cannot be deleted
        if ($document->status == 'disetujui' && auth()->user()->role->name !== 'Admin') {
            abort(403, 'Dokumen yang telah disetujui tidak dapat dihapus.');
        }

        // Only Owner can delete if not Admin
        if (auth()->user()->role->name !== 'Admin' && $document->uploaded_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki wewenang untuk menghapus dokumen ini.');
        }

        // Delete files
        Storage::disk('public')->delete($document->file_path);
        foreach ($document->versions as $version) {
            Storage::disk('public')->delete($version->file_path);
        }

        ActivityLogger::log('delete', "Menghapus dokumen: {$document->title}");
        
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(Document $document)
    {
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_download) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh dokumen.');
        }

        ActivityLogger::log('download', "Mengunduh file dokumen: {$document->title}", $document->id);

        $filePath = Storage::disk('public')->path($document->file_path);

        // Watermark PDF files
        if (str_ends_with(strtolower($document->file_path), '.pdf')) {
            return $this->downloadWithWatermark($document, $filePath);
        }

        // Watermark DOCX / Word files
        if (str_ends_with(strtolower($document->file_path), '.docx')) {
            return $this->downloadWithWordWatermark($document, $filePath);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Generate a watermarked copy of a PDF and stream it to the browser.
     */
    private function downloadWithWatermark(Document $document, string $originalPath)
    {
        $pdf = new \App\Helpers\FpdiAlpha();
        $pdf->SetAutoPageBreak(false);
        $pageCount = $pdf->setSourceFile($originalPath);

        $user = auth()->user();
        $footerText = 'Diunduh oleh: ' . $user->name . ' pada ' . now()->format('d F Y, H:i:s') . ' | Sistem Pengarsipan Digital SMAN 23 Makassar';

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Center Logo Watermark with Alpha Transparency
            $logoPath = public_path('sm23logo.png');
            if (file_exists($logoPath)) {
                $pdf->SetAlpha(0.15); // Semi-transparent
                $logoW = 100; 
                $logoH = 100;
                $centerX = ($size['width'] - $logoW) / 2;
                $centerY = ($size['height'] - $logoH) / 2;
                $pdf->Image($logoPath, $centerX, $centerY, $logoW, $logoH);
                $pdf->SetAlpha(1); // Reset
            }

            // Footer watermark
            $pdf->SetFont('Helvetica', '', 7);
            $pdf->SetTextColor(170, 170, 170);
            $pdf->Text(10, $size['height'] - 5, $footerText);
        }

        $tempPath = storage_path('app/temp_watermarked_' . time() . '.pdf');
        $pdf->Output('F', $tempPath);

        return response()->download($tempPath, $document->file_name)->deleteFileAfterSend(true);
    }

    /**
     * Generate a watermarked copy of a DOCX and stream it to the browser.
     * Creates a transparent PNG watermark image, injects it via PhpWord,
     * and adds a metadata footer to every section.
     */
    private function downloadWithWordWatermark(Document $document, string $originalPath)
    {
        $user = auth()->user();
        $footerText = 'Diunduh oleh: ' . $user->name . ' pada ' . now()->format('d F Y, H:i:s') . ' | Sistem Pengarsipan Digital SMAN 23 Makassar';

        // 1) Generate a transparent PNG with diagonal watermark text
        $watermarkImage = $this->createWatermarkImage('SMAN 23 MKS');

        // 2) Load the original document
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($originalPath);

        // 3) Apply watermark + footer to every section
        foreach ($phpWord->getSections() as $section) {

            // Center logo watermark in the header (behind content)
            $header = $section->addHeader();
            $logoPath = public_path('sm23logo.png');
            if (file_exists($logoPath)) {
                $header->addWatermark($logoPath, [
                    'width'            => 300,
                    'height'           => 300,
                    'marginTop'        => 0,
                    'marginLeft'       => 0,
                    'posHorizontal'    => 'center',
                    'posHorizontalRel' => 'page',
                    'posVertical'      => 'center',
                    'posVerticalRel'   => 'page',
                    'wrappingStyle'    => 'behind',
                ]);
            }

            // Metadata footer
            $footer = $section->addFooter();
            $footer->addText(
                $footerText,
                [
                    'size'   => 7,
                    'color'  => 'AAAAAA',
                    'name'   => 'Arial',
                    'italic' => true,
                ],
                [
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                ]
            );
        }

        // 4) Save to temp file and stream the download
        $tempPath = storage_path('app/temp_watermarked_' . time() . '.docx');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        // Clean up the temporary watermark image
        @unlink($watermarkImage);

        return response()->download($tempPath, $document->file_name)->deleteFileAfterSend(true);
    }

    /**
     * Create a transparent PNG image with angled watermark text using GD.
     * Returns the absolute path to the generated image.
     */
    private function createWatermarkImage(string $text): string
    {
        $width  = 800;
        $height = 500;

        $image = imagecreatetruecolor($width, $height);

        // Enable alpha blending and save full alpha channel
        imagesavealpha($image, true);
        imagealphablending($image, false);

        // Fill with fully transparent background
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $transparent);

        imagealphablending($image, true);

        // Semi-transparent grey for the watermark text
        $grey = imagecolorallocatealpha($image, 180, 180, 180, 80);

        // Draw the watermark text at an angle
        $fontSize = 5; // built-in font size (1-5)
        $stepX = 120;
        $stepY = 80;

        for ($y = -$height; $y < $height * 2; $y += $stepY) {
            for ($x = -$width; $x < $width * 2; $x += $stepX) {
                imagestring($image, $fontSize, $x, $y, $text, $grey);
            }
        }

        // Rotate to create diagonal effect
        $rotated = imagerotate($image, 35, $transparent);
        imagesavealpha($rotated, true);

        $path = storage_path('app/watermark_' . time() . '_' . mt_rand(1000, 9999) . '.png');
        imagepng($rotated, $path);

        imagedestroy($image);
        imagedestroy($rotated);

        return $path;
    }

    /**
     * Download a specific version of a document.
     */
    public function downloadVersion(\App\Models\DocumentVersion $version)
    {
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_download) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh dokumen.');
        }

        $document = $version->document;
        ActivityLogger::log('download', "Mengunduh file dokumen versi v{$version->version_number}: {$document->title}", $document->id);

        $filePath = Storage::disk('public')->path($version->file_path);

        // Watermark PDF files
        if (str_ends_with(strtolower($version->file_path), '.pdf')) {
            return $this->downloadWithWatermark($document, $filePath);
        }

        // Watermark DOCX / Word files
        if (str_ends_with(strtolower($version->file_path), '.docx')) {
            return $this->downloadWithWordWatermark($document, $filePath);
        }

        return Storage::disk('public')->download($version->file_path, "v{$version->version_number}_{$document->file_name}");
    }

    /**
     * Restore a document to a specific version.
     */
    public function restoreVersion(\App\Models\DocumentVersion $version)
    {
        $document = $version->document;

        // Permission check
        if (auth()->user()->role->name !== 'Admin' && !auth()->user()->can_edit) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah dokumen.');
        }

        // Freeze check
        if ($document->status == 'disetujui' && auth()->user()->role->name !== 'Admin') {
            abort(403, 'Dokumen yang telah disetujui tidak dapat diubah.');
        }

        // Only Owner can edit if not Admin
        if (auth()->user()->role->name !== 'Admin' && $document->uploaded_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengubah dokumen ini.');
        }

        // Update document with version's file info
        $document->update([
            'file_path' => $version->file_path,
        ]);

        // Create a new version record for the restoration
        $latestVersion = $document->versions()->max('version_number') ?? 0;
        \App\Models\DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $latestVersion + 1,
            'file_path' => $version->file_path,
            'uploaded_by' => auth()->id(),
            'change_notes' => "Direstorasi ke versi v{$version->version_number}"
        ]);

        ActivityLogger::log('update', "Merestorasi dokumen {$document->title} ke versi v{$version->version_number}", $document->id);

        return redirect()->back()->with('success', "Dokumen berhasil direstorasi ke versi v{$version->version_number}.");
    }
}
