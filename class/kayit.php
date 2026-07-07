<? 

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class Kayit2{
	
	function __construct(){
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

        if($row->ID > 0){
            $_SESSION["kullanici_id"]       = $row->ID;
            $_SESSION["kullanici"]          = $row->KULLANICI;
            $_SESSION["sifre"]              = $row->SIFRE;
            $_SESSION["yetki_id"]           = $row->YETKI_ID;
            $_SESSION["index"]           	= $row->INDEX;
            
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Giriş Yapıldı.";
            $result["URL"]       = '/back' . $row->INDEX;
            
            return $result;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bilgileri Hatalı.";
            return $result;
        }

        return $result;

	}

    public function galeri_yukle(){
        $say = 0;
        
        $YOL            = fncImgPathFolder("galeri");        
        $YUKLEME_TARIHI = date("Y-m-d H:i:s");
        
        foreach($_FILES['resim']['name'] as $key => $resim){
            $DOSYA_TAM_AD   = $_FILES['resim']['name'][$key];         
            $DOSYA          = explode(".", $DOSYA_TAM_AD); 
            $DOSYA_AD       = $DOSYA[0]; 
            $DOSYA_UZANTI   = strtolower($DOSYA[sizeof($DOSYA)-1]);             
            
            list($usec, $sec)= explode(' ',microtime());  
            $TIMESTAMP = str_replace('.', '', ( ((float)$usec + (float)$sec) ));
            $TIMESTAMP = str_pad($TIMESTAMP, 14, "0", STR_PAD_RIGHT);           
            
            // Orijinal dosyayı kopyala
            $hedef_dosya = $YOL . $TIMESTAMP .".". $DOSYA_UZANTI;
            copy($_FILES['resim']['tmp_name'][$key], $hedef_dosya); 
            chmod($hedef_dosya, 0755);
            unlink($_FILES['resim']['tmp_name'][$key]); 
            
            // Resmi boyutlandır
            $this->resizeImage($hedef_dosya, 800, 600); // Genişliği 800px, yüksekliği 600px yapıyoruz
            
            if(file_exists($hedef_dosya)){
                // kapak resminin yüklenmesi
                $data = array();
                $sql = "INSERT INTO GALERI SET  EKLEYEN_ID      = :EKLEYEN_ID,
                                                RESIM_BASLIK    = :RESIM_BASLIK,
                                                RESIM_ACIKLAMA  = :RESIM_ACIKLAMA,
                                                SIRA            = 1,
                                                RESIM_ADI       = :RESIM_ADI,
                                                RESIM_ADI_ILK   = :RESIM_ADI_ILK,
                                                DURUM           = 0
                                                ";
                $data[':EKLEYEN_ID']      = $_SESSION['kullanici_id'];
                $data[':RESIM_BASLIK']    = "";
                $data[':RESIM_ACIKLAMA']  = "";
                $data[':RESIM_ADI']       = $TIMESTAMP . "." . $DOSYA_UZANTI;
                $data[':RESIM_ADI_ILK']   = $DOSYA_TAM_AD;
                $resim_id = DB::insert($sql, $data);
                
                if($resim_id > 0){
                    $say++; 
                }
            }
        }
    }

    public function resizeImage($file, $maxWidth, $maxHeight) {
        list($originalWidth, $originalHeight) = getimagesize($file);

        // Eğer resim zaten verilen boyutlardan küçükse işlem yapma
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return;
        }

        // Oranı koruyarak yeni boyutları hesaplayalım
        $aspectRatio = $originalWidth / $originalHeight;

        if ($originalWidth > $originalHeight) {
            // Genişlik büyükse
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $aspectRatio;
        } else {
            // Yükseklik büyükse veya eşitse
            $newHeight = $maxHeight;
            $newWidth = $maxHeight * $aspectRatio;
        }

        // Yeni resmi oluşturalım
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        $imageExtension = pathinfo($file, PATHINFO_EXTENSION);

        // Resmin formatına göre açma işlemi
        switch ($imageExtension) {
            case 'jpeg':
            case 'jpg':
                $source = imagecreatefromjpeg($file);
                break;
            case 'png':
                $source = imagecreatefrompng($file);
                break;
            case 'webp':
                $source = imagecreatefromwebp($file);
                break;
            default:
                return false;
        }

        // Yeniden boyutlandırma işlemi
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Resmi yeniden kaydedelim
        switch ($imageExtension) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($newImage, $file, 80); // Kaliteyi 80'e ayarladık
                break;
            case 'png':
                imagepng($newImage, $file);
                break;
            case 'webp':
                imagewebp($newImage, $file);
                break;
        }

        imagedestroy($newImage);
        imagedestroy($source);
    }

    public function galeri_resim_sil(){
        
        $YOL = fncImgPathFolder("galeri");
        
        if($_REQUEST['id'] <= 0){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Resim bulunamadı!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM GALERI WHERE ID = :ID";
        $data[":ID"]          = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);
        
        unlink($YOL . $row->RESIM_ADI);
        
        $data = array();
        $sql = "DELETE FROM GALERI  WHERE ID = :ID";
        $data[":ID"]          = $_REQUEST['id'];
        $rowsCount = DB::exec($sql, $data);
        
        if($rowsCount>0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Silindi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Hatalı!";
        }
            
        return $result;
        
    }

    public function mesaj_kaydet(){

        if(strlen(trim($_REQUEST['isim'])) <= 0){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "İsminizi giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['telefon'])) < 14){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Telefon giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['mesaj'])) <= 5){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Mesaj yazınız!";
            return $result;
        }
        
        $data = array();
        $sql = "INSERT INTO MESAJ SET   ISIM        = :ISIM,
                                        TELEFON     = :TELEFON,
                                        MAIL        = :MAIL,
                                        KONU        = :KONU,
                                        MESAJ       = :MESAJ
                                        ";
        $data[':ISIM']     = trim($_REQUEST['isim']);
        $data[':TELEFON']  = trim($_REQUEST['telefon']);
        $data[':MAIL']     = trim($_REQUEST['email']);
        $data[':KONU']     = trim($_REQUEST['konu']);
        $data[':MESAJ']    = trim($_REQUEST['mesaj']);
        $id = DB::insert($sql, $data);
        
        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Mesajın Bana Ulaştı. Benimle İletişime Geçtiğin için Teşekkür Ederim :)";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Diğer resim bulunamadı yada yüklenemedi!";
        }
        
        return $result;  
    }

    function stok_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    S.*,
                    K.KATEGORI,
                    CONCAT('stok/', S.ID, '/', YEAR(S.TARIH), '/', SR.RESIM_ADI) AS RESIM_URL,
                    SR.ALT AS RESIM_ALT
                FROM STOK AS S
                    LEFT JOIN KATEGORI AS K ON K.ID = S.KATEGORI_ID
                    LEFT JOIN STOK_RESIM AS SR ON SR.STOK_ID = S.ID AND SR.VITRIN = 1
                WHERE S.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        $row->RESIM_URL         = fncImgPath($row->RESIM_URL);
        $row->INDIRIMLI_FIYAT   = fncIndirimHesapla($row->FIYAT,20) . '<i class="ri-money-euro-circle-line"></i>'; 
        $row->FIYAT             = FormatSayi::sayi($row->FIYAT) . '<i class="ri-money-euro-circle-line"></i>';

        if ($row->FIRSAT == 1) {
            $html = "<span class='mad-label'>İndirim! <br>-20%</span>";
            $fiyat = "<div class='mad-product-price'>{$row->INDIRIMLI_FIYAT}<span>{$row->FIYAT}</span></div>";
        }else if($row->YENI == 1){
            $html = "<span class='mad-label new'>Yeni!</span>";
            $fiyat = "<div class='mad-product-price'>{$row->FIYAT}</div>";
        }else{
            $html = "";
            $fiyat = "<div class='mad-product-price'>{$row->FIYAT}</div>";
        }

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Ürün Bilgisi.";
            $result["ROW"]       = $row;
            $result["HTML"]      = $html;
            $result["FIYAT"]     = $fiyat;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Ürün Bulunamadı!";
        }

        return $result;
    }

    public function iletisim_kaydet() {
        
        if(strlen(trim($_REQUEST['ad'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ad Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['mail'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Email Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['telefon'])) < 12){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Telefon giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['mesaj'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Mesaj Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO ILETISIM SET    AD          = :AD,
                                            MAIL        = :MAIL,
                                            TELEFON     = :TELEFON,
                                            MESAJ       = :MESAJ
                                            ";
        $data[":AD"]         = trim($_REQUEST['ad']);
        $data[":MAIL"]       = trim($_REQUEST['mail']);
        $data[":TELEFON"]    = $_REQUEST['telefon'];
        $data[":MESAJ"]      = trim($_REQUEST['mesaj']);
        $id = DB::insert($sql, $data);

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Mesajınız Alınmıştır.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function sepete_ekle(){

        $data = array();
        $sql = "SELECT * FROM STOK WHERE ID = :ID";
        $data[":ID"]          = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Ürün bulunamadı!";
            return $result;
        }

        if($_REQUEST['adet'] <= 0){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Adet Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM SEPET WHERE COOKIE_ID = :COOKIE_ID AND STOK_ID = :STOK_ID";
        $data[":COOKIE_ID"]      = $_COOKIE["cookie_id"];
        $data[":STOK_ID"]        = $row->ID;
        $row_ayni = DB::getRow($sql, $data);

        if ($row->FIRSAT == 1) $row->FIYAT = fncIndirimHesaplaDB($row->FIYAT, 20);
       
        if ($row_ayni->ID > 0) {
            $data = array();
            $sql = "UPDATE SEPET SET ADET = :ADET WHERE ID = :ID";
            $data[':ADET']          = $_REQUEST['adet'] + $row_ayni->ADET;
            $data[':ID']            = $row_ayni->ID;
            $update = DB::exec($sql, $data);
        }else{
            $data = array();
            $sql = "INSERT INTO SEPET SET   STOK_ID         = :STOK_ID,
                                            COOKIE_ID       = :COOKIE_ID,
                                            STOK            = :STOK,
                                            FIYAT           = :FIYAT,
                                            ADET            = :ADET
                                            ";
            $data[':STOK_ID']       = $row->ID;
            $data[':COOKIE_ID']     = $_COOKIE["cookie_id"];
            $data[':STOK']          = $row->STOK;
            $data[':FIYAT']         = $row->FIYAT;
            $data[':ADET']          = $_REQUEST['adet'];
            $id = DB::insert($sql, $data);
        }

        $data = array();
        $sql = "SELECT 
                    COUNT(S.ID) AS URUN_SAYISI,
                    SUM(S.FIYAT * S.ADET) AS URUN_FIYAT
                FROM SEPET AS S
                WHERE S.COOKIE_ID = :COOKIE_ID
                ";
        $data[":COOKIE_ID"]      = $_COOKIE["cookie_id"];
        $row_sepet = DB::getRow($sql, $data);

        $row_sepet->URUN_FIYAT = FormatSayi::sayi($row_sepet->URUN_FIYAT) . '<i class="ri-money-euro-circle-line"></i>';

        $data = array();
        $sql = "SELECT 
                    S.*,
                    CONCAT('stok/', ST.ID, '/', YEAR(ST.TARIH), '/', SR.RESIM_ADI) AS RESIM_URL,
                    SR.ALT AS RESIM_ALT,
                    (S.FIYAT * S.ADET) AS TUTAR
                FROM SEPET AS S
                    LEFT JOIN STOK AS ST ON ST.ID = S.STOK_ID
                    LEFT JOIN STOK_RESIM AS SR ON SR.STOK_ID = S.STOK_ID AND SR.VITRIN = 1
                WHERE S.COOKIE_ID = :COOKIE_ID
                ";
        $data[":COOKIE_ID"]      = $_COOKIE["cookie_id"];
        $rows_sepet_detay = DB::get($sql, $data);

        $html = "";
        foreach ($rows_sepet_detay as $key => $row_sepet_detay) {
            $html .= '
            <div class="mad-products hr-type style-2 mb-5">
                <div class="mad-product">
                    <button class="mad-close-item"><i class="material-icons-outlined">close</i></button>
                    <div class="mad-product-image">';
                        if(!is_file(fncImgPathSite($row_sepet_detay->RESIM_URL))){
                            $html .= '<img src=" '.fncImgPath($row_sepet_detay->RESIM_URL).' " alt=" '.$row_sepet_detay->RESIM_ALT.' " width="100" height="100">';
                        }
                    $html .= '
                    </div>
                        <div class="mad-col">
                            <div class="mad-product-description">
                                <h6 class="mad-product-title"><a href="/front/urun.php?id='.$row_sepet_detay->STOK_ID.'" class="mad-link">'.$row_sepet_detay->STOK.'</a></h6>
                            </div>
                            <div class="mad-product-info">
                                <div class="mad-info-item">
                                    <div class="mad-product-price">'.$row_sepet_detay->ADET.' x '.FormatSayi::sayi($row_sepet_detay->FIYAT).'<i class="ri-money-euro-circle-line"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';
        }

        $alt_toplam = '<div class="subtotal">Alt Toplam: '.$row_sepet->URUN_FIYAT.'</div>';
        
        if($id > 0){
            $result["HATA"]             = FALSE;
            $result["ACIKLAMA"]         = "Ürün Sepete Eklendi.";
            $result["SEPET"]            = $row_sepet;
            $result["SEPET_DETAY"]      = $html;
            $result["ALT_TOPLAM"]       = $alt_toplam;
        }else if($update > 0){
            $result["HATA"]             = FALSE;
            $result["ACIKLAMA"]         = "Sepet Güncellendi!";
            $result["SEPET"]            = $row_sepet;
            $result["SEPET_DETAY"]      = $html;
            $result["ALT_TOPLAM"]       = $alt_toplam;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bir Hata Oluştu. Tekrar Deneyiniz!";
        }
        
        return $result;  
    }

    function odeme_yap() {
        global $cPaytr;

        if(strlen(trim($_REQUEST['ad'])) <= 1){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "İsim Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['soyad'])) <= 1){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Soyisim Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['cari'])) <= 5){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Firma Giriniz!";
            return $result;
        }

        if($_REQUEST['ulke_id'] <= 0){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Ülke Seçiniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['adres'])) <= 5){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Adres Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['sehir'])) <= 2){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Şehir Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['posta_kodu'])) <= 3){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Posta Kodu Giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['telefon'])) < 12){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Telefon giriniz!";
            return $result;
        }

        if(strlen(trim($_REQUEST['mail'])) <= 4){
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "EMail giriniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT SUM(FIYAT * ADET) AS TUTAR FROM SEPET WHERE COOKIE_ID = :COOKIE_ID";
        $data[':COOKIE_ID']     = $_COOKIE["cookie_id"];
        $row_sepet = DB::getRow($sql, $data);

        $data = array();
        $sql = "INSERT INTO SIPARIS SET     COOKIE_ID       = :COOKIE_ID,
                                            TUTAR           = :TUTAR,
                                            AD              = :AD,
                                            SOYAD           = :SOYAD,
                                            CARI            = :CARI,
                                            ULKE_ID         = :ULKE_ID,
                                            ADRES           = :ADRES,
                                            SEHIR           = :SEHIR,
                                            POSTA_KODU      = :POSTA_KODU,
                                            TELEFON         = :TELEFON,
                                            MAIL            = :MAIL,
                                            ACIKLAMA        = :ACIKLAMA
                                            "; 
        $data[':COOKIE_ID']     = $_COOKIE["cookie_id"];
        $data[':TUTAR']         = $row_sepet->TUTAR;
        $data[':AD']            = trim($_REQUEST['ad']);
        $data[':SOYAD']         = trim($_REQUEST['soyad']);
        $data[':CARI']          = trim($_REQUEST['cari']);
        $data[':ULKE_ID']       = $_REQUEST['ulke_id'];
        $data[':ADRES']         = trim($_REQUEST['adres']);
        $data[':SEHIR']         = trim($_REQUEST['sehir']);
        $data[':POSTA_KODU']    = trim($_REQUEST['posta_kodu']);
        $data[':TELEFON']       = trim($_REQUEST['telefon']);
        $data[':MAIL']          = trim($_REQUEST['mail']);
        $data[':ACIKLAMA']      = trim($_REQUEST['not']);
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]   = $id;
        $row_odeme = DB::getRow($sql, $data);

        $data = array();
        $sql = "SELECT 
                    S.STOK,
                    S.FIYAT,
                    S.ADET
                FROM SEPET AS S
                WHERE COOKIE_ID = :COOKIE_ID
                ";
        $data[':COOKIE_ID']     = $_COOKIE["cookie_id"];
        $rows_sepet = DB::get($sql, $data);

        $response = $cPaytr->fncPaytrOdeme($row_odeme, FormatSepet::formatSepet($rows_sepet));
        $URL = "/front/paytr.php?token={$response->token}";

        if($response->status == "success"){
            $result["HATA"]             = FALSE;
            $result["ACIKLAMA"]         = "Ödemeye Yönlendiriliyorsunuz.";
            $result["URL"]              = $URL;
        }else{
            $result["HATA"]             = TRUE;
            $result["ACIKLAMA"]         = "Bir Hata Oluştu. Tekrar Deneyiniz!";
        }
        
        return $result;  
    }
}