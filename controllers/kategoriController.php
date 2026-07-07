<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class KategoriController {

    private $select;
    private $site;

    function __construct($select = "", $row_site = "") {
        global $row_site;
        $this->select       = $select;
        $this->site         = $row_site;
    }

    public function UstKategoriler() {
        $data = array();
        $sql = "SELECT
                    UK.ID,
                    UK.UST_KATEGORI AS AD
                FROM UST_KATEGORI AS UK
                WHERE UK.DURUM = 1";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function getKategoriler($request = array()) {

        $data = array();
        $sql = "SELECT 
                    K.*
                FROM KATEGORI AS K
                WHERE 1
                ORDER BY K.SIRA ASC
                ";
        $rows = DB::get($sql, $data);
        return $rows;
    }

    public function getUstKategoriler($request) {

        $data = array();
        $sql = "SELECT * FROM UST_KATEGORI WHERE 1 ORDER BY SIRA ASC";
        $rows = DB::get($sql, $data);
        return $rows;
    }

    function kategori_bilgisi() {

        $data = array();
        $sql = "SELECT 
                    K.*
                FROM KATEGORI AS K
                WHERE K.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);
        
        if(!is_file(fncDocumentRoot($row->RESIM_URL))){
            $row->RESIM_URL = $this->site->LOGO;
        }
        
        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kategori Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kategori Bulunamadı!";
        }

        return $result;
    }

    function ust_kategori_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    UK.*
                FROM UST_KATEGORI AS UK
                WHERE UK.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Üst Kategori Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kategori Bulunamadı!";
        }

        return $result;
    }

    public function kategori_kaydet() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT * FROM KATEGORI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['kategori'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM KATEGORI WHERE SIRA = :SIRA AND ID != :ID";
        $data[":SIRA"]              = trim($_REQUEST['sira']);
        $data[":ID"]                = $row->ID;
        $row_kontrol = DB::getRow($sql, $data);

        if(!is_null($row_kontrol->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Kullanılıyor!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE KATEGORI SET KATEGORI        = :KATEGORI,
                                    SIRA            = :SIRA, 
                                    DURUM           = :DURUM
                                WHERE ID = :ID
                                ";
        $data[":KATEGORI"]          = trim($_REQUEST['kategori']);
        $data[":SIRA"]              = $_REQUEST['sira'];
        $data[":DURUM"]             = $_REQUEST['durum'];
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if (isset($_FILES['resim']) AND $_FILES['resim']['error'] == 0) {

            $data = array();
            $sql = "SELECT 
                        YEAR(K.TARIH) AS YIL,
                        K.ID,
                        K.RESIM_URL
                    FROM KATEGORI AS K
                    WHERE K.ID = :ID
                    ";
            $data[":ID"]  = $row->ID;
            $row_kategori = DB::getRow($sql, $data);

            if($row->ID > 0){
                $yol    = fncImgPathFirma($row_site->IMG_PATH, 'kategori', $row_kategori->ID, $row_kategori->YIL);
                $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

                $data = array();
                $sql = "UPDATE KATEGORI SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
                $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
                $data[":ID"]        = $row->ID;
                $resim = DB::exec($sql, $data);

                //Eski Resimi Siliyoruz
                if($resim > 0) unlink(fncDocumentRoot($row_kategori->RESIM_URL, $row_site->IMG_PATH));
            }
        }

        if($update > 0 OR $resim > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function kategori_ekle() {
        global $cResim, $row_site;

        if(strlen(trim($_REQUEST['kategori'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM KATEGORI WHERE SIRA = :SIRA";
        $data[":SIRA"]              = trim($_REQUEST['sira']);
        $row_kontrol = DB::getRow($sql, $data);

        if(!is_null($row_kontrol->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Kullanılıyor!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO KATEGORI SET    KATEGORI            = :KATEGORI,
                                            SIRA                = :SIRA,            
                                            DURUM               = :DURUM
                                            ";
        $data[":KATEGORI"]          = trim($_REQUEST['kategori']);
        $data[":SIRA"]              = trim($_REQUEST['sira']);
        $data[":DURUM"]             = $_REQUEST['durum'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT 
                    K.ID,
                    YEAR(K.TARIH) AS YIL
                FROM KATEGORI AS K
                WHERE K.ID = :ID
                ";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $yol    = fncImgPathFirma($row_site->IMG_PATH, 'kategori', $row->ID, $row->YIL);
            $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

            $data = array();
            $sql = "UPDATE KATEGORI SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
            $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
            $data[":ID"]        = $row->ID;
            $resim = DB::exec($sql, $data);
        }

        if($id > 0 OR $resim > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Kategori Oluşturuldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function kategori_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM KATEGORI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM KATEGORI WHERE ID = :ID";
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

    public function ust_kategori_ekle() {
        global $cResim, $row_site;

        if(strlen(trim($_REQUEST['ust_kategori'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM UST_KATEGORI WHERE SIRA = :SIRA";
        $data[":SIRA"]  = trim($_REQUEST['sira']);
        $row_kontrol = DB::getRow($sql, $data);

        if(!is_null($row_kontrol->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Kullanılıyor!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO UST_KATEGORI SET    UST_KATEGORI        = :UST_KATEGORI,
                                                UST_KATEGORI_ENG    = :UST_KATEGORI_ENG,
                                                SIRA                = :SIRA,
                                                DURUM               = :DURUM
                                            ";
        $data[":UST_KATEGORI"]      = trim($_REQUEST['ust_kategori']);
        $data[":UST_KATEGORI_ENG"]  = trim($_REQUEST['ust_kategori_eng']);
        $data[":SIRA"]              = trim($_REQUEST['sira']);
        $data[":DURUM"]             = $_REQUEST['durum'];
        $id = DB::insert($sql, $data);

        $data = array();
        $sql = "SELECT 
                    UK.ID,
                    YEAR(UK.TARIH) AS YIL
                FROM UST_KATEGORI AS UK
                WHERE UK.ID = :ID
                ";
        $data[":ID"]  = $id;
        $row = DB::getRow($sql, $data);

        if($row->ID > 0){
            $yol    = fncImgPathFirma($row_site->IMG_PATH, 'ust_kategori', $row->ID, $row->YIL);
            $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

            $data = array();
            $sql = "UPDATE UST_KATEGORI SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
            $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
            $data[":ID"]        = $row->ID;
            $resim = DB::exec($sql, $data);
        }

        if($id > 0 OR $resim > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Üst Kategori Oluşturuldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function ust_kategori_kaydet() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT * FROM UST_KATEGORI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Üst Kategori Bulunamadı!";
            return $result;
        }

        if(strlen(trim($_REQUEST['ust_kategori'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Üst Kategori Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM UST_KATEGORI WHERE SIRA = :SIRA AND ID != :ID";
        $data[":SIRA"]  = trim($_REQUEST['sira']);
        $data[":ID"]    =  $row->ID;
        $row_kontrol = DB::getRow($sql, $data);

        if(!is_null($row_kontrol->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Sıra Kullanılıyor!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE UST_KATEGORI SET UST_KATEGORI        = :UST_KATEGORI,
                                        UST_KATEGORI_ENG    = :UST_KATEGORI_ENG,
                                        SIRA                = :SIRA, 
                                        DURUM               = :DURUM
                                WHERE ID = :ID
                                ";
        $data[":UST_KATEGORI"]      = trim($_REQUEST['ust_kategori']);
        $data[":UST_KATEGORI_ENG"]  = trim($_REQUEST['ust_kategori_eng']);
        $data[":SIRA"]              = $_REQUEST['sira'];
        $data[":DURUM"]             = $_REQUEST['durum'];
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {

            $data = array();
            $sql = "SELECT 
                        YEAR(UK.TARIH) AS YIL,
                        UK.ID,
                        UK.RESIM_URL
                    FROM UST_KATEGORI AS UK
                    WHERE UK.ID = :ID
                    ";
            $data[":ID"]  = $row->ID;
            $row_ust_kategori = DB::getRow($sql, $data);

            if($row->ID > 0){
                $yol    = fncImgPathFirma($row_site->IMG_PATH, 'ust_kategori', $row_ust_kategori->ID, $row_ust_kategori->YIL);
                $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

                $data = array();
                $sql = "UPDATE UST_KATEGORI SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
                $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
                $data[":ID"]        = $row->ID;
                $resim = DB::exec($sql, $data);

                //Eski Resimi Siliyoruz
                if($resim > 0) unlink(fncDocumentRoot($row_ust_kategori->RESIM_URL, $row_site->IMG_PATH));

            }
        }

        if($update > 0 OR $resim > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Güncellendi.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function ust_kategori_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM UST_KATEGORI WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "DELETE FROM UST_KATEGORI WHERE ID = :ID";
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

    public function ust_kategori_resim_sil() {
        global $row_site;

        $data = array();
        $sql = "SELECT 
                    UK.*,
                    YEAR(UK.TARIH) AS YIL
                FROM UST_KATEGORI AS UK
                WHERE UK.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Üst Kategori Bulunamadı!";
            return $result;
        }

        if(is_null($row->RESIM_URL)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Yok!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE UST_KATEGORI SET RESIM_URL = NULL WHERE ID = :ID";
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);
       
        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Silindi.";
            unlink(fncDocumentRoot($row->RESIM_URL, $row_site->IMG_PATH));
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function kategori_resim_sil() {
        global $row_site;

        $data = array();
        $sql = "SELECT 
                    K.*,
                    YEAR(K.TARIH) AS YIL
                FROM KATEGORI AS K
                WHERE K.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Kategori Bulunamadı!";
            return $result;
        }

        if(is_null($row->RESIM_URL)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Yok!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE KATEGORI SET RESIM_URL = NULL WHERE ID = :ID";
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);
       
        if($update > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Silindi.";
            unlink(fncDocumentRoot($row->RESIM_URL, $row_site->IMG_PATH));
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

    public function kategori_sira_kaydet() {
        global $row_site;

        $rows = json_decode($_REQUEST["siralama"]);

        if(!is_array($rows)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Geçersiz Sıralama!";
            return $result;
        }

        $say = 0;
        foreach ($rows as $key => $row) {
            
            $data = array();
            $sql = "UPDATE KATEGORI SET SIRA = :SIRA WHERE ID = :ID";
            $data[":SIRA"]    = $row->sira;
            $data[":ID"]      = $row->id;
            $update = DB::exec($sql, $data);
            if($update > 0) $say++;
        }

        $rows_ust_kategoriler   = $this->getUstKategoriler($_REQUEST);
        $rows_kategoriler       = $this->getKategoriler($_REQUEST);

        foreach ($rows_kategoriler as $key => $row_kategoriler2) {
            $rows_kategori_grup[$row_kategoriler2->UST_KATEGORI][] = $row_kategoriler2;
        }

        $html = '';
        foreach ($rows_kategori_grup as $ust_kategori => $rows_kategori) {
        $ust_class = preg_replace('/[^a-zA-Z0-9]/', '_', $ust_kategori);

        $html .= '
            <tr class="table-dark">
                <td colspan="7" class="text-center position-relative">
                    '.$ust_kategori.'
                    <a href="javascript:void(0)" class="btn btn-dark btn-sm position-absolute end-0 top-50 translate-middle-y" onclick="fncGizle(this)" data-ust_kategori="'.$ust_class.'">
                    <i class="ri-arrow-down-double-line"></i></a>
                </td>
            </tr>
            <tbody class="sortable ust_kategori'.$ust_class.'">';
                foreach ($rows_kategori as $key => $row){
                $html .= '
                    <tr data-id="'.$row->ID.'">
                        <td>'.($key+1).'</td>
                        <td>'.$row->UST_KATEGORI.'</td>
                        <td>'.$row->KATEGORI.'</td>
                        <td>'.$row->KATEGORI_ENG.'</td>
                        <td>'.$row->SIRA.'</td>
                        <td>'.fncDurumSpan($row->DURUM).'</td>
                        <td class="text-end">
                            <a href="javascript:;" class="btn btn-primary btn-sm btn-icon" data-id="'.$row->ID.'" onclick="fncKategoriBilgisi(this)" data-bs-toggle="modal" data-bs-target="#kategoriDuzenleModal"><i class="ri-pencil-line"></i></a>
                            <a href="javascript:;" class="btn btn-danger btn-sm btn-icon" data-id="'.$row->ID.'" onclick="fncKategoriSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                        </td>
                    </tr>
                    ';
                }

            $html .= '</tbody>';
        }
       
        if($say > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Güncellendi.";
            $result["HTML"]      =  $html;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Bilgiler Aynı.";
        }

        return $result;
    }

}