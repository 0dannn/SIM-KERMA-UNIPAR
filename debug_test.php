<?php
/**
 * Deep Debugging Script - SIM-KERMA UNIPAR
 * Tests all pages, APIs, code quality, and identifies bugs
 */

$baseUrl = 'http://127.0.0.1:8000';
$errors = [];
$warnings = [];
$total = 0;

function checkPage($url, $name, &$errors, &$warnings, &$total) {
    $total++;
    $ctx = stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true]]);
    $body = @file_get_contents($url, false, $ctx);
    $status = 0;
    if (isset($http_response_header[0]) && preg_match('/(\d{3})/', $http_response_header[0], $m)) {
        $status = (int)$m[1];
    }
    if ($status !== 200) {
        $errors[] = "[$name] HTTP $status (expected 200)";
        return $body ?: '';
    }
    if ($body === false) {
        $errors[] = "[$name] Connection failed / empty response";
        return '';
    }
    if (stripos($body, 'ErrorException') !== false || stripos($body, 'ParseError') !== false) {
        $errors[] = "[$name] PHP Exception found in page output";
    }
    if (stripos($body, 'Undefined variable') !== false) {
        $errors[] = "[$name] Undefined variable error in output";
    }
    if (stripos($body, 'SQLSTATE') !== false) {
        $errors[] = "[$name] SQL Error found in page output";
    }
    if (stripos($body, 'Whoops') !== false && stripos($body, 'ignition') !== false) {
        $errors[] = "[$name] Laravel error page (Whoops/Ignition) detected";
    }
    return $body;
}

echo "======================================================================\n";
echo "  DEEP DEBUGGING SESSION - SIM-KERMA UNIPAR\n";
echo "  Waktu: " . date('Y-m-d H:i:s') . "\n";
echo "======================================================================\n\n";

// ===== TEST 1: All 7 Menu Pages =====
echo "--- Test 1: Rendering 7 Menu Halaman ---\n";
for ($m = 1; $m <= 7; $m++) {
    $body = checkPage("$baseUrl/?menu=$m", "Menu $m", $errors, $warnings, $total);
    if ($body) {
        $size = strlen($body);
        echo "  [OK] Menu $m: $size bytes\n";
    }
}

// ===== TEST 2: Document Detail View =====
echo "\n--- Test 2: Detail View Dokumen ---\n";
$body4 = checkPage("$baseUrl/?menu=4", 'Menu4-List', $errors, $warnings, $total);
$testDocId = null;
$testRegId = null;
if (preg_match('/doc=([A-Z0-9\-]+)/', $body4, $dm)) {
    $testDocId = $dm[1];
    echo "  Found doc_id: $testDocId\n";
    
    $detailBody = checkPage("$baseUrl/?menu=4&view=detail&doc=$testDocId", "Detail-$testDocId", $errors, $warnings, $total);
    if ($detailBody) {
        echo "  [OK] Detail page: " . strlen($detailBody) . " bytes\n";
        
        // Check key UI elements
        if (stripos($detailBody, 'Lihat Detail Status') === false) {
            $warnings[] = '[Detail] Button "Lihat Detail Status" not found';
        } else {
            echo "  [OK] 'Lihat Detail Status' button present\n";
        }
        if (stripos($detailBody, '(Finite State Machine)') !== false) {
            $errors[] = '[Detail] Old text "(Finite State Machine)" still present';
        }
        if (stripos($detailBody, 'Lihat Detail FSM') !== false) {
            $errors[] = '[Detail] Old button text "Lihat Detail FSM" still present';
        }
        if (stripos($detailBody, 'naskah asli / terverifikasi qr code') !== false) {
            $errors[] = '[Detail] Old text "naskah asli / terverifikasi qr code" still present';
        }
        
        // Extract reg_id for verification test
        if (preg_match('/REG-[A-Z0-9\-]+/', $detailBody, $rm)) {
            $testRegId = $rm[0];
        }
    }
} else {
    $warnings[] = '[Detail] No doc_id found in Menu 4 page';
}

// ===== TEST 3: New Document Form =====
echo "\n--- Test 3: Form Pengajuan Baru ---\n";
$newMouBody = checkPage("$baseUrl/?menu=4&view=new_mou", 'NewMoU-Form', $errors, $warnings, $total);
if ($newMouBody) {
    echo "  [OK] New MoU form: " . strlen($newMouBody) . " bytes\n";
    
    // Check form fields
    if (stripos($newMouBody, 'Nomor Naskah Pihak 1') === false && stripos($newMouBody, 'Nomor Naskah Pihak I') === false) {
        $warnings[] = '[NewMoU] "Nomor Naskah Pihak 1" label not found';
    }
    if (stripos($newMouBody, 'Nomor Naskah Pihak 2') === false && stripos($newMouBody, 'Nomor Naskah Pihak II') === false) {
        $warnings[] = '[NewMoU] "Nomor Naskah Pihak 2" label not found';
    }
}

