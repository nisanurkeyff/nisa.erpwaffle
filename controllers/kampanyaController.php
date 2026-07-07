<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class KampanyaController {

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

    public function kampanya_ekle() {
        global $cResim, $row_site;

        if(strlen(trim($_REQUEST['kampanya'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kampanya Bulunamadı!";
            return $result;
        }

        if(empty($_REQUEST['bas_tarih'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Başlangıç Tarih Bulunamadı!";
            return $result;
        }

        if(empty($_REQUEST['bas_saat'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Başlangıç Saat Bulunamadı!";
            return $result;
        }

        if(empty($_REQUEST['bit_tarih'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bitiş Tarih Bulunamadı!";
            return $result;
        }

        if(empty($_REQUEST['bit_saat'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bitiş Saat Bulunamadı!";
            return $result;
        }

        if(is_null($_FILES['resim'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO KAMPANYA SET    KAMPANYA            = :KAMPANYA,
                                            KATEGORI_ID         = :KATEGORI_ID,
                                            BAS_TARIH           = :BAS_TARIH,
                                            BAS_SAAT            = :BAS_SAAT,
                                            BIT_TARIH           = :BIT_TARIH,
                                            BIT_SAAT            = :BIT_SAAT,
                                            KAMPANYA_BAS_SAAT   = :KAMPANYA_BAS_SAAT,
                                            KAMPANYA_BIT_SAAT   = :KAMPANYA_BIT_SAAT,
                                            ACIKLAMA            = :ACIKLAMA,
                                            KAYIT_YAPAN_ID      = :KAYIT_YAPAN_ID,
                                            TOKEN               = MD5(NOW())
                                            ";
        $data[":KAMPANYA"]          = trim($_REQUEST['kampanya']);
        $data[":KATEGORI_ID"]       = $_REQUEST['kategori_id'];
        $data[":BAS_TARIH"]         = $_REQUEST['bas_tarih'];
        $data[":BAS_SAAT"]          = $_REQUEST['bas_saat'];
        $data[":BIT_TARIH"]         = $_REQUEST['bit_tarih'];
        $data[":BIT_SAAT"]          = $_REQUEST['bit_saat'];
        $data[":KAMPANYA_BAS_SAAT"] = $_REQUEST['kampanya_bas_saat'];
        $data[":KAMPANYA_BIT_SAAT"] = $_REQUEST['kampanya_bit_saat'];
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT 
                    YEAR(K.TARIH) AS YIL,
                    K.ID,
                    K.TOKEN
                FROM KAMPANYA AS K
                WHERE K.ID = :ID
                ";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $yol    = fncImgPathFirma($row_site->IMG_PATH, 'kampanya', $row->ID, $row->YIL);
            $resim = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

            if($resim["HATA"]){
                $result["HATA"]          = TRUE;
                $result["ACIKLAMA"]      = "Resim Yüklerken Bir Sorun Oluştu!";
                return $result;
            }

            $data = array();
            $sql = "UPDATE KAMPANYA SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
            $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
            $data[":ID"]        = $row->ID;
            $resim = DB::exec($sql, $data);
        }

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kampanya Oluşturuldu.";
            $result["URL"]       = "/views/kampanya/kampanya_duzenle.php?route=kampanya/kampanya_listesi&id={$row->ID}&token={$row->TOKEN}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getKampanya($request) {

        $data = array();
        $sql = "SELECT 
                    K.*,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM KAMPANYA AS K
                    LEFT JOIN KULLANICI AS KU ON KU.ID = K.KAYIT_YAPAN_ID
                WHERE K.ID = :ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::getRow($sql, $data);
        return $row;
    }

    public function getKampanyalar($request) {

        $data = array();
        $sql = "SELECT 
                    K.*,
                    KA.KATEGORI,
                    (SELECT GROUP_CONCAT(S.SUBE) FROM SUBE AS S WHERE FIND_IN_SET(S.ID, K.SUBE_IDS)) AS SUBELER,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN,
                    IF(K.DURUM = 1, 'Aktif', 'Pasif') AS DURUM_TEXT
                FROM KAMPANYA AS K
                    LEFT JOIN KATEGORI AS KA ON KA.ID = K.KATEGORI_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = K.KAYIT_YAPAN_ID
                WHERE 1
                ";

        if($request['kampanya']){
            $sql .= " AND K.KAMPANYA LIKE :KAMPANYA";
            $data[':KAMPANYA'] = "%". $request['kampanya'] . "%";
        }

        if($request['sube_id'] > 0){
            $sql .= " AND FIND_IN_SET(:SUBE_ID, K.SUBE_IDS)";
            $data[':SUBE_ID'] = $request['sube_id'];
        }

        if($request['kategori_id'] > 0){
            $sql .= " AND K.KATEGORI_ID = :KATEGORI_ID";
            $data[':KATEGORI_ID'] = $request['kategori_id'];
        }

        if(in_array2($request['durum'],array(0,1))){
            $sql .= " AND K.DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }

        if(!empty($request['tarih']) AND $request['tarih_var'] > 0){
            $sql .= " AND DATE(K.TARIH) >= :TARIH1 AND DATE(K.TARIH) <= :TARIH2";
            $tarih = explode(",", $request['tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
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

    function kampanya_duzenle() {
        
        $data = array();
        $sql = "SELECT * FROM KAMPANYA WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        $href = "/views/kampanya/kampanya_duzenle.php?route=kampanya/kampanya_listesi&id={$row->ID}&token={$row->TOKEN}";

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kampanya Düzenle.";
            $result["URL"]       = $href;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Ürün Bulunamadı!";
        }

        return $result;
    }

    public function kampanya_kaydet() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT * FROM KAMPANYA WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kampanya Bulunamadı!";
            return $result;
        }

        if(strlen(trim($_REQUEST['kampanya'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kampanya Adı Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['bas_tarih'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Başlangıç Tarih Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['bas_saat'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Başlangıç Saat Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['bit_tarih'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bitiş Tarih Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['bit_saat'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bitiş Saat Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['kampanya_bas_saat'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kampanya Başlangıç Saat Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['kampanya_bit_saat'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kampanya Bitiş Saat Giriniz!";
            return $result;
        }

        if(is_null($_FILES['resim'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE KAMPANYA SET KAMPANYA            = :KAMPANYA,
                                    KATEGORI_ID         = :KATEGORI_ID,
                                    BAS_TARIH           = :BAS_TARIH,
                                    BAS_SAAT            = :BAS_SAAT,
                                    BIT_TARIH           = :BIT_TARIH,
                                    BIT_SAAT            = :BIT_SAAT,
                                    KAMPANYA_BAS_SAAT   = :KAMPANYA_BAS_SAAT,
                                    KAMPANYA_BIT_SAAT   = :KAMPANYA_BIT_SAAT,
                                    ACIKLAMA            = :ACIKLAMA,
                                    GTARIH              = NOW()
                                WHERE ID = :ID
                                ";
        $data[":KAMPANYA"]          = trim($_REQUEST['kampanya']);
        $data[":KATEGORI_ID"]       = $_REQUEST['kategori_id'];
        $data[":BAS_TARIH"]         = $_REQUEST['bas_tarih'];
        $data[":BAS_SAAT"]          = $_REQUEST['bas_saat'];
        $data[":BIT_TARIH"]         = $_REQUEST['bit_tarih'];
        $data[":BIT_SAAT"]          = $_REQUEST['bit_saat'];
        $data[":KAMPANYA_BAS_SAAT"] = $_REQUEST['kampanya_bas_saat'];
        $data[":KAMPANYA_BIT_SAAT"] = $_REQUEST['kampanya_bit_saat'];
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {

            $data = array();
            $sql = "SELECT 
                        YEAR(K.TARIH) AS YIL,
                        K.ID,
                        K.TOKEN,
                        K.RESIM_URL
                    FROM KAMPANYA AS K
                    WHERE K.ID = :ID
                    ";
            $data[":ID"]  = $row->ID;
            $row_kampanya = DB::getRow($sql, $data);

            if($row->ID > 0){
                $yol    = fncImgPathFirma($row_site->IMG_PATH, 'kampanya', $row_kampanya->ID, $row_kampanya->YIL);
                $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

                if($resim["HATA"]){
                    $result["HATA"]          = TRUE;
                    $result["ACIKLAMA"]      = "Resim Yüklerken Bir Sorun Oluştu!";
                    return $result;
                }

                $data = array();
                $sql = "UPDATE KAMPANYA SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
                $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
                $data[":ID"]        = $row->ID;
                $resim = DB::exec($sql, $data);

                //Eski Resimi Siliyoruz
                if($resim > 0) unlink(fncDocumentRoot($row_kampanya->RESIM_URL, $row_site->IMG_PATH));

            }
        }

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kayıt Edildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function kampanya_sube_kaydet() {

        $data = array();
        $sql = "SELECT * FROM KAMPANYA WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }
        
        $data = array();
        $sql = "UPDATE KAMPANYA SET SUBE_IDS    = :SUBE_IDS,
                                    GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":SUBE_IDS"]      = is_null($_REQUEST['sube_ids']) ? NULL : implode(',',$_REQUEST['sube_ids']);
        $data[":ID"]            = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kayıt Edildi.";
            $result["URL"]       = "/views/kampanya/kampanya_listesi.php?route=kampanya/kampanya_listesi&kampanya={$row->KAMPANYA}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function kampanya_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM KAMPANYA WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kampanya Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM KAMPANYA WHERE ID = :ID";
        $data[":ID"]        = $row->ID;
        $delete = DB::exec($sql, $data);

        if($delete > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Silindi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }
}