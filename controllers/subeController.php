<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Endroid\QrCode\Label\Font\Font;

class SubeController {

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

    public function Subeler($request = array()) {
        $data = array();
        $sql = "SELECT
                    S.ID,
                    S.SUBE AS AD
                FROM SUBE AS S
                WHERE S.DURUM = 1";

        if($request['satis_turu_id'] > 0) {
            $sql .= " AND S.SATIS_TURU_ID = :SATIS_TURU_ID";
            $data[":SATIS_TURU_ID"] = $request['satis_turu_id'];
        }

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Bolgeler() {
        $data = array();
        $sql = "SELECT
                    B.ID,
                    B.BOLGE AS AD
                FROM BOLGE AS B
                WHERE B.DURUM = 1";

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

    public function Urunler($request = array()) {
        $data = array();
        $sql = "SELECT
                    U.ID,
                    U.URUN AS AD
                FROM URUN AS U
                WHERE U.DURUM = 1
                ";

        if($request['satis_turu_id'] > 0) {
            $sql .= " AND U.SATIS_TURU_ID = :SATIS_TURU_ID";
            $data[":SATIS_TURU_ID"] = $request['satis_turu_id'];
        }

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function getSubeler($request) {

        $data = array();
        $sql = "SELECT 
                    S.*,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN,
                    IL.IL,
                    ILCE.ILCE,
                    ST.SATIS_TURU,
                    IF(S.MENU_DURUM = 1, 'Aktif', 'Pasif') AS MENU_DURUM_TEXT,
                    IF(S.DURUM = 1, 'Aktif', 'Pasif') AS DURUM_TEXT,
                    (SELECT COUNT(K.ID) AS SAY FROM KAMPANYA AS K WHERE FIND_IN_SET(S.ID, K.SUBE_IDS)) KAMPANYA_SAYISI
                FROM SUBE AS S
                    LEFT JOIN IL AS IL ON IL.ID = S.IL_ID
                    LEFT JOIN ILCE AS ILCE ON ILCE.ID = S.ILCE_ID
                    LEFT JOIN SATIS_TURU AS ST ON ST.ID = S.SATIS_TURU_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = S.KAYIT_YAPAN_ID
                WHERE 1";

        if($request['sube']){
            $sql .= " AND S.SUBE LIKE :SUBE";
            $data[':SUBE'] = "%". $request['sube'] . "%";
        }

        if($request['satis_turu_id'] > 0){
            $sql .= " AND S.SATIS_TURU_ID = :SATIS_TURU_ID";
            $data[':SATIS_TURU_ID'] = $request['satis_turu_id'];
        }

        if($request['il_id'] > 0){
            $sql .= " AND S.IL_ID = :IL_ID";
            $data[':IL_ID'] = $request['il_id'];
        }

        if($request['ilce_id'] > 0){
            $sql .= " AND S.ILCE_ID = :ILCE_ID";
            $data[':ILCE_ID'] = $request['ilce_id'];
        }

        if(in_array2($request['menu_durum'],array(0,1))){
            $sql .= " AND S.MENU_DURUM = :MENU_DURUM";
            $data[':MENU_DURUM'] = $request['menu_durum'];
        }

        if(in_array2($request['durum'],array(0,1))){
            $sql .= " AND S.DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }

        $sql .= " ORDER BY S.SUBE ASC";

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

    public function sube_ekle() {

        if(strlen(trim($_REQUEST['sube'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Adı Giriniz!";
            return $result;
        }

        if($_REQUEST['il_id'] < 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İl Seçiniz!";
            return $result;
        }

        if($_REQUEST['ilce_id'] < 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İlçe Seçiniz!";
            return $result;
        }

        if($_REQUEST['satis_turu_id'] < 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Satış Türü Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO SUBE SET    SUBE            = :SUBE,
                                        URL             = :URL,
                                        IL_ID           = :IL_ID,
                                        ILCE_ID         = :ILCE_ID,
                                        SATIS_TURU_ID   = :SATIS_TURU_ID,
                                        ADRES           = :ADRES,
                                        DURUM           = :DURUM,
                                        KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                        TOKEN           = MD5(NOW())
                                        ";
        $data[":SUBE"]              = trim($_REQUEST['sube']);
        $data[":URL"]               = $_REQUEST['url'];
        $data[":IL_ID"]             = $_REQUEST['il_id'];
        $data[":ILCE_ID"]           = $_REQUEST['ilce_id'];
        $data[":SATIS_TURU_ID"]     = $_REQUEST['satis_turu_id'];
        $data[":ADRES"]             = trim($_REQUEST['adres']);
        $data[":DURUM"]             = $_REQUEST['durum'];
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Stok Oluşturuldu.";
            $result["URL"]       = "/views/sube/sube_duzenle.php?route=sube/sube_listesi&id={$row->ID}&token={$row->TOKEN}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getSube($request) {

        $data = array();
        $sql = "SELECT 
                    S.*,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM SUBE AS S
                    LEFT JOIN KULLANICI AS KU ON KU.ID = S.KAYIT_YAPAN_ID
                WHERE S.ID = :ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::getRow($sql, $data);
        return $row;
    }

     public function sube_kaydet() {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['sube'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Adı Giriniz!";
            return $result;
        }

        if($_REQUEST['il_id'] < 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İl Seçiniz!";
            return $result;
        }

        if($_REQUEST['ilce_id'] < 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İlçe Seçiniz!";
            return $result;
        }

        if($_REQUEST['satis_turu_id'] < 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Satış Türü Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SUBE SET SUBE            = :SUBE,
                                URL             = :URL,
                                IL_ID           = :IL_ID,
                                ILCE_ID         = :ILCE_ID,
                                SATIS_TURU_ID   = :SATIS_TURU_ID,
                                ADRES           = :ADRES,
                                DURUM           = :DURUM,
                                BANNER_DURUM    = :BANNER_DURUM,
                                BANNER_BASLIK   = :BANNER_BASLIK,
                                BANNER_ICERIK   = :BANNER_ICERIK,
                                GTARIH          = NOW()
                            WHERE ID = :ID
                            ";
        $data[":SUBE"]              = trim($_REQUEST['sube']);
        $data[":URL"]               = $_REQUEST['url'];
        $data[":IL_ID"]             = $_REQUEST['il_id'];
        $data[":ILCE_ID"]           = $_REQUEST['ilce_id'];
        $data[":SATIS_TURU_ID"]     = $_REQUEST['satis_turu_id'];
        $data[":ADRES"]             = trim($_REQUEST['adres']);
        $data[":DURUM"]             = $_REQUEST['durum'];
        $data[":BANNER_DURUM"]      = $_REQUEST['banner_durum'];
        $data[":BANNER_BASLIK"]     = trim($_REQUEST['banner_baslik']);
        $data[":BANNER_ICERIK"]     = trim($_REQUEST['banner_icerik']);
        $data[":ID"]                = $row->ID;
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

    public function sube_urun_kaydet() {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }
        
        $data = array();
        $sql = "UPDATE SUBE SET URUN_IDS    = :URUN_IDS,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":URUN_IDS"]          = is_null($_REQUEST['urun_ids']) ? NULL : implode(',',$_REQUEST['urun_ids']);
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "SELECT 
                    SU.URUN_ID AS ID,
                    SU.ID AS SUBE_URUN_ID
                FROM SUBE_URUN AS SU
                WHERE SU.SUBE_ID = :SUBE_ID
                ";
        $data[":SUBE_ID"]  = $row->ID;
        $rows_urun = DB::get($sql, $data);
        $rows_urun = arrayIndex($rows_urun);

        $array_urun_ids = array();
        foreach ($_REQUEST['urun_ids'] as $key => $urun_id) {
            $array_urun_ids[] .= $urun_id;
            if($rows_urun[$urun_id]->SUBE_URUN_ID > 0) continue;

            $data = array();
            $sql = "INSERT INTO SUBE_URUN SET   SUBE_ID         = :SUBE_ID,
                                                URUN_ID         = :URUN_ID,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                            ";
            $data[":SUBE_ID"]           = $row->ID;
            $data[":URUN_ID"]           = $urun_id;
            $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
        }

        if(empty($array_urun_ids)){
            $data = array();
            $sql = "DELETE FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID";
            $data[":SUBE_ID"]    = $row->ID;
            DB::exec($sql, $data);
        }else{
            $data = array();
            $sql = "DELETE FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID AND !FIND_IN_SET(URUN_ID, :URUN_IDS)";
            $data[":SUBE_ID"]    = $row->ID;
            $data[":URUN_IDS"]   = FormatYazi::array2str($array_urun_ids);
            DB::exec($sql, $data);
        }

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kayıt Edildi.";
            $result["URL"]       = "/views/sube/sube_listesi.php?route=sube/sube_listesi&sube={$row->SUBE}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    function sube_duzenle() {
        
        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        $href = "/views/sube/sube_duzenle.php?route=sube/sube_listesi&id={$row->ID}&token={$row->TOKEN}";

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Müşteri Düzenle.";
            $result["URL"]       = $href;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Müşteri Bulunamadı!";
        }

        return $result;
    }

    function sube_qr_olustur() {
        global $row_site;
        
        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        if(is_null($row_site->QR_URL)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yöneticiye Başvurun!";
            return $result;
        }
        
        if(is_null($row->URL)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Menü URL Bulunamadı!";
            return $result;
        }

        
        $url = $row_site->QR_URL . $row->URL;
        $yol = "img/{$row_site->IMG_PATH}/sube/qr/{$row->ID}.png";

        if(!is_dir(dirname($yol))) {
            mkdir(dirname($yol), 0777, true);
        }

        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 1000,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::None,
            foregroundColor: new Color(0, 0, 0),  // Siyah renk
            backgroundColor: new Color(255, 255, 255) // Beyaz arka plan
        );

        if(!empty($row_site->FAVICON)){
            $logoPath = ltrim($row_site->LOGO, '/');
        }else{
            $logoPath = 'img/logo.jpg';
        }

        if (file_exists($logoPath)) {
            $logo = new Logo(path: $logoPath,resizeToWidth: 300,punchoutBackground: true);
        }

        $fontPath = fncDocumentRoot("/fonts/Oswald-Bold.ttf");
        $fontSize = 70;

        $label = new Label(
            text: strtoupper($row->SUBE),
            font: new Font($fontPath, $fontSize),
            textColor: new Color(0, 0, 0)
        );

        //$label = new Label(text: strtoupper($row->SUBE),textColor: new Color(35, 59, 47));

        $qr = $writer->write($qrCode, $logo, $label);
        $qr->saveToFile($yol);

        try {
            $writer->validateResult($qr, $url);
        } catch (ValidationException $e) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "QR doğrulama başarısız: " . $e->getMessage();
            return $result;
        }

        $data = array();
        $sql = "UPDATE SUBE SET QR_RESIM    = :QR_RESIM,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":QR_RESIM"]          = str_replace($row_site->IMG_PATH, '', str_replace('img/','',$yol));
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "QR Oluşturuldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getMenuGirisSayilari($request = array()){

        $data = array();
        $sql = "SELECT
                    S.SUBE,
                    COUNT(MG.ID) AS SAY
                FROM MENU_GIRIS AS MG
                    LEFT JOIN SUBE AS S ON S.ID = MG.SUBE_ID
                WHERE MG.SUBE_ID > 0
                ";

        if(!empty($request['tarih'])){
            $tarih = explode(',',$request['tarih']);
            $sql .= " AND DATE(MG.TARIH) <= :TARIH1 AND DATE(MG.TARIH) >= :TARIH2";
            $data[":TARIH1"] = $tarih[0];
            $data[":TARIH2"] = $tarih[1];
        }

        $sql .= " GROUP BY MG.SUBE_ID";
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function sube_menu_kategori_sec() {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        if(count2($_REQUEST['kategori_ids']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Seçiniz!";
            return $result;   
        }

        $data = array();
        $sql = "SELECT * FROM KATEGORI WHERE DURUM = 1";
        $rows_kategori = DB::get($sql, $data);
        $rows_kategori = arrayIndex($rows_kategori);

        $data = array();
        $sql = "SELECT 
                    SU.URUN_ID AS ID,
                    SU.ID AS SUBE_URUN_ID
                FROM SUBE_URUN AS SU
                WHERE SU.SUBE_ID = :SUBE_ID
                ";
        $data[":SUBE_ID"]  = $row->ID;
        $rows_urun_index = DB::get($sql, $data);
        $rows_urun_index = arrayIndex($rows_urun_index);

        $say = 0;
        foreach ($_REQUEST['kategori_ids'] as $key => $kategori_id) {
            if(is_null($rows_kategori[$kategori_id]->ID)) continue;

            $data = array();
            $sql = "SELECT * FROM URUN WHERE KATEGORI_ID = :KATEGORI_ID";
            $data[":KATEGORI_ID"] = $kategori_id;
            $rows_urun = DB::get($sql, $data);

            foreach ($rows_urun as $key => $row_urun) {
                if($row_urun->SATIS_TURU_ID != $row->SATIS_TURU_ID) continue;  //Satış Türü Eşleşmiyorsa Atla
                if($rows_urun_index[$row_urun->ID]->SUBE_URUN_ID > 0) continue; //Ürün Zaten Kayıtlıysa Atla

                $data = array();
                $sql = "INSERT INTO SUBE_URUN SET   SUBE_ID         = :SUBE_ID,
                                                    URUN_ID         = :URUN_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
                $data[":SUBE_ID"]           = $row->ID;
                $data[":URUN_ID"]           = $row_urun->ID;
                $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
                $id = DB::insert($sql, $data);
                if($id > 0) $say++;
            }
        }

        $data = array();
        $sql = "SELECT GROUP_CONCAT(URUN_ID) AS URUN_IDS FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID";
        $data[":SUBE_ID"]   = $row->ID;
        $row_urun = DB::getRow($sql, $data);

        $data = array();
        $sql = "UPDATE SUBE SET URUN_IDS    = :URUN_IDS,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":URUN_IDS"]    = $row_urun->URUN_IDS;
        $data[":ID"]          = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = $say . " Ürünler Aktarıldı";
            $result["URUN_IDS"]  = $row_urun->URUN_IDS;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function durum_kaydet(){
        
        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Stok Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SUBE SET DURUM    = :DURUM,
                                GTARIH   = NOW()
                            WHERE ID = :ID
                            ";
        $data[":DURUM"]    = $_REQUEST['durum'];
        $data[":ID"]       = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Durum Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
        
    }

    public function menu_durum_kaydet(){
        
        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Stok Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SUBE SET MENU_DURUM  = :MENU_DURUM,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":MENU_DURUM"]    = $_REQUEST['menu_durum'];
        $data[":ID"]            = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Menü Durumu Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
        
    }

    public function sube_menu_kopyala() {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["sube_id"];
        $row_sube = DB::getRow($sql, $data);

        if(is_null($row_sube->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kopyalanacak Şube Bulunamadı!";
            return $result;
        }

        if(is_null($row_sube->URUN_IDS) OR empty($row_sube->URUN_IDS)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kopyalanacak Şubede Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM URUN WHERE FIND_IN_SET(ID, :URUN_IDS)";
        $data[":URUN_IDS"] = $row_sube->URUN_IDS;
        $rows_urun = DB::get($sql, $data);

        if(empty($rows_urun)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kopyalanacak Ürün Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT 
                    SU.URUN_ID AS ID,
                    SU.ID AS SUBE_URUN_ID
                FROM SUBE_URUN AS SU
                WHERE SU.SUBE_ID = :SUBE_ID
                ";
        $data[":SUBE_ID"]  = $row->ID;
        $rows_urun_kontrol = DB::get($sql, $data);
        $rows_urun_kontrol = arrayIndex($rows_urun);

        foreach ($rows_urun as $key => $row_urun) {
            if($rows_urun_kontrol[$row_urun->ID]->SUBE_URUN_ID > 0) continue;

            $data = array();
            $sql = "INSERT INTO SUBE_URUN SET   SUBE_ID         = :SUBE_ID,
                                                URUN_ID         = :URUN_ID,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
            $data[":SUBE_ID"]           = $row->ID;
            $data[":URUN_ID"]           = $row_urun->ID;
            $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
        }

        $data = array();
        $sql = "SELECT GROUP_CONCAT(URUN_ID) AS URUN_IDS FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID";
        $data[":SUBE_ID"]   = $row->ID;
        $row_urun = DB::getRow($sql, $data);

        $data = array();
        $sql = "UPDATE SUBE SET URUN_IDS    = :URUN_IDS,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":URUN_IDS"]    = $row_urun->URUN_IDS;
        $data[":ID"]          = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Ürünler Aktarıldı";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function sube_menu_kategori_cikart() {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        if(count2($_REQUEST['kategori_ids']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Seçiniz!";
            return $result;   
        }

        $data = array();
        $sql = "SELECT * FROM KATEGORI WHERE DURUM = 1";
        $rows_kategori = DB::get($sql, $data);
        $rows_kategori = arrayIndex($rows_kategori);

        $data = array();
        $sql = "SELECT 
                    SU.URUN_ID AS ID,
                    SU.ID AS SUBE_URUN_ID
                FROM SUBE_URUN AS SU
                WHERE SU.SUBE_ID = :SUBE_ID
                ";
        $data[":SUBE_ID"]  = $row->ID;
        $rows_urun_index = DB::get($sql, $data);
        $rows_urun_index = arrayIndex($rows_urun_index);

        $say = 0;
        foreach ($_REQUEST['kategori_ids'] as $key => $kategori_id) {
            if(is_null($rows_kategori[$kategori_id]->ID)) continue;

            $data = array();
            $sql = "SELECT * FROM URUN WHERE KATEGORI_ID = :KATEGORI_ID";
            $data[":KATEGORI_ID"] = $kategori_id;
            $rows_urun = DB::get($sql, $data);

            foreach ($rows_urun as $key => $row_urun) {

                $data = array();
                $sql = "DELETE FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID AND URUN_ID = :URUN_ID";
                $data[":SUBE_ID"]           = $row->ID;
                $data[":URUN_ID"]           = $row_urun->ID;
                $delete = DB::exec($sql, $data);
                if($delete > 0) $say++;
            }
        }

        $data = array();
        $sql = "SELECT GROUP_CONCAT(URUN_ID) AS URUN_IDS FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID";
        $data[":SUBE_ID"]   = $row->ID;
        $row_urun = DB::getRow($sql, $data);

        $data = array();
        $sql = "UPDATE SUBE SET URUN_IDS    = :URUN_IDS,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":URUN_IDS"]    = $row_urun->URUN_IDS;
        $data[":ID"]          = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = $say . " Ürünler Çıkartıldı";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function sube_menu_ust_kategori_sec() {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        if(count2($_REQUEST['ust_kategori_ids']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Üst Kategori Seçiniz!";
            return $result;   
        }

        $data = array();
        $sql = "SELECT * FROM UST_KATEGORI WHERE DURUM = 1";
        $rows_kategori = DB::get($sql, $data);
        $rows_kategori = arrayIndex($rows_kategori);

        $data = array();
        $sql = "SELECT 
                    SU.URUN_ID AS ID,
                    SU.ID AS SUBE_URUN_ID
                FROM SUBE_URUN AS SU
                WHERE SU.SUBE_ID = :SUBE_ID
                ";
        $data[":SUBE_ID"]  = $row->ID;
        $rows_urun_index = DB::get($sql, $data);
        $rows_urun_index = arrayIndex($rows_urun_index);

        $say = 0;
        foreach ($_REQUEST['ust_kategori_ids'] as $key => $ust_kategori_id) {
            if(is_null($rows_kategori[$ust_kategori_id]->ID)) continue;

            $data = array();
            $sql = "SELECT 
                        U.*
                    FROM URUN AS U
                        LEFT JOIN KATEGORI AS K ON K.ID = U.KATEGORI_ID
                        LEFT JOIN UST_KATEGORI AS UK ON UK.ID = K.UST_KATEGORI_ID
                    WHERE K.UST_KATEGORI_ID = :UST_KATEGORI_ID";
            $data[":UST_KATEGORI_ID"] = $ust_kategori_id;
            $rows_urun = DB::get($sql, $data);

            foreach ($rows_urun as $key => $row_urun) {
                if($row_urun->SATIS_TURU_ID != $row->SATIS_TURU_ID) continue;  //Satış Türü Eşleşmiyorsa Atla
                if($rows_urun_index[$row_urun->ID]->SUBE_URUN_ID > 0) continue; //Ürün Zaten Kayıtlıysa Atla

                $data = array();
                $sql = "INSERT INTO SUBE_URUN SET   SUBE_ID         = :SUBE_ID,
                                                    URUN_ID         = :URUN_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
                $data[":SUBE_ID"]           = $row->ID;
                $data[":URUN_ID"]           = $row_urun->ID;
                $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
                $id = DB::insert($sql, $data);
                if($id > 0) $say++;
            }
        }

        $data = array();
        $sql = "SELECT GROUP_CONCAT(URUN_ID) AS URUN_IDS FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID";
        $data[":SUBE_ID"]   = $row->ID;
        $row_urun = DB::getRow($sql, $data);

        $data = array();
        $sql = "UPDATE SUBE SET URUN_IDS    = :URUN_IDS,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":URUN_IDS"]    = $row_urun->URUN_IDS;
        $data[":ID"]          = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = $say . " Ürünler Aktarıldı";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function sube_menu_ust_kategori_cikart() {

        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        if(count2($_REQUEST['ust_kategori_ids']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Üst Kategori Seçiniz!";
            return $result;   
        }

        $data = array();
        $sql = "SELECT * FROM UST_KATEGORI WHERE DURUM = 1";
        $rows_kategori = DB::get($sql, $data);
        $rows_kategori = arrayIndex($rows_kategori);

        $data = array();
        $sql = "SELECT 
                    SU.URUN_ID AS ID,
                    SU.ID AS SUBE_URUN_ID
                FROM SUBE_URUN AS SU
                WHERE SU.SUBE_ID = :SUBE_ID
                ";
        $data[":SUBE_ID"]  = $row->ID;
        $rows_urun_index = DB::get($sql, $data);
        $rows_urun_index = arrayIndex($rows_urun_index);

        $say = 0;
        foreach ($_REQUEST['ust_kategori_ids'] as $key => $ust_kategori_id) {
            if(is_null($rows_kategori[$ust_kategori_id]->ID)) continue;

            $data = array();
            $sql = "SELECT 
                        U.*
                    FROM URUN AS U
                        LEFT JOIN KATEGORI AS K ON K.ID = U.KATEGORI_ID
                        LEFT JOIN UST_KATEGORI AS UK ON UK.ID = K.UST_KATEGORI_ID
                    WHERE K.UST_KATEGORI_ID = :UST_KATEGORI_ID";
            $data[":UST_KATEGORI_ID"] = $ust_kategori_id;
            $rows_urun = DB::get($sql, $data);

            foreach ($rows_urun as $key => $row_urun) {

                $data = array();
                $sql = "DELETE FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID AND URUN_ID = :URUN_ID";
                $data[":SUBE_ID"]           = $row->ID;
                $data[":URUN_ID"]           = $row_urun->ID;
                $delete = DB::exec($sql, $data);
                if($delete > 0) $say++;
            }
        }

        $data = array();
        $sql = "SELECT GROUP_CONCAT(URUN_ID) AS URUN_IDS FROM SUBE_URUN WHERE SUBE_ID = :SUBE_ID";
        $data[":SUBE_ID"]   = $row->ID;
        $row_urun = DB::getRow($sql, $data);

        $data = array();
        $sql = "UPDATE SUBE SET URUN_IDS    = :URUN_IDS,
                                GTARIH      = NOW()
                            WHERE ID = :ID
                            ";
        $data[":URUN_IDS"]    = $row_urun->URUN_IDS;
        $data[":ID"]          = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = $say . " Ürünler Çıkartıldı";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function sube_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SUBE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şube Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM SUBE WHERE ID = :ID";
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