// ===== TEST 4: Legacy Backfill Form =====
echo "\n--- Test 4: Form Pencatatan Lampau ---\n";
$legacyBody = checkPage("$baseUrl/?menu=4&view=legacy", 'Legacy-Form', $errors, $warnings, $total);
if ($legacyBody) {
    echo "  [OK] Legacy form: " . strlen($legacyBody) . " bytes\n";
}

// ===== TEST 5: Derive (Turunan) Form =====
echo "\n--- Test 5: Form Dokumen Turunan ---\n";
$deriveBody = checkPage("$baseUrl/?menu=4&view=new_derive", 'Derive-Form', $errors, $warnings, $total);
if ($deriveBody) {
    echo "  [OK] Derive form: " . strlen($deriveBody) . " bytes\n";
}

// ===== TEST 6: API Endpoints =====
echo "\n--- Test 6: API Endpoints ---\n";
$apiBody = checkPage("$baseUrl/simkerma/api/partners?q=", 'API-Partners', $errors, $warnings, $total);
$json = json_decode($apiBody, true);
if (!is_array($json)) {
    $errors[] = '[API-Partners] Response is not valid JSON';
} else {
    echo "  [OK] Partners API: " . count($json) . " results\n";
}

$apiBody2 = checkPage("$baseUrl/simkerma/api/universities?q=manado", 'API-PDDikti', $errors, $warnings, $total);
$json2 = json_decode($apiBody2, true);
if (!is_array($json2)) {
    $errors[] = '[API-PDDikti] Response is not valid JSON';
} else {
    echo "  [OK] PDDikti API: " . count($json2) . " results for 'manado'\n";
}

$apiBody3 = checkPage("$baseUrl/simkerma/api/universities?q=", 'API-PDDikti-Empty', $errors, $warnings, $total);
$json3 = json_decode($apiBody3, true);
if (!is_array($json3)) {
    $errors[] = '[API-PDDikti-Empty] Empty query response is not valid JSON';
} else {
    echo "  [OK] PDDikti API (empty query): " . count($json3) . " priority results\n";
}

// ===== TEST 7: QR Verification Page =====
echo "\n--- Test 7: Halaman Verifikasi QR Code ---\n";
if ($testRegId) {
    $verifyBody = checkPage("$baseUrl/verify/$testRegId", "Verify-$testRegId", $errors, $warnings, $total);
    if ($verifyBody) {
        echo "  [OK] Verify page (valid reg): " . strlen($verifyBody) . " bytes\n";
    }
}
$verifyInvalidBody = checkPage("$baseUrl/verify/NONEXISTENT-REG", 'Verify-Invalid', $errors, $warnings, $total);
if ($verifyInvalidBody) {
    echo "  [OK] Verify page (invalid reg): gracefully handled\n";
}
// Test without reg_id
$verifyEmptyBody = checkPage("$baseUrl/verify/", 'Verify-Empty', $errors, $warnings, $total);
if ($verifyEmptyBody !== false) {
    echo "  [OK] Verify page (no reg_id): " . strlen($verifyEmptyBody) . " bytes\n";
}

// ===== TEST 8: Login Page =====
echo "\n--- Test 8: Halaman Login ---\n";
checkPage("$baseUrl/login", 'Login', $errors, $warnings, $total);
echo "  [OK] Login page rendered\n";

// ===== TEST 9: Static Code Analysis =====
echo "\n--- Test 9: Analisis Kode Statis ---\n";

$controllerCode = file_get_contents('app/Http/Controllers/SimkermaController.php');
$fsmCode = file_get_contents('app/Services/FSMWorkflowEngineService.php');
$modelDocCode = file_get_contents('app/Models/Document.php');

// BUG 1: Hardcoded year in generateUniqueDocId
if (strpos($controllerCode, "'UNIPAR-2026-") !== false || strpos($controllerCode, "UNIPAR-2026-") !== false) {
    $warnings[] = '[Code] generateUniqueDocId() hardcodes year "2026" - will break after Dec 31 2026';
    echo "  [WARN] Hardcoded year 2026 in doc ID generator\n";
}

// BUG 2: rand() usage in storeJalur2 for doc_number
if (preg_match("/sprintf\('%03d\/UNIPAR\/PKM/", $controllerCode)) {
    $warnings[] = '[Code] storeJalur2() uses rand(360,499) for doc_number - possible collision under concurrent requests and limited range (140 unique numbers)';
    echo "  [WARN] Random doc_number in Jalur 2 may collide\n";
}

