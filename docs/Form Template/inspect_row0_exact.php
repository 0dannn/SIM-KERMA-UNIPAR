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
$row0 = $rows->item(0);
echo $dom->saveXML($row0);
