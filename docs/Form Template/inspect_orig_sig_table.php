<?php
$z = new ZipArchive();
$z->open('D:/CLI/Skripsi/convo-4 01 Nov 2026 continous (Internal Chat)/Template MoU.docx');
$xml = $z->getFromName('word/document.xml');
$z->close();

$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$tables = $xpath->query('//w:tbl');
$sigTbl = $tables->item(1);

$rows = $xpath->query('.//w:tr', $sigTbl);
echo "Total rows: " . $rows->length . "\n";
foreach ($rows as $rIdx => $row) {
    echo "--- ROW $rIdx ---\n";
    $cells = $xpath->query('.//w:tc', $row);
    foreach ($cells as $cIdx => $cell) {
        $hasDrawing = $xpath->query('.//w:drawing', $cell)->length;
        echo "  Cell $cIdx: text='" . trim($cell->textContent) . "' drawings=$hasDrawing\n";
    }
}
