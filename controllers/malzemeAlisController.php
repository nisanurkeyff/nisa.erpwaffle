<?

require_once($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class MalzemeAlisController
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

    public function Malzemeler()
    {
        $data = array();
        $sql = "SELECT
                    M.ID,
                    M.MALZEME AS AD
                FROM MALZEME AS M
                WHERE M.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
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

    public function OdemeDurum(){

        $data = array();
        $sql = "SELECT
                    OD.ID,
                    OD.ODEME_DURUM AS AD
                FROM ODEME_DURUM AS OD
                WHERE OD.DURUM = 1
                ";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function getMalzemeAlislar($request){

        $data = array();
        $sql = "SELECT 
                    MA.*,
                    C.CARI,
                    M.MALZEME,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN,
                    OD.ODEME_DURUM
                FROM MALZEME_ALIS AS MA
                    LEFT JOIN CARI AS C ON C.ID = MA.CARI_ID
                    LEFT JOIN MALZEME AS M ON M.ID = MA.MALZEME_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = MA.KAYIT_YAPAN_ID
                    LEFT JOIN ODEME_DURUM AS OD ON OD.ID = MA.ODEME_DURUM_ID
                WHERE MA.DURUM = 1
                ";

        if ($request['cari_id'] > 0) {
            $sql .= " AND MA.CARI_ID = :CARI_ID";
            $data[':CARI_ID'] = $request['cari_id'];
        }

        if ($request['malzeme_id'] > 0) {
            $sql .= " AND MA.MALZEME_ID = :MALZEME_ID";
            $data[':MALZEME_ID'] = $request['malzeme_id'];
        }

        if ($request['odeme_durum_id'] > 0) {
            $sql .= " AND MA.ODEME_DURUM_ID = :ODEME_DURUM_ID";
            $data[':ODEME_DURUM_ID'] = $request['odeme_durum_id'];
        }

        if(!empty($request['fatura_tarih']) AND $request['fatura_tarih_var'] > 0){
            $sql .= " AND DATE(MA.FATURA_TARIH) >= :TARIH1 AND DATE(MA.FATURA_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['fatura_tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
        }

        $sql .= " ORDER BY MA.TARIH DESC";

        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 20);
        $excel_sql = DB::getSQL($sql, $data);

        $sql .= $sayfalama->getLimitOffset();
        $rows = DB::get($sql, $data);

        return [
            'rows' => $rows,
            'sayfalama' => $sayfalama,
            'limit' => $sayfalama->getLimitOffset(),
            'excel_sql' => $excel_sql,
            'sayfa_araligi' => $sayfalama->getGorunumAraligi()
        ];
    }

    public function getMalzemeAlis($id){
        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[':ID'] = $id;
        return DB::getRow($sql, $data);
    }

    public function malzeme_alis_kaydet(){

        if ($_REQUEST['cari_id'] <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Cari Seçiniz!";
            return $result;
        }

        if ($_REQUEST['malzeme_id'] <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Malzeme Seçiniz!";
            return $result;
        }

        if (FormatSayi::sayi2db($_REQUEST['miktar']) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Miktar Giriniz!";
            return $result;
        }

        if (FormatSayi::sayi2db($_REQUEST['birim_fiyat']) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Birim Fiyat Giriniz!";
            return $result;
        }

        $miktar = FormatSayi::sayi2db($_REQUEST['miktar']);
        $birim_fiyat = FormatSayi::sayi2db($_REQUEST['birim_fiyat']);
        $kdv_oran = FormatSayi::sayi2db($_REQUEST['kdv']);

        $ara_toplam = $miktar * $birim_fiyat;
        $kdv_tutar = ($ara_toplam * $kdv_oran) / 100;
        $toplam_tutar = $ara_toplam + $kdv_tutar;

        $data = array();
        $sql = "INSERT INTO MALZEME_ALIS SET    CARI_ID         = :CARI_ID,
                                                MALZEME_ID      = :MALZEME_ID,
                                                MIKTAR          = :MIKTAR,
                                                BIRIM_FIYAT     = :BIRIM_FIYAT,
                                                ARA_TOPLAM      = :ARA_TOPLAM,
                                                KDV_TUTAR       = :KDV_TUTAR,
                                                KDV             = :KDV,
                                                TOPLAM_TUTAR    = :TOPLAM_TUTAR,
                                                ACIKLAMA        = :ACIKLAMA,
                                                FATURA_TARIH    = :FATURA_TARIH,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
        $data[":CARI_ID"]           = $_REQUEST['cari_id'];
        $data[":MALZEME_ID"]        = $_REQUEST['malzeme_id'];
        $data[":MIKTAR"]            = $miktar;
        $data[":BIRIM_FIYAT"]       = $birim_fiyat;
        $data[":ARA_TOPLAM"]        = $ara_toplam;
        $data[":KDV_TUTAR"]         = $kdv_tutar;
        $data[":KDV"]               = $_REQUEST['kdv'];
        $data[":TOPLAM_TUTAR"]      = $toplam_tutar;
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":FATURA_TARIH"]      = $_REQUEST['fatura_tarih'] ? $_REQUEST['fatura_tarih'] : NULL;
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        if ($id > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Alış Kaydedildi.";
        }else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }

        return $result;
    }

    public function malzeme_alis_guncelle(){

        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[':ID'] = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if ($row->ID <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        if ($_REQUEST['cari_id'] <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Cari Seçiniz!";
            return $result;
        }

        if ($_REQUEST['malzeme_id'] <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Malzeme Seçiniz!";
            return $result;
        }

        if (FormatSayi::sayi2db($_REQUEST['miktar']) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Miktar Giriniz!";
            return $result;
        }

        if (FormatSayi::sayi2db($_REQUEST['birim_fiyat']) <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Birim Fiyat Giriniz!";
            return $result;
        }

        $miktar = FormatSayi::sayi2db($_REQUEST['miktar']);
        $birim_fiyat = FormatSayi::sayi2db($_REQUEST['birim_fiyat']);
        $kdv_oran = FormatSayi::sayi2db($_REQUEST['kdv']);

        $ara_toplam = $miktar * $birim_fiyat;
        $kdv_tutar = ($ara_toplam * $kdv_oran) / 100;
        $toplam_tutar = $ara_toplam + $kdv_tutar;

        $data = array();
        $sql = "UPDATE MALZEME_ALIS SET CARI_ID         = :CARI_ID,
                                        MALZEME_ID      = :MALZEME_ID,
                                        MIKTAR          = :MIKTAR,
                                        BIRIM_FIYAT     = :BIRIM_FIYAT,
                                        ARA_TOPLAM      = :ARA_TOPLAM,
                                        KDV_TUTAR       = :KDV_TUTAR,
                                        KDV             = :KDV,
                                        TOPLAM_TUTAR    = :TOPLAM_TUTAR,
                                        ACIKLAMA        = :ACIKLAMA,
                                        FATURA_TARIH    = :FATURA_TARIH
                                    WHERE ID = :ID
                                    ";

        $data[":CARI_ID"]           = $_REQUEST['cari_id'];
        $data[":MALZEME_ID"]        = $_REQUEST['malzeme_id'];
        $data[":MIKTAR"]            = $miktar;
        $data[":BIRIM_FIYAT"]       = $birim_fiyat;
        $data[":ARA_TOPLAM"]        = $ara_toplam;
        $data[":KDV_TUTAR"]         = $kdv_tutar;
        $data[":KDV"]               = $_REQUEST['kdv'];
        $data[":TOPLAM_TUTAR"]      = $toplam_tutar;
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":FATURA_TARIH"]      = $_REQUEST['fatura_tarih'] ? $_REQUEST['fatura_tarih'] : NULL;
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update !== false) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Alış Güncellendi.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Güncelleme Başarısız.";
        }

        return $result;
    }

    public function malzeme_alis_sil(){

        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[":ID"] = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if (is_null($row->ID)) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE MALZEME_ALIS SET DURUM = 0 WHERE ID = :ID";
        $data[":ID"] = $row->ID;
        $update = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "MALZEME_ALIS", "MALZEME_ALIS_SIL");

        if ($update > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Silindi.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }

        return $result;
    }

    public function malzeme_alis_odeme_yap(){

        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[':ID'] = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if ($row->ID <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE MALZEME_ALIS SET ODEME_DURUM_ID = 2 WHERE ID = :ID";
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Ödeme Yapıldı.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata.";
        }

        return $result;
    }

    public function malzeme_alis_odeme_red(){

        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[':ID'] = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if ($row->ID <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE MALZEME_ALIS SET ODEME_DURUM_ID = 3 WHERE ID = :ID";
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Ödeme Yapıldı.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata.";
        }

        return $result;
    }
}
