<?php
$_SERVER['DOCUMENT_ROOT'] = '/Applications/XAMPP/xamppfiles/htdocs/nisa.erpwaffle';
require_once($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

echo "Updating URUN_FIYAT_LOG table schema...\n";

// Drop old URUN_FIYAT_LOG if needed or recreate with full production schema
DB::exec("DROP TABLE IF EXISTS URUN_FIYAT_LOG");

$sql = "CREATE TABLE `URUN_FIYAT_LOG` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `URUN_ID` int(11) NOT NULL,
  `KANAL` varchar(30) NOT NULL COMMENT 'MAGAZA / TELEFON / DIS_PLATFORM',
  `ESKI_FIYAT` decimal(14,4) DEFAULT NULL,
  `YENI_FIYAT` decimal(14,4) DEFAULT NULL,
  `KAYNAK` varchar(50) DEFAULT 'WEB' COMMENT 'WEB, API, IMPORT, CRON, TOPLU_GUNCELLEME',
  `IP_ADRESI` varchar(45) DEFAULT NULL,
  `KULLANICI_ID` int(11) DEFAULT NULL,
  `ACIKLAMA` varchar(255) DEFAULT NULL,
  `OLUSTURMA_TARIHI` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `URUN_ID` (`URUN_ID`),
  KEY `KANAL` (`KANAL`),
  KEY `OLUSTURMA_TARIHI` (`OLUSTURMA_TARIHI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

DB::exec($sql);
echo "URUN_FIYAT_LOG table created successfully.\n";
