<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class SiparisController {

    private $select;
    private $site;

    function __construct($select = "", $row_site = "") {
        global $row_site;
        $this->select       = $select;
        $this->site         = $row_site;
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

    public function SiparisSurecler() {
        $data = array();
        $sql = "SELECT
                    SS.ID,
                    SS.SIPARIS_SUREC AS AD
                FROM SIPARIS_SUREC AS SS
                WHERE SS.DURUM = 1
                ORDER BY 1";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function getSiparisler($request = array()) {

        $data = array();
        $sql = "SELECT 
                    S.*,
                    TIMESTAMPDIFF(MINUTE, S.SIPARIS_TARIH, S.HAZIRLANMA_TARIH) AS HAZIRLANMA_SURESI,
                    SS.SIPARIS_SUREC
                FROM SIPARIS AS S
                    LEFT JOIN SIPARIS_SUREC AS SS ON SS.ID = S.SIPARIS_SUREC_ID
                WHERE 1
                ";

        if($request['siparis_no'] > 0){
            $sql .= " AND S.SIPARIS_NO LIKE :SIPARIS_NO";
            $data[':SIPARIS_NO'] = "%" . $request['siparis_no'] . "%";
        }

        if($request['musteri']){
            $sql .= " AND S.MUSTERI LIKE :MUSTERI";
            $data[':MUSTERI'] = "%" . trim($request['musteri']) . "%";
        }

        if(count2($request['siparis_surec_ids']) > 0){
            $sql .= " AND FIND_IN_SET(S.SIPARIS_SUREC_ID, :SIPARIS_SUREC_IDS)";
            $data[':SIPARIS_SUREC_IDS'] = FormatYazi::array2str($request['siparis_surec_ids']);
        }

        if(!empty($request['siparis_tarih']) AND $request['siparis_tarih_var'] > 0){
            $sql .= " AND DATE(S.SIPARIS_TARIH) >= :TARIH1 AND DATE(S.SIPARIS_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['siparis_tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
        }

        $sql .= " ORDER BY S.SIPARIS_TARIH DESC";
        
        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 10);
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

    public function getSiparis($request = array()) {

        $data = array();
        $sql = "SELECT 
                    S.*
                FROM SIPARIS AS S
                WHERE S.ID = :ID
                ";

        $data[':ID'] = $request['id'];
        $row = DB::getRow($sql, $data);
        return $row;
    }

    public function getSiparisler2($request = array()) {

        $data = array();
        $sql = "SELECT 
                    S.*
                FROM SIPARIS AS S
                WHERE 1
                ";

        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function getSiparisDetay($request = array()) {

        $data = array();
        $sql = "SELECT 
                    SD.*,
                    CONCAT('urun/', U.ID, '/', YEAR(U.TARIH), '/', UR.RESIM_ADI) AS RESIM_URL
                FROM SIPARIS_DETAY AS SD
                    LEFT JOIN SIPARIS AS S ON S.ID = SD.SIPARIS_ID
                    LEFT JOIN URUN AS U ON U.TRENDYOL_URUN_ID = SD.TRENDYOL_URUN_ID
                    LEFT JOIN URUN_RESIM AS UR ON UR.URUN_ID = U.ID AND UR.VITRIN = 1
                WHERE S.ID = :ID
                ";

        $data[':ID'] = $request['id'];
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function getUyeSiparisSayisi($request = array()) {

        $data = array();
        $sql = "SELECT 
                    S.MUSTERI_ID AS ID,
                    COUNT(S.ID) AS TOPLAM
                FROM SIPARIS AS S
                WHERE S.MUSTERI_ID > 0
                GROUP BY S.MUSTERI_ID
                ";

        $rows = DB::get($sql, $data);
        $rows = arrayIndex($rows);
        return $rows;
    }

    public function siparis_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        if($row->SUREC_ID != 1){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sadece 'Sipariş Alındı' Süreçinde Sipariş Silinebilir!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]        = $row->ID;
        $delete = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "SIPARIS", "SIPARIS_DETAY");

        if($delete > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Silindi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function siparis_hazirlaniyor() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SIPARIS SET  SUREC_ID  = :SUREC_ID,
                                    GTARIH    = NOW()
                                WHERE ID = :ID
                                "; 
        $data[':SUREC_ID']     = 2;
        $data[':ID']           = $row->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "INSERT INTO SIPARIS_SUREC_LOG SET   SIPARIS_ID      = :SIPARIS_ID,
                                                    YENI_SUREC_ID   = :YENI_SUREC_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                    ACIKLAMA        = :ACIKLAMA
                                                    "; 
        $data[':SIPARIS_ID']     = $row->ID;
        $data[':YENI_SUREC_ID']  = 2;
        $data[':KAYIT_YAPAN_ID'] = $_SESSION['kullanici_id'];
        $data[':ACIKLAMA']       = "Sipariş Hazırlanıyor.";
        DB::insert($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Sipariş Hazırlanıyor.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getSiparisSurecLog($request = array()) {

        $data = array();
        $sql = "SELECT 
                    SL.*
                FROM SIPARIS_SUREC_LOG AS SL
                    LEFT JOIN SIPARIS AS S ON S.ID = SL.SIPARIS_ID
                WHERE SL.SIPARIS_ID = :SIPARIS_ID
                ";

        $data[':SIPARIS_ID'] = $request['id'];
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function siparis_kargoya_verildi() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SIPARIS SET  SUREC_ID  = :SUREC_ID,
                                    GTARIH    = NOW()
                                WHERE ID = :ID
                                "; 
        $data[':SUREC_ID']     = 3;
        $data[':ID']           = $row->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "INSERT INTO SIPARIS_SUREC_LOG SET   SIPARIS_ID      = :SIPARIS_ID,
                                                    YENI_SUREC_ID   = :YENI_SUREC_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                    ACIKLAMA        = :ACIKLAMA
                                                    "; 
        $data[':SIPARIS_ID']     = $row->ID;
        $data[':YENI_SUREC_ID']  = 3;
        $data[':KAYIT_YAPAN_ID'] = $_SESSION['kullanici_id'];
        $data[':ACIKLAMA']       = "Sipariş Kargoya Verildi.";
        DB::insert($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Sipariş Kargoya Verildi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function siparis_tamamlandi() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SIPARIS SET  SUREC_ID  = :SUREC_ID,
                                    GTARIH    = NOW()
                                WHERE ID = :ID
                                "; 
        $data[':SUREC_ID']     = 10;
        $data[':ID']           = $row->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "INSERT INTO SIPARIS_SUREC_LOG SET   SIPARIS_ID      = :SIPARIS_ID,
                                                    YENI_SUREC_ID   = :YENI_SUREC_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                    ACIKLAMA        = :ACIKLAMA
                                                    "; 
        $data[':SIPARIS_ID']     = $row->ID;
        $data[':YENI_SUREC_ID']  = 10;
        $data[':KAYIT_YAPAN_ID'] = $_SESSION['kullanici_id'];
        $data[':ACIKLAMA']       = "Sipariş Tamamlandı.";
        DB::insert($sql, $data);

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Sipariş Tamamlandı.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function siparis_iptal() {
        global $cMail;

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        if($row->SUREC_ID == 11){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş İptal Edilmiş!";
            return $result;
        }

        if(!in_array($row->SUREC_ID,array(1,2))){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sadece 'Siparis Alındı' ve 'Sipariş Hazırlanıyor' süreçinde iptal edilebilir!";
            return $result;
        }

        if($row->ODEME == 1){
            $response = fncPaytrIade($row->ID, floatval($row->ARA_TOPLAM));

            if ($response["HATA"]) {
                return $response;
            }
        }

        $data = array();
        $sql = "UPDATE SIPARIS SET  SUREC_ID  = :SUREC_ID,
                                    GTARIH    = NOW()
                                WHERE ID = :ID
                                "; 
        $data[':SUREC_ID']     = 11;
        $data[':ID']           = $row->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "INSERT INTO SIPARIS_SUREC_LOG SET   SIPARIS_ID      = :SIPARIS_ID,
                                                    YENI_SUREC_ID   = :YENI_SUREC_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                    ACIKLAMA        = :ACIKLAMA
                                                    "; 
        $data[':SIPARIS_ID']     = $row->ID;
        $data[':YENI_SUREC_ID']  = 11;
        $data[':KAYIT_YAPAN_ID'] = $_SESSION['kullanici_id'];
        $data[':ACIKLAMA']       = "Sipariş İptal Edildi.";
        DB::insert($sql, $data);

        $icerik = '
        <div style="background-color:#f3f3f3; padding:30px; font-family:Arial, sans-serif;">
            <div style="max-width:600px; margin:auto; background-color:#ffffff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.07); overflow:hidden;">
                
                <div style="background-color:#b91c1c; color:#ffffff; padding:20px; text-align:center;">
                    <h2 style="margin:0; font-size:20px;">❌ Sipariş İptal Edildi</h2>
                </div>

                <div style="padding:20px;">
                    <p style="font-size:15px; color:#333;">
                        Aşağıda bilgileri yer alan sipariş <strong>iptal edilmiştir</strong>.
                    </p>

                    <table style="width:100%; border-collapse:collapse; font-size:15px; margin-top:15px;">
                        <tr style="background-color:#f9f9f9;">
                            <th style="text-align:left; padding:10px; color:#333;">Sipariş No</th>
                            <td style="padding:10px;">#'.$row->ID.'</td>
                        </tr>
                        <tr style="background-color:#f0f0f0;">
                            <th style="text-align:left; padding:10px; color:#333;">Müşteri</th>
                            <td style="padding:10px;">'.htmlspecialchars($row->AD).' '.htmlspecialchars($row->SOYAD).' </td>
                        </tr>
                        <tr style="background-color:#f9f9f9;">
                            <th style="text-align:left; padding:10px; color:#333;">E-Posta</th>
                            <td style="padding:10px;">'.htmlspecialchars($row->MAIL).'</td>
                        </tr>
                        <tr style="background-color:#f0f0f0;">
                            <th style="text-align:left; padding:10px; color:#333;">Telefon</th>
                            <td style="padding:10px;">'.htmlspecialchars($row->TELEFON).'</td>
                        </tr>
                        <tr style="background-color:#f9f9f9;">
                            <th style="text-align:left; padding:10px; color:#333;">İptal Tarihi</th>
                            <td style="padding:10px;">'.date("d.m.Y H:i").'</td>
                        </tr>
                    </table>

                    <p style="margin-top:20px; font-size:14px; color:#555;">
                        Eğer bu işlemle ilgili bir hata olduğunu düşünüyorsanız bizimle iletişime geçebilirsiniz.
                    </p>
                </div>

                <div style="background-color:#e7e7e7; color:#666; text-align:center; padding:15px; font-size:13px;">
                    Bu e-posta sistem tarafından otomatik gönderilmiştir.<br>
                    Güneş Optik Ceyhan © 2025
                </div>
            </div>
        </div>';

        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Sipariş İptal Edildi.";
            $cMail->Gonder($row->MAIL, "Siparişiniz İptal Edildi", $icerik);
            $cMail->Gonder("info@sharksbites.com;info@gunesoptikceyhan.com", "Sipariş İptal Bildirimi (#".$row->ID.")", $icerik);
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function siparis_iade() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        if($row->ODEME == 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Ödeme Alınamadı İade Edilemez!";
            return $result;
        }

        $response = fncPaytrIade($row->ID, floatval($row->ARA_TOPLAM));

        if ($response["HATA"]) {
            return $response;
        }

        $data = array();
        $sql = "UPDATE SIPARIS SET  SUREC_ID  = :SUREC_ID,
                                    GTARIH    = NOW()
                                WHERE ID = :ID
                                "; 
        $data[':SUREC_ID']     = 6;
        $data[':ID']           = $row->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "INSERT INTO SIPARIS_SUREC_LOG SET   SIPARIS_ID      = :SIPARIS_ID,
                                                    YENI_SUREC_ID   = :YENI_SUREC_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                    ACIKLAMA        = :ACIKLAMA
                                                    "; 
        $data[':SIPARIS_ID']     = $row->ID;
        $data[':YENI_SUREC_ID']  = 6;
        $data[':KAYIT_YAPAN_ID'] = $_SESSION['kullanici_id'];
        $data[':ACIKLAMA']       = "Siparişin İadesi onaylandı.";
        DB::insert($sql, $data);


        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Sipariş İade Oldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function siparis_iade_red() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SIPARIS SET  SUREC_ID  = :SUREC_ID,
                                    GTARIH    = NOW()
                                WHERE ID = :ID
                                "; 
        $data[':SUREC_ID']     = 7;
        $data[':ID']           = $row->ID;
        $update = DB::exec($sql, $data);

        $data = array();
        $sql = "INSERT INTO SIPARIS_SUREC_LOG SET   SIPARIS_ID      = :SIPARIS_ID,
                                                    YENI_SUREC_ID   = :YENI_SUREC_ID,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                    ACIKLAMA        = :ACIKLAMA
                                                    "; 
        $data[':SIPARIS_ID']     = $row->ID;
        $data[':YENI_SUREC_ID']  = 7;
        $data[':KAYIT_YAPAN_ID'] = $_SESSION['kullanici_id'];
        $data[':ACIKLAMA']       = "Siparişin İadesi satıcı tarafından reddedilmiştir.";
        DB::insert($sql, $data);


        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Sipariş İade Oldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function getSiparisSurecSayilari($request = array()){

        $data = array();
        $sql = "SELECT
                    S.SUREC_ID AS ID,
                    COUNT(S.ID) AS SAY
                FROM SIPARIS AS S
                    LEFT JOIN SUREC AS SS ON SS.ID = S.SUREC_ID
                WHERE S.ODEME = 1 AND S.SUREC_ID > 0
                ";

        $sql .= " GROUP BY S.SUREC_ID";
        $rows = DB::get($sql, $data);
        $rows = arrayIndex($rows);
        return $rows;
    }

    public function kargo_kaydet() {

        $data = array();
        $sql = "SELECT * FROM SIPARIS WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE SIPARIS SET  KARGO_FIRMA         = :KARGO_FIRMA,
                                    KARGO_TAKIP_NO      = :KARGO_TAKIP_NO,
                                    IADE_KARGO_TAKIP_NO = :IADE_KARGO_TAKIP_NO,
                                    GTARIH              = NOW()
                                WHERE ID = :ID
                                ";
        $data[":KARGO_FIRMA"]           = trim($_REQUEST['kargo_firma']);
        $data[":KARGO_TAKIP_NO"]        = trim($_REQUEST['kargo_takip_no']);
        $data[":IADE_KARGO_TAKIP_NO"]   = trim($_REQUEST['iade_kargo_takip_no']);
        $data[":ID"]                    = $row->ID;
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

    function kargo_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    S.*
                FROM SIPARIS AS S
                WHERE S.ID =:ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Sipariş Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Sipariş Bulunamadı!";
        }

        return $result;
    }

    public function getSiparisDetayEkstra($request = array()) {

        $data = array();
        $sql = "SELECT 
                    SE.*
                FROM SIPARIS_EKSTRA AS SE
                WHERE SE.SIPARIS_DETAY_ID = :SIPARIS_DETAY_ID
                ";

        $data[':SIPARIS_DETAY_ID'] = $request['siparis_detay_id'];
        $row = DB::get($sql, $data);
        return $row;
    }

    public function getSiparisDetayCikarilan($request = array()) {

        $data = array();
        $sql = "SELECT 
                    SC.*
                FROM SIPARIS_CIKARILAN AS SC
                WHERE SC.SIPARIS_DETAY_ID = :SIPARIS_DETAY_ID
                ";

        $data[':SIPARIS_DETAY_ID'] = $request['siparis_detay_id'];
        $row = DB::get($sql, $data);
        return $row;
    }

    public function getSiparisDetaylar($request = array()) {

        $data = array();
        $sql = "SELECT 
                    SD.*,
                    S.SIPARIS_TARIH,
                    DATE(S.SIPARIS_TARIH) AS SIPARIS_TARIH_DATE,
                    S.MUSTERI,
                    S.TOKEN,
                    U.ID AS URUN_ID,
                    CONCAT('urun/', U.ID, '/', YEAR(U.TARIH), '/', UR.RESIM_ADI) AS RESIM_URL
                FROM SIPARIS_DETAY AS SD
                    LEFT JOIN SIPARIS AS S ON S.ID = SD.SIPARIS_ID
                    LEFT JOIN URUN AS U ON U.TRENDYOL_URUN_ID = SD.TRENDYOL_URUN_ID
                    LEFT JOIN URUN_RESIM AS UR ON UR.URUN_ID = U.ID AND UR.VITRIN = 1
                WHERE 1
                ";

        if($request['siparis_no'] > 0){
            $sql .= " AND S.SIPARIS_NO LIKE :SIPARIS_NO";
            $data[':SIPARIS_NO'] = "%" . $request['siparis_no'] . "%";
        }

        if($request['musteri']){
            $sql .= " AND S.MUSTERI LIKE :MUSTERI";
            $data[':MUSTERI'] = "%" . trim($request['musteri']) . "%";
        }

        if(count2($request['kategori_ids']) > 0){
            $sql .= " AND FIND_IN_SET(U.KATEGORI_ID, :KATEGORI_IDS)";
            $data[':KATEGORI_IDS'] = FormatYazi::array2str($request['kategori_ids']);
        }

        if(!empty($request['siparis_tarih']) AND $request['siparis_tarih_var'] > 0){
            $sql .= " AND DATE(S.SIPARIS_TARIH) >= :TARIH1 AND DATE(S.SIPARIS_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['siparis_tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
        }

        $sql .= " ORDER BY S.SIPARIS_TARIH DESC";
        
        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 10);
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

    public function getSaatlikSiparisSayisi($request = array()){

        $data = array();
        $sql = "SELECT
                    HOUR(S.SIPARIS_TARIH) AS SAAT,
                    COUNT(S.ID) AS SAY
                FROM SIPARIS AS S
                WHERE 1
                ";

        if(!empty($request['tarih'])){
            $tarih = explode(',',$request['tarih']);
            $sql .= " AND DATE(S.SIPARIS_TARIH) <= :TARIH1 AND DATE(S.SIPARIS_TARIH) >= :TARIH2";
            $data[":TARIH1"] = $tarih[0];
            $data[":TARIH2"] = $tarih[1];
        }

        $sql .= " GROUP BY HOUR(S.SIPARIS_TARIH)";
        $rows = DB::get($sql, $data);
        return $rows;
    }
}