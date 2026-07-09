<?

require_once($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class RaporController
{

    private $select;
    private $site;

    public function __construct($select = "", $row_site = "")
    {
        $this->select = $select;
        $this->site = $row_site;
    }

    public function sayfalamaOlustur($toplamVeri, $request, $sayfaBasinaVeri = 10)
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $baseUrl = $protocol . $host . $scriptName;

        $gecerliSayfa = isset($request['page']) ? (int)$request['page'] : 1;
        $request['page'] = null;

        $queryString = http_build_query(array_filter($request, function ($value) {
            return $value !== null;
        }));
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

    public function getCariHarcamaRapor($request){

        $data = array();
        $sql = "SELECT 
                    MA.*,
                    C.CARI,
                    SUM(BIRIM_FIYAT) AS BIRIM_FIYAT,
                    SUM(ARA_TOPLAM) AS ARA_TOPLAM,
                    SUM(KDV_TUTAR) AS KDV_TUTAR,
                    SUM(TOPLAM_TUTAR) AS TOPLAM_TUTAR
                FROM MALZEME_ALIS AS MA
                    LEFT JOIN CARI AS C ON C.ID = MA.CARI_ID
                    LEFT JOIN MALZEME AS M ON M.ID = MA.MALZEME_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = MA.KAYIT_YAPAN_ID
                WHERE MA.DURUM = 1
                ";

        if ($request['malzeme_id'] > 0) {
            $sql .= " AND MA.MALZEME_ID = :MALZEME_ID";
            $data[':MALZEME_ID'] = $request['malzeme_id'];
        }

        if(!empty($request['fatura_tarih']) AND $request['fatura_tarih_var'] > 0){
            $sql .= " AND DATE(MA.FATURA_TARIH) >= :TARIH1 AND DATE(MA.FATURA_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['fatura_tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
        }

        $sql .= " GROUP BY MA.CARI_ID";

        $sayfalama = $this->sayfalamaOlustur(count2(DB::get($sql, $data)), $request, $request['sayfalama'] ? $request['sayfalama'] : 20);
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

    public function getSiparisDetaylar($request = array()) {

        $data = array();
        $sql = "SELECT 
                    SD.*,
                    (SELECT COALESCE(SUM(SE.FIYAT), 0) FROM SIPARIS_EKSTRA AS SE WHERE SE.SIPARIS_DETAY_ID = SD.ID) * SD.ADET AS EKSTRA_TUTAR,
                    S.SIPARIS_TARIH,
                    DATE(S.SIPARIS_TARIH) AS SIPARIS_TARIH_DATE,
                    S.MUSTERI,
                    S.TOKEN,
                    S.KAYNAK,
                    TIMESTAMPDIFF(MINUTE, S.SIPARIS_TARIH, S.HAZIRLANMA_TARIH) AS HAZIRLANMA_SURESI,
                    U.ID AS URUN_ID,
                    CONCAT('urun/', U.ID, '/', YEAR(U.TARIH), '/', UR.RESIM_ADI) AS RESIM_URL
                FROM SIPARIS_DETAY AS SD
                    LEFT JOIN SIPARIS AS S ON S.ID = SD.SIPARIS_ID
                    LEFT JOIN URUN AS U ON (U.TRENDYOL_URUN_ID = SD.TRENDYOL_URUN_ID AND SD.TRENDYOL_URUN_ID IS NOT NULL AND SD.TRENDYOL_URUN_ID <> '') OR U.ID = SD.URUN_ID
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

        if (!empty($rows)) {
            $detay_ids = array_column($rows, 'ID');
            $placeholders = implode(',', array_fill(0, count($detay_ids), '?'));
            $extras = DB::get("SELECT SIPARIS_DETAY_ID, MALZEME_AD, FIYAT FROM SIPARIS_EKSTRA WHERE SIPARIS_DETAY_ID IN ($placeholders)", $detay_ids);
            $extras_by_detay = array();
            if (is_array($extras)) {
                foreach ($extras as $ex) {
                    $extras_by_detay[$ex->SIPARIS_DETAY_ID][] = $ex;
                }
            }
            foreach ($rows as $row) {
                $row->ekstralar = isset($extras_by_detay[$row->ID]) ? $extras_by_detay[$row->ID] : array();
            }
        }

        return [
            'rows' => $rows,
            'sayfalama' => $sayfalama,
            'limit' => $sayfalama->getLimitOffset(),
            'excel_sql' => $excel_sql,
            'sayfa_araligi' => $sayfalama->getGorunumAraligi()
        ];
    }

    public function getUrunMaliyet($request = array()) {

        $data = array();
        $sql = "SELECT 
                    UM.*
                FROM URUN_MALIYET AS UM
                WHERE 1
                ";

        if(!empty($request['tarih']) AND $request['tarih_var'] > 0){
            $sql .= " AND DATE(UM.MALIYET_TARIH) >= :TARIH1 AND DATE(UM.MALIYET_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
        }

        $rows = DB::get($sql, $data);
        return $rows;
    }

}
