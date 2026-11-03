<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use DOMDocument;
use DOMXPath;
use setasign\Fpdi\Fpdi;
use FPDF;

class DocxQrStampService
{
    /**
     * Hasilkan gambar PNG QR Code dengan badge verifikasi resmi SIMKERMA UNIPAR.
     */
    public function generateQrCodePng(string $verifyUrl, int $size = 300, ?string $regId = null): string
    {
        $qr = new QRCode($verifyUrl, [
            's' => 'qrm', // Error correction level Medium (15%)
            'sf' => 6,    // Scale factor
        ]);

        $sourceImg = $qr->render_image();
        $srcW = imagesx($sourceImg);
        $srcH = imagesy($sourceImg);

        $canvasW = $size;
        $canvasH = (int)($size * 1.15); // Ruang bawah untuk teks identitas verifikasi resmi
        $targetImg = imagecreatetruecolor($canvasW, $canvasH);

        $white = imagecolorallocate($targetImg, 255, 255, 255);
        $blue = imagecolorallocate($targetImg, 13, 110, 253);
        $black = imagecolorallocate($targetImg, 30, 41, 59);
        $gray = imagecolorallocate($targetImg, 100, 116, 139);
        $border = imagecolorallocate($targetImg, 203, 213, 225);

        // Latar belakang putih dan bingkai tipis rapi
        imagefilledrectangle($targetImg, 0, 0, $canvasW, $canvasH, $white);
        imagerectangle($targetImg, 0, 0, $canvasW - 1, $canvasH - 1, $border);

        // Resample QR Code di bagian atas
        $qrSize = (int)($canvasW * 0.88);
        $qrX = (int)(($canvasW - $qrSize) / 2);
        $qrY = 8;
        imagecopyresampled($targetImg, $sourceImg, $qrX, $qrY, 0, 0, $qrSize, $qrSize, $srcW, $srcH);
        imagedestroy($sourceImg);

        // Teks identitas resmi UNIPAR di bawah QR Code
        $textTitle = "SIMKERMA UNIPAR";
        $x1 = (int)(($canvasW - (imagefontwidth(3) * strlen($textTitle))) / 2);
        imagestring($targetImg, 3, $x1, $qrY + $qrSize + 4, $textTitle, $blue);

        $textSub = "VERIFIKASI SAH DOKUMEN";
        $x2 = (int)(($canvasW - (imagefontwidth(2) * strlen($textSub))) / 2);
        imagestring($targetImg, 2, $x2, $qrY + $qrSize + 20, $textSub, $black);

        if ($regId) {
            $x3 = (int)(($canvasW - (imagefontwidth(1) * strlen($regId))) / 2);
            imagestring($targetImg, 1, $x3, $qrY + $qrSize + 34, $regId, $gray);
        }

        // Tangkap buffer PNG
        ob_start();
        imagepng($targetImg, null, 9);
        $pngData = ob_get_clean();
        imagedestroy($targetImg);

        return $pngData;
    }

    /**
     * Method Utama: Sematkan stempel QR Code ke naskah yang dikirim oleh pengusul.
     * Mengutamakan berkas draf yang dikirim (format PDF) dan menempatkan stempel
     * di bagian ujung kanan bawah halaman terakhir dokumen (A4 / F4).
     */
    public function stampDocument(Document $doc, ?string $inputPath = null, ?string $paperSize = null): array
    {
        // 1. Tentukan berkas draf yang diunggah pengusul
        $targetPath = null;
        if ($inputPath && file_exists($inputPath)) {
            $targetPath = $inputPath;
        } elseif (!empty($doc->draft_file) && file_exists(public_path($doc->draft_file))) {
            $targetPath = public_path($doc->draft_file);
        } elseif (!empty($doc->docx_path) && file_exists(public_path($doc->docx_path))) {
            $targetPath = public_path($doc->docx_path);
        }

        $ext = strtolower(pathinfo($targetPath ?? '', PATHINFO_EXTENSION));

        // Jika berkas yang dikirim berformat DOCX
        if ($ext === 'docx') {
            return $this->stampMoU($doc, $targetPath, $paperSize);
        }

        // Format standar utama pengusul: PDF (.pdf)
        return $this->stampPdf($doc, $targetPath, $paperSize);
    }

