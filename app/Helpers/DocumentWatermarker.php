<?php

namespace App\Helpers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

class DocumentWatermarker
{
    /**
     * Apply watermark to a document file.
     * 
     * @param Document $document
     * @return bool
     */
    public static function watermark(Document $document)
    {
        $filePath = Storage::disk('public')->path($document->file_path);
        
        if (!file_exists($filePath)) {
            return false;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $logoPath = public_path('sm23logo.png');

        if (!file_exists($logoPath)) {
            return false;
        }

        try {
            if ($extension === 'pdf') {
                return self::watermarkPdf($filePath, $logoPath);
            } elseif (in_array($extension, ['doc', 'docx'])) {
                return self::watermarkWord($filePath, $logoPath);
            }
        } catch (\Exception $e) {
            \Log::error("Watermarking failed for document {$document->id}: " . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Watermark PDF using FpdiAlpha.
     */
    private static function watermarkPdf($filePath, $logoPath)
    {
        $pdf = new FpdiAlpha();
        $pageCount = $pdf->setSourceFile($filePath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            
            // 1. Draw Template (The original document content)
            $pdf->useTemplate($templateId);

            // 2. Set Alpha for the Logo (Transparency)
            $pdf->SetAlpha(0.15); // 15% opacity to avoid covering text

            // 3. Add Logo in Center
            $logoW = 100;
            $logoH = 100;
            $centerX = ($size['width'] - $logoW) / 2;
            $centerY = ($size['height'] - $logoH) / 2;
            $pdf->Image($logoPath, $centerX, $centerY, $logoW, $logoH);

            // 4. Reset Alpha
            $pdf->SetAlpha(1);
        }

        $pdf->Output('F', $filePath);
        return true;
    }

    /**
     * Watermark Word using PHPWord.
     */
    private static function watermarkWord($filePath, $logoPath)
    {
        $phpWord = IOFactory::load($filePath);

        foreach ($phpWord->getSections() as $section) {
            $header = $section->addHeader();
            
            // Add watermark image centered behind text
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

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);
        
        return true;
    }
}
