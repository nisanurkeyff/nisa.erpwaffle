<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class GirisController {

    public function sayfalama($toplamVeri, $request, $sayfaBasinaVeri = 10){
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

    public function giris_yap() {

        if(empty($_REQUEST['kullanici']) OR empty($_REQUEST['sifre'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bilgiler Eksik!";
            return $result;
        }   
        
        @session_start();
        $kullanici  = $_REQUEST["kullanici"];
        $sifre      = $_REQUEST["sifre"];
        session_regenerate_id(true);

        $data = array();
        $sql = "SELECT 
                    K.*,
                    CONCAT_WS(' ',K.AD,K.SOYAD) AS KULLANICI,
                    Y.ID AS YETKI_ID,
                    Y.INDEX
                FROM KULLANICI AS K
                    LEFT JOIN YETKI AS Y ON Y.ID = K.YETKI_ID
                WHERE K.DURUM = 1 AND K.KULLANICI = :KULLANICI AND K.SIFRE = :SIFRE";
        $data[":KULLANICI"]     = $kullanici;
        $data[":SIFRE"]         = $sifre;
        $row = DB::getRow($sql, $data);

        if(is_null($row)){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bilgileri Hatalı.";
            return $result;
        }

        if($row->FIRMA_ID <= 0){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Yöneticiye Başvurun.";
            return $result;
        }

        if($row->ID > 0){
            $_SESSION["kullanici_id"]       = $row->ID;
            $_SESSION["kullanici"]          = $row->KULLANICI;
            $_SESSION["sifre"]              = $row->SIFRE;
            $_SESSION["yetki_id"]           = $row->YETKI_ID;
            $_SESSION["index"]              = $row->INDEX;
            $_SESSION["firma_id"]           = $row->FIRMA_ID;
            
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Giriş Yapıldı.";
            $result["URL"]       = '/views' . $row->INDEX;
            
            return $result;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bilgileri Hatalı.";
            return $result;
        }

        return $result;
    }
}