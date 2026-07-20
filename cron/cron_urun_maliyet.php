<?php
/**
 * @deprecated Deprecated cron job.
 * Product cost calculation is now event-driven (real-time).
 * This file is kept as a CLI/web maintenance tool to recalculate all product costs at once if needed.
 */
if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
}
require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    echo "<pre style='font-family: monospace;'>";
}

$bugun = date("Y-m-d");
echo "=========================================\n";
echo "ÜRÜN MALİYET YENİDEN HESAPLAMA BAKIM ARACI\n";
echo "Tarih: $bugun\n";
echo "=========================================\n\n";

$islenen_sayisi = UrunMaliyetService::hesaplaTumUrunMaliyetleri();

echo "=========================================\n";
echo "İŞLEM TAMAMLANDI. TOPLAM GÜNCELLENEN ÜRÜN: $islenen_sayisi\n";
echo "=========================================\n";

if (!$is_cli) {
    echo "</pre>";
}