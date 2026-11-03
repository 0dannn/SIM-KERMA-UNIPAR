<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Document;
use App\Models\AuditTrail;
use App\Models\Activity;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "Memulai proses pembersihan dokumen kerja sama...\n";

// 1. Truncate tabel documents, audit_trails, activities
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
AuditTrail::truncate();
Activity::truncate();
Document::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Tabel basis data berhasil dikosongkan:\n";
echo "- Documents: " . Document::count() . " data\n";
echo "- Audit Trails: " . AuditTrail::count() . " data\n";
echo "- Activities: " . Activity::count() . " data\n";
echo "- Users (tetap dipertahankan): " . User::count() . " akun\n";
echo "- Partners: " . Partner::count() . " data\n";

// 2. Bersihkan file berkas sampel di public/uploads/documents/ (jika ada)
$uploadsDir = public_path('uploads/documents');
if (File::exists($uploadsDir)) {
    $files = File::files($uploadsDir);
    $deletedFiles = 0;
    foreach ($files as $file) {
        if ($file->getFilename() !== '.gitkeep') {
            File::delete($file->getPathname());
            $deletedFiles++;
        }
    }
    echo "Berkas sampel di folder public/uploads/documents/ berhasil dibersihkan ({$deletedFiles} berkas dihapus).\n";
}

echo "\n>>> SELURUH DOKUMEN DAN RIWAYAT BERHASIL DIBERSIHKAN DENGAN BERSIH & AMAN. <<<\n";
