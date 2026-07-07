<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class BolgeController {

    private $select;
    private $site;
    private $resim_yol;

    public function __construct($select = "", $row_site = "") {
        $this->select       = $select;
        $this->site        = $row_site;
        $this->resim_yol    = $resim_yol;
    }

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

    public function getBolgeler($request) {

        $data = array();
        $sql = "SELECT 
                    B.*
                FROM BOLGE AS B
                WHERE 1
                ";
        $rows = DB::get($sql, $data);
        return $rows;
    }

    function bolge_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    B.*
                FROM BOLGE AS B
                WHERE B.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Bölge Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bulunamadı!";
        }

        return $result;
    }

    public function Yetkiler() {
        $data = array();
        $sql = "SELECT
                    Y.ID,
                    Y.YETKI AS AD
                FROM YETKI AS Y
                WHERE Y.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function bolge_ekle() {

        if(strlen(trim($_REQUEST['bolge'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bölge Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO BOLGE SET   BOLGE        = :BOLGE,
                                        DURUM        = :DURUM
                                        ";
        $data[":BOLGE"]      = trim($_REQUEST['bolge']);
        $data[":DURUM"]      = $_REQUEST['durum'];
        $id = DB::insert($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Bölge Oluşturuldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function bolge_kaydet() {

        $data = array();
        $sql = "SELECT * FROM BOLGE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bölge Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['bolge'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bölge Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE BOLGE SET    BOLGE        = :BOLGE,
                                    DURUM        = :DURUM
                                WHERE ID = :ID
                                ";
        $data[":BOLGE"]      = trim($_REQUEST['bolge']);
        $data[":DURUM"]      = $_REQUEST['durum'];
        $data[":ID"]         = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function bolge_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM BOLGE WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Bölge Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM BOLGE WHERE ID = :ID";
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