    /**
     * Sematkan stempel QR Code resmi ke berkas PDF yang dikirim oleh pengusul
     * di bagian ujung kanan bawah halaman terakhir dokumen (mendukung ukuran A4 dan F4).
     */
    public function stampPdf(Document $doc, ?string $inputPdfPath = null, ?string $paperSize = null): array
    {
        $regId = $doc->reg_id ?: 'REG-' . $doc->doc_id;
        $cleanRegId = preg_replace('/[^A-Za-z0-9_\-]/', '_', $regId);
        $verifyUrl = url('/verify/' . $regId);

        $paperSize = strtoupper($paperSize ?: $doc->paper_size ?: 'A4');
        if (!in_array($paperSize, ['A4', 'F4'])) {
            $paperSize = 'A4';
        }

        $uploadsDir = public_path('uploads/documents');
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        // Tentukan berkas sumber PDF yang dikirim dari draf pengusul
        $sourcePdf = null;
        if ($inputPdfPath && file_exists($inputPdfPath)) {
            $sourcePdf = $inputPdfPath;
        } elseif (!empty($doc->draft_file) && file_exists(public_path($doc->draft_file))) {
            $sourcePdf = public_path($doc->draft_file);
        } elseif (!empty($doc->signed_file) && file_exists(public_path($doc->signed_file))) {
            $sourcePdf = public_path($doc->signed_file);
        }

        // Jika belum ada berkas PDF draf, buatkan draf dasar pengusul
        if (!$sourcePdf || !file_exists($sourcePdf)) {
            $sourcePdf = $this->generateFallbackPdf($doc, $paperSize);
        }

        // 1. Buat gambar QR Code PNG resmi dengan badge SIMKERMA UNIPAR
        $qrPngBinary = $this->generateQrCodePng($verifyUrl, 320, $regId);
        $qrRelativePath = 'uploads/documents/qr_' . $cleanRegId . '.png';
        $qrAbsolutePath = public_path($qrRelativePath);
        file_put_contents($qrAbsolutePath, $qrPngBinary);

        // 2. Tentukan nama berkas hasil stempel resmi
        $timestamp = time();
        $stampedRelativePath = 'uploads/documents/Dokumen_' . $cleanRegId . '_' . $timestamp . '.pdf';
        $stampedAbsolutePath = public_path($stampedRelativePath);

        $stamped = false;

        // 3. Gunakan FPDI untuk mengimpor seluruh halaman naskah PDF asli yang dikirim pengusul
        // dan menyematkan QR Code di ujung kanan bawah halaman terakhir
        try {
            $fpdi = new Fpdi();
            $pageCount = $fpdi->setSourceFile($sourcePdf);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $fpdi->importPage($pageNo);
                $size = $fpdi->getTemplateSize($templateId);

                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($templateId);

                // Pada halaman terakhir: pasang stempel QR Code di ujung kanan bawah
                if ($pageNo === $pageCount) {
                    $stampWidth = 34; // mm (~3.4 cm)
                    $stampHeight = $stampWidth * 1.15; // mm (~3.91 cm)
                    $marginRight = 10; // mm dari tepi kanan kertas
                    $marginBottom = 10; // mm dari tepi bawah kertas

                    $x = $size['width'] - $stampWidth - $marginRight;
                    $y = $size['height'] - $stampHeight - $marginBottom;

                    $fpdi->Image($qrAbsolutePath, $x, $y, $stampWidth, $stampHeight);
                }
            }

            $fpdi->Output('F', $stampedAbsolutePath);
            $stamped = true;
        } catch (\Throwable $e) {
            Log::warning("FPDI stamp notice: " . $e->getMessage() . ". Mencoba fallback Word COM...");
        }

