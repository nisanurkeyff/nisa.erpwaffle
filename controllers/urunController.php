<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class UrunController {

    private $select;
    private $site;
    private $resim_yol;

    public function __construct($select = "", $row_site = "") {
        $this->select       = $select;
        $this->site        = $row_site;
        $this->resim_yol    = $resim_yol;
    }

    public function sayfalamaOlustur($toplamVeri, $request, $sayfaBasinaVeri = 10){
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $baseUrl = $protocol . $host . $scriptName;

        $gecerliSayfa = isset($request['page']) ? (int) $request['page'] : 1;
        $request['page'] = null;

        $queryString = http_build_query(array_filter($request, function ($value) {return $value !== null;}));
        $url = $baseUrl . '?' . $queryString;
        return new Sayfalama($toplamVeri, $sayfaBasinaVeri, $gecerliSayfa, $url);
    }

    public function StokMarkalar() {
        $data = array();
        $sql = "SELECT
                    SM.ID,
                    SM.STOK_MARKA AS AD
                FROM STOK_MARKA AS SM
                WHERE SM.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Durum() {
        $data = array();
        $sql = "SELECT
                    D.ID,
                    D.DURUM AS AD
                FROM DURUM AS D
                WHERE 1";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Urunler() {
        $data = array();
        $sql = "SELECT
                    U.ID,
                    U.URUN AS AD
                FROM URUN AS U
                WHERE U.DURUM = 1
                ORDER BY 2
                ";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Siralama() {

        $data = array();
        $sql = "SELECT
                    S.ID,
                    S.SIRALAMA AS AD
                FROM SIRALAMA AS S
                WHERE 1
                ";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Kdv() {
        $data = array();
        $sql = "SELECT
                    K.KDV AS ID,
                    K.KDV AS AD
                FROM KDV AS K
                WHERE K.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Kategoriler() {
        $data = array();
        $sql = "SELECT
                    K.ID,
                    K.KATEGORI AS AD
                FROM KATEGORI AS K
                WHERE K.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function UstKategoriler() {
        $data = array();
        $sql = "SELECT
                    UK.ID,
                    UK.UST_KATEGORI AS AD
                FROM UST_KATEGORI AS UK
                WHERE UK.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function SecimTuru() {
        $data = array();
        $sql = "SELECT
                    S.ID,
                    S.SECIM AS AD
                FROM SECIM AS S
                WHERE S.DURUM = 1 AND S.SECIM_TURU IN('TUR')
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function SecimIslem() {
        $data = array();
        $sql = "SELECT
                    S.ID,
                    S.SECIM AS AD
                FROM SECIM AS S
                WHERE S.DURUM = 1 AND S.SECIM_TURU IN('ISLEM')
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function SatisTürleri() {
        $data = array();
        $sql = "SELECT
                    ST.ID,
                    ST.SATIS_TURU AS AD
                FROM SATIS_TURU AS ST
                WHERE ST.DURUM = 1";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Kullanicilar() {
        $data = array();
        $sql = "SELECT
                    K.ID,
                    CONCAT_WS(' ',K.AD,K.SOYAD) AS AD
                FROM KULLANICI AS K
                WHERE K.DURUM = 1
                ";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Sayfalama() {
        $data = array();
        $sql = "SELECT
                    S.SAYFALAMA AS ID,
                    S.SAYFALAMA AS AD
                FROM SAYFALAMA AS S
                WHERE S.DURUM = 1
                ";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Subeler() {
        $data = array();
        $sql = "SELECT
                    S.ID,
                    S.SUBE AS AD
                FROM SUBE AS S
                WHERE S.DURUM = 1";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function HamurKullanim() {
        $data = array();
        $sql = "SELECT
                    HK.ID,
                    HK.HAMUR_KULLANIM AS AD
                FROM HAMUR_KULLANIM AS HK
                WHERE HK.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function urun_ekle() {

        if(strlen(trim($_REQUEST['urun'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Adı Giriniz!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM URUN WHERE URUN = :URUN";
        $data[":URUN"]              = trim($_REQUEST['urun']);
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "{$row->URUN} Daha Önce Kayıt Edilmiş!";
            return $result;
        }

        if(FormatSayi::sayi2db($_REQUEST['fiyat']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Fiyat Giriniz!";
            return $result;
        }
       

        if($_REQUEST['kategori_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO URUN SET    URUN                = :URUN,
                                        ACIKLAMA            = :ACIKLAMA,
                                        KATEGORI_ID         = :KATEGORI_ID,
                                        HAMUR_KULLANIM_ID   = :HAMUR_KULLANIM_ID,
                                        FIYAT               = :FIYAT,
                                        DURUM               = :DURUM,
                                        KAYIT_YAPAN_ID      = :KAYIT_YAPAN_ID,
                                        TOKEN               = MD5(NOW())
                                        ";
        $data[":URUN"]                  = trim($_REQUEST['urun']);
        $data[":ACIKLAMA"]              = trim($_REQUEST['aciklama']);
        $data[":FIYAT"]                 = FormatSayi::sayi2db($_REQUEST['fiyat']);
        $data[":KATEGORI_ID"]           = $_REQUEST['kategori_id'];
        $data[":HAMUR_KULLANIM_ID"]     = $_REQUEST['hamur_kullanim_id'];
        $data[":DURUM"]                 = $_REQUEST['durum'];
        $data[":KAYIT_YAPAN_ID"]        = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT * FROM URUN WHERE ID = :ID";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Stok Oluşturuldu.";
            $result["URL"]       = "/views/urun/urun_duzenle.php?id={$row->ID}&token={$row->TOKEN}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getUrunler($request) {
        global $row_site;

        $data = array();
        $sql = "SELECT 
                    U.*,
                    K.KATEGORI,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN,
                    CONCAT('urun/', U.ID, '/', YEAR(U.TARIH), '/', UR.RESIM_ADI) AS RESIM_URL,
                    IF(U.DURUM = 1, 'Aktif', 'Pasif') AS DURUM_TEXT
                FROM URUN AS U
                    LEFT JOIN URUN_RESIM AS UR ON UR.URUN_ID = U.ID AND UR.VITRIN = 1
                    LEFT JOIN KATEGORI AS K ON K.ID = U.KATEGORI_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = U.KAYIT_YAPAN_ID
                WHERE 1";

        if($request['urun']){
            $sql .= " AND U.URUN LIKE :URUN";
            $data[':URUN'] = "%" . $request['urun'] . "%";
        }

        if($request['satis_turu_id'] > 0){
            $sql .= " AND U.SATIS_TURU_ID = :SATIS_TURU_ID";
            $data[':SATIS_TURU_ID'] = $request['satis_turu_id'];
        }

        if($request['kategori_id'] > 0){
            $sql .= " AND U.KATEGORI_ID = :KATEGORI_ID";
            $data[':KATEGORI_ID'] = $request['kategori_id'];
        }

        if($request['ust_kategori_id'] > 0){
            $sql .= " AND K.UST_KATEGORI_ID = :UST_KATEGORI_ID";
            $data[':UST_KATEGORI_ID'] = $request['ust_kategori_id'];
        }

        if(in_array2($request['durum'],array(0,1))){
            $sql .= " AND U.DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }
        
        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 50);
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

    function urun_duzenle() {
        
        $data = array();
        $sql = "SELECT * FROM URUN WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Ürün Düzenle.";
            $result["URL"]       = $href;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Ürün Bulunamadı!";
        }

        return $result;
    }

    public function getUrun($request) {

        $data = array();
        $sql = "SELECT 
                    U.*,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM URUN AS U
                    LEFT JOIN KULLANICI AS KU ON KU.ID = U.KAYIT_YAPAN_ID
                WHERE U.ID =:ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::getRow($sql, $data);
        return $row;
    }

    public function getStokResimler($request) {

        $data = array();
        $sql = "SELECT 
                    UR.*,
                    CONCAT('urun/', U.ID, '/', YEAR(U.TARIH), '/', UR.RESIM_ADI) AS RESIM_URL,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM URUN_RESIM AS UR
                    LEFT JOIN URUN AS U ON U.ID = UR.URUN_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = U.KAYIT_YAPAN_ID
                WHERE U.ID =:ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::get($sql, $data);
        return $row;
    }

    public function getUrunSayisi() {

        $data = array();
        $sql = "SELECT 
                    SUM(IF(U.DURUM = 1, 1, 0)) AS AKTIF,
                    SUM(IF(U.DURUM = 0, 1, 0)) AS PASIF
                FROM URUN AS U
                WHERE 1
                ";

        $row = DB::getRow($sql, $data);
        return $row;
    }

    public function urun_kaydet() {
        
        $data = array();
        $sql = "SELECT * FROM URUN WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['urun'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Adı Giriniz!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM URUN WHERE URUN = :URUN AND ID != :ID";
        $data[":URUN"]              = trim($_REQUEST['urun']);
        $data[":ID"]                = $row->ID;
        $row_kontrol = DB::getRow($sql, $data);

        if($row_kontrol->ID > 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "{$row->URUN} Daha Önce Kayıt Edilmiş!";
            return $result;
        }
        
        if($_REQUEST['kategori_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE URUN SET URUN                = :URUN,
                                ACIKLAMA            = :ACIKLAMA,
                                KATEGORI_ID         = :KATEGORI_ID,
                                HAMUR_KULLANIM_ID   = :HAMUR_KULLANIM_ID,
                                FIYAT               = :FIYAT,
                                DURUM               = :DURUM,
                                GTARIH              = NOW()
                            WHERE ID = :ID
                            ";
        $data[":URUN"]                  = trim($_REQUEST['urun']);
        $data[":ACIKLAMA"]              = trim($_REQUEST['aciklama']);
        $data[":FIYAT"]                 = FormatSayi::sayi2db($_REQUEST['fiyat']);
        $data[":KATEGORI_ID"]           = $_REQUEST['kategori_id'];
        $data[":HAMUR_KULLANIM_ID"]     = $_REQUEST['hamur_kullanim_id'];
        $data[":DURUM"]                 = $_REQUEST['durum'];
        $data[":ID"]                    = $row->ID;
        $update = DB::exec($sql, $data);

        if($row->FIYAT != FormatSayi::sayi2db($_REQUEST['fiyat'])){
            $data = array();
            $sql = "UPDATE URUN SET ESKI_FIYAT = :ESKI_FIYAT WHERE ID = :ID";
            $data[":ESKI_FIYAT"]    = $row->FIYAT;
            $data[":ID"]            = $row->ID;
            DB::exec($sql, $data);

            $data = array();
            $sql = "INSERT INTO URUN_FIYAT_LOG SET  URUN_ID         = :URUN_ID,
                                                    ESKI_FIYAT      = :ESKI_FIYAT,
                                                    FIYAT           = :FIYAT,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
            $data[":URUN_ID"]           = $row->ID;
            $data[":ESKI_FIYAT"]        = $row->FIYAT;
            $data[":FIYAT"]             = FormatSayi::sayi2db($_REQUEST['fiyat']);
            $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
        }
     
        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN", "URUN_DUZENLE");

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kayıt Edildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function resim_yukle() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT 
                    U.ID,
                    YEAR(U.TARIH) AS YIL
                FROM URUN AS U
                WHERE U.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Bulunamadı!";
            return $result;
        }

        if(is_null($_FILES['files'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $yol      = fncImgPathFirma($row_site->IMG_PATH, 'urun', $row->ID, $row->YIL);
        $resimler = $cResim->fncResimYukle($yol, $_FILES['files']);
        
        $say = 0;
        foreach ($resimler as $key => $resim) {
            $data = array();
            $sql = "INSERT INTO URUN_RESIM SET  URUN_ID         = :URUN_ID,
                                                RESIM_ADI       = :RESIM_ADI,
                                                RESIM_ADI_ILK   = :RESIM_ADI_ILK,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
            $data[":URUN_ID"]         = $row->ID;
            $data[':RESIM_ADI']       = $resim["RESIM_ADI"];
            $data[':RESIM_ADI_ILK']   = $resim["RESIM_ADI_ILK"];
            $data[":KAYIT_YAPAN_ID"]  = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
            if ($id > 0)$say++;

            fncIslemLog($id, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN_RESIM", "URUN_DUZENLE");
        }

        $data = array();
        $sql = "SELECT COUNT(*) AS SAY FROM URUN_RESIM WHERE URUN_ID = :URUN_ID";
        $data[":URUN_ID"]         = $row->ID;
        $row_resim_say = DB::getRow($sql, $data);

        if($row_resim_say->SAY == 1){
            $data = array();
            $sql = "UPDATE URUN_RESIM SET VITRIN = 1 WHERE URUN_ID = :URUN_ID";
            $data[":URUN_ID"]   = $row->ID;
            DB::exec($sql, $data);
        }


        if($say > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Resimler Yüklendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function resim_sil() {
        global $row_site;

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT 
                    UR.*,
                    CONCAT('urun/', U.ID, '/', YEAR(U.TARIH), '/', UR.RESIM_ADI) AS RESIM_URL
                FROM URUN_RESIM AS UR
                    LEFT JOIN URUN AS U ON U.ID = UR.URUN_ID
                WHERE UR.ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }
        
        $data = array();
        $sql = "DELETE FROM URUN_RESIM WHERE ID = :ID";
        $data[":ID"]        = $row->ID;
        $delete = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN_RESIM", "URUN_DUZENLE");

        if($delete > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Silindi.";
            unlink(fncImgPathFolder2($row->RESIM_URL, $row_site->IMG_PATH));
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function vitrin_yap() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM URUN_RESIM WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE URUN_RESIM SET VITRIN = 0 WHERE URUN_ID = :URUN_ID";
        $data[":URUN_ID"]        = $row->URUN_ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "UPDATE URUN_RESIM SET VITRIN = 1 WHERE ID = :ID";
        $data[":ID"]        = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Vitrine Alındı.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function adet_guncelle() {

        $data = array();
        $sql = "SELECT * FROM STOK WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Stok Bulunamadı!";
            return $result;
        }

        if(FormatSayi::sayi2db($_REQUEST['adet']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Adet 0'dan Küçük Olamaz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE STOK SET ADET            = :ADET,
                                GTARIH          = NOW()
                            WHERE ID = :ID
                            ";
        $data[":ADET"]              = FormatSayi::sayi2db($_REQUEST['adet']);
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Adet Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function firsat_kaydet(){
        
        $data = array();
        $sql = "SELECT * FROM STOK WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Stok Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE STOK SET FIRSAT          = :FIRSAT,
                                GTARIH          = NOW()
                            WHERE ID = :ID
                            ";
        $data[":FIRSAT"]   = $_REQUEST['firsat'];
        $data[":ID"]       = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Fırsat Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
        
    }

    public function yeni_kaydet(){
        
        $data = array();
        $sql = "SELECT * FROM STOK WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Stok Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE STOK SET YENI          = :YENI,
                                GTARIH        = NOW()
                            WHERE ID = :ID
                            ";
        $data[":YENI"]   = $_REQUEST['yeni'];
        $data[":ID"]     = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
        
    }

    public function alt_guncelle() {

        $data = array();
        $sql = "SELECT * FROM STOK_RESIM WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        if(strlen(trim($_REQUEST['alt']) <= 0)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Alt Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE STOK_RESIM SET   ALT     = :ALT,
                                        GTARIH  = NOW()
                                    WHERE ID = :ID
                                    ";
        $data[":ALT"]               = trim($_REQUEST['alt']);
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function excel_fiyat_guncelle() {

        if($_REQUEST['sube_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Seçiniz!";
            return $result;
        }

        if (!isset($_FILES['excel']) OR $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Excel Dosyası Yükleyiniz!";
            return $result;
        }

        $spreadsheet    = IOFactory::load($_FILES['excel']['tmp_name']);
        $sheet          = $spreadsheet->getActiveSheet();
        $rows           = $sheet->toArray();
        
        $data = array();
        $sql = "SELECT 
                    UF.URUN_ID AS ID,
                    UF.ID AS URUN_FIYAT_ID,
                    UF.SUBE_ID,
                    UF.FIYAT
                FROM URUN_FIYAT AS UF
                WHERE UF.SUBE_ID = :SUBE_ID";
        $data[":SUBE_ID"] = $_REQUEST['sube_id'];
        $rows_urun = DB::get($sql, $data);
        $rows_urun = arrayIndex($rows_urun);

        $say = 0;
        foreach ($rows as $key => $row) {
            if($key == 0) continue;
            if($rows_urun[$row[0]]->FIYAT == FormatSayi::sayi2db(trim($row[6]))) continue;

            $data = array();
            $sql = "UPDATE URUN_FIYAT SET   FIYAT       = :FIYAT,
                                            ESKI_FIYAT  = :ESKI_FIYAT,
                                            GTARIH      = NOW()
                                        WHERE ID = :ID
                                        ";
            $data[":FIYAT"]         = trim($row[6]);
            $data[":ESKI_FIYAT"]    = $rows_urun[$row[0]]->FIYAT;
            $data[":ID"]            = $rows_urun[$row[0]]->URUN_FIYAT_ID;
            $update = DB::exec($sql, $data);

            if($update > 0){
                $say++;

                $data = array();
                $sql = "INSERT INTO URUN_FIYAT_LOG SET  URUN_ID         = :URUN_ID,
                                                        SUBE_ID         = :SUBE_ID,
                                                        ESKI_FIYAT      = :ESKI_FIYAT,
                                                        FIYAT           = :FIYAT,
                                                        KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                        ";
                $data[":URUN_ID"]           = $rows_urun[$row[0]]->ID;
                $data[":SUBE_ID"]           = $rows_urun[$row[0]]->SUBE_ID;
                $data[":ESKI_FIYAT"]        = $rows_urun[$row[0]]->FIYAT;
                $data[":FIYAT"]             = trim($row[6]);
                $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
                DB::insert($sql, $data);
            } 
        }

        if($say > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "{$say} Kadar Ürünün Fiyatı Güncellendi";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hiç Ürün Güncellenmedi.";
        }

        return $result;
    }

    public function getUrunFiyatLog($request) {

        $data = array();
        $sql = "SELECT 
                    UFL.*,
                    U.URUN,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN, 
                    K.KATEGORI
                FROM URUN_FIYAT_LOG AS UFL
                    LEFT JOIN URUN AS U ON U.ID = UFL.URUN_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = UFL.KAYIT_YAPAN_ID
                    LEFT JOIN KATEGORI AS K ON K.ID = U.KATEGORI_ID
                WHERE 1";

        if($request['urun']){
            $sql .= " AND U.URUN LIKE :URUN";
            $data[':URUN'] = "%". $request['urun'] . "%";
        }

        if($request['kategori_id'] > 0){
            $sql .= " AND U.KATEGORI_ID = :KATEGORI_ID";
            $data[':KATEGORI_ID'] = $request['kategori_id'];
        }

        if($request['kayit_yapan_id'] > 0){
            $sql .= " AND UFL.KAYIT_YAPAN_ID = :KAYIT_YAPAN_ID";
            $data[':KAYIT_YAPAN_ID'] = $request['kayit_yapan_id'];
        }

        $sql .= " ORDER BY UFL.TARIH DESC";

        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 50);
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

    public function getAlerjenler($request) {

        $data = array();
        $sql = "SELECT 
                    A.*
                FROM ALERJEN AS A
                WHERE A.DURUM = 1
                ORDER BY A.SIRA ASC
                ";
        $row = DB::get($sql, $data);
        return $row;
    }

    public function alerjen_kaydet() {

        $data = array();
        $sql = "SELECT * FROM URUN WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE URUN SET ALERJEN_IDS     = :ALERJEN_IDS,
                                GTARIH          = NOW()
                            WHERE ID = :ID
                            ";
        $data[":ALERJEN_IDS"]    = $_REQUEST['alerjen_ids'];
        $data[":ID"]             = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kayıt Edildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function fiyat_guncelle() {

        if($_REQUEST['sube_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Seçiniz!";
            return $result;
        }

        if($_REQUEST['kategori_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Seçiniz!";
            return $result;
        }

        if($_REQUEST['secim_turu'] == 1 AND FormatSayi::sayi2db($_REQUEST['oran']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Oran Giriniz!";
            return $result;
        }

        if($_REQUEST['secim_turu'] == 2 AND FormatSayi::sayi2db($_REQUEST['fiyat']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Fiyat Giriniz!";
            return $result;
        }

        if($_REQUEST['yuvarlama'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıfırdan Büyük Olmalıdır!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM KATEGORI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST['kategori_id'];
        $row_kategori = DB::getRow($sql, $data);

        if($row_kategori->ID <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST['sube_id'];
        $row_sube = DB::getRow($sql, $data);

        if($row_sube->ID <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM URUN WHERE KATEGORI_ID = :KATEGORI_ID AND FIND_IN_SET(ID, :IDS)";
        $data[":KATEGORI_ID"]    = $_REQUEST['kategori_id'];
        $data[":IDS"]            = $row_sube->URUN_IDS;
        $rows_urun = DB::get($sql, $data);

        $data = array();
        $sql = "SELECT 
                    UF.URUN_ID,
                    UF.SUBE_ID,
                    UF.ID AS URUN_FIYAT_ID,
                    UF.FIYAT
                FROM URUN_FIYAT AS UF
                WHERE SUBE_ID = :SUBE_ID AND KATEGORI_ID = :KATEGORI_ID";
        $data[":KATEGORI_ID"]   = $row_kategori->ID;
        $data[":SUBE_ID"]       = $row_sube->ID;
        $rows_urun_fiyat = DB::get($sql, $data);

        foreach($rows_urun_fiyat as $key => $row_urun_fiyat){
            $row_urun_fiyat_index[$row_urun_fiyat->URUN_ID][$row_urun_fiyat->SUBE_ID]    = $row_urun_fiyat;
        }

        $say_insert = 0;
        $say_update = 0;
        foreach ($rows_urun as $key => $row_urun) {
            if(is_null($row_urun_fiyat_index[$row_urun->ID][$row_sube->ID]->URUN_FIYAT_ID)){

                if($_REQUEST['secim_islem'] == 3){ //Zam Yapılacak
                    if($_REQUEST['secim_turu'] == 2){ //Fiyat

                        $data = array();
                        $sql = "INSERT INTO URUN_FIYAT SET  URUN_ID         = :URUN_ID,
                                                            KATEGORI_ID     = :KATEGORI_ID,
                                                            SUBE_ID         = :SUBE_ID,
                                                            FIYAT           = :FIYAT,
                                                            KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                            ";
                        $data[":URUN_ID"]           = $row_urun->ID;
                        $data[":KATEGORI_ID"]       = $row_kategori->ID;
                        $data[":SUBE_ID"]           = $row_sube->ID;
                        $data[":FIYAT"]             = FormatSayi::sayi2db($_REQUEST['fiyat']);
                        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
                        $id = DB::insert($sql, $data);
                        if($id > 0) $say_insert++;
                        fncIslemLog(0, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN_FIYAT", "URUN_LISTESI");
                    }

                }

            }else{

                if($_REQUEST['secim_islem'] == 3){ //Zam Yapılacak
                    if($_REQUEST['secim_turu'] == 1){ //Oran

                        $oran = 1 + ($_REQUEST['oran'] / 100);

                        $data = array();
                        $sql = "UPDATE URUN_FIYAT SET   FIYAT     = FIYAT * :ORAN,
                                                        GTARIH    = NOW()
                                                    WHERE SUBE_ID = :SUBE_ID AND KATEGORI_ID = :KATEGORI_ID
                                                    ";
                        $data[":ORAN"]          = $oran;
                        $data[":KATEGORI_ID"]   = $row_kategori->ID;
                        $data[":SUBE_ID"]       = $row_sube->ID;
                        $update = DB::exec($sql, $data);
                        if($update > 0) $say_update++;
                        fncIslemLog(0, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN_FIYAT", "URUN_LISTESI");

                    }else if($_REQUEST['secim_turu'] == 2){ //Fiyat

                        $data = array();
                        $sql = "UPDATE URUN_FIYAT SET   FIYAT     = FIYAT + :YENI_FIYAT,
                                                        GTARIH    = NOW()
                                                    WHERE SUBE_ID = :SUBE_ID AND KATEGORI_ID = :KATEGORI_ID
                                                    ";
                        $data[":YENI_FIYAT"]    = FormatSayi::sayi2db($_REQUEST['fiyat']);
                        $data[":KATEGORI_ID"]   = $row_kategori->ID;
                        $data[":SUBE_ID"]       = $row_sube->ID;
                        $update = DB::exec($sql, $data);
                        if($update > 0) $say_update++;
                        fncIslemLog(0, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN_FIYAT", "URUN_LISTESI");
                    }

                }

                if($_REQUEST['secim_islem'] == 4){ //İndirim Yapılacak
                    if($_REQUEST['secim_turu'] == 1){ //Oran

                        $oran = 1 - ($_REQUEST['oran'] / 100);

                        $data = array();
                        $sql = "UPDATE URUN_FIYAT SET   FIYAT     = FIYAT * :ORAN,
                                                        GTARIH    = NOW()
                                            WHERE SUBE_ID = :SUBE_ID AND KATEGORI_ID = :KATEGORI_ID
                                            ";
                        $data[":ORAN"]          = $oran;
                        $data[":KATEGORI_ID"]   = $row_kategori->ID;
                        $data[":SUBE_ID"]       = $row_sube->ID;
                        $update = DB::exec($sql, $data);
                        if($update > 0) $say_update++;
                        fncIslemLog(0, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN_FIYAT", "URUN_LISTESI");

                    }else if($_REQUEST['secim_turu'] == 2){ //Fiyat

                        $data = array();
                        $sql = "UPDATE URUN_FIYAT SET   FIYAT     = FIYAT - :YENI_FIYAT,
                                                        GTARIH    = NOW()
                                                    WHERE SUBE_ID = :SUBE_ID AND KATEGORI_ID = :KATEGORI_ID
                                                    ";
                        $data[":YENI_FIYAT"]    = FormatSayi::sayi2db($_REQUEST['fiyat']);
                        $data[":KATEGORI_ID"]   = $row_kategori->ID;
                        $data[":SUBE_ID"]       = $row_sube->ID;
                        $update = DB::exec($sql, $data);
                        if($update > 0) $say_update++;
                        fncIslemLog(0, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN_FIYAT", "URUN_LISTESI");
                    }
                }
            }
        }
        
        $data = array();
        $sql = "SELECT * FROM URUN_FIYAT WHERE SUBE_ID = :SUBE_ID AND KATEGORI_ID = :KATEGORI_ID";
        $data[":KATEGORI_ID"]   = $row_kategori->ID;
        $data[":SUBE_ID"]       = $row_sube->ID;
        $rows_urun = DB::get($sql, $data);

        foreach($rows_urun as $key => $row_urun){
            //Yuvarlama İşlemini Yap
            $yuvarlanmis_fiyat = fncYuvarla($row_urun->FIYAT, $_REQUEST['yuvarlama']);

            $data = array();
            $sql = "UPDATE URUN_FIYAT SET   ESKI_FIYAT  = :ESKI_FIYAT,
                                            FIYAT       = :FIYAT
                                        WHERE ID = :ID
                                        ";
            $data[":ESKI_FIYAT"]    = $row_urun_fiyat_index[$row_urun->URUN_ID][$row_sube->ID]->FIYAT;
            $data[":FIYAT"]         = $yuvarlanmis_fiyat;
            $data[":ID"]            = $row_urun->ID;
            DB::exec($sql, $data);

            $data = array();
            $sql = "INSERT INTO URUN_FIYAT_LOG SET  URUN_ID         = :URUN_ID,
                                                    SUBE_ID         = :SUBE_ID,
                                                    ESKI_FIYAT      = :ESKI_FIYAT,
                                                    FIYAT           = :FIYAT,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
            $data[":URUN_ID"]           = $row_urun->ID;
            $data[":SUBE_ID"]           = $row_sube->ID;
            $data[":ESKI_FIYAT"]        = $row_urun_fiyat_index[$row_urun->URUN_ID][$row_sube->ID]->FIYAT;;
            $data[":FIYAT"]             = $yuvarlanmis_fiyat;
            $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
        }

        if($say_insert > 0 OR $say_update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "{$say_insert} Eklendi {$say_update} Güncellendi";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function toplu_fiyat_guncelle() {

        if($_REQUEST['sube_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Seçilmemiş!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]    = $_REQUEST['sube_id'];
        $row = DB::getRow($sql, $data);

        $data = array();
        $sql = "SELECT * FROM URUN WHERE 1";
        $rows_urun = DB::get($sql, $data);
        $rows_urun = arrayIndex($rows_urun);

        $data = array();
        $sql = "SELECT * FROM URUN_FIYAT WHERE SUBE_ID = :SUBE_ID";
        $data[":SUBE_ID"]    = $row->ID;
        $rows_urun_fiyat2 = DB::get($sql, $data);

        foreach($rows_urun_fiyat2 as $key => $row_urun_fiyat2){ //Ürünün Fiyatlarını alıyoruz
            $rows_urun_fiyat[$row_urun_fiyat2->URUN_ID][$row_urun_fiyat2->SUBE_ID]    = $row_urun_fiyat2;
        }

        $say_insert = 0;
        $say_update = 0;
        foreach ($_REQUEST['fiyat'] as $urun_id  => $fiyat) {
            if($rows_urun_fiyat[$urun_id][$row->ID]->FIYAT == FormatSayi::sayi2db($fiyat)) continue;
            if(is_null($rows_urun[$urun_id]->ID)) continue;

            if(is_null($rows_urun_fiyat[$urun_id][$row->ID]->ID)){

                $data = array();
                $sql = "INSERT INTO URUN_FIYAT SET  URUN_ID         = :URUN_ID,
                                                    KATEGORI_ID     = :KATEGORI_ID,
                                                    SUBE_ID         = :SUBE_ID,
                                                    FIYAT           = :FIYAT,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
                $data[":URUN_ID"]           = $rows_urun[$urun_id]->ID;
                $data[":KATEGORI_ID"]       = $rows_urun[$urun_id]->KATEGORI_ID;
                $data[":SUBE_ID"]           = $row->ID;
                $data[":FIYAT"]             = FormatSayi::sayi2db($fiyat);
                $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
                $id = DB::insert($sql, $data);
                if($id > 0) $say_insert++;

            }else{

                $data = array();
                $sql = "UPDATE URUN_FIYAT SET   ESKI_FIYAT  = FIYAT,
                                                FIYAT       = :FIYAT,
                                                GTARIH      = NOW()
                                            WHERE ID = :ID
                                            ";
                $data[":FIYAT"]     = FormatSayi::sayi2db($fiyat);
                $data[":ID"]        = $rows_urun_fiyat[$urun_id][$row->ID]->ID;
                $update = DB::exec($sql, $data);
                if($update > 0) $say_update++;
            }

            $data = array();
            $sql = "INSERT INTO URUN_FIYAT_LOG SET  URUN_ID         = :URUN_ID,
                                                    SUBE_ID         = :SUBE_ID,
                                                    ESKI_FIYAT      = :ESKI_FIYAT,
                                                    FIYAT           = :FIYAT,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
            $data[":URUN_ID"]           = $urun_id;
            $data[":SUBE_ID"]           = $row->ID;
            $data[":ESKI_FIYAT"]        = $rows_urun_fiyat[$urun_id][$row->ID]->FIYAT;
            $data[":FIYAT"]             = FormatSayi::sayi2db($fiyat);
            $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
        }

        if($say_insert > 0 OR $say_update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = $say_insert . " Eklendi ". $say_update ." Fiyat Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Değişiklik Yok.";
        }

        return $result;
    }

    public function getSubeler($request) {

        $data = array();
        $sql = "SELECT 
                    S.*
                FROM SUBE AS S
                WHERE S.DURUM = 1
                ";

        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function urun_fiyat_kaydet() {

        if(count2($_REQUEST['fiyat']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Fiyat Gelmedi!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM URUN WHERE ID = :ID";
        $data[":ID"]    = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        $data = array();
        $sql = "SELECT * FROM URUN_FIYAT WHERE URUN_ID = :URUN_ID";
        $data[":URUN_ID"]    = $row->ID;
        $rows_urun_fiyat2 = DB::get($sql, $data);

        foreach($rows_urun_fiyat2 as $key => $row_urun_fiyat2){ //Ürünün Fiyatlarını alıyoruz
            $rows_urun_fiyat[$row_urun_fiyat2->URUN_ID][$row_urun_fiyat2->SUBE_ID]    = $row_urun_fiyat2;
        }

        $say_insert = 0;
        $say_update = 0;
        foreach ($_REQUEST['fiyat'] as $sube_id => $fiyat) {
            if($rows_urun_fiyat[$row->ID][$sube_id]->FIYAT != 0 AND FormatSayi::sayi2db($fiyat) == 0) continue;
            if($rows_urun_fiyat[$row->ID][$sube_id]->FIYAT == FormatSayi::sayi2db($fiyat)) continue;

            if(is_null($rows_urun_fiyat[$row->ID][$sube_id]->ID)){

                $data = array();
                $sql = "INSERT INTO URUN_FIYAT SET  URUN_ID         = :URUN_ID,
                                                    KATEGORI_ID     = :KATEGORI_ID,
                                                    SUBE_ID         = :SUBE_ID,
                                                    FIYAT           = :FIYAT,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
                $data[":URUN_ID"]           = $row->ID;
                $data[":KATEGORI_ID"]       = $row->KATEGORI_ID;
                $data[":SUBE_ID"]           = $sube_id;
                $data[":FIYAT"]             = FormatSayi::sayi2db($fiyat);
                $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
                $id = DB::insert($sql, $data);
                if($id > 0) $say_insert++;

            }else{

                $data = array();
                $sql = "UPDATE URUN_FIYAT SET   ESKI_FIYAT  = FIYAT,
                                                FIYAT       = :FIYAT,
                                                GTARIH      = NOW()
                                            WHERE ID = :ID
                                            ";
                $data[":FIYAT"]     = FormatSayi::sayi2db($fiyat);
                $data[":ID"]        = $rows_urun_fiyat[$row->ID][$sube_id]->ID;
                $update = DB::exec($sql, $data);
                if($update > 0) $say_update++;
            }

            $data = array();
            $sql = "INSERT INTO URUN_FIYAT_LOG SET  URUN_ID         = :URUN_ID,
                                                    SUBE_ID         = :SUBE_ID,
                                                    ESKI_FIYAT      = :ESKI_FIYAT,
                                                    FIYAT           = :FIYAT,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
            $data[":URUN_ID"]           = $row->ID;
            $data[":SUBE_ID"]           = $sube_id;
            $data[":ESKI_FIYAT"]        = $rows_urun_fiyat[$row->ID][$sube_id]->FIYAT;
            $data[":FIYAT"]             = FormatSayi::sayi2db($fiyat);
            $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
        }

        if($say_insert > 0 OR $say_update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = $say_insert . " Eklendi ". $say_update ." Fiyat Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Değişiklik Yok.";
        }

        return $result;
    }

    public function getUrunFiyat($request) {

        $data = array();
        $sql = "SELECT 
                    UF.*
                FROM URUN_FIYAT AS UF
                WHERE UF.URUN_ID = :URUN_ID
                ";

        $data[":URUN_ID"]    = $_REQUEST['id'];
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function getSubeFiyat($request) {

        $data = array();
        $sql = "SELECT 
                    UF.*
                FROM URUN_FIYAT AS UF
                WHERE UF.SUBE_ID = :SUBE_ID
                ";

        $data[":SUBE_ID"]    = $_REQUEST['sube_id'];
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function getSubeUrunler($request = array()) {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[':ID'] = $request['sube_id'];
        $row_sube = DB::getRow($sql, $data);

        $data = array();
        $sql = "SELECT 
                    U.*,
                    K.KATEGORI,
                    UK.UST_KATEGORI,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN,
                    CONCAT('urun/', U.ID, '/', YEAR(U.TARIH), '/', UR.RESIM_ADI) AS RESIM_URL,
                    ST.SATIS_TURU,
                    IF(U.DURUM = 1, 'Aktif', 'Pasif') AS DURUM_TEXT,
                    (SELECT GROUP_CONCAT(A.ALERJEN) FROM ALERJEN AS A WHERE FIND_IN_SET(A.ID, U.ALERJEN_IDS)) AS ALERJENLER,
                    UF.ESKI_FIYAT,
                    UF.FIYAT
                FROM URUN AS U
                    LEFT JOIN URUN_RESIM AS UR ON UR.URUN_ID = U.ID AND UR.VITRIN = 1
                    LEFT JOIN KATEGORI AS K ON K.ID = U.KATEGORI_ID
                    LEFT JOIN UST_KATEGORI AS UK ON UK.ID = K.UST_KATEGORI_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = U.KAYIT_YAPAN_ID
                    LEFT JOIN SATIS_TURU AS ST ON ST.ID = U.SATIS_TURU_ID
                    LEFT JOIN URUN_FIYAT AS UF ON UF.SUBE_ID = :SUBE_ID AND UF.URUN_ID = U.ID
                WHERE FIND_IN_SET(U.ID, :URUN_IDS)
                ";

        $data[':URUN_IDS']  = $row_sube->URUN_IDS;
        $data[':SUBE_ID']   = $row_sube->ID;

        if($request['urun']){
            $sql .= " AND U.URUN LIKE :URUN";
            $data[':URUN'] = "%". $request['urun'] . "%";
        }

        if($request['satis_turu_id'] > 0){
            $sql .= " AND U.SATIS_TURU_ID = :SATIS_TURU_ID";
            $data[':SATIS_TURU_ID'] = $request['satis_turu_id'];
        }

        if(count2($request['kategori_ids']) > 0){
            $sql .= " AND FIND_IN_SET(U.KATEGORI_ID, :KATEGORI_IDS)";
            $data[':KATEGORI_IDS'] = FormatYazi::array2str($request['kategori_ids']);
        }

        if($request['ust_kategori_id'] > 0){
            $sql .= " AND K.UST_KATEGORI_ID = :UST_KATEGORI_ID";
            $data[':UST_KATEGORI_ID'] = $request['ust_kategori_id'];
        }

        if(in_array2($request['durum'],array(0,1))){
            $sql .= " AND U.DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }

        $sql .= " ORDER BY K.KATEGORI,U.URUN ASC";
        
        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 50);
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

    function kategori_doldur() {
        
        $data = array();
        $sql = "SELECT
                    K.ID,
                    K.KATEGORI AS AD
                FROM KATEGORI AS K
                WHERE K.DURUM = 1 AND K.UST_KATEGORI_ID = :UST_KATEGORI_ID
                ORDER BY 2
                ";
        $data[":UST_KATEGORI_ID"] = $_REQUEST["ust_kategori_id"];
        $rows = DB::get($sql, $data);
        
        $html .= "<option value='-1' >Seçiniz</option>";
        foreach($rows as $key => $row) {
            $html .= "<option value=".$row->ID." >".$row->AD."</option>";
        }

        if(count2($rows) > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kategoriler Dolduruldu.";
            $result["HTML"]      = $html;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kategoriler Bulunamadı!";
        }

        return $result;
    }

    public function urun_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM URUN WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM URUN WHERE ID = :ID";
        $data[":ID"]        = $row->ID;
        $delete = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "URUN", "URUN_LISTESI");

        if($delete > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "{$row->URUN} Silindi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    function sube_doldur() {
        
        $data = array();
        $sql = "SELECT
                    S.ID,
                    S.SUBE AS AD
                FROM SUBE AS S
                WHERE S.SATIS_TURU_ID = :SATIS_TURU_ID
                ORDER BY 2
                ";
        $data[":SATIS_TURU_ID"]   = $_REQUEST["satis_turu_id"];
        $rows = DB::get($sql, $data);
        
        $html .= "<option value='-1'>Seçiniz</option>";
        foreach($rows as $key => $row) {
            $html .= "<option value=".$row->ID." >".$row->AD."</option>";
        }

        if(count2($rows) > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Şubeler Dolduruldu.";
            $result["HTML"]      = $html;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "İlçe Bulunamadı!";
        }

        return $result;
    }

    public function sube_urun_kaydet() {

        if(count2($_REQUEST['sube_ids']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Seçiniz!";
            return $result;
        }
        
        if(strlen($_REQUEST['urun_ids']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE FIND_IN_SET(ID, :SUBE_IDS)";
        $data[":SUBE_IDS"]    = FormatYazi::array2str($_REQUEST['sube_ids']);
        $rows_sube = DB::get($sql, $data);

        foreach ($rows_sube as $key => $row_sube) {
            $urun_ids = explode(',', $_REQUEST['urun_ids']);

            $sube_urun_ids = array_filter(array_map('trim', explode(',', $row_sube->URUN_IDS ?? '')));
            foreach ($urun_ids as $key => $urun_id) {
                if (in_array($urun_id, $sube_urun_ids)) continue;
                $sube_urun_ids[] = $urun_id;

                $data = array();
                $sql = "INSERT INTO SUBE_URUN SET   SUBE_ID         = :SUBE_ID,
                                                    URUN_ID         = :URUN_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
                $data[":SUBE_ID"]           = $row_sube->ID;
                $data[":URUN_ID"]           = $urun_id;
                $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
                DB::insert($sql, $data);
            }

            $urun_ids = implode(',', $sube_urun_ids);

            $data = array();
            $sql = "UPDATE SUBE SET URUN_IDS     = :URUN_IDS,
                                    GTARIH       = NOW()
                                WHERE ID = :ID";
            $data[":URUN_IDS"]  = $urun_ids;
            $data[":ID"]        = $row_sube->ID;
            $update = DB::exec($sql, $data);   
        }

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Ürünler Eklendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Ürünler Eklenmedi!.";
        }

        return $result;
    }

    public function sube_urun_cikart() {

        $data = array();
        $sql = "SELECT * FROM URUN WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["sube_id"];
        $row_sube = DB::getRow($sql, $data);

        if(is_null($row_sube->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        // URUN_IDS listesinden bu ürünü çıkart
        $mevcut_ids = array_filter(array_map('trim', explode(',', $row_sube->URUN_IDS ?? '')));
        $kalan_ids = array_filter($mevcut_ids, function($id) {
            return $id != $_REQUEST["id"];
        });

        $urun_ids = implode(',', $kalan_ids);

        $data = array();
        $sql = "UPDATE SUBE SET URUN_IDS  = :URUN_IDS,
                                GTARIH     = NOW()
                            WHERE ID = :ID";
        $data[":URUN_IDS"]  = $urun_ids;
        $data[":ID"]        = $row_sube->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "DELETE FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID AND URUN_ID = :URUN_ID";
        $data[":URUN_ID"]           = $row->ID;
        $data[":SUBE_ID"]           = $row_sube->ID;
        $delete = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Ürün Menüden Çıkarıldı.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getMalzemeler($request) {
        global $row_site;

        $data = array();
        $sql = "SELECT 
                    * 
                FROM MALZEME AS M
                WHERE 1";

        if($request['malzeme']){
            $sql .= " AND M.MALZEME LIKE :MALZEME";
            $data[':MALZEME'] = "%" . $request['malzeme'] . "%";
        }

        if(in_array2($request['durum'],array(0,1))){
            $sql .= " AND M.DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }
        
        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 50);
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

    public function malzeme_ekle() {

        if(strlen(trim($_REQUEST['malzeme'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Malzeme Adı Giriniz!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM MALZEME WHERE MALZEME = :MALZEME";
        $data[":MALZEME"]              = trim($_REQUEST['malzeme']);
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "{$row->MALZEME} Daha Önce Kayıt Edilmiş!";
            return $result;
        }

        if(FormatSayi::sayi2db($_REQUEST['fiyat']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Fiyat Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO MALZEME SET MALZEME         = :MALZEME,
                                        URUN_ID         = :URUN_ID,
                                        ACIKLAMA        = :ACIKLAMA,
                                        FIYAT           = :FIYAT,
                                        EKSTRA_FIYAT    = :EKSTRA_FIYAT,
                                        EKSTRA          = :EKSTRA,
                                        DURUM           = :DURUM,
                                        KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                        TOKEN           = MD5(NOW())
                                        ";
        $data[":MALZEME"]           = trim($_REQUEST['malzeme']);
        $data[":URUN_ID"]           = $_REQUEST['urun_id'];
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":FIYAT"]             = FormatSayi::sayi2db($_REQUEST['fiyat']);
        $data[":EKSTRA_FIYAT"]      = FormatSayi::sayi2db($_REQUEST['ekstra_fiyat']);
        $data[":EKSTRA"]            = $_REQUEST['ekstra'] ? $_REQUEST['ekstra'] : '0';
        $data[":DURUM"]             = $_REQUEST['durum'];
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT * FROM MALZEME WHERE ID = :ID";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Malzeme Oluşturuldu.";
            $result["URL"]       = "/views/urun/malzeme_duzenle.php?route=malzeme_listesi&id={$row->ID}&token={$row->TOKEN}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function malzeme_kaydet() {
        
        $data = array();
        $sql = "SELECT * FROM MALZEME WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Malzeme Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['malzeme'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Malzeme Adı Giriniz!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM MALZEME WHERE MALZEME = :MALZEME AND ID != :ID";
        $data[":MALZEME"]           = trim($_REQUEST['malzeme']);
        $data[":ID"]                = $row->ID;
        $row_kontrol = DB::getRow($sql, $data);

        if($row_kontrol->ID > 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "{$row->MALZEME} Daha Önce Kayıt Edilmiş!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE MALZEME SET  MALZEME         = :MALZEME,
                                    URUN_ID         = :URUN_ID,
                                    ACIKLAMA        = :ACIKLAMA,
                                    FIYAT           = :FIYAT,
                                    EKSTRA_FIYAT    = :EKSTRA_FIYAT,
                                    EKSTRA          = :EKSTRA,
                                    DURUM           = :DURUM,
                                    GTARIH          = NOW()
                                WHERE ID = :ID
                                ";
        $data[":MALZEME"]           = trim($_REQUEST['malzeme']);
        $data[":URUN_ID"]           = $_REQUEST['urun_id'];
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":FIYAT"]             = FormatSayi::sayi2db($_REQUEST['fiyat']);
        $data[":EKSTRA_FIYAT"]      = FormatSayi::sayi2db($_REQUEST['ekstra_fiyat']);
        $data[":EKSTRA"]            = $_REQUEST['ekstra'] ? $_REQUEST['ekstra'] : '0';
        $data[":DURUM"]             = $_REQUEST['durum'];
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "MALZEME", "MALZEME_DUZENLE");

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kayıt Edildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getMalzeme($request) {

        $data = array();
        $sql = "SELECT 
                    M.*,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM MALZEME AS M
                    LEFT JOIN KULLANICI AS KU ON KU.ID = M.KAYIT_YAPAN_ID
                WHERE M.ID = :ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::getRow($sql, $data);
        return $row;
    }

    public function getMalzemeler2($request) {

        $data = array();
        $sql = "SELECT 
                    M.*
                FROM MALZEME AS M
                WHERE M.DURUM = 1 AND ONCELIK = 1
                ORDER BY M.MALZEME ASC
                ";
        $row = DB::get($sql, $data);
        return $row;
    }

    public function urun_malzeme_kaydet() {

        $data = array();
        $sql = "SELECT * FROM MALZEME WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE URUN SET MALZEME_IDS     = :MALZEME_IDS,
                                GTARIH          = NOW()
                            WHERE ID = :ID
                            ";
        $data[":MALZEME_IDS"]    = $_REQUEST['malzeme_ids'];
        $data[":ID"]             = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kayıt Edildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function urun_recete_kaydet() {

        $urun_id = intval($_REQUEST["urun_id"]);

        if ($urun_id <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT ID FROM URUN WHERE ID = :ID";
        $data[":ID"] = $urun_id;
        $row = DB::getRow($sql, $data);

        if (is_null($row->ID)) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM URUN_RECETE WHERE URUN_ID = :URUN_ID";
        $data[":URUN_ID"] = $urun_id;
        DB::exec($sql, $data);

        $aktif_ids = array(); // MALZEME_IDS için tutacağız

        if (!empty($_REQUEST["gramaj"])) {
            foreach ($_REQUEST["gramaj"] as $malzeme_id => $miktar) {

                $malzeme_id  = intval($malzeme_id);
                $miktar      = FormatSayi::sayi2db($miktar);

                if ($malzeme_id > 0) {

                    $aktif = isset($_REQUEST["aktif"][$malzeme_id]) ? 1 : 0;

                    if ($aktif == 1) {
                        $aktif_ids[] = $malzeme_id;
                    }

                    $data = array();
                    $sql = "INSERT INTO URUN_RECETE SET URUN_ID     = :URUN_ID,
                                                        MALZEME_ID  = :MALZEME_ID,
                                                        MIKTAR      = :MIKTAR
                                                        ";
                    $data[":URUN_ID"]    = $urun_id;
                    $data[":MALZEME_ID"] = $malzeme_id;
                    $data[":MIKTAR"]     = $miktar;
                    DB::insert($sql, $data);
                }
            }
        }

        $malzeme_ids = !empty($aktif_ids) ? implode(",", $aktif_ids) : "";

        $data = array();
        $sql = "UPDATE URUN SET MALZEME_IDS = :MALZEME_IDS,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":MALZEME_IDS"] = $malzeme_ids;
        $data[":ID"]          = $urun_id;
        DB::exec($sql, $data);

        $result["HATA"] = FALSE;
        $result["ACIKLAMA"] = "Reçete Kaydedildi.";

        return $result;
    }

    // public function urun_recete_kaydet() {

    //     $urun_id = intval($_REQUEST["urun_id"]);

    //     if ($urun_id <= 0) {
    //         $result["HATA"] = TRUE;
    //         $result["ACIKLAMA"] = "Ürün Bulunamadı!";
    //         return $result;
    //     }

    //     $data = array();
    //     $sql = "SELECT ID FROM URUN WHERE ID = :ID";
    //     $data[":ID"] = $urun_id;
    //     $row = DB::getRow($sql, $data);

    //     if (is_null($row->ID)) {
    //         $result["HATA"] = TRUE;
    //         $result["ACIKLAMA"] = "Ürün Bulunamadı!";
    //         return $result;
    //     }

    //     $data = array();
    //     $sql = "DELETE FROM URUN_RECETE WHERE URUN_ID = :URUN_ID";
    //     $data[":URUN_ID"] = $urun_id;
    //     DB::exec($sql, $data);

    //     if (!empty($_REQUEST["gramaj"])) {
    //         foreach ($_REQUEST["gramaj"] as $malzeme_id => $miktar) {
    //             $malzeme_id = intval($malzeme_id);
    //             $miktar = FormatSayi::sayi2db($miktar);

    //             if ($malzeme_id > 0 && $miktar > 0) {

    //                 $data = array();
    //                 $sql = "INSERT INTO URUN_RECETE SET URUN_ID         = :URUN_ID,
    //                                                     MALZEME_ID      = :MALZEME_ID,
    //                                                     MIKTAR          = :MIKTAR
    //                                                     ";

    //                 $data[":URUN_ID"]       = $urun_id;
    //                 $data[":MALZEME_ID"]    = $malzeme_id;
    //                 $data[":MIKTAR"]        = $miktar;
    //                 DB::insert($sql, $data);
    //             }
    //         }
    //     }

    //     $result["HATA"] = FALSE;
    //     $result["ACIKLAMA"] = "Reçete Kaydedildi.";

    //     return $result;
    // }

    public function getUrunRecete($request) {

        $data = array();
        $sql = "SELECT 
                    UR.*
                FROM URUN_RECETE AS UR
                WHERE UR.URUN_ID = :URUN_ID
                ";
        $data[":URUN_ID"]  = $request['urun_id'];
        $rows = DB::get($sql, $data);
        return $rows;
    }

}