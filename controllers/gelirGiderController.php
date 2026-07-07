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

    public function getGelirGiderler($request){

        $data = array();
        $sql = "SELECT 
                    GG.*,
                    C.CARI,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM GELIR_GIDER AS GG
                    LEFT JOIN CARI AS C ON C.ID = GG.CARI_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = GG.KAYIT_YAPAN_ID
                WHERE GG.DURUM = 1
                ";

        if ($request['cari_id'] > 0) {
            $sql .= " AND GG.CARI_ID = :CARI_ID";
            $data[':CARI_ID'] = $request['cari_id'];
        }

        if (!empty($request['tip'])) {
            $sql .= " AND GG.TIP = :TIP";
            $data[':TIP'] = $request['tip'];
        }

        if(!empty($request['fatura_tarih']) AND $request['fatura_tarih_var'] > 0){
            $sql .= " AND DATE(GG.FATURA_TARIH) >= :TARIH1 AND DATE(GG.FATURA_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['fatura_tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
        }

        $sql .= " ORDER BY GG.FATURA_TARIH DESC";

        $sayfaDegeri = isset($request['sayfalama']) && $request['sayfalama'] ? $request['sayfalama'] : 20;
        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $sayfaDegeri);
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

    public function getGelirGider($id){
        $data = array();
        $sql = "SELECT * FROM GELIR_GIDER WHERE ID = :ID";
        $data[':ID'] = $id;
        return DB::getRow($sql, $data);
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

        $data = array();
        $sql = "INSERT INTO GELIR_GIDER SET     TIP             = :TIP,
                                                CARI_ID         = :CARI_ID,
                                                TUTAR           = :TUTAR,
                                                ACIKLAMA        = :ACIKLAMA,
                                                FATURA_TARIH    = :FATURA_TARIH,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
        $data[":TIP"]               = $_REQUEST['tip'];
        $data[":CARI_ID"]           = $_REQUEST['cari_id'];
        $data[":TUTAR"]             = $tutar;
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":FATURA_TARIH"]      = !empty($_REQUEST['fatura_tarih']) ? $_REQUEST['fatura_tarih'] : NULL;
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        if ($id > 0) {
            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "İşlem Kaydedildi.";
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

        $data = array();
        $sql = "UPDATE GELIR_GIDER SET  TIP             = :TIP,
                                        CARI_ID         = :CARI_ID,
                                        TUTAR           = :TUTAR,
                                        ACIKLAMA        = :ACIKLAMA,
                                        FATURA_TARIH    = :FATURA_TARIH
                                    WHERE ID = :ID
                                    ";

        $data[":TIP"]               = $_REQUEST['tip'];
        $data[":CARI_ID"]           = $_REQUEST['cari_id'];
        $data[":TUTAR"]             = $tutar;
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":FATURA_TARIH"]      = !empty($_REQUEST['fatura_tarih']) ? $_REQUEST['fatura_tarih'] : NULL;
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update !== false) {
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

        $data = array();
        $sql = "UPDATE GELIR_GIDER SET DURUM = 0 WHERE ID = :ID";
        $data[":ID"] = $row->ID;
        $update = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "GELIR_GIDER", "GELIR_GIDER_SIL");

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

}
