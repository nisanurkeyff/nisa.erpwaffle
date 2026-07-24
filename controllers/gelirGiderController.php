<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class GelirGiderController
{

    private $select;
    private $site;

    public function __construct($select = "", $row_site = "")
    {
        $this->select = $select;
        $this->site = $row_site;
    }

    public function sayfalamaOlustur($toplamVeri, $request, $sayfaBasinaVeri = 10)
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $baseUrl = $protocol . $host . $scriptName;

        $gecerliSayfa = isset($request['page']) ? (int)$request['page'] : 1;
        $request['page'] = null;

        $queryString = http_build_query(array_filter($request, function ($value) {
            return $value !== null;
        }));
        $url = $baseUrl . '?' . $queryString;
        return new Sayfalama($toplamVeri, $sayfaBasinaVeri, $gecerliSayfa, $url);
    }

    public function Cariler()
    {
        $data = array();
        $sql = "SELECT
                    C.ID,
                    C.CARI AS AD
                FROM CARI AS C
                WHERE C.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    private function buildFilterSql($request)
    {
        $where = " WHERE GG.DURUM = 1";
        $data = array();

        if (isset($request['cari_id']) && $request['cari_id'] > 0) {
            $where .= " AND GG.CARI_ID = :CARI_ID";
            $data[':CARI_ID'] = $request['cari_id'];
        }

        if (isset($request['kategori_id']) && $request['kategori_id'] > 0) {
            $where .= " AND GG.KATEGORI_ID = :KATEGORI_ID";
            $data[':KATEGORI_ID'] = $request['kategori_id'];
        }

        if (isset($request['islem_kaynagi_id']) && $request['islem_kaynagi_id'] > 0) {
            $where .= " AND GG.ISLEM_KAYNAGI_ID = :ISLEM_KAYNAGI_ID";
            $data[':ISLEM_KAYNAGI_ID'] = $request['islem_kaynagi_id'];
        }

        if (!empty($request['tip'])) {
            $where .= " AND GG.TIP = :TIP";
            $data[':TIP'] = $request['tip'];
        }

        if (!empty($request['hareket_durumu'])) {
            $where .= " AND GG.HAREKET_DURUMU = :HAREKET_DURUMU";
            $data[':HAREKET_DURUMU'] = $request['hareket_durumu'];
        }

        if (!empty($request['fatura_no'])) {
            $where .= " AND GG.FATURA_NO LIKE :FATURA_NO";
            $data[':FATURA_NO'] = '%' . trim($request['fatura_no']) . '%';
        }

        if (!empty($request['tutar_min'])) {
            $where .= " AND GG.TUTAR >= :TUTAR_MIN";
            $data[':TUTAR_MIN'] = FormatSayi::sayi2db($request['tutar_min']);
        }

        if (!empty($request['tutar_max'])) {
            $where .= " AND GG.TUTAR <= :TUTAR_MAX";
            $data[':TUTAR_MAX'] = FormatSayi::sayi2db($request['tutar_max']);
        }

        if (!empty($request['fatura_tarih']) && isset($request['fatura_tarih_var']) && ($request['fatura_tarih_var'] == 'on' || $request['fatura_tarih_var'] == '1' || $request['fatura_tarih_var'] > 0)) {
            $where .= " AND DATE(GG.FATURA_TARIH) >= :TARIH1 AND DATE(GG.FATURA_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['fatura_tarih']);
            $tarih1 = trim($tarih[0]);
            $tarih2 = isset($tarih[1]) ? trim($tarih[1]) : $tarih1;
            $data[':TARIH1'] = FormatTarih::nokta2db($tarih1);
            $data[':TARIH2'] = FormatTarih::nokta2db($tarih2);
        }

        return [$where, $data];
    }

    public function getGelirGiderler($request)
    {
        list($where, $data) = $this->buildFilterSql($request);

        $sql = "SELECT 
                    GG.*,
                    C.CARI,
                    GK.KATEGORI AS KATEGORI_ADI,
                    GK.ICON AS KATEGORI_ICON,
                    FIK.KAYNAK_ADI,
                    FIK.RENK AS KAYNAK_RENK,
                    FIK.ICON AS KAYNAK_ICON,
                    CONCAT_WS(' ', KU.AD, KU.SOYAD) AS KAYIT_YAPAN
                FROM GELIR_GIDER AS GG
                    LEFT JOIN CARI AS C ON C.ID = GG.CARI_ID
                    LEFT JOIN GELIR_GIDER_KATEGORI AS GK ON GK.ID = GG.KATEGORI_ID
                    LEFT JOIN FINANS_ISLEM_KAYNAGI AS FIK ON FIK.ID = GG.ISLEM_KAYNAGI_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = GG.KAYIT_YAPAN_ID
                " . $where . " ORDER BY GG.FATURA_TARIH DESC";

        $sayfaDegeri = isset($request['sayfalama']) && $request['sayfalama'] ? $request['sayfalama'] : 20;
        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $sayfaDegeri);
        $excel_sql = DB::getSQL($sql, $data);

        $sql .= $sayfalama->getLimitOffset();
        $rows = DB::get($sql, $data);

        // Fetch document attachments counts per row
        if (count2($rows) > 0) {
            foreach ($rows as $row) {
                $row->DOSYA_SAYISI = DB::getVar("SELECT COUNT(ID) FROM GELIR_GIDER_DOSYA WHERE GELIR_GIDER_ID = :ID AND DURUM = 1", [':ID' => $row->ID]);
            }
        }

        return [
            'rows' => $rows,
            'sayfalama' => $sayfalama,
            'limit' => $sayfalama->getLimitOffset(),
            'excel_sql' => $excel_sql,
            'sayfa_araligi' => $sayfalama->getGorunumAraligi()
        ];
    }

    public function getFinansOzet($request)
    {
        list($where, $data) = $this->buildFilterSql($request);

        $sql = "SELECT 
                    GG.TIP,
                    GG.HAREKET_DURUMU,
                    SUM(GG.TUTAR) AS TOPLAM
                FROM GELIR_GIDER AS GG
                " . $where . " GROUP BY GG.TIP, GG.HAREKET_DURUMU";

        $rows = DB::get($sql, $data);

        $ozet = [
            'GELIR_TAMAMLANDI' => 0.00,
            'GIDER_TAMAMLANDI' => 0.00,
            'GELIR_BEKLIYOR'    => 0.00,
            'GIDER_BEKLIYOR'    => 0.00,
            'NET_NAKIT'         => 0.00
        ];

        foreach ($rows as $row) {
            $key = $row->TIP . '_' . $row->HAREKET_DURUMU;
            if (array_key_exists($key, $ozet)) {
                $ozet[$key] = floatval($row->TOPLAM);
            }
        }

        $ozet['NET_NAKIT'] = $ozet['GELIR_TAMAMLANDI'] - $ozet['GIDER_TAMAMLANDI'];

        return $ozet;
    }

    public function getIslemKaynaklari()
    {
        return DB::get("SELECT * FROM FINANS_ISLEM_KAYNAGI WHERE DURUM = 1 ORDER BY SIRA ASC");
    }

    public function getGelirGider($id){
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER WHERE ID = :ID";
        $data[':ID'] = $id;
        return DB::getRow($sql, $data);
    }

    public static function logEkle($referansId, $referansNo, $islemTuru, $alan, $eskiDeger, $yeniDeger, $aciklama) {
        $kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : 0;
        $kullanici_adsoyad = isset($_SESSION['ad_soyad']) ? $_SESSION['ad_soyad'] : (isset($_SESSION['kullanici']) ? $_SESSION['kullanici'] : 'Sistem');
        if ($kullanici_id > 0 && ($kullanici_adsoyad == 'Sistem' || empty($kullanici_adsoyad))) {
            $user = DB::getRow("SELECT CONCAT_WS(' ', AD, SOYAD) AS ADSOYAD FROM KULLANICI WHERE ID = :ID", [':ID' => $kullanici_id]);
            if ($user && !empty($user->ADSOYAD)) {
                $kullanici_adsoyad = $user->ADSOYAD;
            }
        }
        $tarih = date('Y-m-d');
        $saat = date('H:i:s');
        
        $sql = "INSERT INTO GENEL_LOG SET 
                    LOG_TURU = 'Finans',
                    REFERANS_ID = :REFERANS_ID,
                    REFERANS_NO = :REFERANS_NO,
                    ISLEM_TURU = :ISLEM_TURU,
                    KULLANICI_ID = :KULLANICI_ID,
                    KULLANICI_ADSOYAD = :KULLANICI_ADSOYAD,
                    ALAN = :ALAN,
                    ESKI_DEGER = :ESKI_DEGER,
                    YENI_DEGER = :YENI_DEGER,
                    ACIKLAMA = :ACIKLAMA,
                    TARIH = :TARIH,
                    SAAT = :SAAT";
                    
        DB::insert($sql, [
            ':REFERANS_ID' => $referansId,
            ':REFERANS_NO' => $referansNo,
            ':ISLEM_TURU' => $islemTuru,
            ':KULLANICI_ID' => $kullanici_id,
            ':KULLANICI_ADSOYAD' => $kullanici_adsoyad,
            ':ALAN' => $alan,
            ':ESKI_DEGER' => $eskiDeger,
            ':YENI_DEGER' => $yeniDeger,
            ':ACIKLAMA' => $aciklama,
            ':TARIH' => $tarih,
            ':SAAT' => $saat
        ]);
    }

    public function gelir_gider_kaydet(){

        if ($_REQUEST['cari_id'] <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Cari Seçiniz!";
            return $result;
        }

        if (FormatSayi::sayi2db($_REQUEST['tutar']) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Tutar Giriniz!";
            return $result;
        }

        if (empty($_REQUEST['tip']) || !in_array($_REQUEST['tip'], ['GELIR', 'GIDER'])) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Geçerli Bir Tip (Gelir/Gider) Seçiniz!";
            return $result;
        }

        $tutar = FormatSayi::sayi2db($_REQUEST['tutar']);
        $fatura_no = trim($_REQUEST['fatura_no'] ?? '');
        $hareket_durumu = !empty($_REQUEST['hareket_durumu']) ? $_REQUEST['hareket_durumu'] : 'BEKLIYOR';
        $fatura_tarih = !empty($_REQUEST['fatura_tarih']) ? FormatTarih::nokta2db($_REQUEST['fatura_tarih']) : NULL;
        $odeme_tarihi = ($hareket_durumu == 'TAMAMLANDI') ? (!empty($_REQUEST['odeme_tarihi']) ? FormatTarih::nokta2db($_REQUEST['odeme_tarihi']) : date('Y-m-d H:i:s')) : NULL;

        $data = array();
        $sql = "INSERT INTO GELIR_GIDER SET     TIP             = :TIP,
                                                HAREKET_DURUMU  = :HAREKET_DURUMU,
                                                CARI_ID         = :CARI_ID,
                                                KATEGORI_ID     = :KATEGORI_ID,
                                                ISLEM_KAYNAGI_ID= 1,
                                                TUTAR           = :TUTAR,
                                                FATURA_NO       = :FATURA_NO,
                                                FATURA_TARIH    = :FATURA_TARIH,
                                                ODEME_TARIHI    = :ODEME_TARIHI,
                                                ACIKLAMA        = :ACIKLAMA,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                DURUM           = 1
                                                ";
        $data[":TIP"]               = $_REQUEST['tip'];
        $data[":HAREKET_DURUMU"]    = $hareket_durumu;
        $data[":CARI_ID"]           = $_REQUEST['cari_id'];
        $data[":KATEGORI_ID"]       = $_REQUEST['kategori_id'] > 0 ? $_REQUEST['kategori_id'] : NULL;
        $data[":TUTAR"]             = $tutar;
        $data[":FATURA_NO"]         = !empty($fatura_no) ? $fatura_no : NULL;
        $data[":FATURA_TARIH"]      = $fatura_tarih;
        $data[":ODEME_TARIHI"]      = $odeme_tarihi;
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama'] ?? '');
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        
        $id = DB::insert($sql, $data);

        if ($id > 0) {
            $this->fncDosyaYukle($id);
            // Log creation
            self::logEkle($id, $fatura_no ? $fatura_no : $id, 'Kayıt', 'Yeni Giriş', '', '', "Yeni Gelir/Gider hareketi oluşturuldu. Tutar: " . FormatSayi::sayi($tutar, 2) . " ₺");
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "İşlem Kaydedildi.";
            $result["ID"] = $id;
        }else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }

        return $result;
    }

    public function gelir_gider_guncelle(){

        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER WHERE ID = :ID";
        $data[':ID'] = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if ($row->ID <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        // Tek Kayit Prensibi
        if ($row->ISLEM_KAYNAGI_ID != 1) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Bu kayıt Malzeme Alışı modülü tarafından yönetilmektedir. Doğrudan düzenlenemez!";
            return $result;
        }

        if ($_REQUEST['cari_id'] <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Cari Seçiniz!";
            return $result;
        }

        if (FormatSayi::sayi2db($_REQUEST['tutar']) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Tutar Giriniz!";
            return $result;
        }

        if (empty($_REQUEST['tip']) || !in_array($_REQUEST['tip'], ['GELIR', 'GIDER'])) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Geçerli Bir Tip (Gelir/Gider) Seçiniz!";
            return $result;
        }

        $tutar = FormatSayi::sayi2db($_REQUEST['tutar']);
        $fatura_no = trim($_REQUEST['fatura_no'] ?? '');
        $hareket_durumu = !empty($_REQUEST['hareket_durumu']) ? $_REQUEST['hareket_durumu'] : 'BEKLIYOR';
        $fatura_tarih = !empty($_REQUEST['fatura_tarih']) ? FormatTarih::nokta2db($_REQUEST['fatura_tarih']) : NULL;
        $odeme_tarihi = ($hareket_durumu == 'TAMAMLANDI') ? (!empty($_REQUEST['odeme_tarihi']) ? FormatTarih::nokta2db($_REQUEST['odeme_tarihi']) : date('Y-m-d H:i:s')) : NULL;

        // Logging preparation
        $logChanges = function($alan, $eski, $yeni) use ($row) {
            if (trim($eski ?? '') != trim($yeni ?? '')) {
                self::logEkle($row->ID, $row->FATURA_NO ? $row->FATURA_NO : $row->ID, 'Güncelleme', $alan, $eski, $yeni, "$alan bilgisi güncellendi.");
            }
        };

        $data = array();
        $sql = "UPDATE GELIR_GIDER SET  TIP             = :TIP,
                                        HAREKET_DURUMU  = :HAREKET_DURUMU,
                                        CARI_ID         = :CARI_ID,
                                        KATEGORI_ID     = :KATEGORI_ID,
                                        TUTAR           = :TUTAR,
                                        FATURA_NO       = :FATURA_NO,
                                        FATURA_TARIH    = :FATURA_TARIH,
                                        ODEME_TARIHI    = :ODEME_TARIHI,
                                        ACIKLAMA        = :ACIKLAMA
                                    WHERE ID = :ID
                                    ";

        $data[":TIP"]               = $_REQUEST['tip'];
        $data[":HAREKET_DURUMU"]    = $hareket_durumu;
        $data[":CARI_ID"]           = $_REQUEST['cari_id'];
        $data[":KATEGORI_ID"]       = $_REQUEST['kategori_id'] > 0 ? $_REQUEST['kategori_id'] : NULL;
        $data[":TUTAR"]             = $tutar;
        $data[":FATURA_NO"]         = !empty($fatura_no) ? $fatura_no : NULL;
        $data[":FATURA_TARIH"]      = $fatura_tarih;
        $data[":ODEME_TARIHI"]      = $odeme_tarihi;
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama'] ?? '');
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update !== false) {
            $this->fncDosyaYukle($row->ID);
            // Write logs
            $logChanges('Tip', $row->TIP, $_REQUEST['tip']);
            $logChanges('Hareket Durumu', $row->HAREKET_DURUMU, $hareket_durumu);
            $logChanges('Cari ID', $row->CARI_ID, $_REQUEST['cari_id']);
            $logChanges('Kategori ID', $row->KATEGORI_ID, $_REQUEST['kategori_id']);
            $logChanges('Tutar', $row->TUTAR, $tutar);
            $logChanges('Fatura No', $row->FATURA_NO, $fatura_no);
            $logChanges('Fatura Tarihi', $row->FATURA_TARIH, $_REQUEST['fatura_tarih']);
            $logChanges('Ödeme Tarihi', $row->ODEME_TARIHI, $odeme_tarihi);
            $logChanges('Açıklama', $row->ACIKLAMA, $_REQUEST['aciklama']);

            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "İşlem Güncellendi.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Güncelleme Başarısız.";
        }

        return $result;
    }

    public function gelir_gider_sil(){

        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER WHERE ID = :ID";
        $data[":ID"] = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if (is_null($row->ID)) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        // Tek Kayit Prensibi
        if ($row->ISLEM_KAYNAGI_ID != 1) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Bu kayıt Malzeme Alışı modülü tarafından yönetilmektedir. Doğrudan silinemez!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE GELIR_GIDER SET DURUM = 0 WHERE ID = :ID";
        $data[":ID"] = $row->ID;
        $update = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "GELIR_GIDER", "GELIR_GIDER_SIL");

        if ($update > 0) {
            self::logEkle($row->ID, $row->FATURA_NO ? $row->FATURA_NO : $row->ID, 'Silme', 'Durum', '1', '0', "Gelir/Gider hareketi silindi.");
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Silindi.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }

        return $result;
    }

    public function getKategoriler($request = array())
    {
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER_KATEGORI WHERE DURUM = 1";
        if (!empty($request['tip'])) {
            $sql .= " AND TIP = :TIP";
            $data[':TIP'] = $request['tip'];
        }
        $sql .= " ORDER BY KATEGORI ASC";
        return DB::get($sql, $data);
    }

    public function getKategori($id)
    {
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER_KATEGORI WHERE ID = :ID";
        $data[':ID'] = $id;
        return DB::getRow($sql, $data);
    }

    public function kategori_ekle()
    {
        if (strlen(trim($_REQUEST['kategori'])) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kategori Adı Giriniz!";
            return $result;
        }
        if (empty($_REQUEST['tip']) || !in_array($_REQUEST['tip'], ['GELIR', 'GIDER'])) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Geçerli Bir Tip Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO GELIR_GIDER_KATEGORI SET 
                    KATEGORI = :KATEGORI,
                    TIP      = :TIP,
                    ICON     = :ICON,
                    DURUM    = :DURUM";
        $data[":KATEGORI"] = trim($_REQUEST['kategori']);
        $data[":TIP"]      = $_REQUEST['tip'];
        $data[":ICON"]     = !empty($_REQUEST['icon']) ? trim($_REQUEST['icon']) : 'ri-folder-line';
        $data[":DURUM"]    = isset($_REQUEST['durum']) ? $_REQUEST['durum'] : 1;

        $id = DB::insert($sql, $data);
        if ($id > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Kategori Oluşturuldu.";
        } else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }
        return $result;
    }

    public function kategori_bilgisi()
    {
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER_KATEGORI WHERE ID = :ID";
        $data[":ID"] = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if ($row->ID > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Kategori Düzenle.";
            $result["ROW"] = $row;
        } else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kategori Bulunamadı!";
        }
        return $result;
    }

    public function kategori_kaydet()
    {
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER_KATEGORI WHERE ID = :ID";
        $data[":ID"] = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if (is_null($row->ID)) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kategori Bulunamadı!";
            return $result;
        }

        if (strlen(trim($_REQUEST['kategori'])) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kategori Adı Giriniz!";
            return $result;
        }

        if (empty($_REQUEST['tip']) || !in_array($_REQUEST['tip'], ['GELIR', 'GIDER'])) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Geçerli Bir Tip Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE GELIR_GIDER_KATEGORI SET 
                    KATEGORI = :KATEGORI,
                    TIP      = :TIP,
                    ICON     = :ICON,
                    DURUM    = :DURUM
                WHERE ID = :ID";
        $data[":KATEGORI"] = trim($_REQUEST['kategori']);
        $data[":TIP"]      = $_REQUEST['tip'];
        $data[":ICON"]     = !empty($_REQUEST['icon']) ? trim($_REQUEST['icon']) : 'ri-folder-line';
        $data[":DURUM"]    = isset($_REQUEST['durum']) ? $_REQUEST['durum'] : 1;
        $data[":ID"]       = $row->ID;

        $update = DB::exec($sql, $data);
        if ($update !== false) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Kategori Güncellendi.";
        } else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Güncelleme Başarısız.";
        }
        return $result;
    }

    public function kategori_sil()
    {
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER_KATEGORI WHERE ID = :ID";
        $data[":ID"] = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if (is_null($row->ID)) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kategori Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE GELIR_GIDER_KATEGORI SET DURUM = 0 WHERE ID = :ID";
        $data[":ID"] = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Kategori Silindi.";
        } else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }
        return $result;
    }

    public function getFiles($gelir_gider_id)
    {
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER_DOSYA WHERE GELIR_GIDER_ID = :GG_ID AND DURUM = 1 ORDER BY ID ASC";
        $data[':GG_ID'] = $gelir_gider_id;
        return DB::get($sql, $data);
    }

    public function dosya_sil()
    {
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER_DOSYA WHERE ID = :ID";
        $data[':ID'] = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if (!$row) {
            return ["HATA" => TRUE, "ACIKLAMA" => "Dosya Bulunamadı!"];
        }

        // Check if parent record is locked
        $parent = DB::getRow("SELECT ISLEM_KAYNAGI_ID FROM GELIR_GIDER WHERE ID = :ID", [':ID' => $row->GELIR_GIDER_ID]);
        if ($parent && $parent->ISLEM_KAYNAGI_ID != 1) {
            return ["HATA" => TRUE, "ACIKLAMA" => "Bu kayıt Malzeme Alışı tarafından yönetilmektedir. Evrakları silinemez!"];
        }

        // Soft delete file
        DB::exec("UPDATE GELIR_GIDER_DOSYA SET DURUM = 0 WHERE ID = :ID", [':ID' => $row->ID]);
        
        // Log file deletion
        self::logEkle($row->GELIR_GIDER_ID, $row->ID, 'Silme', 'Dosya', $row->DOSYA_ADI, '', "Finans evrakı silindi: " . $row->DOSYA_ADI);
        
        return ["HATA" => FALSE, "ACIKLAMA" => "Dosya Silindi."];
    }

    private function fncDosyaYukle($gelir_gider_id)
    {
        if (!empty($_FILES['evraklar']['tmp_name'][0])) {
            $yol = $_SERVER['DOCUMENT_ROOT'] . '/img/gelir_gider/';
            if (!is_dir($yol)) {
                mkdir($yol, 0777, true);
            }
            
            foreach ($_FILES['evraklar']['tmp_name'] as $key => $tmp_name) {
                if (empty($tmp_name)) continue;
                
                $orj_ad = $_FILES['evraklar']['name'][$key];
                $ext = strtolower(pathinfo($orj_ad, PATHINFO_EXTENSION));
                
                if (!in_array($ext, ['pdf', 'png', 'jpg', 'jpeg'])) {
                    continue; // Skip invalid extensions
                }
                
                // Generate unique name
                $new_name = md5(uniqid(rand(), true)) . '.' . $ext;
                $dosyaYolu = $yol . $new_name;
                
                if (move_uploaded_file($tmp_name, $dosyaYolu)) {
                    $boyut = $_FILES['evraklar']['size'][$key];
                    $aciklama = isset($_POST['evrak_aciklama'][$key]) ? trim($_POST['evrak_aciklama'][$key]) : '';
                    
                    $sql = "INSERT INTO GELIR_GIDER_DOSYA SET 
                                GELIR_GIDER_ID = :GELIR_GIDER_ID,
                                DOSYA_YOLU     = :DOSYA_YOLU,
                                DOSYA_ADI      = :DOSYA_ADI,
                                ACIKLAMA       = :ACIKLAMA,
                                BOYUT          = :BOYUT,
                                UZANTI         = :UZANTI,
                                KAYIT_YAPAN_ID = :KAYIT_YAPAN_ID,
                                DURUM          = 1";
                    DB::insert($sql, [
                        ':GELIR_GIDER_ID'=> $gelir_gider_id,
                        ':DOSYA_YOLU'    => '/img/gelir_gider/' . $new_name,
                        ':DOSYA_ADI'     => $orj_ad,
                        ':ACIKLAMA'      => $aciklama,
                        ':BOYUT'         => $boyut,
                        ':UZANTI'        => $ext,
                        ':KAYIT_YAPAN_ID'=> $_SESSION['kullanici_id']
                    ]);
            }
        }
    }
}

    public function dosya_listesi()
    {
        $rows = $this->getFiles($_REQUEST['gelir_gider_id']);
        return ["HATA" => FALSE, "ROWS" => $rows];
    }

}
