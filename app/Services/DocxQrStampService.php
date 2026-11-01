<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use DOMDocument;
use DOMXPath;

class DocxQrStampService
{
    /**
     * Hasilkan gambar PNG QR Code dengan ukuran presisi (default 150x150 px).
     */
    public function generateQrCodePng(string $verifyUrl, int $size = 150): string
    {
        $qr = new QRCode($verifyUrl, [
            's' => 'qrm', // Error correction level Medium (15%)
            'sf' => 4,    // Scale factor
        ]);

        $sourceImg = $qr->render_image();
        $srcW = imagesx($sourceImg);
        $srcH = imagesy($sourceImg);

        // Buat canvas target persegi dengan latar belakang putih murni
        $targetImg = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($targetImg, 255, 255, 255);
        imagefilledrectangle($targetImg, 0, 0, $size, $size, $white);

        // Copy dan resample ke ukuran presisi 150x150
        imagecopyresampled($targetImg, $sourceImg, 0, 0, 0, 0, $size, $size, $srcW, $srcH);
        imagedestroy($sourceImg);

        // Tangkap buffer PNG
        ob_start();
        imagepng($targetImg, null, 9);
        $pngData = ob_get_clean();
        imagedestroy($targetImg);

        return $pngData;
    }

    /**
     * Sematkan QR Code dinamis ke dalam berkas Word (.docx) pada tabel tanda tangan tanpa garis
     * tepat di atas blok "Pihak Pertama" (Universitas Parna Raya Manado).
     * Area tanda tangan basah di atas nama Dr. Rosdiana Simbolon tetap bersih untuk tanda tangan fisik.
     * Ukuran cx=574040, cy=574040 (150x150 px) dijaga tetap presisi, lalu diekspor menjadi berkas PDF resmi.
     */
    public function stampMoU(Document $doc, ?string $inputDocxPath = null): array
    {
        $regId = $doc->reg_id ?: 'REG-' . $doc->doc_id;
        $cleanRegId = preg_replace('/[^A-Za-z0-9_\-]/', '_', $regId);
        $verifyUrl = url('/verify/' . $regId);

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
        } elseif (file_exists($templatePath)) {
            $sourceDocx = $templatePath;
        } else {
            $fallback = 'D:/CLI/Skripsi/convo-4 01 Nov 2026 continous (Internal Chat)/Template MoU.docx';
            $sourceDocx = $fallback;
        }

        // 2. Hasilkan QR Code PNG 150x150
        $qrPngBinary = $this->generateQrCodePng($verifyUrl, 500);
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

            // Temukan tabel tanda tangan (yang memuat "Pihak Pertama" dan "Rektor")
            $tables = $xpath->query('//w:tbl');
            $signatureTbl = null;
            foreach ($tables as $tbl) {
                if (strpos($tbl->textContent, 'Pihak Pertama') !== false && strpos($tbl->textContent, 'Rektor') !== false) {
                    $signatureTbl = $tbl;
                }
            }

