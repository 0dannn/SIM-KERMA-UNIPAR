<?php
$z = new ZipArchive();
$z->open('D:/CLI/Skripsi/convo-4 01 Nov 2026 continous (Internal Chat)/Template MoU.docx');
$xml = $z->getFromName('word/document.xml');
$z->close();

$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$drawings = $xpath->query('//w:drawing');
foreach ($drawings as $i => $d) {
    echo "=== Drawing $i ===\n";
    $curr = $d;
    $hierarchy = [];
    while ($curr) {
        $name = $curr->nodeName;
        $info = "";
        if ($name === 'w:tc' || $name === 'w:tr' || $name === 'w:tbl') {
            $info = " [text: " . mb_substr(preg_replace('/\s+/', ' ', trim($curr->textContent)), 0, 40) . "]";
        }
        $hierarchy[] = $name . $info;
        $curr = $curr->parentNode;
    }
    echo implode(" -> ", array_reverse($hierarchy)) . "\n\n";
}
