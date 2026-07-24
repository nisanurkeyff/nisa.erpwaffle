<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
session_kontrol();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Geçersiz dosya ID.");
}

// Fetch file from database
$doc = DB::getRow("SELECT * FROM GELIR_GIDER_DOSYA WHERE ID = :ID AND DURUM = 1", [':ID' => $id]);
if (!$doc) {
    die("Dosya bulunamadı veya silinmiş.");
}

$filePath = $_SERVER['DOCUMENT_ROOT'] . $doc->DOSYA_YOLU;

if (!file_exists($filePath)) {
    die("Evrak dosyası sunucuda bulunamadı.");
}

// Secure Content-Type check based on extension
$ext = strtolower($doc->UZANTI);
$mimeTypes = [
    'pdf'  => 'application/pdf',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg'
];

$contentType = isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : 'application/octet-stream';

// If view is requested, output inline; otherwise, download as attachment
$disposition = (isset($_GET['download']) && $_GET['download'] == 1) ? 'attachment' : 'inline';

// Send secure headers
header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: ' . $disposition . '; filename="' . basename($doc->DOSYA_ADI) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Clear output buffering
ob_clean();
flush();

// Stream file
readfile($filePath);
exit;
?>
