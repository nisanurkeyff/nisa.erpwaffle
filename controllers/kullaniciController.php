<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class KullaniciController {

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

    public function Yetkiler() {
        $data = array();
        $sql = "SELECT
                    Y.ID,
                    Y.YETKI AS AD
                FROM YETKI AS Y
                WHERE Y.DURUM = 1 AND Y.ID NOT IN(1)
                ORDER BY 1";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Iller() {
        $data = array();
        $sql = "SELECT
                    I.ID,
                    I.IL AS AD
                FROM IL AS I
                WHERE I.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Firmalar() {
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

    public function Ilceler($request = array()) {
        
        $data = array();
        $sql = "SELECT
                    I.ID,
                    I.ILCE AS AD
                FROM ILCE AS I
                WHERE I.DURUM = 1
                ";

        if ($request['il_id'] > 0) {
            $sql .= " AND I.IL_ID = :IL_ID";
            $data[":IL_ID"]  = $request['il_id'];
        }

        $sql .= " ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Musteriler() {
        $data = array();
        $sql = "SELECT
                    M.ID,
                    CONCAT_WS(' ',M.AD,M.SOYAD) AS AD
                FROM MUSTERI AS M
                WHERE M.DURUM = 1
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

    function ilce_doldur() {
        
        $data = array();
        $sql = "SELECT
                    I.ID,
                    I.ILCE AS AD
                FROM ILCE AS I
                WHERE I.IL_ID = :IL_ID
                ORDER BY 2
                ";
        $data[":IL_ID"]       = $_REQUEST["il_id"];
        $rows = DB::get($sql, $data);
        
        $html .= "<option value='-1' >Seçiniz</option>";
        foreach($rows as $key => $row) {
            $html .= "<option value=".$row->ID." >".$row->AD."</option>";
        }

        if(count2($rows) > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "İlçeler Dolduruldu.";
            $result["HTML"]      = $html;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "İlçe Bulunamadı!";
        }

        return $result;
    }

    public function kullanici_ekle() {

        if(strlen(trim($_REQUEST['kullanici'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kullanıcı Adı Giriniz!";
            return $result;
        }

        if(FormatValid::kullanici(trim($_REQUEST['kullanici']))){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kullanıcı Adı Doğru Formatta Değil!";
            return $result;
        }

        if(strlen(trim($_REQUEST['mail'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Email Giriniz!";
            return $result;
        }

        if(FormatValid::mail(trim($_REQUEST['mail']))){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Mail Doğru Formatta Değil!";
            return $result;
        }

        if(strlen(trim($_REQUEST['sifre'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şifre Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['sifre_tekrar'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şifre Tekrar Giriniz!";
            return $result;
        }

        if(trim($_REQUEST['sifre']) != trim($_REQUEST['sifre_tekrar'])){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Şifreler Uyuşmuyor!";
            return $result;
        }

        if($_REQUEST['yetki_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetki Seçiniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ad Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['soyad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Soyad Giriniz!";
            return $result;
        }

        if(strlen($_REQUEST['telefon']) < 12){
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
        $sql = "SELECT * FROM KULLANICI WHERE KULLANICI = :KULLANICI";
        $data[":KULLANICI"]  = trim($_REQUEST['kullanici']);
        $row_kontrol = DB::getRow($sql, $data);

        if($row_kontrol->ID > 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kullanıcı Adı Kullanılıyor!";
            return $result;   
        }

        if(!in_array($_SESSION['yetki_id'],array(1))){
            $_REQUEST['firma_id'] = $_SESSION["firma_id"];
        }

        $data = array();
        $sql = "INSERT INTO KULLANICI SET   FIRMA_ID        = :FIRMA_ID,
                                            KULLANICI       = :KULLANICI,
                                            MAIL            = :MAIL,
                                            SIFRE           = :SIFRE,
                                            SIFRE_TEKRAR    = :SIFRE_TEKRAR,
                                            YETKI_ID        = :YETKI_ID,
                                            AD              = :AD,
                                            SOYAD           = :SOYAD,
                                            TELEFON         = :TELEFON,
                                            IL_ID           = :IL_ID,
                                            ILCE_ID         = :ILCE_ID,
                                            ADRES           = :ADRES,
                                            TWITTER         = :TWITTER,
                                            FACEBOOK        = :FACEBOOK,
                                            INSTAGRAM       = :INSTAGRAM,
                                            LINKEDIN        = :LINKEDIN,
                                            TOKEN           = MD5(NOW())
                                            ";
        $data[":FIRMA_ID"]      = $_REQUEST['firma_id'];
        $data[":KULLANICI"]     = trim($_REQUEST['kullanici']);
        $data[":MAIL"]          = trim($_REQUEST['mail']);
        $data[":SIFRE"]         = trim($_REQUEST['sifre']);
        $data[":SIFRE_TEKRAR"]  = trim($_REQUEST['sifre_tekrar']);
        $data[":YETKI_ID"]      = $_REQUEST['yetki_id'];
        $data[":AD"]            = trim($_REQUEST['ad']);
        $data[":SOYAD"]         = trim($_REQUEST['soyad']);
        $data[":TELEFON"]       = $_REQUEST['telefon'];
        $data[":IL_ID"]         = $_REQUEST['il_id'];
        $data[":ILCE_ID"]       = $_REQUEST['ilce_id'];
        $data[":ADRES"]         = trim($_REQUEST['adres']);
        $data[":TWITTER"]       = trim($_REQUEST['twitter']);
        $data[":FACEBOOK"]      = trim($_REQUEST['facebook']);
        $data[":INSTAGRAM"]     = trim($_REQUEST['instagram']);
        $data[":LINKEDIN"]      = trim($_REQUEST['linkedin']);
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT * FROM KULLANICI WHERE ID = :ID";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kullanıcı Oluşturuldu.";
            $result["URL"]       = "/views/kullanici/duzenle.php?route=kullanici/kampanya_listesi&id={$row->ID}&token={$row->TOKEN}";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getKullanicilar($request = array()) {

        $data = array();
        $sql = "SELECT 
                    K.*,
                    Y.YETKI,
                    IL.IL,
                    ILCE.ILCE,
                    A.AVATAR,
                    A.CLASS,
                    A.ALT
                FROM KULLANICI AS K
                    LEFT JOIN YETKI AS Y ON Y.ID = K.YETKI_ID
                    LEFT JOIN IL AS IL ON IL.ID = K.IL_ID
                    LEFT JOIN ILCE AS ILCE ON ILCE.ID = K.ILCE_ID
                    LEFT JOIN AVATAR AS A ON A.ID = K.AVATAR_ID
                WHERE K.YETKI_ID NOT IN(1)";

        if($request['ad']){
            $sql .= " AND K.AD LIKE :AD";
            $data[':AD'] = "%". $request['ad'] . "%";
        }

        if($request['telefon']){
            $sql .= " AND K.TELEFON LIKE :TELEFON";
            $data[':TELEFON'] = "%". $request['telefon'] . "%";
        }

        if($request['mail']){
            $sql .= " AND K.MAIL LIKE :MAIL";
            $data[':MAIL'] = "%". $request['mail'] . "%";
        }

        $sql .= " ORDER BY K.TARIH DESC";
        
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

    function kullanici_duzenle() {
        
        $data = array();
        $sql = "SELECT * FROM KULLANICI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        $href = "/views/kullanici/duzenle.php?route=kullanici/kullanicilar&id={$row->ID}&token={$row->TOKEN}";

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kullanıcı Düzenle.";
            $result["URL"]      = $href;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bulunamadı!";
        }

        return $result;
    }

    public function getKullanici($request) {

        $data = array();
        $sql = "SELECT 
                    K.*,
                    Y.YETKI,
                    IL.IL,
                    ILCE.ILCE,
                    A.AVATAR
                FROM KULLANICI AS K
                    LEFT JOIN YETKI AS Y ON Y.ID = K.YETKI_ID
                    LEFT JOIN IL AS IL ON IL.ID = K.IL_ID
                    LEFT JOIN ILCE AS ILCE ON ILCE.ID = K.ILCE_ID
                    LEFT JOIN AVATAR AS A ON A.ID = K.AVATAR_ID
                WHERE K.ID =:ID
                ";

        $data[':ID'] = $request['id'];
        $rows = DB::getRow($sql, $data);
        return $rows;
    }

    function kullanici_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    K.*,
                    Y.YETKI,
                    IL.IL,
                    ILCE.ILCE 
                FROM KULLANICI AS K
                    LEFT JOIN YETKI AS Y ON Y.ID = K.YETKI_ID
                    LEFT JOIN IL AS IL ON IL.ID = K.IL_ID
                    LEFT JOIN ILCE AS ILCE ON ILCE.ID = K.ILCE_ID
                WHERE K.ID =:ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kullanıcı Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bulunamadı!";
        }

        return $result;
    }

    public function kullanici_kaydet() {

        $data = array();
        $sql = "SELECT * FROM KULLANICI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kullanıcı Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ad Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['soyad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Soyad Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['mail'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Email Giriniz!";
            return $result;
        }

        if($_REQUEST['yetki_id'] <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetki Seçiniz!";
            return $result;
        }

        if(!in_array($_REQUEST['durum'],array(0,1))){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Durum Seçiniz!";
            return $result;
        }

        if(strlen($_REQUEST['telefon']) < 12){
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
        $sql = "UPDATE KULLANICI SET    AD              = :AD,
                                        SOYAD           = :SOYAD,
                                        MAIL            = :MAIL,
                                        DURUM           = :DURUM,
                                        YETKI_ID        = :YETKI_ID,
                                        TELEFON         = :TELEFON,
                                        IL_ID           = :IL_ID,
                                        ILCE_ID         = :ILCE_ID,
                                        GTARIH          = NOW()
                                    WHERE ID = :ID
                                    ";
        $data[":AD"]            = trim($_REQUEST['ad']);
        $data[":SOYAD"]         = trim($_REQUEST['soyad']);
        $data[":MAIL"]          = trim($_REQUEST['mail']);
        $data[":DURUM"]         = $_REQUEST['durum'];
        $data[":YETKI_ID"]      = $_REQUEST['yetki_id'];
        $data[":TELEFON"]       = $_REQUEST['telefon'];
        $data[":IL_ID"]         = $_REQUEST['il_id'];
        $data[":ILCE_ID"]       = $_REQUEST['ilce_id'];
        $data[":ID"]            = $row->ID;
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

    public function kullanici_pasif_et() {
        
        $data = array();
        $sql = "SELECT * FROM KULLANICI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kullanıcı Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE KULLANICI SET DURUM = :DURUM WHERE ID = :ID";
        $data[":DURUM"]     = 0;
        $data[":ID"]        = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Pasif Edildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function kullanici_aktif_et() {
        
        $data = array();
        $sql = "SELECT * FROM KULLANICI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kullanıcı Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE KULLANICI SET DURUM = :DURUM WHERE ID = :ID";
        $data[":DURUM"]     = 1;
        $data[":ID"]        = $row->ID;
        $update = DB::exec($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Aktif Edildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getKullaniciAvatarlar() {

        $data = array();
        $sql = "SELECT 
                    A.*,
                    A.AVATAR
                FROM AVATAR AS A
                WHERE A.DURUM = 1 AND A.AVATAR_TURU_ID = 1
                ";

        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function avatar_kaydet() {

        $data = array();
        $sql = "SELECT * FROM KULLANICI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kullanıcı Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM AVATAR WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["avatar_id"];
        $row_avatar = DB::getRow($sql, $data);
        
        if(is_null($row_avatar->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Avatar Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE KULLANICI SET    AVATAR_ID   = :AVATAR_ID,
                                        GTARIH      = NOW()
                                    WHERE ID = :ID
                                    ";
        $data[":AVATAR_ID"]     = $row_avatar->ID;
        $data[":ID"]            = $row->ID;
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

    public function kullanici_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM KULLANICI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Anamenü Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM KULLANICI WHERE ID = :ID";
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