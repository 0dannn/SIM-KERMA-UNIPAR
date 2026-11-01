<?php
require 'D:/CLI/Skripsi/SIMKERMA_Laravel11/vendor/autoload.php';
$app = require_once 'D:/CLI/Skripsi/SIMKERMA_Laravel11/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\Partner;
use App\Models\AuditTrail;
use App\Services\DocxQrStampService;
use App\Services\FSMWorkflowEngineService;
use Illuminate\Http\Request;
use App\Http\Controllers\SimkermaController;

echo "========================================================\n";
echo "PENGUJIAN END-TO-END FITUR DRAF TEMPLATE & STEMPEL QR (REVISI)\n";
echo "========================================================\n";

// 1. Uji Ketersediaan Berkas Template Draf Publik & Pastikan QR Code TELAH DIHAPUS
$templatePublicPath = public_path('templates/Template_MoU_UNIPAR.docx');
if (file_exists($templatePublicPath)) {
    echo "[PASS] Berkas Template Draf Publik ditemukan: " . filesize($templatePublicPath) . " bytes\n";
    
    // Verifikasi bahwa template TIDAK memiliki gambar QR Code (bersih)
    $z = new ZipArchive();
    $z->open($templatePublicPath);
    $xml = $z->getFromName('word/document.xml');
    $rels = $z->getFromName('word/_rels/document.xml.rels');
    $hasImg2 = $z->locateName('word/media/image2.png') !== false;
    $z->close();
    
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    $drawings = $xpath->query('//w:drawing');
    
    if (!$hasImg2 && strpos($rels, 'image2.png') === false && $drawings->length === 1) {
        echo "[PASS] QR Code pada Template Draf Publik TELAH DIHAPUS secara bersih (hanya memuat logo UNIPAR)!\n";
    } else {
        echo "[FAIL] Template draf publik masih memuat gambar QR Code!\n";
        exit(1);
    }
} else {
    echo "[FAIL] Berkas Template Draf Publik TIDAK ditemukan di: " . $templatePublicPath . "\n";
    exit(1);
}

// 2. Buat Draf Dokumen Baru
$testDocId = 'UNIPAR-' . date('Y') . '-' . rand(700, 999);
$testRegId = 'REG-' . $testDocId;
$testDocNumber = rand(500, 599) . '/UNIPAR/MoU/X/' . date('Y');

$doc = Document::create([
    'doc_id' => $testDocId,
    'reg_id' => $testRegId,
    'doc_number' => $testDocNumber,
    'partner_doc_number' => '045/DGS/MOU/2026',
    'track' => 'Jalur 1: Kemitraan Bertahap',
    'unit' => 'Program Studi Teknik Informatika',
    'collab_unit' => null,
    'partner_name' => 'PT Digital Inovasi Nusantara',
    'partner_category' => 'DUDI / Swasta',
    'doc_type' => 'MoU',
    'parent_mou' => '-',
    'scope' => 'Pengembangan Sistem Cerdas & Penerapan Tridharma Berbasis Industri',
    'validity' => '3 Tahun',
    'warning_threshold' => 'H-90 Hari (3 Bulan)',
    'status' => 'DRAFT',
    'draft_file' => null,
    'docx_path' => null,
    'qr_code_path' => null,
    'notes' => 'Penyusunan naskah awal MoU menggunakan template bersih resmi UNIPAR.'
]);

echo "[PASS] Dokumen Draf berhasil dibuat: {$doc->doc_id} ({$doc->status})\n";

// 3. Simulasikan Transisi SUBMIT (Draf -> REVIEW)
echo "Menjalankan Transisi SUBMIT -> REVIEW via Controller & Service...\n";

$fsm = app(FSMWorkflowEngineService::class);
$stampService = app(DocxQrStampService::class);
$controller = new SimkermaController($fsm, $stampService);

// Buat HTTP Request tiruan untuk SUBMIT
$request = Request::create('/simkerma/transition', 'POST', [
    'doc_id' => $doc->doc_id,
    'event' => 'SUBMIT',
    'notes' => 'Pengajuan naskah draf MoU ke meja telaah pimpinan rektorat dengan stempel QR Code otomatis.'
]);

session(['active_role' => 'UNIT']);
$response = $controller->transition($request);

// Refresh data dokumen dari database
$doc->refresh();

echo "[PASS] Status dokumen setelah SUBMIT: {$doc->status}\n";
if ($doc->status !== 'REVIEW') {
    echo "[FAIL] Status dokumen seharusnya REVIEW, tetapi tercatat: {$doc->status}\n";
    exit(1);
}

