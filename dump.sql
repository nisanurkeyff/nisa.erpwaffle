SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ALERJEN
-- ----------------------------
DROP TABLE IF EXISTS `ALERJEN`;
CREATE TABLE `ALERJEN`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ALERJEN` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `RESIM_URL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ANAMENU
-- ----------------------------
DROP TABLE IF EXISTS `ANAMENU`;
CREATE TABLE `ANAMENU`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ANAMENU` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ROUTE` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `SIRA` int NULL DEFAULT 1,
  `ICON` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for AVATAR
-- ----------------------------
DROP TABLE IF EXISTS `AVATAR`;
CREATE TABLE `AVATAR`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `AVATAR_TURU_ID` int NULL DEFAULT NULL,
  `AVATAR` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `CLASS` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ALT` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for AVATAR_TURU
-- ----------------------------
DROP TABLE IF EXISTS `AVATAR_TURU`;
CREATE TABLE `AVATAR_TURU`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `AVATAR_TURU` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for CARI
-- ----------------------------
DROP TABLE IF EXISTS `CARI`;
CREATE TABLE `CARI`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CARI` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `AD` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SOYAD` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TELEFON` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `MAIL` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `IL_ID` int NULL DEFAULT NULL,
  `ILCE_ID` int NULL DEFAULT NULL,
  `ADRES` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `RESIM_URL` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  `GTARIH` datetime NULL DEFAULT NULL,
  `TOKEN` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  `DB_HOST` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DB_KULLANICI` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DB_SIFRE` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DB_AD` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `IMG_PATH` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TITLE` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `YONETIM_URL` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `QR_URL` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `BANNER_BASLIK` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `BANNER_ICERIK` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `BANNER_DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `TEMA_RENK` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for CARI_RESIM
-- ----------------------------
DROP TABLE IF EXISTS `CARI_RESIM`;
CREATE TABLE `CARI_RESIM`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CARI_ID` int NULL DEFAULT NULL,
  `KAYIT_YAPAN_ID` int NOT NULL,
  `SIRA` tinyint NULL DEFAULT 9,
  `RESIM_ADI` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `RESIM_ADI_ILK` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` timestamp NULL DEFAULT current_timestamp(),
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `VITRIN` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '0',
  `ALT` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `CARI_ID`(`CARI_ID` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for DURUM
-- ----------------------------
DROP TABLE IF EXISTS `DURUM`;
CREATE TABLE `DURUM`  (
  `ID` int NOT NULL,
  `DURUM` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for HAKKIMIZDA
-- ----------------------------
DROP TABLE IF EXISTS `HAKKIMIZDA`;
CREATE TABLE `HAKKIMIZDA`  (
  `ID` int NOT NULL,
  `BASLIK` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL,
  `BASLIK2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL,
  `MUTLU_MUSTERI_SAYISI` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `GUNLUK_SIPARIS_SAYISI` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `URUN_CESIT_SAYISI` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ACIKLAMA_BASLIK1` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ACIKLAMA1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL,
  `ACIKLAMA_BASLIK2` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ACIKLAMA2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL,
  `URL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for HAMUR_KULLANIM