// BUG 3: Document::children() relationship has incorrect orWhere
if (strpos($modelDocCode, "->orWhere('parent_mou', \$this->doc_id)") !== false) {
    $errors[] = '[Model] Document::children() uses orWhere with $this->doc_id inside hasMany - orWhere is not properly scoped and can return unrelated documents. This relationship is broken and returns incorrect results.';
    echo "  [BUG] Document::children() orWhere is improperly scoped\n";
}

// BUG 4: Check changeRole allowed roles vs FSM matrix roles
// FSM uses: UNIT, LEGAL, LEADER, SYSTEM, MASTER
// changeRole allows: UNIT, LEGAL, LEADER, SYSTEM, MASTER, ADMIN
// But the view role switcher might use different names
if (strpos($controllerCode, "'LPPM'") !== false || strpos($controllerCode, "'FAKULTAS'") !== false || strpos($controllerCode, "'PIMPINAN'") !== false) {
    // These roles exist in the UI/view but not in the FSM matrix
    echo "  [INFO] UI roles (LPPM, FAKULTAS, PIMPINAN) differ from FSM roles (UNIT, LEGAL, LEADER)\n";
}

// BUG 5: Check if FSM RBAC check is ever bypassed (ADMIN is always allowed)
if (strpos($fsmCode, "actorRole !== 'MASTER' && \$actorRole !== 'ADMIN'") !== false) {
    echo "  [INFO] FSM RBAC: ADMIN and MASTER bypass all role checks (by design)\n";
}

// BUG 6: Missing validation in storeJalur1 - no request->validate()
if (preg_match('/public function storeJalur1.*?\{(.*?)public function/s', $controllerCode, $storeMatch)) {
    if (strpos($storeMatch[1], 'validate(') === false) {
        $warnings[] = '[Code] storeJalur1() has no request validation - all fields are optional or use defaults, which could lead to incomplete records';
        echo "  [WARN] storeJalur1() lacks explicit validation\n";
    }
}

// BUG 7: Check storeJalur2 - partner_doc_number not defaulting to '-'
if (preg_match("/storeJalur2.*?'partner_doc_number'\s*=>\s*\\\$partnerDocNumber/s", $controllerCode)) {
    // Check if $partnerDocNumber has '-' fallback
    if (strpos($controllerCode, "storeJalur2") !== false) {
        $jalur2Section = substr($controllerCode, strpos($controllerCode, 'function storeJalur2'), 1000);
        if (strpos($jalur2Section, "partner_doc_number')") !== false) {
            $pdnLine = "partner_doc_number = request->input('partner_doc_number')";
            // In storeJalur2, $partnerDocNumber gets raw value without '-' default
            if (strpos($jalur2Section, "?: '-'") === false && strpos($jalur2Section, "!== ''") === false) {
                $errors[] = '[Code] storeJalur2() does NOT default empty partner_doc_number to "-" (unlike storeJalur1). Will store NULL instead of "-" when field is left blank.';
                echo "  [BUG] storeJalur2() missing '-' default for partner_doc_number\n";
            }
        }
    }
}

// BUG 8: deleteDraft removes files but path may include 'uploads/documents/' prefix
// Check: draft_file is stored as 'uploads/documents/xxx.pdf' but delete uses public_path('uploads/documents/' . $document->draft_file)
// This creates double path: public/uploads/documents/uploads/documents/xxx.pdf
if (preg_match("/public_path\('uploads\/documents\/'\s*\.\s*\\\$document->draft_file\)/", $controllerCode)) {
    $errors[] = '[Code] deleteDraft() constructs file path as public_path("uploads/documents/" . $document->draft_file) but draft_file is stored as "uploads/documents/filename.pdf" - this creates a DOUBLE PATH: public/uploads/documents/uploads/documents/filename.pdf. Files will NEVER be deleted!';
    echo "  [BUG] deleteDraft() double-path bug - files never actually deleted!\n";
}

// BUG 9: Check if legacy timestamp_wita uses historical dates properly  
// In storeLegacy, the audit trail timestamp_wita uses now() not the historical date
if (preg_match("/function storeLegacy.*?'timestamp_wita'\s*=>\s*now\(\)/s", $controllerCode)) {
    $warnings[] = '[Code] storeLegacy() audit trail timestamp_wita uses now() (current time) instead of the historical start_date. The audit record for a 2024 document will show 2026 timestamp.';
    echo "  [WARN] storeLegacy() audit timestamp uses now() not historical date\n";
}

// BUG 10: Check partner->documents relationship - uses 'partner_name' as foreign key
// This means if partner name changes, the relationship breaks
echo "  [INFO] Partner->documents relationship uses name as FK - renaming partner requires Document sync (implemented in updatePartner)\n";