        // Fallback Microsoft Word COM jika PDF memerlukan perlakuan khusus
        if (!$stamped && class_exists('COM')) {
            $word = null;
            try {
                $word = new \COM("Word.Application");
                $word->Visible = false;
                $word->DisplayAlerts = 0;

                $docObj = $word->Documents->Open(realpath($sourcePdf), false, false);
                $shape = $docObj->Shapes->AddPicture($qrAbsolutePath, false, true);
                $shape->Name = "QRCode";
                $shape->RelativeHorizontalPosition = 0; // Margin
                $shape->RelativeVerticalPosition = 0;   // Margin
                $shape->Left = -999996; // wdShapeRight
                $shape->Top = -999997;  // wdShapeBottom
                $shape->Width = 96;
                $shape->Height = 110;

                $docObj->ExportAsFixedFormat($stampedAbsolutePath, 17);
                $docObj->Close(false);
                $stamped = true;
            } catch (\Throwable $e2) {
                Log::error("Word COM PDF stamp error: " . $e2->getMessage());
            } finally {
                if ($word) {
                    try { $word->Quit(); } catch (\Throwable $ignore) {}
                }
            }
        }

        // Jika tetap belum dapat diproses, salin berkas sumber agar tidak hilang
        if (!$stamped || !file_exists($stampedAbsolutePath)) {
            copy($sourcePdf, $stampedAbsolutePath);
        }

        // 4. Update model dokumen ke berkas PDF yang sudah dibubuhi stempel
        $doc->paper_size = $paperSize;
        $doc->qr_code_path = $qrRelativePath;
        $doc->draft_file = $stampedRelativePath;
        $doc->signed_file = $stampedRelativePath;
        $doc->save();