-- ----------------------------
DROP TABLE IF EXISTS `HAMUR_KULLANIM`;
CREATE TABLE `HAMUR_KULLANIM`  (
  `ID` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `HAMUR_KULLANIM` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for IL
-- ----------------------------
DROP TABLE IF EXISTS `IL`;
CREATE TABLE `IL`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `IL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ULKE_ID` int NULL DEFAULT NULL,
  `KODU` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `ONCELIK` int NULL DEFAULT NULL,
  `BOLGE_ID` int NULL DEFAULT NULL,
  `ILS` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 82 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ILCE
-- ----------------------------
DROP TABLE IF EXISTS `ILCE`;
CREATE TABLE `ILCE`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ILCE` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `IL_ID` int NULL DEFAULT NULL,
  `ONCELIK` int NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `ILCES` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ILCE_KODU` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ILCE_UAVT_KODU` int NULL DEFAULT NULL,
  `ENLEM` double(20, 8) NULL DEFAULT NULL,
  `BOYLAM` double(20, 8) NULL DEFAULT NULL COMMENT '37.44318370',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ILCE_KODU`(`ILCE_KODU` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1007 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ILETISIM
-- ----------------------------
DROP TABLE IF EXISTS `ILETISIM`;
CREATE TABLE `ILETISIM`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `AD` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `MAIL` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TELEFON` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `MESAJ` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ISLEM_LOG
-- ----------------------------
DROP TABLE IF EXISTS `ISLEM_LOG`;
CREATE TABLE `ISLEM_LOG`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URUN_ID` int NOT NULL,
  `SAYFA` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TABLO` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ISLEM` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `KULLANICI_ID` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SORGU` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL,
  `TARIH` timestamp NULL DEFAULT current_timestamp(),
  `ROW` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `URUN_ID`(`URUN_ID` ASC) USING BTREE,
  INDEX `TABLO`(`TABLO` ASC) USING BTREE,
  INDEX `ISLEM`(`ISLEM` ASC) USING BTREE,
  INDEX `TARIH`(`TARIH` ASC) USING BTREE,
  INDEX `KULLANICI_ID`(`KULLANICI_ID` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 891 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for KATEGORI
-- ----------------------------
DROP TABLE IF EXISTS `KATEGORI`;
CREATE TABLE `KATEGORI`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `KATEGORI` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SLUG` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `SIRA` int NULL DEFAULT NULL,
  `RESIM_URL` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  `MALZEME_IDS` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for KDV
-- ----------------------------
DROP TABLE IF EXISTS `KDV`;
CREATE TABLE `KDV`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `KDV` int NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for KULLANICI
-- ----------------------------
DROP TABLE IF EXISTS `KULLANICI`;
CREATE TABLE `KULLANICI`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `YETKI_ID` int NULL DEFAULT NULL,
  `FIRMA_ID` int NULL DEFAULT NULL,
  `AVATAR_ID` int NULL DEFAULT 1,
  `KULLANICI` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TCK` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `AD` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SOYAD` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TELEFON` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `MAIL` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SIFRE` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SIFRE_TEKRAR` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `IL_ID` int NULL DEFAULT NULL,
  `ILCE_ID` int NULL DEFAULT NULL,
  `ADRES` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SORGU` int NULL DEFAULT 0,
  `TWITTER` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `FACEBOOK` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `INSTAGRAM` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `LINKEDIN` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  `GTARIH` datetime NULL DEFAULT NULL,
  `TOKEN` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for KULLANICI_MUSTERI
-- ----------------------------
DROP TABLE IF EXISTS `KULLANICI_MUSTERI`;
CREATE TABLE `KULLANICI_MUSTERI`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `KULLANICI_ID` int NULL DEFAULT NULL,
  `MUSTERI_ID` int NULL DEFAULT NULL,
  `SIRA` int NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for MALZEME
-- ----------------------------
DROP TABLE IF EXISTS `MALZEME`;
CREATE TABLE `MALZEME`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `MALZEME` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `FIYAT` decimal(14, 2) NULL DEFAULT 0.00,
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `TOKEN` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ACIKLAMA` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ONCELIK` int NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for MENU
-- ----------------------------
DROP TABLE IF EXISTS `MENU`;
CREATE TABLE `MENU`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ANAMENU_ID` int NULL DEFAULT NULL,
  `MENU` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `LINK` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TITLE` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SIRA` tinyint NULL DEFAULT 1,
  `ROUTE` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `YETKI_IDS` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `FILTRE` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 34 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;



-- ----------------------------
-- Table structure for SAYFALAMA
-- ----------------------------
DROP TABLE IF EXISTS `SAYFALAMA`;
CREATE TABLE `SAYFALAMA`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SAYFALAMA` int NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for SECIM
-- ----------------------------
DROP TABLE IF EXISTS `SECIM`;
CREATE TABLE `SECIM`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SECIM` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `SECIM_TURU` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for SIPARIS
-- ----------------------------
DROP TABLE IF EXISTS `SIPARIS`;
CREATE TABLE `SIPARIS`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SIPARIS_ID` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SIPARIS_NO` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TUTAR` decimal(14, 2) NULL DEFAULT NULL,
  `TELEFON` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `MUSTERI_ID` int NULL DEFAULT NULL,
  `MUSTERI` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ODEME` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SIPARIS_NOT` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TAHMINI_TESLIMAT` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SIPARIS_TARIH` datetime NULL DEFAULT NULL,
  `HAZIRLANMA_TARIH` datetime NULL DEFAULT NULL,
  `TOKEN` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for SIPARIS_ICERIK
-- ----------------------------
DROP TABLE IF EXISTS `SIPARIS_ICERIK`;
CREATE TABLE `SIPARIS_ICERIK`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SIPARIS_ID` int NULL DEFAULT NULL,
  `STOK_ID` int NULL DEFAULT NULL,
  `STOK` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `FIYAT` decimal(12, 2) NULL DEFAULT 0.00,
  `KONUM` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ICERIK` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `KG` int NULL DEFAULT NULL,
  `ACIKLAMA` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SIRA` int NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  `ESANS_TIP` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 91 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for SITE
-- ----------------------------
DROP TABLE IF EXISTS `SITE`;
CREATE TABLE `SITE`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `FIRMA` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TITLE` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `FAVICON` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `LOGO` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `IMG_PATH` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TELEFON` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `MAIL` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `FOOTER` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `HAKKIMIZDA` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `LOGO_PX` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `INSTAGRAM` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `FACEBOOK` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TWITTER` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ADRES` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `LINKEDIN` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `YOUTUBE` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `AD` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `SOYAD` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `IL_ID` int NULL DEFAULT NULL,
  `ILCE_ID` int NULL DEFAULT NULL,
  `TEMA_RENK` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  `YONETIM_URL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `QR_URL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `FIYAT_DEGISIM_TARIH` date NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for SITE_RESIM
-- ----------------------------
DROP TABLE IF EXISTS `SITE_RESIM`;
CREATE TABLE `SITE_RESIM`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SITE_ID` int NULL DEFAULT NULL,
  `KAYIT_YAPAN_ID` int NOT NULL,
  `SIRA` tinyint NULL DEFAULT 9,
  `RESIM_ADI` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `RESIM_ADI_ILK` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` timestamp NULL DEFAULT current_timestamp(),
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `VITRIN` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '0',
  `ALT` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `SITE_ID`(`SITE_ID` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for URUN
-- ----------------------------
DROP TABLE IF EXISTS `URUN`;
CREATE TABLE `URUN`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URUN` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ACIKLAMA` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ESKI_FIYAT` decimal(14, 4) NULL DEFAULT 0.0000,
  `FIYAT` decimal(14, 4) NULL DEFAULT 0.0000,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  `KATEGORI_ID` int NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  `TOKEN` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `MALZEME_IDS` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `HAMUR_KULLANIM_ID` int NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for URUN_FAVORI
-- ----------------------------
DROP TABLE IF EXISTS `URUN_FAVORI`;
CREATE TABLE `URUN_FAVORI`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URUN_ID` int NULL DEFAULT NULL,
  `COOKIE_ID` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for URUN_FIYAT
-- ----------------------------
DROP TABLE IF EXISTS `URUN_FIYAT`;
CREATE TABLE `URUN_FIYAT`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SUBE_ID` int NULL DEFAULT NULL,
  `URUN_ID` int NULL DEFAULT NULL,
  `KATEGORI_ID` int NULL DEFAULT NULL,
  `ESKI_FIYAT` decimal(12, 2) NULL DEFAULT 0.00,
  `FIYAT` decimal(12, 2) NULL DEFAULT 0.00,
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 13702 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for URUN_FIYAT_LOG
-- ----------------------------
DROP TABLE IF EXISTS `URUN_FIYAT_LOG`;
CREATE TABLE `URUN_FIYAT_LOG`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URUN_ID` int NULL DEFAULT NULL,
  `SUBE_ID` int NULL DEFAULT NULL,
  `ESKI_FIYAT` decimal(12, 2) NULL DEFAULT 0.00,
  `FIYAT` decimal(12, 2) NULL DEFAULT 0.00,
  `KAYIT_YAPAN_ID` int NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for URUN_KATEGORI
-- ----------------------------
DROP TABLE IF EXISTS `URUN_KATEGORI`;
CREATE TABLE `URUN_KATEGORI`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URUN_KATEGORI` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for URUN_RESIM
-- ----------------------------
DROP TABLE IF EXISTS `URUN_RESIM`;
CREATE TABLE `URUN_RESIM`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URUN_ID` int NULL DEFAULT NULL,
  `KAYIT_YAPAN_ID` int NOT NULL,
  `SIRA` tinyint NULL DEFAULT 9,
  `RESIM_ADI` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `RESIM_ADI_ILK` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` timestamp NULL DEFAULT current_timestamp(),
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  `BOYUT` decimal(14, 2) NULL DEFAULT NULL,
  `VITRIN` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '0',
  `ALT` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `GTARIH` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `URUN_ID`(`URUN_ID` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for URUN_YORUM
-- ----------------------------
DROP TABLE IF EXISTS `URUN_YORUM`;
CREATE TABLE `URUN_YORUM`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `URUN_ID` int NULL DEFAULT NULL,
  `ISIM` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `MAIL` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `BASLIK` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `ICERIK` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `TARIH` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for YETKI
-- ----------------------------
DROP TABLE IF EXISTS `YETKI`;
CREATE TABLE `YETKI`  (
  `ID` int NOT NULL AUTO_INCREMENT,
  `YETKI` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `INDEX` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT NULL,
  `DURUM` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NULL DEFAULT '1',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