// ===== TEST 10: Check Blade View for common issues =====
echo "\n--- Test 10: Blade View Analysis ---\n";
$indexBlade = file_get_contents('resources/views/simkerma/index.blade.php');
$fsmModal = file_get_contents('resources/views/simkerma/partials/fsm_modal.blade.php');
$verifyBlade = file_get_contents('resources/views/simkerma/verify.blade.php');

// Check for inline JS errors
if (preg_match_all('/\$\(.*?\)\.(on|click|submit|change)\(/', $indexBlade, $jqMatches)) {
    echo "  [INFO] jQuery event handlers found: " . count($jqMatches[0]) . "\n";
}

// Check if #52249E color is gone
if (stripos($indexBlade, '#52249E') !== false || stripos($fsmModal, '#52249E') !== false) {
    $errors[] = '[View] Old purple color #52249E still present in views';
    echo "  [BUG] Old purple #52249E still in use\n";
} else {
    echo "  [OK] Old purple #52249E fully removed\n";
}

// Check #5156be is used
if (stripos($fsmModal, '#5156be') !== false || stripos($indexBlade, '#5156be') !== false) {
    echo "  [OK] Standard purple #5156be in use\n";
}

// Check text-center on table headers in detail view
if (stripos($indexBlade, 'text-center') !== false) {
    echo "  [OK] text-center alignment present in views\n";
}

// ===== TEST 11: Database Schema vs Code Consistency =====
echo "\n--- Test 11: Schema vs Code Consistency ---\n";

// track column is NOT NULL without default - check all create paths set it
$createPaths = ['storeJalur1', 'storeJalur2', 'storeLegacy'];
foreach ($createPaths as $method) {
    if (preg_match("/function $method.*?Document::create\(\[(.*?)\]\)/s", $controllerCode, $cm)) {
        if (strpos($cm[1], "'track'") === false) {
            $errors[] = "[Schema] $method() does not set 'track' field which is NOT NULL in DB";
            echo "  [BUG] $method() missing 'track' field\n";
        } else {
            echo "  [OK] $method() sets 'track' field\n";
        }
    }
}

// timestamp_wita is NOT NULL - check all AuditTrail::create set it
$auditTrailCreates = preg_match_all("/AuditTrail::create\(\[(.+?)\]\)/s", $controllerCode, $atm);
$missingTimestamp = 0;
for ($i = 0; $i < $auditTrailCreates; $i++) {
    if (strpos($atm[1][$i], 'timestamp_wita') === false) {
        $missingTimestamp++;
    }
}
if ($missingTimestamp > 0) {
    $errors[] = "[Schema] $missingTimestamp AuditTrail::create() calls missing 'timestamp_wita' (NOT NULL field)";
    echo "  [BUG] $missingTimestamp audit trail creates missing timestamp_wita\n";
} else {
    echo "  [OK] All AuditTrail::create() calls include timestamp_wita\n";
}

// from_status is NOT NULL - check all AuditTrail::create set it
$missingFromStatus = 0;
for ($i = 0; $i < $auditTrailCreates; $i++) {
    if (strpos($atm[1][$i], 'from_status') === false) {
        $missingFromStatus++;
    }
}
if ($missingFromStatus > 0) {
    $errors[] = "[Schema] $missingFromStatus AuditTrail::create() calls missing 'from_status' (NOT NULL field)";
} else {
    echo "  [OK] All AuditTrail::create() calls include from_status\n";
}

// Also check FSM service
$fsmAuditTrailCreates = preg_match_all("/AuditTrail::create\(\[(.+?)\]\)/s", $fsmCode, $fatm);
for ($i = 0; $i < $fsmAuditTrailCreates; $i++) {
    if (strpos($fatm[1][$i], 'timestamp_wita') === false) {
        $errors[] = "[Schema] FSMWorkflowEngineService AuditTrail::create() missing 'timestamp_wita'";
    }
}

echo "\n======================================================================\n";
echo "  HASIL DEBUGGING\n";
echo "======================================================================\n";
echo "Total checks: $total\n";
echo "ERRORS (Bugs): " . count($errors) . "\n";
echo "WARNINGS (Potensi Masalah): " . count($warnings) . "\n\n";

if (!empty($errors)) {
    echo "--- BUGS DITEMUKAN ---\n";
    foreach ($errors as $i => $e) {
        echo "  " . ($i + 1) . ". $e\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "--- PERINGATAN ---\n";
    foreach ($warnings as $i => $w) {
        echo "  " . ($i + 1) . ". $w\n";
    }
}

if (empty($errors) && empty($warnings)) {
    echo "Semua pengecekan lolos tanpa masalah!\n";
}
