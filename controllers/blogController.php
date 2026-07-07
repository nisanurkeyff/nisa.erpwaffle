<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class BlogController {

    private $select;
    private $site;

    function __construct($select = "", $row_site = "") {
        global $row_site;
        $this->select       = $select;
        $this->site         = $row_site;
    }

    public function sayfalama($toplamVeri, $sayfaBasinaVeri, $request){
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

    public function blog_ekle() {

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ad Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['baslik'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Stok İsmi Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['icerik'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İçerik Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['url'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "URL Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM BLOG WHERE URL = :URL";
        $data[":URL"]  = trim($_REQUEST['url']);
        $row_kontrol = DB::getRow($sql, $data);

        if($row_kontrol->ID > 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Aynı URL ile Blog Oluşturulmuş!";
            return $result;
        }        

        $data = array();
        $sql = "INSERT INTO BLOG SET    AD              = :AD,
                                        BASLIK          = :BASLIK,
                                        ACIKLAMA        = :ACIKLAMA,
                                        URL             = :URL,
                                        ICERIK          = :ICERIK,
                                        KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                        DURUM           = :DURUM,    
                                        TOKEN           = MD5(NOW())
                                        ";
        $data[":AD"]                = trim($_REQUEST['ad']);
        $data[":BASLIK"]            = trim($_REQUEST['baslik']);
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":URL"]               = trim($_REQUEST['url']);
        $data[":ICERIK"]            = trim($_REQUEST['icerik']);
        $data[":DURUM"]             = $_REQUEST['durum'];
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT * FROM BLOG WHERE ID = :ID";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kullanıcı Oluşturuldu.";
            $result["URL"]       = "/views/blog/blog_duzenle.php?id={$row->ID}&token={$row->TOKEN}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getBloglar($request) {

        $data = array();
        $sql = "SELECT
                    B.ID,
                    B.TOKEN,
                    B.AD,
                    B.BASLIK,
                    B.ACIKLAMA,
                    B.URL,
                    B.GTARIH,
                    B.TARIH,
                    CONCAT_WS(' ',K.AD,K.SOYAD) AS KAYIT_YAPAN,
                    CONCAT('blog/', B.ID, '/', YEAR(B.TARIH), '/', BR.RESIM_ADI) AS RESIM_URL,
                    BR.ALT AS RESIM_ALT
                FROM BLOG AS B
                    LEFT JOIN KULLANICI AS K ON K.ID = B.KAYIT_YAPAN_ID
                    LEFT JOIN BLOG_RESIM AS BR ON BR.BLOG_ID = B.ID
                WHERE 1";

        if($request['baslik']){
            $sql .= " AND B.BASLIK LIKE :BASLIK";
            $data[':BASLIK'] = "%". $request['baslik'] . "%";
        }

        if($request['ad']){
            $sql .= " AND B.AD LIKE :AD";
            $data[':AD'] = "%". $request['ad'] . "%";
        }

        if(in_array($request['durum'],array(0,1))){
            $sql .= " AND B.DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }

        if($request['kategori_id'] > 0){
            $sql .= " AND FIND_IN_SET(B.KATEGORI_IDS, :KATEGORI_ID)";
            $data[':KATEGORI_ID'] = $request['kategori_id'];
        }

        $sayfalama = $this->sayfalama(count2(DB::get($sql, $data)), 10, $request);

        $sql .= $sayfalama->getLimitOffset();
        $rows = DB::get($sql, $data);

        return [
            'rows' => $rows,
            'sayfalama' => $sayfalama,
            'limit' => $sayfalama->getLimitOffset()
        ];
    }

    function blog_duzenle() {
        
        $data = array();
        $sql = "SELECT * FROM BLOG WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        $href = "/back/views/blog/blog_duzenle.php?id={$row->ID}&token={$row->TOKEN}";

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Blog Düzenle.";
            $result["URL"]       = $href;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Blog Bulunamadı!";
        }

        return $result;
    }

    function blog_git() {
        
        $data = array();
        $sql = "SELECT * FROM BLOG WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        $href = "/blog/{$row->URL}";

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Blog.";
            $result["URL"]       = $href;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Blog Bulunamadı!";
        }

        return $result;
    }

    public function getBlog($request) {

        $data = array();
        $sql = "SELECT 
                    B.*,
                    CONCAT_WS(' ',K.AD,K.SOYAD) AS KAYIT_YAPAN
                FROM BLOG AS B
                    LEFT JOIN KULLANICI AS K ON K.ID = B.KAYIT_YAPAN_ID
                WHERE B.ID =:ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::getRow($sql, $data);
        return $row;
    }

    public function blog_kaydet() {

        $data = array();
        $sql = "SELECT * FROM BLOG WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Blog Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ad Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['baslik'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Stok İsmi Giriniz!";
            return $result;
        }

        if(count2($_REQUEST['kategori_ids']) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Seçiniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['icerik'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "İçerik Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE BLOG SET     AD              = :AD,
                                    BASLIK          = :BASLIK,
                                    ACIKLAMA        = :ACIKLAMA,
                                    URL             = :URL,
                                    KATEGORI_IDS    = :KATEGORI_IDS,
                                    ICERIK          = :ICERIK,
                                    DURUM           = :DURUM,
                                    GTARIH          = NOW()
                                WHERE ID = :ID
                                ";
        $data[":AD"]                = trim($_REQUEST['ad']);
        $data[":BASLIK"]            = trim($_REQUEST['baslik']);
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":URL"]               = trim($_REQUEST['url']);
        $data[":KATEGORI_IDS"]      = implode(',', $_REQUEST['kategori_ids']);
        $data[":ICERIK"]            = trim($_REQUEST['icerik']);
        $data[":DURUM"]             = $_REQUEST['durum'];
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

    public function getBlogResimler($request) {

        $data = array();
        $sql = "SELECT 
                    BR.*,
                    CONCAT('blog/', B.ID, '/', YEAR(B.TARIH), '/', BR.RESIM_ADI) AS RESIM_URL,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM BLOG_RESIM AS BR
                    LEFT JOIN BLOG AS B ON B.ID = BR.BLOG_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = B.KAYIT_YAPAN_ID
                WHERE B.ID =:ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::get($sql, $data);
        return $row;
    }

    public function resim_yukle() {
        global $cResim;

        $data = array();
        $sql = "SELECT 
                    B.ID,
                    YEAR(B.TARIH) AS YIL
                FROM BLOG AS B
                WHERE B.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Blog Bulunamadı!";
            return $result;
        }

        if(is_null($_FILES['files'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $yol = "img/blog/" . $row->ID . "/" . $row->YIL . "/";
        $resimler = $cResim->fncResimYukle($yol, $_FILES['files']);
        
        $say = 0;
        foreach ($resimler as $key => $resim) {
            $data = array();
            $sql = "INSERT INTO BLOG_RESIM SET  BLOG_ID         = :BLOG_ID,
                                                RESIM_ADI       = :RESIM_ADI,
                                                RESIM_ADI_ILK   = :RESIM_ADI_ILK,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
            $data[":BLOG_ID"]         = $row->ID;
            $data[':RESIM_ADI']       = $resim["RESIM_ADI"];
            $data[':RESIM_ADI_ILK']   = $resim["RESIM_ADI_ILK"];
            $data[":KAYIT_YAPAN_ID"]  = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);

            $data = array();
            $sql = "SELECT COUNT(*) AS SAY FROM BLOG_RESIM WHERE BLOG_ID = :BLOG_ID";
            $data[":BLOG_ID"]         = $row->ID;
            $row_resim_say = DB::getRow($sql, $data);

            if($row_resim_say->SAY == 1){
                $data = array();
                $sql = "UPDATE BLOG_RESIM SET VITRIN = 1 WHERE BLOG_ID = :BLOG_ID";
                $data[":BLOG_ID"]   = $row->ID;
                DB::exec($sql, $data);
            }

            if ($id > 0)$say++;
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

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM BLOG_RESIM WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM BLOG_RESIM WHERE ID = :ID";
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

    public function blog_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM BLOG WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Blog Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM BLOG WHERE ID = :ID";
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

    public function alt_guncelle() {

        $data = array();
        $sql = "SELECT * FROM BLOG_RESIM WHERE ID = :ID";
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
        $sql = "UPDATE BLOG_RESIM SET   ALT     = :ALT,
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

    public function vitrin_yap() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM BLOG_RESIM WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE BLOG_RESIM SET VITRIN = 0 WHERE BLOG_ID = :BLOG_ID";
        $data[":BLOG_ID"]        = $row->BLOG_ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "UPDATE BLOG_RESIM SET VITRIN = 1 WHERE ID = :ID";
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
}