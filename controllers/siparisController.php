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
                    LEFT JOIN URUN AS U ON (U.TRENDYOL_URUN_ID = SD.TRENDYOL_URUN_ID AND SD.TRENDYOL_URUN_ID IS NOT NULL AND SD.TRENDYOL_URUN_ID <> '') OR U.ID = SD.URUN_ID
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

        if($row->SIPARIS_SUREC_ID != 1){
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
        $sql = "UPDATE SIPARIS SET  SIPARIS_SUREC_ID  = :SIPARIS_SUREC_ID
                                WHERE ID = :ID
                                "; 
        $data[':SIPARIS_SUREC_ID']     = 2;
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
        $sql = "UPDATE SIPARIS SET  SIPARIS_SUREC_ID  = :SIPARIS_SUREC_ID
                                WHERE ID = :ID
                                "; 
        $data[':SIPARIS_SUREC_ID']     = 3;
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
        $sql = "UPDATE SIPARIS SET  SIPARIS_SUREC_ID  = :SIPARIS_SUREC_ID
                                WHERE ID = :ID
                                "; 
        $data[':SIPARIS_SUREC_ID']     = 10;
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
            // Stock reduction logic
            // 1. Deduct recipe materials (standard recipe)
            $order_details = DB::get("SELECT ID, URUN_ID, ADET FROM SIPARIS_DETAY WHERE SIPARIS_ID = :ID", [':ID' => $row->ID]);
            if (is_array($order_details)) {
                foreach ($order_details as $det) {
                    $recipe = DB::get("SELECT MALZEME_ID, MIKTAR FROM URUN_RECETE WHERE URUN_ID = :URUN_ID", [':URUN_ID' => $det->URUN_ID]);
                    if (is_array($recipe)) {
                        foreach ($recipe as $rec) {
                            $deduct_qty = floatval($rec->MIKTAR) * intval($det->ADET);
                            DB::exec("UPDATE MALZEME SET STOK = STOK - :QTY WHERE ID = :MID", [
                                ':QTY' => $deduct_qty,
                                ':MID' => $rec->MALZEME_ID
                            ]);
                        }
                    }
                    
                    // 2. Deduct extra materials
                    $extras = DB::get("SELECT MALZEME_ID FROM SIPARIS_EKSTRA WHERE SIPARIS_DETAY_ID = :DET_ID", [':DET_ID' => $det->ID]);
                    if (is_array($extras)) {
                        foreach ($extras as $ex) {
                            $deduct_qty = 1 * intval($det->ADET);
                            DB::exec("UPDATE MALZEME SET STOK = STOK - :QTY WHERE ID = :MID", [
                                ':QTY' => $deduct_qty,
                                ':MID' => $ex->MALZEME_ID
                            ]);
                        }
                    }
                }
            }

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

        if($row->SIPARIS_SUREC_ID == 11){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sipariş İptal Edilmiş!";
            return $result;
        }

        if(!in_array($row->SIPARIS_SUREC_ID,array(1,2))){
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
        $sql = "UPDATE SIPARIS SET  SIPARIS_SUREC_ID  = :SIPARIS_SUREC_ID
                                WHERE ID = :ID
                                "; 
        $data[':SIPARIS_SUREC_ID']     = 11;
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
        $sql = "UPDATE SIPARIS SET  SIPARIS_SUREC_ID  = :SIPARIS_SUREC_ID
                                WHERE ID = :ID
                                "; 
        $data[':SIPARIS_SUREC_ID']     = 6;
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
        $sql = "UPDATE SIPARIS SET  SIPARIS_SUREC_ID  = :SIPARIS_SUREC_ID
                                WHERE ID = :ID
                                "; 
        $data[':SIPARIS_SUREC_ID']     = 7;
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
                    S.SIPARIS_SUREC_ID AS ID,
                    COUNT(S.ID) AS SAY
                FROM SIPARIS AS S
                    LEFT JOIN SIPARIS_SUREC AS SS ON SS.ID = S.SIPARIS_SUREC_ID
                WHERE S.ODEME = 1 AND S.SIPARIS_SUREC_ID > 0
                ";

        $sql .= " GROUP BY S.SIPARIS_SUREC_ID";
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
                                    IADE_KARGO_TAKIP_NO = :IADE_KARGO_TAKIP_NO
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

    public function siparis_ekle() {
        if (!in_array($_SESSION['yetki_id'], array(1, 2, 3))) {
            return [
                "HATA" => true,
                "ACIKLAMA" => "Sipariş eklemek/düzenlemek için yetkiniz yok!"
            ];
        }

        // Support both JSON cart_data and form arrays
        if (isset($_POST['cart_data']) && !empty($_POST['cart_data'])) {
            $cart = json_decode($_POST['cart_data'], true);
            if (!is_array($cart) || empty($cart)) {
                return [
                    "HATA" => true,
                    "ACIKLAMA" => "Lütfen en az bir ürün ekleyin."
                ];
            }
            $urun_ids = [];
            $quantities = [];
            $extras_by_prod_idx = [];
            foreach ($cart as $idx => $item) {
                $urun_ids[$idx] = intval($item['id']);
                $quantities[$idx] = intval($item['quantity']);
                $extras_by_prod_idx[$idx] = isset($item['extras']) ? $item['extras'] : [];
            }
        } else {
            if (empty($_POST['urun_id']) || !is_array($_POST['urun_id'])) {
                return [
                    "HATA" => true,
                    "ACIKLAMA" => "Lütfen en az bir ürün ekleyin."
                ];
            }
            $urun_ids = array_map('intval', $_POST['urun_id']);
            $quantities = isset($_POST['adet']) ? array_map('intval', $_POST['adet']) : [];
            $extras_by_prod_idx = [];
        }

        $subtotal = 0;
        $extras_total = 0;
        $items_to_insert = array();

        if (!empty($urun_ids)) {
            $placeholders = implode(',', array_fill(0, count($urun_ids), '?'));
            $products = DB::get("SELECT ID, URUN, TRENDYOL_URUN_ID, FIYAT FROM URUN WHERE ID IN ($placeholders)", $urun_ids);
            $products_index = array();
            foreach ($products as $p) {
                $products_index[$p->ID] = $p;
            }

            foreach ($urun_ids as $idx => $prod_id) {
                $qty = isset($quantities[$idx]) ? intval($quantities[$idx]) : 0;
                if ($qty < 1) {
                    continue;
                }
                if (!isset($products_index[$prod_id])) {
                    continue;
                }
                
                $p = $products_index[$prod_id];
                $unit_price = floatval($p->FIYAT);
                $total_price = $unit_price * $qty;
                $subtotal += $total_price;

                $item_extras = [];
                $item_extras_total = 0;
                if (isset($extras_by_prod_idx[$idx]) && is_array($extras_by_prod_idx[$idx])) {
                    $extra_m_ids = array_map('intval', array_column($extras_by_prod_idx[$idx], 'id'));
                    if (!empty($extra_m_ids)) {
                        $ex_placeholders = implode(',', array_fill(0, count($extra_m_ids), '?'));
                        $extra_materials = DB::get("SELECT ID, MALZEME, EKSTRA_FIYAT FROM MALZEME WHERE ID IN ($ex_placeholders)", $extra_m_ids);
                        foreach ($extra_materials as $em) {
                            $ex_unit_price = floatval($em->EKSTRA_FIYAT);
                            $ex_total_price = $ex_unit_price * $qty;
                            $extras_total += $ex_total_price;
                            $item_extras_total += $ex_total_price;
                            $item_extras[] = [
                                'id' => $em->ID,
                                'name' => $em->MALZEME,
                                'price' => $ex_unit_price
                            ];
                        }
                    }
                }

                $items_to_insert[] = [
                    'urun_id' => $p->ID,
                    'trendyol_urun_id' => $p->TRENDYOL_URUN_ID,
                    'urun_adi' => $p->URUN,
                    'fiyat' => $unit_price,
                    'adet' => $qty,
                    'tutar' => $total_price + $item_extras_total,
                    'extras' => $item_extras
                ];
            }
        }

        if (empty($items_to_insert)) {
            return [
                "HATA" => true,
                "ACIKLAMA" => "Geçerli ürün ve adet bulunamadı."
            ];
        }

        $indirim_tutar = isset($_POST['indirim_tutar']) ? floatval($_POST['indirim_tutar']) : 0;
        if ($indirim_tutar < 0) {
            $indirim_tutar = 0;
        }
        $teslimat_ucreti = isset($_POST['teslimat_ucreti']) ? floatval($_POST['teslimat_ucreti']) : 0;
        if ($teslimat_ucreti < 0) {
            $teslimat_ucreti = 0;
        }

        $grand_total = ($subtotal + $extras_total) - $indirim_tutar + $teslimat_ucreti;
        if ($grand_total < 0) {
            $grand_total = 0;
        }

        $kaynak = !empty($_POST['kaynak']) ? trim($_POST['kaynak']) : 'Mağaza';
        $musteri = !empty($_POST['musteri']) ? trim($_POST['musteri']) : 'Mağaza Müşterisi';
        $telefon = !empty($_POST['telefon']) ? trim($_POST['telefon']) : '';
        $odeme = !empty($_POST['odeme']) ? trim($_POST['odeme']) : 'Nakit';
        $siparis_not = !empty($_POST['siparis_not']) ? trim($_POST['siparis_not']) : '';
        $siparis_tipi = !empty($_POST['siparis_tipi']) ? trim($_POST['siparis_tipi']) : 'Gel Al';
        $hazirlanma_suresi = isset($_POST['hazirlanma_suresi']) && $_POST['hazirlanma_suresi'] !== '' ? intval($_POST['hazirlanma_suresi']) : null;
        
        $komisyon_orani = isset($_POST['komisyon_orani']) && $_POST['komisyon_orani'] !== '' ? floatval($_POST['komisyon_orani']) : null;
        if (is_null($komisyon_orani)) {
            if ($kaynak == 'Trendyol Go') $komisyon_orani = 38.00;
            else if ($kaynak == 'Yemeksepeti') $komisyon_orani = 35.00;
            else if ($kaynak == 'Getir') $komisyon_orani = 30.00;
            else $komisyon_orani = 0.00;
        }

        $is_edit = isset($_POST['id']) && intval($_POST['id']) > 0;
        $order_id = $is_edit ? intval($_POST['id']) : 0;
        $kayit_yapan_id = intval($_SESSION['kullanici_id']);

        if ($is_edit) {
            $old_order = DB::getRow("SELECT * FROM SIPARIS WHERE ID = :ID", [':ID' => $order_id]);
            if (is_null($old_order->ID)) {
                return [
                    "HATA" => true,
                    "ACIKLAMA" => "Düzenlenecek sipariş bulunamadı."
                ];
            }
            $siparis_no = $old_order->SIPARIS_NO;
            $token = $old_order->TOKEN;

            // Load old details and extras to compare changes
            $old_details = DB::get("SELECT * FROM SIPARIS_DETAY WHERE SIPARIS_ID = :ID", [':ID' => $order_id]);
            $old_items = array();
            foreach ($old_details as $det) {
                $extras_rows = DB::get("SELECT * FROM SIPARIS_EKSTRA WHERE SIPARIS_DETAY_ID = :DET_ID", [':DET_ID' => $det->ID]);
                $extras = array();
                foreach ($extras_rows as $ex) {
                    $extras[$ex->MALZEME_ID] = $ex->MALZEME_AD;
                }
                $old_items[$det->URUN_ID] = [
                    'adet' => $det->ADET,
                    'urun' => $det->URUN,
                    'extras' => $extras
                ];
            }

            $logChanges = function($alan, $eski, $yeni, $aciklama = '') use ($order_id, $siparis_no) {
                self::logEkle('Sipariş', $order_id, $siparis_no, 'Güncelleme', $alan, $eski, $yeni, $aciklama);
            };

            // Compare headers
            if ($old_order->KAYNAK != $kaynak) {
                $logChanges('Kaynak', $old_order->KAYNAK, $kaynak, "Sipariş kaynağı değiştirildi.");
            }
            if ($old_order->MUSTERI != $musteri) {
                $logChanges('Müşteri', $old_order->MUSTERI, $musteri, "Müşteri adı soyadı değiştirildi.");
            }
            if ($old_order->TELEFON != $telefon) {
                $logChanges('Telefon', $old_order->TELEFON, $telefon, "Telefon numarası değiştirildi.");
            }
            if ($old_order->ODEME != $odeme) {
                $logChanges('Ödeme', $old_order->ODEME, $odeme, "Ödeme yöntemi değiştirildi.");
            }
            if ($old_order->SIPARIS_NOT != $siparis_not) {
                $logChanges('Sipariş Notu', $old_order->SIPARIS_NOT, $siparis_not, "Sipariş notu değiştirildi.");
            }
            if ($old_order->SIPARIS_TIPI != $siparis_tipi) {
                $logChanges('Sipariş Tipi', $old_order->SIPARIS_TIPI, $siparis_tipi, "Sipariş tipi değiştirildi.");
            }
            if (floatval($old_order->INDIRIM_TUTAR) != $indirim_tutar) {
                $logChanges('İndirim', floatval($old_order->INDIRIM_TUTAR) . ' TL', $indirim_tutar . ' TL', "İndirim tutarı değiştirildi.");
            }
            if (floatval($old_order->TESLIMAT_UCRETI) != $teslimat_ucreti) {
                $logChanges('Teslimat Ücreti', floatval($old_order->TESLIMAT_UCRETI) . ' TL', $teslimat_ucreti . ' TL', "Teslimat ücreti değiştirildi.");
            }
            if (intval($old_order->HAZIRLANMA_SURESI) != intval($hazirlanma_suresi)) {
                $logChanges('Hazırlanma Süresi', intval($old_order->HAZIRLANMA_SURESI) . ' Dk', intval($hazirlanma_suresi) . ' Dk', "Hazırlanma süresi değiştirildi.");
            }
            if (floatval($old_order->KOMISYON_ORANI) != $komisyon_orani) {
                $logChanges('Komisyon Oranı', floatval($old_order->KOMISYON_ORANI) . '%', $komisyon_orani . '%', "Komisyon oranı değiştirildi.");
            }

            // Compare items
            $new_items = array();
            foreach ($items_to_insert as $item) {
                $new_items[$item['urun_id']] = [
                    'adet' => $item['adet'],
                    'urun' => $item['urun_adi'],
                    'extras' => $item['extras']
                ];
            }

            foreach ($old_items as $urun_id => $old_item) {
                if (!isset($new_items[$urun_id])) {
                    $logChanges('Ürün Çıkarıldı', $old_item['urun'], '', "{$old_item['urun']} siparişten çıkarıldı.");
                }
            }

            foreach ($new_items as $urun_id => $new_item) {
                if (!isset($old_items[$urun_id])) {
                    $logChanges('Ürün Eklendi', '', $new_item['urun'], "{$new_item['urun']} siparişe eklendi.");
                    foreach ($new_item['extras'] as $ex) {
                        $logChanges('Ekstra Malzeme', '', "+ {$ex['name']} eklendi", "Siparişe {$new_item['urun']} için {$ex['name']} ekstrası eklendi.");
                    }
                } else {
                    $old_item = $old_items[$urun_id];
                    if ($old_item['adet'] != $new_item['adet']) {
                        $logChanges('Ürün Adedi (' . $new_item['urun'] . ')', $old_item['adet'], $new_item['adet'], "{$new_item['urun']} adedi değiştirildi.");
                    }
                    $old_exts = $old_item['extras'];
                    $new_exts = array();
                    foreach ($new_item['extras'] as $ex) {
                        $new_exts[$ex['id']] = $ex['name'];
                    }
                    foreach ($new_exts as $ex_id => $ex_name) {
                        if (!isset($old_exts[$ex_id])) {
                            $logChanges('Ekstra Malzeme', '', "+ $ex_name eklendi", "Siparişe {$new_item['urun']} için $ex_name ekstrası eklendi.");
                        }
                    }
                    foreach ($old_exts as $ex_id => $ex_name) {
                        if (!isset($new_exts[$ex_id])) {
                            $logChanges('Ekstra Malzeme', "+ $ex_name çıkarıldı", '', "Siparişten {$new_item['urun']} için $ex_name ekstrası çıkarıldı.");
                        }
                    }
                }
            }

            $hazirlanma_tarih = $old_order->SIPARIS_TARIH;
            if ($hazirlanma_suresi > 0) {
                $hazirlanma_tarih = date('Y-m-d H:i:s', strtotime($old_order->SIPARIS_TARIH) + ($hazirlanma_suresi * 60));
            }

            $sql_siparis = "UPDATE SIPARIS SET 
                                KAYNAK = :KAYNAK,
                                TUTAR = :TUTAR,
                                TELEFON = :TELEFON,
                                MUSTERI = :MUSTERI,
                                ODEME = :ODEME,
                                SIPARIS_NOT = :SIPARIS_NOT,
                                HAZIRLANMA_TARIH = :HAZIRLANMA_TARIH,
                                INDIRIM = :INDIRIM,
                                INDIRIM_TUTAR = :INDIRIM_TUTAR,
                                SIPARIS_TIPI = :SIPARIS_TIPI,
                                TESLIMAT_UCRETI = :TESLIMAT_UCRETI,
                                HAZIRLANMA_SURESI = :HAZIRLANMA_SURESI,
                                KOMISYON_ORANI = :KOMISYON_ORANI
                            WHERE ID = :ID";
            DB::exec($sql_siparis, [
                ':KAYNAK' => $kaynak,
                ':TUTAR' => $grand_total,
                ':TELEFON' => $telefon,
                ':MUSTERI' => $musteri,
                ':ODEME' => $odeme,
                ':SIPARIS_NOT' => $siparis_not,
                ':HAZIRLANMA_TARIH' => $hazirlanma_tarih,
                ':INDIRIM' => ($indirim_tutar > 0) ? 'İndirim' : '',
                ':INDIRIM_TUTAR' => $indirim_tutar,
                ':SIPARIS_TIPI' => $siparis_tipi,
                ':TESLIMAT_UCRETI' => $teslimat_ucreti,
                ':HAZIRLANMA_SURESI' => $hazirlanma_suresi,
                ':KOMISYON_ORANI' => $komisyon_orani,
                ':ID' => $order_id
            ]);

            // Clear old detail & extras
            DB::exec("DELETE FROM SIPARIS_EKSTRA WHERE SIPARIS_DETAY_ID IN (SELECT ID FROM SIPARIS_DETAY WHERE SIPARIS_ID = :ID)", [':ID' => $order_id]);
            DB::exec("DELETE FROM SIPARIS_DETAY WHERE SIPARIS_ID = :ID", [':ID' => $order_id]);
            $siparis_id = $order_id;

        } else {
            // Create new order
            $siparis_no = 'M' . time();
            $token = md5(microtime() . rand(1, 1000000));
            $now = date('Y-m-d H:i:s');
            
            $hazirlanma_tarih = $now;
            if ($hazirlanma_suresi > 0) {
                $hazirlanma_tarih = date('Y-m-d H:i:s', strtotime($now) + ($hazirlanma_suresi * 60));
            }

            $data_siparis = array(
                ':KAYNAK' => $kaynak,
                ':SIPARIS_NO' => $siparis_no,
                ':TUTAR' => $grand_total,
                ':TELEFON' => $telefon,
                ':MUSTERI' => $musteri,
                ':ODEME' => $odeme,
                ':SIPARIS_NOT' => $siparis_not,
                ':SIPARIS_TARIH' => $now,
                ':HAZIRLANMA_TARIH' => $hazirlanma_tarih,
                ':INDIRIM' => ($indirim_tutar > 0) ? 'İndirim' : '',
                ':INDIRIM_TUTAR' => $indirim_tutar,
                ':SIPARIS_TIPI' => $siparis_tipi,
                ':TESLIMAT_UCRETI' => $teslimat_ucreti,
                ':HAZIRLANMA_SURESI' => $hazirlanma_suresi,
                ':KOMISYON_ORANI' => $komisyon_orani,
                ':KAYIT_YAPAN_ID' => $kayit_yapan_id,
                ':TOKEN' => $token
            );

            $sql_siparis = "INSERT INTO SIPARIS SET 
                                KAYNAK = :KAYNAK,
                                SIPARIS_NO = :SIPARIS_NO,
                                SIPARIS_SUREC_ID = 1,
                                TUTAR = :TUTAR,
                                TELEFON = :TELEFON,
                                MUSTERI = :MUSTERI,
                                ODEME = :ODEME,
                                SIPARIS_NOT = :SIPARIS_NOT,
                                SIPARIS_TARIH = :SIPARIS_TARIH,
                                HAZIRLANMA_TARIH = :HAZIRLANMA_TARIH,
                                INDIRIM = :INDIRIM,
                                INDIRIM_TUTAR = :INDIRIM_TUTAR,
                                SIPARIS_TIPI = :SIPARIS_TIPI,
                                TESLIMAT_UCRETI = :TESLIMAT_UCRETI,
                                HAZIRLANMA_SURESI = :HAZIRLANMA_SURESI,
                                KOMISYON_ORANI = :KOMISYON_ORANI,
                                KAYIT_YAPAN_ID = :KAYIT_YAPAN_ID,
                                TOKEN = :TOKEN";

            $siparis_id = DB::insert($sql_siparis, $data_siparis);
        }

        if ($siparis_id > 0) {
            foreach ($items_to_insert as $item) {
                $data_detay = array(
                    ':SIPARIS_ID' => $siparis_id,
                    ':SIPARIS_NO' => $siparis_no,
                    ':URUN_ID' => $item['urun_id'],
                    ':TRENDYOL_URUN_ID' => $item['trendyol_urun_id'],
                    ':URUN' => $item['urun_adi'],
                    ':FIYAT' => $item['fiyat'],
                    ':ADET' => $item['adet'],
                    ':TUTAR' => $item['tutar']
                );

                $sql_detay = "INSERT INTO SIPARIS_DETAY SET 
                                SIPARIS_ID = :SIPARIS_ID,
                                SIPARIS_NO = :SIPARIS_NO,
                                URUN_ID = :URUN_ID,
                                TRENDYOL_URUN_ID = :TRENDYOL_URUN_ID,
                                URUN = :URUN,
                                FIYAT = :FIYAT,
                                ADET = :ADET,
                                TUTAR = :TUTAR";
                $detay_id = DB::insert($sql_detay, $data_detay);

                // Insert extras
                if (!empty($item['extras'])) {
                    foreach ($item['extras'] as $ex) {
                        $sql_ekstra = "INSERT INTO SIPARIS_EKSTRA SET 
                                            SIPARIS_DETAY_ID = :DETAY_ID,
                                            MALZEME_ID = :MALZEME_ID,
                                            MALZEME_AD = :MALZEME_AD,
                                            FIYAT = :FIYAT";
                        DB::insert($sql_ekstra, [
                            ':DETAY_ID' => $detay_id,
                            ':MALZEME_ID' => $ex['id'],
                            ':MALZEME_AD' => $ex['name'],
                            ':FIYAT' => $ex['price']
                        ]);
                    }
                }
            }

            return [
                "HATA" => false,
                "ACIKLAMA" => $is_edit ? "Sipariş başarıyla güncellendi." : "Sipariş başarıyla oluşturuldu.",
                "ID" => $siparis_id,
                "TOKEN" => $token
            ];
        } else {
            return [
                "HATA" => true,
                "ACIKLAMA" => "Sipariş kaydedilirken bir hata oluştu."
            ];
        }
    }

    // GENERAL LOGGING METHOD
    public static function logEkle($logTuru, $referansId, $referansNo, $islemTuru, $alan, $eskiDeger, $yeniDeger, $aciklama) {
        $kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : 0;
        $kullanici_adsoyad = isset($_SESSION['ad_soyad']) ? $_SESSION['ad_soyad'] : (isset($_SESSION['kullanici']) ? $_SESSION['kullanici'] : 'Sistem');
        if ($kullanici_id > 0 && ($kullanici_adsoyad == 'Sistem' || empty($kullanici_adsoyad))) {
            $user = DB::getRow("SELECT CONCAT_WS(' ', AD, SOYAD) AS ADSOYAD FROM KULLANICI WHERE ID = :ID", [':ID' => $kullanici_id]);
            if ($user && !empty($user->ADSOYAD)) {
                $kullanici_adsoyad = $user->ADSOYAD;
            }
        }
        $tarih = date('Y-m-d');
        $saat = date('H:i:s');
        
        $sql = "INSERT INTO GENEL_LOG SET 
                    LOG_TURU = :LOG_TURU,
                    REFERANS_ID = :REFERANS_ID,
                    REFERANS_NO = :REFERANS_NO,
                    ISLEM_TURU = :ISLEM_TURU,
                    KULLANICI_ID = :KULLANICI_ID,
                    KULLANICI_ADSOYAD = :KULLANICI_ADSOYAD,
                    ALAN = :ALAN,
                    ESKI_DEGER = :ESKI_DEGER,
                    YENI_DEGER = :YENI_DEGER,
                    ACIKLAMA = :ACIKLAMA,
                    TARIH = :TARIH,
                    SAAT = :SAAT";
                    
        DB::insert($sql, [
            ':LOG_TURU' => $logTuru,
            ':REFERANS_ID' => $referansId,
            ':REFERANS_NO' => $referansNo,
            ':ISLEM_TURU' => $islemTuru,
            ':KULLANICI_ID' => $kullanici_id,
            ':KULLANICI_ADSOYAD' => $kullanici_adsoyad,
            ':ALAN' => $alan,
            ':ESKI_DEGER' => $eskiDeger,
            ':YENI_DEGER' => $yeniDeger,
            ':ACIKLAMA' => $aciklama,
            ':TARIH' => $tarih,
            ':SAAT' => $saat
        ]);
    }

    // SIPARIS KAYNAKLARI (Sources)
    public function getSiparisKaynaklari($request = []) {
        $data = [];
        $sql = "SELECT * FROM SIPARIS_KAYNAK WHERE 1";
        if (isset($request['durum']) && in_array($request['durum'], ['0', '1'])) {
            $sql .= " AND DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }
        if (isset($request['kaynak']) && !empty($request['kaynak'])) {
            $sql .= " AND KAYNAK LIKE :KAYNAK";
            $data[':KAYNAK'] = '%' . trim($request['kaynak']) . '%';
        }
        $sql .= " ORDER BY ID ASC";
        
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

    public function getSiparisKaynagi($request) {
        $sql = "SELECT * FROM SIPARIS_KAYNAK WHERE ID = :ID";
        return DB::getRow($sql, [':ID' => $request['id']]);
    }

    public function siparis_kaynagi_ekle() {
        if (!in_array($_SESSION['yetki_id'], [1, 2])) {
            return ["HATA" => true, "ACIKLAMA" => "Yetkiniz Yok!"];
        }
        if (strlen(trim($_REQUEST['kaynak'])) <= 0) {
            return ["HATA" => true, "ACIKLAMA" => "Kaynak Adı Giriniz!"];
        }
        $token = md5(microtime() . $_REQUEST['kaynak']);
        $sql = "INSERT INTO SIPARIS_KAYNAK SET KAYNAK = :KAYNAK, DURUM = :DURUM, ACIKLAMA = :ACIKLAMA, TOKEN = :TOKEN";
        $id = DB::insert($sql, [
            ':KAYNAK' => trim($_REQUEST['kaynak']),
            ':DURUM' => $_REQUEST['durum'],
            ':ACIKLAMA' => trim($_REQUEST['aciklama']),
            ':TOKEN' => $token
        ]);
        if ($id > 0) {
            return ["HATA" => false, "ACIKLAMA" => "Sipariş Kaynağı Oluşturuldu.", "URL" => "/views/tanimlama/siparis_kaynagi_listesi.php?route=tanimlama/siparis_kaynagi_listesi"];
        }
        return ["HATA" => true, "ACIKLAMA" => "Hata Oluştu."];
    }

    public function siparis_kaynagi_kaydet() {
        if (!in_array($_SESSION['yetki_id'], [1, 2])) {
            return ["HATA" => true, "ACIKLAMA" => "Yetkiniz Yok!"];
        }
        $row = DB::getRow("SELECT * FROM SIPARIS_KAYNAK WHERE ID = :ID", [':ID' => $_REQUEST['id']]);
        if (!$row) {
            return ["HATA" => true, "ACIKLAMA" => "Kayıt Bulunamadı!"];
        }
        $sql = "UPDATE SIPARIS_KAYNAK SET KAYNAK = :KAYNAK, DURUM = :DURUM, ACIKLAMA = :ACIKLAMA WHERE ID = :ID";
        $update = DB::exec($sql, [
            ':KAYNAK' => trim($_REQUEST['kaynak']),
            ':DURUM' => $_REQUEST['durum'],
            ':ACIKLAMA' => trim($_REQUEST['aciklama']),
            ':ID' => $row->ID
        ]);
        return ["HATA" => false, "ACIKLAMA" => "Kayıt Güncellendi."];
    }

    public function siparis_kaynagi_sil() {
        if (!in_array($_SESSION['yetki_id'], [1, 2])) {
            return ["HATA" => true, "ACIKLAMA" => "Yetkiniz Yok!"];
        }
        $sql = "DELETE FROM SIPARIS_KAYNAK WHERE ID = :ID";
        DB::exec($sql, [':ID' => $_REQUEST['id']]);
        return ["HATA" => false, "ACIKLAMA" => "Silindi."];
    }

    // SIPARIS TIPLERI (Types)
    public function getSiparisTipleri($request = []) {
        $data = [];
        $sql = "SELECT * FROM SIPARIS_TIPI WHERE 1";
        if (isset($request['durum']) && in_array($request['durum'], ['0', '1'])) {
            $sql .= " AND DURUM = :DURUM";
            $data[':DURUM'] = $request['durum'];
        }
        if (isset($request['siparis_tipi']) && !empty($request['siparis_tipi'])) {
            $sql .= " AND SIPARIS_TIPI LIKE :TIPI";
            $data[':TIPI'] = '%' . trim($request['siparis_tipi']) . '%';
        }
        $sql .= " ORDER BY ID ASC";
        
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

    public function getSiparisTipi($request) {
        $sql = "SELECT * FROM SIPARIS_TIPI WHERE ID = :ID";
        return DB::getRow($sql, [':ID' => $request['id']]);
    }

    public function siparis_tipi_ekle() {
        if (!in_array($_SESSION['yetki_id'], [1, 2])) {
            return ["HATA" => true, "ACIKLAMA" => "Yetkiniz Yok!"];
        }
        if (strlen(trim($_REQUEST['siparis_tipi'])) <= 0) {
            return ["HATA" => true, "ACIKLAMA" => "Sipariş Tipi Adı Giriniz!"];
        }
        $token = md5(microtime() . $_REQUEST['siparis_tipi']);
        $sql = "INSERT INTO SIPARIS_TIPI SET SIPARIS_TIPI = :TIPI, DURUM = :DURUM, ACIKLAMA = :ACIKLAMA, TOKEN = :TOKEN";
        $id = DB::insert($sql, [
            ':TIPI' => trim($_REQUEST['siparis_tipi']),
            ':DURUM' => $_REQUEST['durum'],
            ':ACIKLAMA' => trim($_REQUEST['aciklama']),
            ':TOKEN' => $token
        ]);
        if ($id > 0) {
            return ["HATA" => false, "ACIKLAMA" => "Sipariş Tipi Oluşturuldu.", "URL" => "/views/tanimlama/siparis_tipi_listesi.php?route=tanimlama/siparis_tipi_listesi"];
        }
        return ["HATA" => true, "ACIKLAMA" => "Hata Oluştu."];
    }

    public function siparis_tipi_kaydet() {
        if (!in_array($_SESSION['yetki_id'], [1, 2])) {
            return ["HATA" => true, "ACIKLAMA" => "Yetkiniz Yok!"];
        }
        $row = DB::getRow("SELECT * FROM SIPARIS_TIPI WHERE ID = :ID", [':ID' => $_REQUEST['id']]);
        if (!$row) {
            return ["HATA" => true, "ACIKLAMA" => "Kayıt Bulunamadı!"];
        }
        $sql = "UPDATE SIPARIS_TIPI SET SIPARIS_TIPI = :TIPI, DURUM = :DURUM, ACIKLAMA = :ACIKLAMA WHERE ID = :ID";
        $update = DB::exec($sql, [
            ':TIPI' => trim($_REQUEST['siparis_tipi']),
            ':DURUM' => $_REQUEST['durum'],
            ':ACIKLAMA' => trim($_REQUEST['aciklama']),
            ':ID' => $row->ID
        ]);
        return ["HATA" => false, "ACIKLAMA" => "Kayıt Güncellendi."];
    }

    public function siparis_tipi_sil() {
        if (!in_array($_SESSION['yetki_id'], [1, 2])) {
            return ["HATA" => true, "ACIKLAMA" => "Yetkiniz Yok!"];
        }
        $sql = "DELETE FROM SIPARIS_TIPI WHERE ID = :ID";
        DB::exec($sql, [':ID' => $_REQUEST['id']]);
        return ["HATA" => false, "ACIKLAMA" => "Silindi."];
    }

    // LOGLAR
    public function getSiparisLoglari($request = []) {
        $data = [];
        $sql = "SELECT * FROM GENEL_LOG WHERE LOG_TURU = 'Sipariş' ";
        if (!empty($request['siparis_no'])) {
            $sql .= " AND REFERANS_NO LIKE :SIPARIS_NO";
            $data[':SIPARIS_NO'] = '%' . trim($request['siparis_no']) . '%';
        }
        if (!empty($request['kullanici'])) {
            $sql .= " AND KULLANICI_ADSOYAD LIKE :KULLANICI";
            $data[':KULLANICI'] = '%' . trim($request['kullanici']) . '%';
        }
        $sql .= " ORDER BY ID DESC";

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

    public function siparis_kaynagi_bilgisi() {
        $row = $this->getSiparisKaynagi(['id' => $_REQUEST['id']]);
        if ($row) {
            return ["HATA" => false, "ROW" => $row];
        }
        return ["HATA" => true, "ACIKLAMA" => "Kayıt Bulunamadı."];
    }

    public function siparis_tipi_bilgisi() {
        $row = $this->getSiparisTipi(['id' => $_REQUEST['id']]);
        if ($row) {
            return ["HATA" => false, "ROW" => $row];
        }
        return ["HATA" => true, "ACIKLAMA" => "Kayıt Bulunamadı."];
    }
}