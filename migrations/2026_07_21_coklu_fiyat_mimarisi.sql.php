<?php
$_SERVER['DOCUMENT_ROOT'] = '/Applications/XAMPP/xamppfiles/htdocs/nisa.erpwaffle';
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

echo "Checking URUN table columns...\n";
$cols = DB::get("DESCRIBE URUN");
$colNames = array_map(function($c) { return $c->Field; }, $cols);

if (!in_array('FIYAT_MAGAZA', $colNames)) {
    echo "Adding FIYAT_MAGAZA...\n";
    DB::exec("ALTER TABLE URUN ADD COLUMN FIYAT_MAGAZA DECIMAL(14,4) NULL DEFAULT 0.0000 AFTER ACIKLAMA");
}

if (!in_array('FIYAT_TELEFON', $colNames)) {
    echo "Adding FIYAT_TELEFON...\n";
    DB::exec("ALTER TABLE URUN ADD COLUMN FIYAT_TELEFON DECIMAL(14,4) NULL DEFAULT 0.0000 AFTER FIYAT_MAGAZA");
}

if (!in_array('FIYAT_DIS_PLATFORM', $colNames)) {
    echo "Adding FIYAT_DIS_PLATFORM...\n";
    DB::exec("ALTER TABLE URUN ADD COLUMN FIYAT_DIS_PLATFORM DECIMAL(14,4) NULL DEFAULT 0.0000 AFTER FIYAT_TELEFON");
}

echo "Migrating existing FIYAT to FIYAT_MAGAZA...\n";
$migrated = DB::exec("UPDATE URUN SET FIYAT_MAGAZA = FIYAT WHERE (FIYAT_MAGAZA IS NULL OR FIYAT_MAGAZA = 0) AND FIYAT > 0");
echo "Migrated $migrated rows.\n";

echo "Migration completed successfully.\n";