        return [
            'success' => true,
            'paper_size' => $paperSize,
            'pdf_path' => $stampedRelativePath,
            'qr_code_path' => $qrRelativePath,
            'verification_url' => $verifyUrl,
        ];
    }

    /**
     * Hasilkan PDF draf dasar pengusul jika tidak ada berkas yang diunggah.
     */
    protected function generateFallbackPdf(Document $doc, string $paperSize = 'A4'): string
    {
        $size = ($paperSize === 'F4') ? [215, 330] : 'A4';
        $pdf = new FPDF('P', 'mm', $size);
        $pdf->SetMargins(20, 20, 20);
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(0, 10, 'UNIVERSITAS PARNA RAYA MANADO', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 7, 'NASKAH DRAF ' . strtoupper($doc->doc_type ?: 'KERJA SAMA'), 0, 1, 'C');
        $pdf->Ln(4);
        $pdf->Line(20, $pdf->GetY(), ($paperSize === 'F4' ? 195 : 190), $pdf->GetY());
        $pdf->Ln(8);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Nomor Registrasi', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(0, 6, $doc->reg_id ?: '-', 0, 1);

        $pdf->Cell(50, 6, 'Nomor Dokumen', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(0, 6, $doc->doc_number ?: '---/UNIPAR/' . ($doc->doc_type ?: 'MoU') . '/X/' . date('Y'), 0, 1);

        $pdf->Cell(50, 6, 'Judul Kerja Sama', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->MultiCell(0, 6, $doc->title ?: 'Perjanjian Kerja Sama Kemitraan');

        $pdf->Cell(50, 6, 'Pihak Pertama (Pengusul)', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(0, 6, $doc->unit ?: 'Universitas Parna Raya Manado', 0, 1);

        $pdf->Cell(50, 6, 'Pihak Kedua (Mitra)', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->Cell(0, 6, ($doc->partner_name ?: 'Mitra Strategis') . ' (' . ($doc->partner_category ?: 'DUDI / Swasta') . ')', 0, 1);

        $pdf->Cell(50, 6, 'Ruang Lingkup', 0, 0);
        $pdf->Cell(5, 6, ':', 0, 0);
        $pdf->MultiCell(0, 6, $doc->scope ?: 'Kerja sama kemitraan Tridharma Perguruan Tinggi');

        $pdf->Ln(12);
        $pdf->MultiCell(0, 6, "Demikian naskah draf kerja sama ini disusun untuk ditelaah dan diverifikasi oleh pihak-pihak berwenang.");

        $fallbackPath = public_path('uploads/documents/draf_generated_' . ($doc->id ?: time()) . '.pdf');
        $pdf->Output('F', $fallbackPath);
        return $fallbackPath;
    }

    /**
     * Sematkan QR Code dinamis ke dalam berkas Word (.docx) di bagian ujung kanan bawah
     * halaman terakhir dokumen, dengan dukungan penuh untuk ukuran kertas A4 dan F4 (Folio).
     */
    public function stampMoU(Document $doc, ?string $inputDocxPath = null, ?string $paperSize = null): array
    {
        $regId = $doc->reg_id ?: 'REG-' . $doc->doc_id;
        $cleanRegId = preg_replace('/[^A-Za-z0-9_\-]/', '_', $regId);
        $verifyUrl = url('/verify/' . $regId);

        // Tentukan ukuran kertas (A4 vs F4)
        $paperSize = strtoupper($paperSize ?: $doc->paper_size ?: 'A4');
        if (!in_array($paperSize, ['A4', 'F4'])) {
            $paperSize = 'A4';
        }

        // 1. Buat direktori tujuan upload publik jika belum ada
        $uploadsDir = public_path('uploads/documents');
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        // Tentukan path berkas sumber
        $templatePath = public_path('templates/Template_MoU_UNIPAR.docx');
        $isUsingSystemTemplate = true;

        if ($inputDocxPath && file_exists($inputDocxPath)) {
            $sourceDocx = $inputDocxPath;
            $isUsingSystemTemplate = false;
        } elseif (!empty($doc->docx_path) && file_exists(public_path($doc->docx_path))) {
            $sourceDocx = public_path($doc->docx_path);
            $isUsingSystemTemplate = false;
        } elseif (file_exists($templatePath)) {
            $sourceDocx = $templatePath;
        } else {
            $fallback = 'D:/CLI/Skripsi/convo-4 01 Nov 2026 continous (Internal Chat)/Template MoU.docx';
            $sourceDocx = $fallback;
        }

        // 2. Hasilkan QR Code PNG resmi dengan badge SIMKERMA UNIPAR
        $qrPngBinary = $this->generateQrCodePng($verifyUrl, 320, $regId);
        $qrRelativePath = 'uploads/documents/qr_' . $cleanRegId . '.png';
        file_put_contents(public_path($qrRelativePath), $qrPngBinary);

        // 3. Gandakan berkas Word ke path berkas stempel ber-QR
        $timestamp = time();
        $docxRelativePath = 'uploads/documents/MoU_' . $cleanRegId . '_' . $timestamp . '.docx';
        $docxAbsolutePath = public_path($docxRelativePath);
        copy($sourceDocx, $docxAbsolutePath);

        // 4. Modifikasi arsip OpenXML (.docx via ZipArchive)
        $zip = new ZipArchive();
        if ($zip->open($docxAbsolutePath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            $rels = $zip->getFromName('word/_rels/document.xml.rels');

            // Daftarkan relasi gambar QR Code
            $relId = 'rIdQR99';
            if (strpos($rels, 'Target="media/image_qr.png"') === false) {
                $relTag = '<Relationship Id="' . $relId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/image_qr.png"/>';
                $rels = str_replace('</Relationships>', $relTag . '</Relationships>', $rels);
                $zip->deleteName('word/_rels/document.xml.rels');
                $zip->addFromString('word/_rels/document.xml.rels', $rels);
            }
            $zip->addFromString('word/media/image_qr.png', $qrPngBinary);

            // Parsing XML dokumen
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $xpath->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');
            $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $xpath->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
            $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

            // 4a. Bersihkan QR Code atau gambar di seluruh sel tabel tanda tangan
            $tables = $xpath->query('//w:tbl');
            foreach ($tables as $tbl) {
                if (strpos($tbl->textContent, 'Pihak Pertama') !== false || strpos($tbl->textContent, 'Rektor') !== false) {
                    $drawingsInTbl = $xpath->query('.//w:drawing', $tbl);
                    foreach ($drawingsInTbl as $d) {
                        $d->parentNode->removeChild($d);
                    }
                }
            }

            // 4b. Bersihkan QR Code lama yang mungkin pernah disematkan sebelumnya
            $existingQrDrawings = $xpath->query('//w:drawing[.//wp:docPr[@name="QRCode" or @name="QR Code"] or .//pic:cNvPr[@name="QRCode" or @name="QR Code"]]');
            foreach ($existingQrDrawings as $eq) {
                $pParent = $eq->parentNode;
                while ($pParent && $pParent->nodeName !== 'w:p') {
                    $pParent = $pParent->parentNode;
                }
                if ($pParent && $pParent->parentNode) {
                    $pParent->parentNode->removeChild($pParent);
                }
            }

            // 4c. Konfigurasi Ukuran Kertas Dokumen (A4 vs F4 / Folio)
            $pgSzNodes = $xpath->query('//w:pgSz');
            foreach ($pgSzNodes as $pgSz) {
                if ($paperSize === 'F4') {
                    $pgSz->setAttribute('w:w', '12189');
                    $pgSz->setAttribute('w:h', '18708');
                    if ($pgSz->hasAttribute('w:code')) {
                        $pgSz->removeAttribute('w:code');
                    }
                } else {
                    $pgSz->setAttribute('w:w', '11906');
                    $pgSz->setAttribute('w:h', '16838');
                    $pgSz->setAttribute('w:code', '9');
                }
            }

            // 4d. Sematkan QR Code di Bagian Ujung Kanan Bawah Halaman Terakhir Dokumen
            $body = $xpath->query('//w:body')->item(0);
            $lastSectPr = $xpath->query('.//w:sectPr[parent::w:body]', $body)->item(0);

            $cx = 765387;
            $cy = 880195;

            $drawingXml = '<w:p xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
                '<w:pPr>' .
                    '<w:spacing w:before="0" w:after="0" w:line="1" w:lineRule="exact"/>' .
                    '<w:jc w:val="right"/>' .
                '</w:pPr>' .
                '<w:r>' .
                    '<w:rPr><w:noProof/></w:rPr>' .
                    '<w:drawing>' .
                        '<wp:anchor distT="0" distB="0" distL="0" distR="0" simplePos="0" relativeHeight="251658240" behindDoc="0" locked="0" layoutInCell="1" allowOverlap="1">' .
                            '<wp:simplePos x="0" y="0"/>' .
                            '<wp:positionH relativeFrom="margin">' .
                                '<wp:align>right</wp:align>' .
                            '</wp:positionH>' .
                            '<wp:positionV relativeFrom="margin">' .
                                '<wp:align>bottom</wp:align>' .
                            '</wp:positionV>' .
                            '<wp:extent cx="' . $cx . '" cy="' . $cy . '"/>' .
                            '<wp:effectExtent l="0" t="0" r="0" b="0"/>' .
                            '<wp:wrapNone/>' .
                            '<wp:docPr id="999" name="QRCode"/>' .
                            '<wp:cNvGraphicFramePr>' .
                                '<a:graphicFrameLocks noChangeAspect="1"/>' .
                            '</wp:cNvGraphicFramePr>' .
                            '<a:graphic>' .
                                '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
                                    '<pic:pic>' .
                                        '<pic:nvPicPr>' .
                                            '<pic:cNvPr id="0" name="QR Code"/>' .
                                            '<pic:cNvPicPr><a:picLocks noChangeAspect="1" noChangeArrowheads="1"/></pic:cNvPicPr>' .
                                        '</pic:nvPicPr>' .
                                        '<pic:blipFill>' .
                                            '<a:blip r:embed="' . $relId . '" cstate="print"/>' .
                                            '<a:srcRect/>' .
                                            '<a:stretch><a:fillRect/></a:stretch>' .
                                        '</pic:blipFill>' .
                                        '<pic:spPr bwMode="auto">' .
                                            '<a:xfrm><a:off x="0" y="0"/><a:ext cx="' . $cx . '" cy="' . $cy . '"/></a:xfrm>' .
                                            '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                                            '<a:noFill/><a:ln><a:noFill/></a:ln>' .
                                        '</pic:spPr>' .
                                    '</pic:pic>' .
                                '</a:graphicData>' .
                            '</a:graphic>' .
                        '</wp:anchor>' .
                    '</w:drawing>' .
                '</w:r>' .
            '</w:p>';

            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML($drawingXml);

            if ($lastSectPr) {
                $body->insertBefore($fragment, $lastSectPr);
            } else {
                $body->appendChild($fragment);
            }

            $xml = $dom->saveXML();

            if ($isUsingSystemTemplate) {
                $docNumber = $doc->doc_number ?: '---/UNIPAR/MoU/X/' . date('Y');
                $partnerDocNumber = $doc->partner_doc_number ?: '---/MITRA/2026';
                $partnerNameUpper = strtoupper($doc->partner_name);
                $dateText = 'Pada hari ini ' . now()->translatedFormat('l') . ' tanggal ' . now()->translatedFormat('d F Y') . ' bertempat di Kota Manado';

                $patternNomor1 = '/Nomor:\s*_{5,}/u';
                if (preg_match($patternNomor1, $xml)) {
                    $xml = preg_replace($patternNomor1, 'Nomor: ' . htmlspecialchars($docNumber, ENT_XML1), $xml, 1);
                }

                $patternMitra = '/<w:t>_{5,}<\/w:t>/u';
                if (preg_match($patternMitra, $xml)) {
                    $xml = preg_replace($patternMitra, '<w:t>' . htmlspecialchars($partnerNameUpper, ENT_XML1) . '</w:t>', $xml, 1);
                }

                if (preg_match($patternNomor1, $xml)) {
                    $xml = preg_replace($patternNomor1, 'Nomor: ' . htmlspecialchars($partnerDocNumber, ENT_XML1), $xml, 1);
                }

                $patternTanggal = '/Pada hari ini ______.*?kami yan/su';
                if (preg_match($patternTanggal, $xml)) {
                    $xml = preg_replace($patternTanggal, htmlspecialchars($dateText, ENT_XML1) . ', kami yang bertanda tangan di bawah ini:', $xml, 1);
                }
            }

            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        }

        // 5. Konversi Berkas Word (.docx) ke PDF Resmi (.pdf) via Microsoft Word COM
        $pdfRelativePath = 'uploads/documents/MoU_' . $cleanRegId . '_' . $timestamp . '.pdf';
        $pdfAbsolutePath = public_path($pdfRelativePath);

        if (class_exists('COM')) {
            $word = null;
            try {
                $word = new \COM("Word.Application");
                $word->Visible = false;
                $word->DisplayAlerts = 0;

                $docObj = $word->Documents->Open(realpath($docxAbsolutePath), false, false);
                $docObj->ExportAsFixedFormat($pdfAbsolutePath, 17);
                $docObj->Close(false);
            } catch (\Throwable $e) {
                Log::warning("Word COM PDF conversion notice: " . $e->getMessage());
            } finally {
                if ($word) {
                    try {
                        $word->Quit();
                        if (function_exists('com_release')) {
                            com_release($word);
                        }
                    } catch (\Throwable $e) {}
                }
            }
        }

        // 6. Update Model Dokumen secara menyeluruh
        $doc->paper_size = $paperSize;
        $doc->docx_path = $docxRelativePath;
        $doc->qr_code_path = $qrRelativePath;
        if (file_exists($pdfAbsolutePath)) {
            $doc->draft_file = $pdfRelativePath;
            $doc->signed_file = $pdfRelativePath;
        }
        $doc->save();

        return [
            'success' => true,
            'paper_size' => $paperSize,
            'docx_path' => $docxRelativePath,
            'pdf_path' => file_exists($pdfAbsolutePath) ? $pdfRelativePath : null,
            'qr_code_path' => $qrRelativePath,
            'verification_url' => $verifyUrl,
        ];
    }
}
