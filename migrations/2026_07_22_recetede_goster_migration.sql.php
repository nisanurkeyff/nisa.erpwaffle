<?php
$_SERVER['DOCUMENT_ROOT'] = '/Applications/XAMPP/xamppfiles/htdocs/nisa.erpwaffle';
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

echo "Checking MALZEME table columns...\n";
$cols = DB::get("DESCRIBE MALZEME");
$colNames = array_map(function($c) { return $c->Field; }, $cols);

if (!in_array('RECETEDE_GOSTER', $colNames)) {
    echo "Adding RECETEDE_GOSTER...\n";
    DB::exec("ALTER TABLE MALZEME ADD COLUMN RECETEDE_GOSTER TINYINT(1) NULL DEFAULT 1 AFTER DURUM");
    echo "RECETEDE_GOSTER column added successfully.\n";
} else {
    echo "RECETEDE_GOSTER column already exists.\n";
}

echo "Migration completed successfully.\n";