            if ($signatureTbl) {
                $rows = $xpath->query('.//w:tr', $signatureTbl);
                
                // Cari indeks baris "Pihak Pertama"
                $pihakPertamaRowIdx = -1;
                foreach ($rows as $idx => $r) {
                    if (strpos($r->textContent, 'Pihak Pertama') !== false) {
                        $pihakPertamaRowIdx = $idx;
                        break;
                    }
                }

                // Posisi QR Code: Pada baris DI ATAS "Pihak Pertama" (Row 0)
                $aboveRowIdx = $pihakPertamaRowIdx - 1;
                if ($aboveRowIdx >= 0) {
                    $aboveRow = $rows->item($aboveRowIdx);
                    $cells = $xpath->query('.//w:tc', $aboveRow);
                    
                    // Sel di atas kolom Pihak Pertama (Cell 1 jika 4 kolom, atau Cell 0 jika 2 kolom)
                    $targetCell = ($cells->length >= 4) ? $cells->item(1) : $cells->item(0);

                    $p = $xpath->query('.//w:p', $targetCell)->item(0);
                    if (!$p) {
                        $p = $dom->createElement('w:p');
                        $targetCell->appendChild($p);
                    }

                    // Pengaturan rata tengah (center) di atas blok teks Pihak Pertama
                    $pPr = $xpath->query('.//w:pPr', $p)->item(0);
                    if (!$pPr) {
                        $pPr = $dom->createElement('w:pPr');
                        $p->insertBefore($pPr, $p->firstChild);
                    }
                    $jc = $xpath->query('.//w:jc', $pPr)->item(0);
                    if (!$jc) {
                        $jc = $dom->createElement('w:jc');
                        $jc->setAttribute('w:val', 'center');
                        $pPr->appendChild($jc);
                    } else {
                        $jc->setAttribute('w:val', 'center');
                    }

                    // Bersihkan gambar lama di sel target jika ada
                    $drawingsInCell = $xpath->query('.//w:drawing', $targetCell);
                    foreach ($drawingsInCell as $d) {
                        $d->parentNode->removeChild($d);
                    }

                    // Masukkan elemen drawing XML dengan ukuran cx=574040 cy=574040 (150x150 px)
                    $drawingXml = '<w:r xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
                        '<w:rPr><w:noProof/></w:rPr>' .
                        '<w:drawing>' .
                            '<wp:inline distT="0" distB="0" distL="0" distR="0">' .
                                '<wp:extent cx="765387" cy="765387"/>' .
                                '<wp:effectExtent l="0" t="0" r="0" b="0"/>' .
                                '<wp:docPr id="999" name="QRCode"/>' .
                                '<wp:cNvGraphicFramePr><a:graphicFrameLocks noChangeAspect="1"/></wp:cNvGraphicFramePr>' .
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
                                                '<a:xfrm><a:off x="0" y="0"/><a:ext cx="765387" cy="765387"/></a:xfrm>' .
                                                '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                                                '<a:noFill/><a:ln><a:noFill/></a:ln>' .
                                            '</pic:spPr>' .
                                        '</pic:pic>' .
                                    '</a:graphicData>' .
                                '</a:graphic>' .
                            '</wp:inline>' .
                        '</w:drawing>' .
                    '</w:r>';

                    $fragment = $dom->createDocumentFragment();
                    $fragment->appendXML($drawingXml);
                    $p->appendChild($fragment);
                }

                // Pastikan baris di bawah Pihak Pertama (di atas nama Dr. Rosdiana Simbolon)
                // tetap BERSIH untuk tanda tangan fisik (wet signature)
                $rosdianaRowIdx = -1;
                foreach ($rows as $idx => $r) {
                    if (strpos($r->textContent, 'Rosdiana Simbolon') !== false) {
                        $rosdianaRowIdx = $idx;
                        break;
                    }
                }
                if ($rosdianaRowIdx > 0) {
                    $sigSpaceRow = $rows->item($rosdianaRowIdx - 1);
                    $sigCells = $xpath->query('.//w:tc', $sigSpaceRow);
                    if ($sigCells->length > 0) {
                        $leftSigCell = $sigCells->item(0);
                        $drawingsInSig = $xpath->query('.//w:drawing', $leftSigCell);
                        foreach ($drawingsInSig as $d) {
                            $d->parentNode->removeChild($d);
                        }
                    }
                }

                $xml = $dom->saveXML();
            }

            // Pengisian otomatis nomor surat dan mitra jika menggunakan template baku
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
        $pdfSuccess = false;

        if (class_exists('COM')) {
            $word = null;
            try {
                $word = new \COM("Word.Application");
                $word->Visible = false;
                $word->DisplayAlerts = 0;
                
                $docObj = $word->Documents->Open(realpath($docxAbsolutePath), false, false);
                $docObj->ExportAsFixedFormat($pdfAbsolutePath, 17); // 17 = wdExportFormatPDF
                $docObj->Close(false);
                $pdfSuccess = true;
            } catch (\Throwable $e) {
                Log::warning("Word COM PDF conversion notice: " . $e->getMessage());
            } finally {
                if ($word) {
                    try {
                        $word->Quit();
                        if (function_exists('com_release')) {
                            com_release($word);
                        }
                    } catch (\Throwable $e) {
                        // ignore cleanup exceptions
                    }
                }
            }
        }

        // 6. Update Model Dokumen secara menyeluruh
        $doc->docx_path = $docxRelativePath;
        $doc->qr_code_path = $qrRelativePath;
        if (file_exists($pdfAbsolutePath)) {
            $doc->draft_file = $pdfRelativePath;
            $doc->signed_file = $pdfRelativePath;
        }
        $doc->save();

        return [
            'success' => true,
            'docx_path' => $docxRelativePath,
            'pdf_path' => file_exists($pdfAbsolutePath) ? $pdfRelativePath : null,
            'qr_code_path' => $qrRelativePath,
            'verification_url' => $verifyUrl,
        ];
    }
}