// 4. Verifikasi Berkas Word (.docx) Ber-QR Code
echo "Memeriksa berkas DOCX hasil stempel...\n";
if (!empty($doc->docx_path) && file_exists(public_path($doc->docx_path))) {
    $docxSize = filesize(public_path($doc->docx_path));
    echo "[PASS] Berkas DOCX tersimpan di: {$doc->docx_path} ({$docxSize} bytes)\n";
    
    // Periksa apakah gambar QR Code di dalam DOCX adalah 500x500 (resolusi HD baru)
    $z = new ZipArchive();
    $z->open(public_path($doc->docx_path));
    $qrData = $z->getFromName('word/media/image_qr.png') ?: $z->getFromName('word/media/image2.png');
    $xmlContent = $z->getFromName('word/document.xml');
    $z->close();
    
    $tmpImgPath = 'D:/CLI/Skripsi/temp_check_lifecycle_qr.png';
    file_put_contents($tmpImgPath, $qrData);
    $imgInfo = getimagesize($tmpImgPath);
    echo "[PASS] Gambar QR Code pada tabel tanda tangan: {$imgInfo[0]}x{$imgInfo[1]} px ({$imgInfo['mime']})\n";
    
    if ($imgInfo[0] === 500 && $imgInfo[1] === 500) {
        echo "[PASS] Ukuran resolusi QR Code 100% PERSIS 500x500 piksel (resolusi tajam HD) sesuai instruksi Jordan!\n";
    } else {
        echo "[FAIL] Ukuran QR Code tidak sesuai: {$imgInfo[0]}x{$imgInfo[1]}\n";
        exit(1);
    }

    // Verifikasi posisi presisi: QR Code harus DI ATAS 'Pihak Pertama' (Row 0), BUKAN di atas nama Rektor (Row 2)
    $chkDom = new DOMDocument();
    $chkDom->loadXML($xmlContent);
    $chkXPath = new DOMXPath($chkDom);
    $chkXPath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    $sigTables = $chkXPath->query('//w:tbl');
    $sigTbl = null;
    foreach ($sigTables as $tbl) {
        if (strpos($tbl->textContent, 'Pihak Pertama') !== false && strpos($tbl->textContent, 'Rektor') !== false) {
            $sigTbl = $tbl;
            break;
        }
    }
    if ($sigTbl) {
        $sigRows = $chkXPath->query('.//w:tr', $sigTbl);
        $row0Drawings = $chkXPath->query('.//w:drawing', $sigRows->item(0))->length;
        $row2Drawings = $chkXPath->query('.//w:drawing', $sigRows->item(2))->length;
        if ($row0Drawings === 1 && $row2Drawings === 0) {
            echo "[PASS] Posisi QR Code terverifikasi DI ATAS 'Pihak Pertama' (Row 0) dan Row 2 BERSIH untuk tanda tangan basah!\n";
        } else {
            echo "[FAIL] Posisi QR Code salah! Row 0 drawings: $row0Drawings, Row 2 drawings: $row2Drawings\n";
            exit(1);
        }
    }
} else {
    echo "[FAIL] Berkas DOCX tidak ditemukan pada path: {$doc->docx_path}\n";
    exit(1);
}

// 5. Verifikasi Berkas PDF Resmi
echo "Memeriksa berkas PDF hasil konversi Word COM...\n";
if (!empty($doc->draft_file) && file_exists(public_path($doc->draft_file))) {
    $pdfSize = filesize(public_path($doc->draft_file));
    echo "[PASS] Berkas PDF resmi diterbitkan di: {$doc->draft_file} ({$pdfSize} bytes)\n";
} else {
    echo "[FAIL] Berkas PDF tidak ditemukan pada path: {$doc->draft_file}\n";
    exit(1);
}

// 6. Verifikasi Berkas QR Code PNG Mandiri
if (!empty($doc->qr_code_path) && file_exists(public_path($doc->qr_code_path))) {
    echo "[PASS] Berkas QR Code PNG mandiri tersedia: {$doc->qr_code_path}\n";
} else {
    echo "[FAIL] Berkas QR Code PNG tidak ditemukan\n";
    exit(1);
}

// 7. Verifikasi Halaman Verifikasi Publik (/verify/{reg_id})
echo "Memeriksa endpoint publik verifikasi...\n";
$verifyRequest = Request::create('/verify/' . $doc->reg_id, 'GET');
$verifyView = $controller->verify($verifyRequest, $doc->reg_id);
$renderedHtml = $verifyView->render();

if (strpos($renderedHtml, $doc->reg_id) !== false && strpos($renderedHtml, 'PT Digital Inovasi Nusantara') !== false) {
    echo "[PASS] Halaman Verifikasi Publik berhasil memuat data registrasi dan nama mitra!\n";
} else {
    echo "[FAIL] Halaman Verifikasi tidak memuat data dokumen dengan benar.\n";
    exit(1);
}

if (strpos($renderedHtml, 'Unduh Naskah Word Ber-QR Code') !== false && strpos($renderedHtml, 'Naskah Resmi (.pdf)') !== false) {
    echo "[PASS] Tombol unduh DOCX dan PDF terpasang pada halaman verifikasi!\n";
} else {
    echo "[FAIL] Tombol unduh tidak ditemukan di halaman verifikasi.\n";
    exit(1);
}

echo "========================================================\n";
echo "SEMUA 7 PENGUJIAN LULUS 100% TANPA KESALAHAN!\n";
echo "========================================================\n";
