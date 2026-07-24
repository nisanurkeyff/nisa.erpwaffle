<?

require_once($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class MalzemeAlisController
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

    public function Malzemeler()
    {
        $data = array();
        $sql = "SELECT
                    M.ID,
                    M.MALZEME AS AD
                FROM MALZEME AS M
                WHERE M.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function Cariler()
    {
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

    public function OdemeDurum(){

        $data = array();
        $sql = "SELECT
                    OD.ID,
                    OD.ODEME_DURUM AS AD
                FROM ODEME_DURUM AS OD
                WHERE OD.DURUM = 1
                ";

        $rows = DB::get($sql, $data);
        $this->select->setTemizle();
        $this->select->setData($rows);
        return $this->select;
    }

    public function getMalzemeAlislar($request){

        $data = array();
        $sql = "SELECT 
                    MA.*,
                    C.CARI,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN,
                    OD.ODEME_DURUM,
                    (SELECT COUNT(ID) FROM MALZEME_ALIS_DETAY WHERE MALZEME_ALIS_ID = MA.ID) AS MALZEME_SAYISI
                FROM MALZEME_ALIS AS MA
                    LEFT JOIN CARI AS C ON C.ID = MA.CARI_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = MA.KAYIT_YAPAN_ID
                    LEFT JOIN ODEME_DURUM AS OD ON OD.ID = MA.ODEME_DURUM_ID
                WHERE MA.DURUM = 1
                ";

        if ($request['cari_id'] > 0) {
            $sql .= " AND MA.CARI_ID = :CARI_ID";
            $data[':CARI_ID'] = $request['cari_id'];
        }

        if ($request['malzeme_id'] > 0) {
            $sql .= " AND MA.ID IN (SELECT DISTINCT MALZEME_ALIS_ID FROM MALZEME_ALIS_DETAY WHERE MALZEME_ID = :MALZEME_ID)";
            $data[':MALZEME_ID'] = $request['malzeme_id'];
        }

        if ($request['odeme_durum_id'] > 0) {
            $sql .= " AND MA.ODEME_DURUM_ID = :ODEME_DURUM_ID";
            $data[':ODEME_DURUM_ID'] = $request['odeme_durum_id'];
        }

        if(!empty($request['fatura_tarih']) AND $request['fatura_tarih_var'] > 0){
            $sql .= " AND DATE(MA.FATURA_TARIH) >= :TARIH1 AND DATE(MA.FATURA_TARIH) <= :TARIH2";
            $tarih = explode(",", $request['fatura_tarih']);
            $data[':TARIH1'] = FormatTarih::nokta2db(trim($tarih[0]));
            $data[':TARIH2'] = FormatTarih::nokta2db(trim($tarih[1]));
        }

        $sql .= " ORDER BY MA.TARIH DESC";

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

    public function getMalzemeAlis($id){
        $data = array();
        $sql = "SELECT 
                    MA.*, 
                    C.CARI, 
                    OD.ODEME_DURUM,
                    CONCAT_WS(' ',KU.AD,KU.SOYAD) AS KAYIT_YAPAN
                FROM MALZEME_ALIS AS MA 
                    LEFT JOIN CARI AS C ON C.ID = MA.CARI_ID
                    LEFT JOIN ODEME_DURUM AS OD ON OD.ID = MA.ODEME_DURUM_ID
                    LEFT JOIN KULLANICI AS KU ON KU.ID = MA.KAYIT_YAPAN_ID
                WHERE MA.ID = :ID";
        $data[':ID'] = $id;
        return DB::getRow($sql, $data);
    }

    public function getMalzemeAlisDetaylar($id){
        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS_DETAY WHERE MALZEME_ALIS_ID = :MALZEME_ALIS_ID ORDER BY ID ASC";
        $data[':MALZEME_ALIS_ID'] = $id;
        return DB::get($sql, $data);
    }

    public function malzeme_alis_kaydet(){

        if ($_REQUEST['cari_id'] <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Cari Seçiniz!";
            return $result;
        }

        if (empty($_REQUEST['cart_data'])) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Lütfen sepetinize en az bir malzeme ekleyin!";
            return $result;
        }

        $cart = json_decode($_REQUEST['cart_data'], true);
        if (!is_array($cart) || empty($cart)) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Lütfen sepetinize en az bir malzeme ekleyin!";
            return $result;
        }

        $kdv_tipi = trim($_REQUEST['kdv_tipi']) ? trim($_REQUEST['kdv_tipi']) : 'haric';

        $ara_toplam = 0;
        $kdv_tutar = 0;
        $toplam_tutar = 0;
        $items_to_insert = array();

        foreach ($cart as $item) {
            $malzeme_id = intval($item['id']);
            $miktar = floatval($item['quantity']);
            $entered_price = floatval($item['price']);
            $kdv_oran = intval($item['kdv']);
            $birim = trim($item['unit']);

            if ($malzeme_id <= 0) {
                $result["HATA"] = TRUE;
                $result["ACIKLAMA"] = "Geçersiz Malzeme Seçimi!";
                return $result;
            }
            if ($miktar <= 0) {
                $result["HATA"] = TRUE;
                $result["ACIKLAMA"] = "Lütfen tüm satırlarda miktar giriniz ve sıfırdan büyük olduğundan emin olunuz!";
                return $result;
            }
            if ($entered_price <= 0) {
                $result["HATA"] = TRUE;
                $result["ACIKLAMA"] = "Lütfen tüm satırlarda sıfırdan büyük bir birim fiyat giriniz!";
                return $result;
            }
            if ($kdv_oran < 0 || $kdv_oran > 100) {
                $result["HATA"] = TRUE;
                $result["ACIKLAMA"] = "Geçersiz KDV Oranı!";
                return $result;
            }

            // Normalization & division by zero prevention
            $denominator = 1 + ($kdv_oran / 100);
            if ($denominator <= 0) {
                $denominator = 1;
            }

            if ($kdv_tipi === 'dahil') {
                $birim_fiyat = round($entered_price / $denominator, 4);
            } else {
                $birim_fiyat = round($entered_price, 4);
            }

            $line_subtotal = round($miktar * $birim_fiyat, 2);
            $line_kdv_amount = round(($line_subtotal * $kdv_oran) / 100, 2);
            $line_total = round($line_subtotal + $line_kdv_amount, 2);

            if ($line_subtotal < 0 || $line_kdv_amount < 0 || $line_total < 0) {
                $result["HATA"] = TRUE;
                $result["ACIKLAMA"] = "Hesaplanan tutarlar negatif olamaz!";
                return $result;
            }

            $ara_toplam += $line_subtotal;
            $kdv_tutar += $line_kdv_amount;
            $toplam_tutar += $line_total;

            $items_to_insert[] = [
                'malzeme_id' => $malzeme_id,
                'malzeme' => trim($item['name']),
                'miktar' => $miktar,
                'birim' => $birim ? $birim : 'KG',
                'birim_fiyat' => $birim_fiyat,
                'kdv' => $kdv_oran,
                'kdv_tutar' => $line_kdv_amount,
                'ara_toplam' => $line_subtotal,
                'toplam_tutar' => $line_total
            ];
        }

        $ara_toplam = round($ara_toplam, 2);
        $kdv_tutar = round($kdv_tutar, 2);
        $toplam_tutar = round($toplam_tutar, 2);

        // Financial Consistency Check
        if (abs(($ara_toplam + $kdv_tutar) - $toplam_tutar) > 0.01) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Finansal tutarlılık hatası: Ara Toplam ve KDV toplamı genel toplam ile uyuşmuyor!";
            return $result;
        }

        $fis_no = 'F' . time();
        $odeme_durum_id = $_REQUEST['odeme_durum_id'] ? $_REQUEST['odeme_durum_id'] : 1;
        $odeme_tarihi = ($odeme_durum_id == 2) ? date('Y-m-d H:i:s') : NULL;

        $data = array();
        $sql = "INSERT INTO MALZEME_ALIS SET    FIS_NO          = :FIS_NO,
                                                CARI_ID         = :CARI_ID,
                                                FATURA_NO       = :FATURA_NO,
                                                FATURA_TARIH    = :FATURA_TARIH,
                                                ALIS_TARIH      = :ALIS_TARIH,
                                                VADE_TARIH      = :VADE_TARIH,
                                                ODEME_DURUM_ID  = :ODEME_DURUM_ID,
                                                ODEME_TURU      = :ODEME_TURU,
                                                ODEME_TARIHI    = :ODEME_TARIHI,
                                                ARA_TOPLAM      = :ARA_TOPLAM,
                                                KDV_TUTAR       = :KDV_TUTAR,
                                                TOPLAM_TUTAR    = :TOPLAM_TUTAR,
                                                ACIKLAMA        = :ACIKLAMA,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                                                TOKEN           = :TOKEN,
                                                KDV_TIPI        = :KDV_TIPI
                                                ";

        $data[":FIS_NO"]            = $fis_no;
        $data[":CARI_ID"]           = $_REQUEST['cari_id'];
        $data[":FATURA_NO"]         = trim($_REQUEST['fatura_no']);
        $data[":FATURA_TARIH"]      = $_REQUEST['fatura_tarih'] ? $_REQUEST['fatura_tarih'] : NULL;
        $data[":ALIS_TARIH"]        = $_REQUEST['alis_tarih'] ? $_REQUEST['alis_tarih'] : date('Y-m-d');
        $data[":VADE_TARIH"]        = $_REQUEST['vade_tarih'] ? $_REQUEST['vade_tarih'] : NULL;
        $data[":ODEME_DURUM_ID"]    = $odeme_durum_id;
        $data[":ODEME_TURU"]        = $_REQUEST['odeme_turu'];
        $data[":ODEME_TARIHI"]      = $odeme_tarihi;
        $data[":ARA_TOPLAM"]        = $ara_toplam;
        $data[":KDV_TUTAR"]         = $kdv_tutar;
        $data[":TOPLAM_TUTAR"]      = $toplam_tutar;
        $data[":ACIKLAMA"]          = trim($_REQUEST['aciklama']);
        $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
        $data[":TOKEN"]             = md5(uniqid(rand(), true));
        $data[":KDV_TIPI"]          = $kdv_tipi;

        $id = DB::insert($sql, $data);

        if ($id > 0) {
            foreach ($items_to_insert as $item) {
                $sql_detay = "INSERT INTO MALZEME_ALIS_DETAY SET 
                                    MALZEME_ALIS_ID = :MALZEME_ALIS_ID,
                                    MALZEME_ID      = :MALZEME_ID,
                                    MALZEME         = :MALZEME,
                                    MIKTAR          = :MIKTAR,
                                    BIRIM           = :BIRIM,
                                    BIRIM_FIYAT     = :BIRIM_FIYAT,
                                    KDV             = :KDV,
                                    KDV_TUTAR       = :KDV_TUTAR,
                                    ARA_TOPLAM      = :ARA_TOPLAM,
                                    TOPLAM_TUTAR    = :TOPLAM_TUTAR";
                
                DB::insert($sql_detay, [
                    ':MALZEME_ALIS_ID' => $id,
                    ':MALZEME_ID'      => $item['malzeme_id'],
                    ':MALZEME'         => $item['malzeme'],
                    ':MIKTAR'          => $item['miktar'],
                    ':BIRIM'           => $item['birim'],
                    ':BIRIM_FIYAT'     => $item['birim_fiyat'],
                    ':KDV'             => $item['kdv'],
                    ':KDV_TUTAR'       => $item['kdv_tutar'],
                    ':ARA_TOPLAM'      => $item['ara_toplam'],
                    ':TOPLAM_TUTAR'    => $item['toplam_tutar']
                ]);
            }

            $etkilenen_malzeme_idleri = array_column($items_to_insert, 'malzeme_id');
            if (!empty($etkilenen_malzeme_idleri)) {
                UrunMaliyetService::hesaplaMalzemeIleIlgiliUrunMaliyetleri($etkilenen_malzeme_idleri);
            }

            $this->syncFinansHareketi($id);

            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Alış Kaydedildi.";
            $result["ID"] = $id;
            $result["TOKEN"] = $data[":TOKEN"];
        }else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }

        return $result;
    }



    public function malzeme_alis_sil(){

        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[":ID"] = $_REQUEST["id"];
        $row = DB::getRow($sql, $data);

        if (is_null($row->ID)) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        $sql_detay = "SELECT MALZEME_ID FROM MALZEME_ALIS_DETAY WHERE MALZEME_ALIS_ID = :ID";
        $detaylar = DB::get($sql_detay, [':ID' => $row->ID]);

        $data = array();
        $sql = "UPDATE MALZEME_ALIS SET DURUM = 0 WHERE ID = :ID";
        $data[":ID"] = $row->ID;
        $update = DB::exec($sql, $data);

        fncIslemLog($row->ID, DB::getSQL($sql, $data), $row, __FUNCTION__, "MALZEME_ALIS", "MALZEME_ALIS_SIL");

        if ($update > 0) {
            if ($detaylar) {
                $etkilenen_ids = array();
                foreach ($detaylar as $d) {
                    $etkilenen_ids[] = $d->MALZEME_ID;
                }
                UrunMaliyetService::hesaplaMalzemeIleIlgiliUrunMaliyetleri($etkilenen_ids);
            }

            $this->syncFinansHareketi($row->ID);

            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Silindi.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata Oluştu.";
        }

        return $result;
    }

    public function malzeme_alis_odeme_yap(){

        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[':ID'] = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if ($row->ID <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE MALZEME_ALIS SET ODEME_DURUM_ID = 2, ODEME_TARIHI = NOW() WHERE ID = :ID";
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update > 0) {
            $this->syncFinansHareketi($row->ID);

            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Ödeme Yapıldı.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata.";
        }

        return $result;
    }

    public function malzeme_alis_odeme_red(){

        $data = array();
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $data[':ID'] = $_REQUEST['id'];
        $row = DB::getRow($sql, $data);

        if ($row->ID <= 0) {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Kayıt Bulunamadı!";
            return $result;
        }

        $data = array();
        $sql = "UPDATE MALZEME_ALIS SET ODEME_DURUM_ID = 3, ODEME_TARIHI = NULL WHERE ID = :ID";
        $data[":ID"]                = $row->ID;
        $update = DB::exec($sql, $data);

        if ($update > 0) {
            $this->syncFinansHareketi($row->ID);

            $result["HATA"] = FALSE;
            $result["ACIKLAMA"] = "Ödeme Reddedildi.";
        }
        else {
            $result["HATA"] = TRUE;
            $result["ACIKLAMA"] = "Hata.";
        }

        return $result;
    }

    private function syncFinansHareketi($alis_id) {
        $sql = "SELECT * FROM MALZEME_ALIS WHERE ID = :ID";
        $alis = DB::getRow($sql, [':ID' => $alis_id]);
        if (!$alis) return;

        // Find the category ID for "Malzeme Alışı" (TIP = 'GIDER')
        $sql_kat = "SELECT ID FROM GELIR_GIDER_KATEGORI WHERE KATEGORI = 'Malzeme Alışı' AND TIP = 'GIDER' AND DURUM = 1 LIMIT 1";
        $kat_id = DB::getVar($sql_kat);
        if (!$kat_id) {
            $sql_ins_kat = "INSERT INTO GELIR_GIDER_KATEGORI SET KATEGORI = 'Malzeme Alışı', TIP = 'GIDER', ICON = 'ri-shopping-basket-2-line', DURUM = 1";
            $kat_id = DB::insert($sql_ins_kat);
        }

        // Map ODEME_DURUM_ID to HAREKET_DURUMU
        // 1 -> BEKLIYOR, 2 -> TAMAMLANDI, 3 -> IPTAL
        $hareket_durumu = 'BEKLIYOR';
        if ($alis->ODEME_DURUM_ID == 2) {
            $hareket_durumu = 'TAMAMLANDI';
        } elseif ($alis->ODEME_DURUM_ID == 3) {
            $hareket_durumu = 'IPTAL';
        }

        // Check if GELIR_GIDER record exists for this purchase
        $sql_check = "SELECT ID, DURUM FROM GELIR_GIDER WHERE ISLEM_KAYNAGI_ID = 2 AND KAYNAK_ID = :KAYNAK_ID LIMIT 1";
        $gg_row = DB::getRow($sql_check, [':KAYNAK_ID' => $alis_id]);

        $data = [
            ':TIP'              => 'GIDER',
            ':HAREKET_DURUMU'   => $hareket_durumu,
            ':CARI_ID'          => $alis->CARI_ID,
            ':KATEGORI_ID'      => $kat_id,
            ':TUTAR'            => $alis->TOPLAM_TUTAR,
            ':FATURA_NO'        => $alis->FATURA_NO,
            ':FATURA_TARIH'     => $alis->FATURA_TARIH,
            ':ACIKLAMA'         => "Malzeme Alış Fişi: " . $alis->FIS_NO . ($alis->ACIKLAMA ? " - " . $alis->ACIKLAMA : ""),
            ':ODEME_TARIHI'     => $alis->ODEME_TARIHI,
            ':DURUM'            => $alis->DURUM
        ];

        if ($gg_row) {
            // Update existing record
            $sql_gg = "UPDATE GELIR_GIDER SET 
                            TIP             = :TIP,
                            HAREKET_DURUMU  = :HAREKET_DURUMU,
                            CARI_ID         = :CARI_ID,
                            KATEGORI_ID     = :KATEGORI_ID,
                            TUTAR           = :TUTAR,
                            FATURA_NO       = :FATURA_NO,
                            FATURA_TARIH    = :FATURA_TARIH,
                            ACIKLAMA        = :ACIKLAMA,
                            ODEME_TARIHI    = :ODEME_TARIHI,
                            DURUM           = :DURUM
                        WHERE ID = :ID";
            $data[':ID'] = $gg_row->ID;
            DB::exec($sql_gg, $data);
        } else {
            // Insert new record (ISLEM_KAYNAGI_ID = 2 refers to MALZEME_ALISI)
            $sql_gg = "INSERT INTO GELIR_GIDER SET 
                            TIP             = :TIP,
                            HAREKET_DURUMU  = :HAREKET_DURUMU,
                            CARI_ID         = :CARI_ID,
                            KATEGORI_ID     = :KATEGORI_ID,
                            ISLEM_KAYNAGI_ID= 2,
                            KAYNAK_ID       = :KAYNAK_ID,
                            TUTAR           = :TUTAR,
                            FATURA_NO       = :FATURA_NO,
                            FATURA_TARIH    = :FATURA_TARIH,
                            ACIKLAMA        = :ACIKLAMA,
                            ODEME_TARIHI    = :ODEME_TARIHI,
                            KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID,
                            DURUM           = :DURUM";
            $data[':KAYNAK_ID']      = $alis_id;
            $data[':KAYIT_YAPAN_ID'] = $alis->KAYIT_YAPAN_ID;
            DB::insert($sql_gg, $data);
        }
    }
}
