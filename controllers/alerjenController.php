<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class AlerjenController {

    private $select;
    private $site;
    private $resim_yol;

    public function __construct($select = "", $row_site = "") {
        global $row_site;
        $this->select       = $select;
        $this->site         = $row_site;
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

    public function getAlerjenler($request = array()) {

        $data = array();
        $sql = "SELECT 
                    A.*
                FROM ALERJEN AS A
                WHERE 1
                ORDER BY A.SIRA ASC
                ";
        $rows = DB::get($sql, $data);
        return $rows;
    }

    function alerjen_bilgisi() {
        
        $data = array();
        $sql = "SELECT 
                    A.*
                FROM ALERJEN AS A
                WHERE A.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(!is_file(fncDocumentRoot($row->RESIM_URL))){
            $row->RESIM_URL = $this->site->LOGO;
        }

        if($row->ID > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Alerjen Düzenle.";
            $result["ROW"]       = $row;
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Kullanıcı Bulunamadı!";
        }

        return $result;
    }

    public function alerjen_ekle() {
        global $cResim, $row_site;

        if(strlen(trim($_REQUEST['alerjen'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Alerjen Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "INSERT INTO ALERJEN SET ALERJEN     = :ALERJEN,
                                        ALERJEN_ENG = :ALERJEN_ENG,
                                        SIRA        = :SIRA,
                                        DURUM       = :DURUM
                                        ";
        $data[":ALERJEN"]       = trim($_REQUEST['alerjen']);
        $data[":ALERJEN_ENG"]   = trim($_REQUEST['alerjen_eng']);
        $data[":SIRA"]          = trim($_REQUEST['sira']);
        $data[":DURUM"]         = $_REQUEST['durum'];
        $id = DB::insert($sql, $data);

        if (isset($_FILES['resim']) AND $_FILES['resim']['error'] == 0) {

            $data = array();
            $sql = "SELECT 
                        YEAR(A.TARIH) AS YIL,
                        A.ID,
                        A.RESIM_URL
                    FROM ALERJEN AS A
                    WHERE A.ID = :ID
                    ";
            $data[":ID"]  = $id;
            $row = DB::getRow($sql, $data);

            if($row->ID > 0){
                $yol    = fncImgPathFirma($row_site->IMG_PATH, 'alerjen', $row->ID, $row->YIL);
                $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

                $data = array();
                $sql = "UPDATE ALERJEN SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
                $data[":RESIM_URL"]     = '/' . $yol . $resim["RESIM_ADI"];
                $data[":ID"]            = $row->ID;
                $resim = DB::exec($sql, $data);
            }
        }

        if($id > 0){
            $result["HATA"]      = FALSE;
            $result["ACIKLAMA"]  = "Alerjen Oluşturuldu.";
        }else{
            $result["HATA"]      = TRUE;
            $result["ACIKLAMA"]  = "Hata Oluştu.";
        }

        return $result;
    }

    public function alerjen_kaydet() {
        global $cResim, $row_site;

        $data = array();
        $sql = "SELECT * FROM ALERJEN WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Alerjen Bulunamadı!";
            return $result;
        }
        
        if(strlen(trim($_REQUEST['alerjen'])) <= 0){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Alerjen Giriniz!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE ALERJEN SET  ALERJEN      = :ALERJEN,
                                    ALERJEN_ENG  = :ALERJEN_ENG,
                                    SIRA         = :SIRA,
                                    DURUM        = :DURUM
                                WHERE ID = :ID
                                ";
        $data[":ALERJEN"]       = trim($_REQUEST['alerjen']);
        $data[":ALERJEN_ENG"]   = trim($_REQUEST['alerjen_eng']);
        $data[":SIRA"]          = trim($_REQUEST['sira']);
        $data[":DURUM"]         = $_REQUEST['durum'];
        $data[":ID"]            = $row->ID;
        $update = DB::exec($sql, $data);

        if (isset($_FILES['resim']) AND $_FILES['resim']['error'] == 0) {

            $data = array();
            $sql = "SELECT 
                        YEAR(A.TARIH) AS YIL,
                        A.ID,
                        A.RESIM_URL
                    FROM ALERJEN AS A
                    WHERE A.ID = :ID
                    ";
            $data[":ID"]  = $row->ID;
            $row_alerjen = DB::getRow($sql, $data);

            if($row->ID > 0){
                $yol    = fncImgPathFirma($row_site->IMG_PATH, 'alerjen', $row_alerjen->ID, $row_alerjen->YIL);
                $resim  = $cResim->fncTekResimYukle($yol, $_FILES['resim']);

                $data = array();
                $sql = "UPDATE ALERJEN SET RESIM_URL = :RESIM_URL WHERE ID = :ID";
                $data[":RESIM_URL"] = '/' . $yol . $resim["RESIM_ADI"];
                $data[":ID"]        = $row->ID;
                $resim = DB::exec($sql, $data);

                //Eski Resimi Siliyoruz
                if($resim > 0) unlink(fncDocumentRoot($row_alerjen->RESIM_URL, $row_site->IMG_PATH));
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

    public function alerjen_sil() {

        if (!in_array($_SESSION['yetki_id'],array(1,2))) {
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Yetkiniz Yok!";
            return $result;
        }
        
        $data = array();
        $sql = "SELECT * FROM ALERJEN WHERE ID = :ID";
        $data[":ID"]  = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Alerjen Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "SELECT * FROM URUN WHERE FIND_IN_SET(:ALERJEN_ID, ALERJEN_IDS)";
        $data[":ALERJEN_ID"]  = $row->ID;
        $rows_urun = DB::get($sql, $data);

        if (!empty($rows_urun)) {
            foreach ($rows_urun as $urun) {
                $alerjen_ids = explode(',', $urun->ALERJEN_IDS);
                $alerjen_ids = array_filter($alerjen_ids, function($id) use ($row) {
                    return $id != $row->ID;
                });
                $yeni_alerjen_ids = implode(',', $alerjen_ids);

                $data = array();
                $sql = "UPDATE URUN SET ALERJEN_IDS = :ALERJEN_IDS WHERE ID = :ID";
                $data[":ALERJEN_IDS"]  = $yeni_alerjen_ids;
                $data[":ID"]           = $urun->ID;
                DB::exec($sql, $data);
            }
        }

        $data = array();
        $sql = "DELETE FROM ALERJEN WHERE ID = :ID";
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

    public function alerjen_resim_sil() {
        global $row_site;

        $data = array();
        $sql = "SELECT 
                    A.*,
                    YEAR(A.TARIH) AS YIL
                FROM ALERJEN AS A
                WHERE A.ID = :ID
                ";
        $data[":ID"]  = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if(is_null($row->ID)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Alerjen Bulunamadı!";
            return $result;
        }

        if(is_null($row->RESIM_URL)){
            $result["HATA"]          = TRUE;
            $result["ACIKLAMA"]      = "Resim Yok!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE ALERJEN SET RESIM_URL = NULL WHERE ID = :ID";
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

    public function alerjen_sira_kaydet() {
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
            $sql = "UPDATE ALERJEN SET SIRA = :SIRA WHERE ID = :ID";
            $data[":SIRA"]    = $row->sira;
            $data[":ID"]      = $row->id;
            $update = DB::exec($sql, $data);
            if($update > 0) $say++;
        }

        $rows = $this->getAlerjenler();

        $html = "";
        foreach ($rows as $key => $row){
            $html .= '
                <tr data-id="'.$row->ID.'">
                    <td>'.($key+1).'</td>
                    <td align="center">';
                        if(is_file(fncDocumentRoot($row->RESIM_URL))){
                            $html .= '<img src="'.$row->RESIM_URL.'" class="rounded-3 fancybox" alt="Alerjen Resim" height="50">';
                        }else{
                            $html .= '<img src="'.$this->site->LOGO.'" class="fancybox" alt="Menü Yönetim" height="50"/>';
                        }
                    $html .= '
                    </td>
                    <td>'.$row->ALERJEN.'</td>
                    <td>'.$row->SIRA.'</td>
                    <td>'.fncDurumSpan($row->DURUM).'</td>
                    <td align="right">
                        <a href="javascript:;" class="btn btn-primary btn-icon btn-sm" data-id="'.$row->ID.'" onclick="fncBilgisi(this)" data-bs-target="#alerjenDuzenleModal" data-bs-toggle="modal"><i class="ri-pencil-line"></i></a>
                        <a href="javascript:;" class="btn btn-danger btn-icon btn-sm" data-id="'.$row->ID.'" onclick="fncSil(this)"><i class="ri-delete-bin-5-line"></i></a>
                    </td>
                </tr>
            ';
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