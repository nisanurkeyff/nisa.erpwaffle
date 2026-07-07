<?

	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

    $data = array();
    $sql = "SELECT 
                TRENDYOL_SIPARIS_ID AS ID,
                ID AS SIPARIS_ID
            FROM SIPARIS AS UR
            WHERE 1
            ";
    $rows_kontrol = DB::get($sql, $data);
    $rows_kontrol = arrayIndex($rows_kontrol);

    $data = array();
    $sql = "SELECT 
                SS.TRENDYOL_SUREC AS ID,
                SS.ID AS SUREC_ID
            FROM SIPARIS_SUREC AS SS
            WHERE SS.DURUM = 1
            ";
    $rows_surec = DB::get($sql, $data);
    $rows_surec = arrayIndex($rows_surec);

	$rows = $cTrendyol->getTumSiparisler();

	$say = 0;
    $update_say = 0;
	foreach ($rows as $key => $row) {
        
        if ($rows_kontrol[$row->orderId]->SIPARIS_ID > 0){

            $data = array();
            $sql = "UPDATE SIPARIS SET  SIPARIS_NO              = :SIPARIS_NO,
                                        SIPARIS_SUREC_ID        = :SIPARIS_SUREC_ID,
                                        TUTAR                   = :TUTAR,
                                        TELEFON                 = :TELEFON,
                                        MUSTERI_ID              = :MUSTERI_ID,
                                        MUSTERI                 = :MUSTERI,
                                        ODEME                   = :ODEME,
                                        SIPARIS_NOT             = :SIPARIS_NOT,
                                        TAHMINI_TESLIMAT        = :TAHMINI_TESLIMAT,
                                        SIPARIS_TARIH           = :SIPARIS_TARIH,
                                        HAZIRLANMA_TARIH        = :HAZIRLANMA_TARIH,
                                        INDIRIM                 = :INDIRIM,
                                        INDIRIM_TUTAR           = :INDIRIM_TUTAR
                                    WHERE ID = :ID
                                    ";
            $data[":SIPARIS_NO"]            = $row->orderNumber;
            $data[":SIPARIS_SUREC_ID"]      = $rows_surec[$row->packageStatus]->SUREC_ID;
            $data[":TUTAR"]                 = $row->totalPrice;
            $data[":TELEFON"]               = $row->callCenterPhone;
            $data[":MUSTERI_ID"]            = $row->customer->id;
            $data[":MUSTERI"]               = $row->customer->firstName . " " . $row->customer->lastName;
            $data[":ODEME"]                 = $row->payment->paymentType;
            $data[":SIPARIS_NOT"]           = $row->customerNote;
            $data[":TAHMINI_TESLIMAT"]      = $row->eta;
            $data[":SIPARIS_TARIH"]         = date('Y-m-d H:i:s', intval($row->packageCreationDate / 1000));
            $data[":HAZIRLANMA_TARIH"]      = date('Y-m-d H:i:s', intval($row->packageModificationDate / 1000));
            $data[":INDIRIM"]               = $row->promotions[0]->description;
            $data[":INDIRIM_TUTAR"]         = $row->promotions[0]->totalSellerAmount;
            $data[":ID"]                    = $rows_kontrol[$row->orderId]->SIPARIS_ID;
            $update = DB::exec($sql, $data);
            if ($update > 0) $update_say++;

        }else{

            $data = array();
            $sql = "INSERT INTO SIPARIS SET TRENDYOL_SIPARIS_ID     = :TRENDYOL_SIPARIS_ID,
                                            SIPARIS_NO              = :SIPARIS_NO,
                                            SIPARIS_SUREC_ID        = :SIPARIS_SUREC_ID,
                                            TUTAR                   = :TUTAR,
                                            TELEFON                 = :TELEFON,
                                            MUSTERI_ID              = :MUSTERI_ID,
                                            MUSTERI                 = :MUSTERI,
                                            ODEME                   = :ODEME,
                                            SIPARIS_NOT             = :SIPARIS_NOT,
                                            TAHMINI_TESLIMAT        = :TAHMINI_TESLIMAT,
                                            SIPARIS_TARIH           = :SIPARIS_TARIH,
                                            HAZIRLANMA_TARIH        = :HAZIRLANMA_TARIH,
                                            KAYIT_YAPAN_ID          = 1,
                                            TOKEN                   = MD5(:TRENDYOL_SIPARIS_ID)
                                            ";
            $data[":TRENDYOL_SIPARIS_ID"]   = $row->orderId;
            $data[":SIPARIS_NO"]            = $row->orderNumber;
            $data[":SIPARIS_SUREC_ID"]      = $rows_surec[$row->packageStatus]->SUREC_ID;
            $data[":TUTAR"]                 = $row->totalPrice;
            $data[":TELEFON"]               = $row->callCenterPhone;
            $data[":MUSTERI_ID"]            = $row->customer->id;
            $data[":MUSTERI"]               = $row->customer->firstName . " " . $row->customer->lastName;
            $data[":ODEME"]                 = $row->payment->paymentType;
            $data[":SIPARIS_NOT"]           = $row->customerNote;
            $data[":TAHMINI_TESLIMAT"]      = $row->eta;
            $data[":SIPARIS_TARIH"]         = date('Y-m-d H:i:s', intval($row->packageCreationDate / 1000));
            $data[":HAZIRLANMA_TARIH"]      = date('Y-m-d H:i:s', intval($row->packageModificationDate / 1000));
            $id = DB::insert($sql, $data);
            if ($id > 0) $say++;

            foreach ($row->lines as $key => $row_detay) {

                $adet = count($row_detay->items);
                $tutar = ($row_detay->unitSellingPrice ?? $row_detay->price) * $adet;

                $ekstra_json = json_encode($row_detay->extraIngredients);
                $cikarilan_json  = json_encode($row_detay->removedIngredients);

                // SIPARIS_DETAY INSERT
                $data = array();
                $sql = "INSERT INTO SIPARIS_DETAY SET   SIPARIS_ID          = :SIPARIS_ID,
                                                        SIPARIS_NO          = :SIPARIS_NO,
                                                        TRENDYOL_URUN_ID    = :TRENDYOL_URUN_ID,
                                                        URUN                = :URUN,
                                                        FIYAT               = :FIYAT,
                                                        ADET                = :ADET,
                                                        TUTAR               = :TUTAR,
                                                        KUPON               = :KUPON,
                                                        PROMOSYON           = :PROMOSYON,
                                                        DEGISEN_MALZEME     = :DEGISEN_MALZEME,
                                                        EKSTRA_MALZEME      = :EKSTRA_MALZEME,
                                                        CIKARILAN_MALZEME   = :CIKARILAN_MALZEME
                                                        ";
                $data[":SIPARIS_ID"]               = $id;
                $data[":SIPARIS_NO"]               = $row->orderNumber;
                $data[":TRENDYOL_URUN_ID"]         = $row_detay->productId;
                $data[":URUN"]                     = $row_detay->name;
                $data[":FIYAT"]                    = $row_detay->price;
                $data[":ADET"]                     = $adet;
                $data[":TUTAR"]                    = $tutar;
                $data[":KUPON"]                    = $row_detay->coupon ?? null;
                $data[":PROMOSYON"]                = $row_detay->customerNote ?? null;
                $data[":DEGISEN_MALZEME"]          = $row_detay->customerNote ?? null;
                $data[":EKSTRA_MALZEME"]           = $ekstra_json;
                $data[":CIKARILAN_MALZEME"]        = $cikarilan_json;
                $detay_id = DB::insert($sql, $data);

                // SIPARIS_EKSTRA INSERT
                if (!empty($row_detay->extraIngredients)) {
                    foreach ($row_detay->extraIngredients as $row_ekstra) {
                        $data = array();
                        $sql = "INSERT INTO SIPARIS_EKSTRA SET  SIPARIS_DETAY_ID = :DETAY_ID,
                                                                MALZEME_ID       = :MALZEME_ID,
                                                                MALZEME_AD       = :MALZEME_AD,
                                                                FIYAT            = :FIYAT
                                                                ";
                        $data[':DETAY_ID']      = $detay_id;
                        $data[':MALZEME_ID']    = $row_ekstra->id;
                        $data[':MALZEME_AD']    = $row_ekstra->name;
                        $data[':FIYAT']         = $row_ekstra->price;
                        DB::insert($sql, $data);
                    }
                }

                if (!empty($row_detay->removedIngredients)) {
                    foreach ($row_detay->removedIngredients as $row_cikarilan) {
                        $data = array();
                        $sql = "INSERT INTO SIPARIS_CIKARILAN SET   SIPARIS_DETAY_ID    = :DETAY_ID,
                                                                    MALZEME_ID          = :MALZEME_ID,
                                                                    MALZEME_AD          = :MALZEME_AD
                                                                    ";
                        $data[':DETAY_ID']      = $detay_id;
                        $data[':MALZEME_ID']    = $row_cikarilan->id;
                        $data[':MALZEME_AD']    = $row_cikarilan->name;
                        DB::insert($sql, $data);
                    }
                }

            }
        }
	}

	echo '
    <div style="max-width:600px; margin:40px auto; padding:25px; background:#111; color:#00ff88; font-family:monospace; border-radius:12px; box-shadow:0 0 25px rgba(0,255,136,0.3);">
        <h2 style="margin-top:0;color:#fff;">🚀 Trendyol Sipariş Entegrasyonu</h2>
        <p><strong>Tarih:</strong> '.date('Y-m-d H:i:s').'</p>
        <p><strong>Eklenen Sipariş:</strong> '.$say.'</p>
        <p><strong>Güncellenen Sipariş:</strong> '.$update_say.'</p>
        <p style="color:#00ff88;"><strong>Durum:</strong> Başarıyla tamamlandı</p>
    </div>
    ';