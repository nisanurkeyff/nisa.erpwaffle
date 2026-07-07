<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

	$filtre = array();
    $sql = "SELECT 
				U.*,
				YEAR(U.TARIH) AS YIL
			FROM URUN AS U
			WHERE U.ID <= 52
			ORDER BY U.ID DESC
			";
	$rows = DB::get($sql, $data);

	$say = 0;
	foreach ($rows as $key => $row) {

		$arrContextOptions=array(
	      "ssl"=>array(
	            "verify_peer"=>false,
	            "verify_peer_name"=>false,
	        ),
	    );

		$url = "https://menuyonetim.ozgurh.tr/resim/" . str_replace(' ', '%20', $row->URUN) . ".png";
		$headers = @get_headers($url);

		if ($headers && strpos($headers[0], '200') !== false) {
		    $result = file_get_contents($url, false, stream_context_create($arrContextOptions));
		} else {
		    $url = "https://menuyonetim.ozgurh.tr/resim/" . str_replace(' ', '%20', fncTurkish($row->URUN)) . ".png";
		    $headers = @get_headers($url);

		    if ($headers && strpos($headers[0], '200') !== false) {
		        $result = file_get_contents($url, false, stream_context_create($arrContextOptions));
		    } else {
		        continue;
		    }
		}

		$yol = "img/urun/" . $row->ID . "/" . $row->YIL . "/";
		if(!file_exists($yol)){
		    mkdir($yol, 0777, true);
		}

		$DOSYA_TAM_AD  = strtolower(fncEnglish(str_replace(' ','_',$row->URUN))).".png";
		$DOSYA         = explode(".", $DOSYA_TAM_AD); 
		$DOSYA_AD      = $DOSYA[0]; 
		$DOSYA_UZANTI  = strtolower($DOSYA[count($DOSYA)-1]);

		// Benzersiz zaman damgası oluştur
		list($usec, $sec) = explode(' ', microtime());  
		$TIMESTAMP = str_replace('.', '', (((float)$usec + (float)$sec)));
		$TIMESTAMP = str_pad($TIMESTAMP, 14, "0", STR_PAD_RIGHT);

		// Dosyayı kaydet
		$DOSYA_YOLU = $yol . $TIMESTAMP .".". $DOSYA_UZANTI;
		file_put_contents($DOSYA_YOLU, $result);

		fncResimBoyut($DOSYA_YOLU, 500, 500);

        $data = array();
        $sql = "INSERT INTO URUN_RESIM SET  URUN_ID         = :URUN_ID,
                                            RESIM_ADI       = :RESIM_ADI,
                                            RESIM_ADI_ILK   = :RESIM_ADI_ILK,
                                            KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                            ";
        $data[":URUN_ID"]         = $row->ID;
        $data[':RESIM_ADI']       = $TIMESTAMP .".". $DOSYA_UZANTI;
        $data[':RESIM_ADI_ILK']   = $DOSYA_TAM_AD;
        $data[":KAYIT_YAPAN_ID"]  = 1;
        $id = DB::insert($sql, $data);
        if ($id > 0)$say++;
	}

	echo $say . " KADAR RESIM KAYIT EDİLDİ";
