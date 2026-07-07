<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class SiteController {

    private $select;

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

    public function site_kaydet() {
        global $cResim;

        $data = array();
        $sql = "SELECT * FROM SITE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Site Bilgileri Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SITE SET FIRMA                   = :FIRMA,
                                AD                      = :AD,
                                SOYAD                   = :SOYAD,
                                TELEFON                 = :TELEFON,
                                MAIL                    = :MAIL,
                                IL_ID                   = :IL_ID,
                                ILCE_ID                 = :ILCE_ID,
                                ADRES                   = :ADRES,
                                TEMA_RENK               = :TEMA_RENK,
                                TITLE                   = :TITLE,
                                YONETIM_URL             = :YONETIM_URL,
                                QR_URL                  = :QR_URL,
                                FIYAT_DEGISIM_TARIH     = :FIYAT_DEGISIM_TARIH,
                                GTARIH                  = NOW()
                            WHERE ID = :ID
                            ";
        $data[":FIRMA"]                 = trim($_REQUEST['firma']);
        $data[":AD"]                    = trim($_REQUEST['ad']);
        $data[":SOYAD"]                 = trim($_REQUEST['soyad']);
        $data[":TELEFON"]               = $_REQUEST['telefon'];
        $data[":MAIL"]                  = trim($_REQUEST['mail']);
        $data[":IL_ID"]                 = $_REQUEST['il_id'];
        $data[":ILCE_ID"]               = $_REQUEST['ilce_id'];
        $data[":ADRES"]                 = trim($_REQUEST['adres']);
        $data[":TEMA_RENK"]             = $_REQUEST['tema_renk'];
        $data[":TITLE"]                 = trim($_REQUEST['title']);
        $data[":YONETIM_URL"]           = trim($_REQUEST['yonetim_url']);
        $data[":QR_URL"]                = trim($_REQUEST['qr_url']);
        $data[":FIYAT_DEGISIM_TARIH"]   = trim($_REQUEST['fiyat_degisim_tarih']);
        $data[":ID"]                    = $row->ID;
        $update = DB::exec($sql, $data);

        if (isset($_FILES['resim']) AND $_FILES['resim']['error'] == 0) {

            $yol    = "img/site/{$row->ID}/";
            $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

            $data = array();
            $sql = "UPDATE SITE SET LOGO = :LOGO WHERE ID = :ID";
            $data[":LOGO"]   = '/' . $yol . $resim["RESIM_ADI"];
            $data[":ID"]     = $row->ID;
            $resim = DB::exec($sql, $data);

            //Eski Resimi Siliyoruz
            if($resim > 0) unlink(fncDocumentRoot($row->LOGO));
        }

        if (isset($_FILES['favicon']) AND $_FILES['favicon']['error'] == 0) {

            $yol    = "img/site/{$row->ID}/";
            $resim  = $cResim->fncTekResimYukle($yol, $_FILES['favicon']);

            $data = array();
            $sql = "UPDATE SITE SET FAVICON = :FAVICON WHERE ID = :ID";
            $data[":FAVICON"]   = '/' . $yol . $resim["RESIM_ADI"];
            $data[":ID"]     = $row->ID;
            $resim = DB::exec($sql, $data);

            //Eski Resimi Siliyoruz
            if($resim > 0) unlink(fncDocumentRoot($row->FAVICON));
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
}