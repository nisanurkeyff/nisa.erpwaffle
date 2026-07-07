<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class MenuController {

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

    public function getAnamenuler($request) {

        $data = array();
        $sql = "SELECT * FROM ANAMENU WHERE 1 ORDER BY SIRA ASC";
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function getMenuler($request) {

        $data = array();
        $sql = "SELECT 
                    M.*,
                    A.ANAMENU
                FROM MENU AS M
                    LEFT JOIN ANAMENU AS A ON A.ID = M.ANAMENU_ID
                WHERE 1
                ORDER BY SIRA ASC
                ";
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function anamenu_ekle() {

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Adı Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['klasor'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Klasör Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['icon'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İcon Giriniz!";
            return $result;
        }

        if($_REQUEST['sira'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO ANAMENU SET ANAMENU      = :ANAMENU,
                                        ROUTE        = :ROUTE,
                                        ICON         = :ICON,
                                        SIRA         = :SIRA,
                                        DURUM        = :DURUM
                                        ";
        $data[":ANAMENU"]    = trim($_REQUEST['ad']);
        $data[":ROUTE"]      = trim($_REQUEST['klasor']);
        $data[":ICON"]       = trim($_REQUEST['icon']);
        $data[":SIRA"]       = $_REQUEST['sira'];
        $data[":DURUM"]      = $_REQUEST['durum'];
        $id = DB::insert($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Anamenü Oluşturuldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function anamenu_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM ANAMENU WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Anamenü Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM ANAMENU WHERE ID = :ID";
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

    function anamenu_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    A.*
                FROM ANAMENU AS A
                WHERE A.ID =:ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Anamenü Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Anamenü Bulunamadı!";
        }

        return $result;
    }

    public function anamenu_kaydet() {

        $data = array();
        $sql = "SELECT * FROM ANAMENU WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Anamenü Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Adı Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['klasor'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Klasör Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['icon'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İcon Giriniz!";
            return $result;
        }

        if($_REQUEST['sira'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE ANAMENU SET  ANAMENU      = :ANAMENU,
                                    ROUTE        = :ROUTE,
                                    ICON         = :ICON,
                                    SIRA         = :SIRA,
                                    DURUM        = :DURUM
                                WHERE ID = :ID
                                ";
        $data[":ANAMENU"]    = trim($_REQUEST['ad']);
        $data[":ROUTE"]      = trim($_REQUEST['klasor']);
        $data[":ICON"]       = trim($_REQUEST['icon']);
        $data[":SIRA"]       = $_REQUEST['sira'];
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

    function menu_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    M.*
                FROM MENU AS M
                WHERE M.ID =:ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        $row->YETKI_IDS = explode(',',$row->YETKI_IDS);

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Menü Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bulunamadı!";
        }

        return $result;
    }

    public function Anamenuler() {
        $data = array();
        $sql = "SELECT
                    A.ID,
                    A.ANAMENU AS AD
                FROM ANAMENU AS A
                WHERE A.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
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

    public function menu_ekle() {

        if($_REQUEST['anamenu_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Anamenü Seçiniz!";
            return $result;
        }

        if($_REQUEST['yetki_ids'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkileri Seçiniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Adı Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['klasor'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Klasör Giriniz!";
            return $result;
        }

        if($_REQUEST['sira'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO MENU SET    ANAMENU_ID   = :ANAMENU_ID,
                                        YETKI_IDS    = :YETKI_IDS,
                                        MENU         = :MENU,
                                        LINK         = :LINK,
                                        ROUTE        = :ROUTE,
                                        SIRA         = :SIRA,
                                        DURUM        = :DURUM
                                        ";
        $data[":ANAMENU_ID"] = $_REQUEST['anamenu_id'];
        $data[":YETKI_IDS"]  = implode(',',$_REQUEST['yetki_ids']);
        $data[":MENU"]       = trim($_REQUEST['ad']);
        $data[":LINK"]       = trim($_REQUEST['klasor']);
        $data[":ROUTE"]      = trim($_REQUEST['route']);
        $data[":SIRA"]       = $_REQUEST['sira'];
        $data[":DURUM"]      = $_REQUEST['durum'];
        $id = DB::insert($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Menü Oluşturuldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function menu_kaydet() {

        $data = array();
        $sql = "SELECT * FROM MENU WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Menü Bulunamadı!";
            return $result;
        }
        
        if($_REQUEST['anamenu_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Anamenü Seçiniz!";
            return $result;
        }

        if($_REQUEST['yetki_ids'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkileri Seçiniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Adı Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['klasor'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Klasör Giriniz!";
            return $result;
        }

        if($_REQUEST['sira'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE MENU SET ANAMENU_ID   = :ANAMENU_ID,
                                YETKI_IDS    = :YETKI_IDS,
                                MENU         = :MENU,
                                LINK         = :LINK,
                                ROUTE        = :ROUTE,
                                SIRA         = :SIRA,
                                DURUM        = :DURUM
                            WHERE ID = :ID
                            ";
        $data[":ANAMENU_ID"] = $_REQUEST['anamenu_id'];
        $data[":YETKI_IDS"]  = implode(',',$_REQUEST['yetki_ids']);
        $data[":MENU"]       = trim($_REQUEST['ad']);
        $data[":LINK"]       = trim($_REQUEST['klasor']);
        $data[":ROUTE"]      = trim($_REQUEST['route']);
        $data[":SIRA"]       = $_REQUEST['sira'];
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

    public function menu_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM MENU WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Menü Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM MENU WHERE ID = :ID";
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