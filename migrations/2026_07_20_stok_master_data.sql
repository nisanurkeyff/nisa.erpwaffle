-- ==============================================================================
-- ERP STOK MODÜLÜ FAZ 1 (MASTER DATA) - VERİTABANI MİGRATION SCRIPT
-- ==============================================================================
-- Tarih: 2026-07-20
-- UYUMLULUK: MySQL 5.7+, MySQL 8.0+, MariaDB 10+ (Tüm MySQL Sürümleri)
-- Özellikler: Geriye uyumlu, %100 Idempotent (Tekrarlanabilir), Production-Ready.
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- 1. BIRIM TABLOSUNUN OLUŞTURULMASI
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `BIRIM` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `KODU` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `BIRIM_ADI` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `KISA_ADI` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `TIP` VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL COMMENT 'ADET, AGIRLIK, HACIM',
  `HASSASIYET` TINYINT NOT NULL DEFAULT 2 COMMENT 'Virgülden sonraki basamak sayısı (0, 1, 2, 3, 4)',
  `SISTEM_BIRIMI` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Sistem temel birimi (silinemez)',
  `DURUM` VARCHAR(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `SIRA` INT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UK_BIRIM_KODU` (`KODU`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ------------------------------------------------------------------------------
-- 2. BIRIM VARSAYILAN (MASTER) VERİLERİNİN EKLENMESİ
-- ------------------------------------------------------------------------------
INSERT IGNORE INTO `BIRIM` (`KODU`, `BIRIM_ADI`, `KISA_ADI`, `TIP`, `HASSASIYET`, `SISTEM_BIRIMI`, `DURUM`, `SIRA`) VALUES
('ADT', 'Adet', 'Adet', 'ADET', 0, 1, '1', 1),
('GR', 'Gram', 'gr', 'AGIRLIK', 2, 1, '1', 2),
('KG', 'Kilogram', 'kg', 'AGIRLIK', 3, 1, '1', 3),
('ML', 'Mililitre', 'ml', 'HACIM', 0, 1, '1', 4),
('LT', 'Litre', 'lt', 'HACIM', 3, 1, '1', 5);

-- ------------------------------------------------------------------------------
-- 3. MALZEME_TIPI TABLOSUNUN OLUŞTURULMASI
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `MALZEME_TIPI` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `KODU` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `MALZEME_TIPI` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `DURUM` VARCHAR(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `SIRA` INT NULL DEFAULT 1,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UK_MALZEME_TIPI_KODU` (`KODU`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ------------------------------------------------------------------------------
-- 4. MALZEME_TIPI VARSAYILAN (MASTER) VERİLERİNİN EKLENMESİ
-- ------------------------------------------------------------------------------
INSERT IGNORE INTO `MALZEME_TIPI` (`KODU`, `MALZEME_TIPI`, `DURUM`, `SIRA`) VALUES
('RAW', 'Hammadde', '1', 1),
('SEMI', 'Yarı Mamul', '1', 2),
('FIN', 'Mamul', '1', 3),
('PKG', 'Ambalaj', '1', 4),
('CONS', 'Sarf Malzemesi', '1', 5),
('CLN', 'Temizlik Malzemesi', '1', 6);

-- ------------------------------------------------------------------------------
-- 5. MALZEME TABLOSUNA YENİ SÜTUNLARIN KONTROLLÜ (IDEMPOTENT) EKLENMESİ
-- ------------------------------------------------------------------------------

-- Column: TEMEL_BIRIM_ID
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MALZEME' AND COLUMN_NAME = 'TEMEL_BIRIM_ID');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `MALZEME` ADD COLUMN `TEMEL_BIRIM_ID` INT NULL AFTER `URUN_ID`;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Column: MALZEME_TIPI_ID
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MALZEME' AND COLUMN_NAME = 'MALZEME_TIPI_ID');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `MALZEME` ADD COLUMN `MALZEME_TIPI_ID` INT NULL AFTER `TEMEL_BIRIM_ID`;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Column: STOK_TAKIP
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MALZEME' AND COLUMN_NAME = 'STOK_TAKIP');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `MALZEME` ADD COLUMN `STOK_TAKIP` TINYINT(1) NULL DEFAULT 1 AFTER `MALZEME_TIPI_ID`;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Column: MIN_STOK_SEVIYESI
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MALZEME' AND COLUMN_NAME = 'MIN_STOK_SEVIYESI');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `MALZEME` ADD COLUMN `MIN_STOK_SEVIYESI` DECIMAL(14,4) NULL DEFAULT NULL AFTER `STOK_TAKIP`;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ------------------------------------------------------------------------------
-- 6. ÖNCE İNDEKSLERİN (INDEX), SONRA YABANCI ANAHTARLARIN (FOREIGN KEY) EKLENMESİ
-- ------------------------------------------------------------------------------

-- Step 6a: Index for TEMEL_BIRIM_ID
SET @idx_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MALZEME' AND INDEX_NAME = 'IDX_MALZEME_TEMEL_BIRIM_ID');
SET @sql = IF(@idx_exists = 0, 'CREATE INDEX `IDX_MALZEME_TEMEL_BIRIM_ID` ON `MALZEME` (`TEMEL_BIRIM_ID`);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6b: FK: TEMEL_BIRIM_ID -> BIRIM(ID)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'FK_MALZEME_TEMEL_BIRIM' AND TABLE_NAME = 'MALZEME');
SET @sql = IF(@fk_exists = 0, 'ALTER TABLE `MALZEME` ADD CONSTRAINT `FK_MALZEME_TEMEL_BIRIM` FOREIGN KEY (`TEMEL_BIRIM_ID`) REFERENCES `BIRIM` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6c: Index for MALZEME_TIPI_ID
SET @idx_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'MALZEME' AND INDEX_NAME = 'IDX_MALZEME_MALZEME_TIPI_ID');
SET @sql = IF(@idx_exists = 0, 'CREATE INDEX `IDX_MALZEME_MALZEME_TIPI_ID` ON `MALZEME` (`MALZEME_TIPI_ID`);', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6d: FK: MALZEME_TIPI_ID -> MALZEME_TIPI(ID)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'FK_MALZEME_MALZEME_TIPI' AND TABLE_NAME = 'MALZEME');
SET @sql = IF(@fk_exists = 0, 'ALTER TABLE `MALZEME` ADD CONSTRAINT `FK_MALZEME_MALZEME_TIPI` FOREIGN KEY (`MALZEME_TIPI_ID`) REFERENCES `MALZEME_TIPI` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;', 'SELECT 1;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
