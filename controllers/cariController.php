<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class CariController {

    private $select;
    private $site;

    public function __construct($select = "") {
        $this->select       = $select;
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

    public function getCariler($request) {

        $data = array();
        $sql = "SELECT 
                    C.*,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN,
                    IL.IL,
                    ILCE.ILCE
                FROM CARI AS C
                    LEFT JOIN KULLANICI AS KU ON KU.ID = C.KAYIT_YAPAN_ID
                    LEFT JOIN IL AS IL ON IL.ID = C.IL_ID
                    LEFT JOIN ILCE AS ILCE ON ILCE.ID = C.ILCE_ID
                WHERE 1
                ";

        if($request['cari']){
            $sql .= " AND C.CARI LIKE :CARI";
            $data[':CARI'] = "%". $request['cari'] . "%";
        }

        if(in_array2($request['durum'],array(0,1))){
            $sql .= " AND C.DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }

        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, 10);
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

    public function cari_ekle() {
        global $cResim, $row_site;

        if(strlen(trim($_REQUEST['cari'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Firma ismi Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkili Adı Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['telefon'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Telefon Giriniz!";
            return $result;
        }

        if($_REQUEST['il_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İl Seçiniz!";
            return $result;
        }

        if($_REQUEST['ilce_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İlçe Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO CARI SET    CARI            = :CARI,
                                        AD              = :AD,
                                        SOYAD           = :SOYAD,
                                        TELEFON         = :TELEFON,
                                        MAIL            = :MAIL,
                                        IL_ID           = :IL_ID,
                                        ILCE_ID         = :ILCE_ID,
                                        ADRES           = :ADRES,
                                        KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                        TOKEN           = MD5(NOW())
                                            ";
        $data[":CARI"]              = trim($_REQUEST['cari']);
        $data[":AD"]                = trim($_REQUEST['ad']);
        $data[":SOYAD"]             = trim($_REQUEST['soyad']);
        $data[":TELEFON"]           = $_REQUEST['telefon'];
        $data[":MAIL"]              = trim($_REQUEST['mail']);
        $data[":IL_ID"]             = $_REQUEST['il_id'];
        $data[":ILCE_ID"]           = $_REQUEST['ilce_id'];
        $data[":ADRES"]             = trim($_REQUEST['adres']);
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT 
                    YEAR(C.TARIH) AS YIL,
                    C.ID,
                    C.TOKEN
                FROM CARI AS C
                WHERE C.ID = :ID
                ";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $yol    = "img/cari/{$row->ID}/{$row->YIL}/";
            $resim = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

            $data = array();
            $sql = "UPDATE CARI SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
            $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
            $data[":ID"]        = $row->ID;
            $resim = DB::exec($sql, $data);
        }

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Firma Oluşturuldu.";
            $result["URL"]       = "/views/cari/cari_duzenle.php?route=cari/cari_listesi&id={$row->ID}&token={$row->TOKEN}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function cari_kaydet() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT * FROM CARI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kampanya Bulunamadı!";
            return $result;
        }

        if(strlen(trim($_REQUEST['cari'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Firma ismi Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkili Adı Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['telefon'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Telefon Giriniz!";
            return $result;
        }

        if($_REQUEST['il_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İl Seçiniz!";
            return $result;
        }

        if($_REQUEST['ilce_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İlçe Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE CARI SET CARI            = :CARI,
                                AD              = :AD,
                                SOYAD           = :SOYAD,
                                TELEFON         = :TELEFON,
                                MAIL            = :MAIL,
                                IL_ID           = :IL_ID,
                                ILCE_ID         = :ILCE_ID,
                                ADRES           = :ADRES,
                                GTARIH          = NOW()
                            WHERE ID = :ID
                            ";
        $data[":CARI"]              = trim($_REQUEST['cari']);
        $data[":AD"]                = trim($_REQUEST['ad']);
        $data[":SOYAD"]             = trim($_REQUEST['soyad']);
        $data[":TELEFON"]           = $_REQUEST['telefon'];
        $data[":MAIL"]              = trim($_REQUEST['mail']);
        $data[":IL_ID"]             = $_REQUEST['il_id'];
        $data[":ILCE_ID"]           = $_REQUEST['ilce_id'];
        $data[":ADRES"]             = trim($_REQUEST['adres']);
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {

            $data = array();
            $sql = "SELECT 
                        YEAR(C.TARIH) AS YIL,
                        C.ID,
                        C.TOKEN,
                        C.RESIM_URL
                    FROM CARI AS C
                    WHERE C.ID = :ID
                    ";
            $data[":ID"]  = $row->ID;
            $row_cari = DB::getRow($sql, $data);

            if($row->ID > 0){
                $yol    = "img/cari/{$row_cari->ID}/{$row_cari->YIL}/";
                $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

                $data = array();
                $sql = "UPDATE CARI SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
                $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
                $data[":ID"]        = $row->ID;
                $resim = DB::exec($sql, $data);

                //Eski Resimi Siliyoruz
                if($resim > 0) unlink(fncDocumentRoot($row_kampanya->RESIM_URL));
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

    public function musteri_cari_kaydet() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT * FROM CARI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Cari Bulunamadı!";
            return $result;
        }

        if(strlen(trim($_REQUEST['cari'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Firma ismi Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkili Adı Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['soyad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkili Soyadı Giriniz!";
            return $result;
        }

        if(empty($_REQUEST['telefon'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Telefon Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['mail'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Mail Giriniz!";
            return $result;
        }

        if(FormatValid::mail(trim($_REQUEST['mail']))){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Mail Doğru Formatta Değil!";
            return $result;
        }

        if($_REQUEST['il_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İl Seçiniz!";
            return $result;
        }

        if($_REQUEST['ilce_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İlçe Seçiniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE CARI SET CARI            = :CARI,
                                AD              = :AD,
                                SOYAD           = :SOYAD,
                                TELEFON         = :TELEFON,
                                MAIL            = :MAIL,
                                IL_ID           = :IL_ID,
                                ILCE_ID         = :ILCE_ID,
                                ADRES           = :ADRES,
                                TEMA_RENK       = :TEMA_RENK,
                                GTARIH          = NOW()
                            WHERE ID = :ID
                            ";
        $data[":CARI"]              = trim($_REQUEST['cari']);
        $data[":AD"]                = trim($_REQUEST['ad']);
        $data[":SOYAD"]             = trim($_REQUEST['soyad']);
        $data[":TELEFON"]           = $_REQUEST['telefon'];
        $data[":MAIL"]              = trim($_REQUEST['mail']);
        $data[":IL_ID"]             = $_REQUEST['il_id'];
        $data[":ILCE_ID"]           = $_REQUEST['ilce_id'];
        $data[":ADRES"]             = trim($_REQUEST['adres']);
        $data[":TEMA_RENK"]         = $_REQUEST['tema_renk'];
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {

            $data = array();
            $sql = "SELECT 
                        YEAR(C.TARIH) AS YIL,
                        C.ID,
                        C.TOKEN,
                        C.RESIM_URL
                    FROM CARI AS C
                    WHERE C.ID = :ID
                    ";
            $data[":ID"]  = $row->ID;
            $row_cari = DB::getRow($sql, $data);

            if($row->ID > 0){
                $yol    = "img/cari/{$row_cari->ID}/{$row_cari->YIL}/";
                $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

                $data = array();
                $sql = "UPDATE CARI SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
                $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
                $data[":ID"]        = $row->ID;
                $resim = DB::exec($sql, $data);

                //Eski Resimi Siliyoruz
                if($resim > 0) unlink(fncDocumentRoot($row_kampanya->RESIM_URL));
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

    public function getCari($request) {
        global $row_site;

        $data = array();
        $sql = "SELECT 
                    C.*,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM CARI AS C
                    LEFT JOIN KULLANICI AS KU ON KU.ID = C.KAYIT_YAPAN_ID
                WHERE C.ID = :ID
                ";

        if(in_array($_SESSION['yetki_id'],array(1))){
            $data[':ID'] = $request['id'];
        }else{
            $data[':ID'] = $row_site->ID;
        }
        
        $row = DB::getRow($sql, $data);
        return $row;
    }

    public function resim_yukle() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT 
                    C.ID,
                    YEAR(C.TARIH) AS YIL
                FROM CARI AS C
                WHERE C.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Cari Bulunamadı!";
            return $result;
        }

        if(is_null($_FILES['files'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $yol      = fncImgPathFirma($row_site->IMG_PATH, 'cari', $row->ID, $row->YIL);
        $resimler = $cResim->fncResimYukle($yol, $_FILES['files']);
        
        $say = 0;
        foreach ($resimler as $key => $resim) {
            $data = array();
            $sql = "INSERT INTO CARI_RESIM SET  CARI_ID         = :CARI_ID,
                                                RESIM_ADI       = :RESIM_ADI,
                                                RESIM_ADI_ILK   = :RESIM_ADI_ILK,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
            $data[":CARI_ID"]         = $row->ID;
            $data[':RESIM_ADI']       = $resim["RESIM_ADI"];
            $data[':RESIM_ADI_ILK']   = $resim["RESIM_ADI_ILK"];
            $data[":KAYIT_YAPAN_ID"]  = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
            if ($id > 0)$say++;

            fncIslemLog($id, DB::getSQL($sql, $data), $row, __FUNCTION__, "CARI_RESIM", "CARI_DUZENLE");
        }

        $data = array();
        $sql = "SELECT COUNT(*) AS SAY FROM CARI_RESIM WHERE CARI_ID = :CARI_ID";
        $data[":CARI_ID"]         = $row->ID;
        $row_resim_say = DB::getRow($sql, $data);

        if($row_resim_say->SAY == 1){
            $data = array();
            $sql = "UPDATE CARI_RESIM SET VITRIN = 1 WHERE CARI_ID = :CARI_ID";
            $data[":CARI_ID"]   = $row->ID;
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

    public function getCariResimler($request) {
        global $row_site;

        $data = array();
        $sql = "SELECT 
                    CR.*,
                    CONCAT('cari/', C.ID, '/', YEAR(C.TARIH), '/', CR.RESIM_ADI) AS RESIM_URL,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM CARI_RESIM AS CR
                    LEFT JOIN CARI AS C ON C.ID = CR.CARI_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = C.KAYIT_YAPAN_ID
                WHERE C.ID = :ID
                ";

        if(in_array($_SESSION['yetki_id'],array(1))){
            $data[':ID'] = $request['id'];
        }else{
            $data[':ID'] = $row_site->ID;
        }

        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function vitrin_yap() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM CARI_RESIM WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE CARI_RESIM SET VITRIN = 0 WHERE CARI_ID = :CARI_ID";
        $data[":CARI_ID"]        = $row->CARI_ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "UPDATE CARI_RESIM SET VITRIN = 1 WHERE ID = :ID";
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

    public function resim_sil() {
        global $row_site;

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT 
                    CR.*,
                    CONCAT('cari/', C.ID, '/', YEAR(C.TARIH), '/', CR.RESIM_ADI) AS RESIM_URL
                FROM CARI_RESIM AS CR
                    LEFT JOIN CARI AS C ON C.ID = CR.CARI_ID
                WHERE CR.ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }
        
        $data = array();
        $sql = "DELETE FROM CARI_RESIM WHERE ID = :ID";
        $data[":ID"]        = $row->ID;
        $delete = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "CARI_RESIM", "CARI_DUZENLE");

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

}