<?
	function count2($array) {
	    return is_array($array) ? count($array) : 0;
	}

	function fncStdClass(&$obj){
		if (!isset($obj)) {
			$obj = new stdClass();
		}
	}

	function implode2($ayrac,$array) {
		return implode($ayrac, (is_array($array) ? $array : array()));
	}

	function var_dump2($str) {
		echo "<pre>";
		var_dump($str);
		echo "</pre>";
		
	}

	function session_kontrol(){
		if(!isset($_SESSION["kullanici_id"])) { 
			echo "<script type='text/javascript'>window.top.location='/views/giris.php';</script>"; exit;
		}
		
	}
	/*
	function session_kontrol() {
	    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');
	    $host = $_SERVER['HTTP_HOST'];
	    $url = $protocol . '://' . $host . '/views/giris.php';

	    if (empty($_SESSION["kullanici_id"])) {
	        header("Location: $url");
	        exit;
	    }
	}
	*/

	function routeActive($route){
		if(empty($_REQUEST['route'])) return "";
		
		$hedef 	= explode("/", $route);
		$kaynak	= explode("/", $_REQUEST['route']);
		if($hedef[0] == $kaynak[0] AND count($hedef) == 1 ){
			return "active";
		} 
		
		if($hedef[1] == $kaynak[1]){
			return "active";
		}
		
	}

	function fncSecilen($secilen, $deger) {
	    if (is_array($deger)) {
	        if (in_array($secilen, $deger)) {
	            return 'selected';
	        }
	    } else {
	        if ($secilen == $deger) {
	            return 'selected';
	        }
	    }
	    return '';
	}

	function fncSifreUret($ad = "", $soyad = "") {
	    $chars = array(
	        'Ç' => 'C', 'Ş' => 'S', 'İ' => 'I', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
	        'ç' => 'c', 'ş' => 's', 'ı' => 'i', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g'
	    );
	   
	    $ad 	= strtr($ad, $chars);
	    $soyad  = strtr($soyad, $chars);
	    
	    $kad = strtoupper(substr($ad, 0, 2) . substr($soyad, 0, 2));
	    $chars = "0123456789";
	    $sifre = $kad . substr(str_shuffle($chars), 0, 4);
	    return $sifre;
	}

	function fncTckGizle($tck) {
		if(is_null($tck) OR empty($tck)) return "";	

		$first = substr($tck, 0, 2);
    	$last  = substr($tck, -2);
    	$tck = $first."*******".$last;

		return $tck;
	}

	function fncTalepSql(&$data, &$sql){

		if($_SESSION['yetki_id'] == 3){
			$sql.=" AND T.SUREC_ID = :SUREC_ID";
			$data[":SUREC_ID"] = 3;
		}
	}

	function fncDurumRenk($durum){
		if(in_array($durum,array("0","Pasif"))){
			$html = '<span class="badge badge-danger">Pasif</span>';
		} else if(in_array($durum,array("1","Aktif"))){
			$html = '<span class="badge badge-success">Aktif</span>';
		}
		
		return $html;
	}

	function fncIsvalid($durum){
		if(in_array($durum,array("0","Pasif"))){
			$html = 'is-invalid';
		} else if(in_array($durum,array("1","Aktif"))){
			$html = 'is-valid';
		}
		
		return $html;
	}

	function fncImgPath($img, $firma = ""){
		if(!empty($firma)){
			$path = "/img/{$firma}/{$img}";
		}else{
			$path = "/img/{$img}";
		}

		return $path;
	}

	function fncImgPathSite($img) {
	    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
	    $path = $protocol . "://" . $_SERVER['SERVER_NAME'] . "/img/" . $img;
	    return $path;
	}


	function fnctelTemizle($tel){

		$tel = trim(str_replace(' ', '', $tel));
		$tel = trim(str_replace('(', '', $tel));
		$tel = trim(str_replace(')', '', $tel));
		$tel = trim(str_replace('-', '', $tel));
		$tel = "90" . $tel;
		
		return $tel;
	}

	function fncImgPathFolder($img, $firma){
		return $_SERVER['DOCUMENT_ROOT'] . "/img/{$firma}/{$img}/";
	}

	function fncImgPathFolder2($img, $firma){
		return $_SERVER['DOCUMENT_ROOT'] . "/img/{$firma}/{$img}";
	}

	function fncDocumentRoot($img){
		return $_SERVER['DOCUMENT_ROOT'] . $img;
	}

	function is_pdf($file){
		$arr = explode('.', $file);
		if(strtoupper($arr[count($arr)-1]) == "PDF"){
			return true;
		}
		return false;
	}

	function fncDurumSpan($durum){
		if(in_array($durum,array("0","Pasif"))){
			$html = '<span class="badge bg-label-danger rounded-pill">Pasif</span>';
		} else if(in_array($durum,array("1","Aktif"))){
			$html = '<span class="badge bg-label-success rounded-pill">Aktif</span>';
		}
		
		return $html;
	}

	function fncOdemeDurumSpan($durum){
		if(in_array($durum,array(1))){
			$html = '<span class="badge bg-label-warning rounded-pill">Ödeme Bekliyor</span>';
		} else if(in_array($durum,array(2))){
			$html = '<span class="badge bg-label-success rounded-pill">Ödendi</span>';
		} else if(in_array($durum,array(3))){
			$html = '<span class="badge bg-label-danger rounded-pill">Red</span>';
		}
		
		return $html;
	}

	function fncPopulerSpan($durum){
		if(in_array($durum,array("0","Pasif"))){
			$html = '<span class="badge bg-danger rounded-pill">Popüler Değil</span>';
		} else if(in_array($durum,array("1","Aktif"))){
			$html = '<span class="badge bg-success rounded-pill">Popüler</span>';
		}
		
		return $html;
	}

	function fncSurecSpan($durum){
		if(in_array($durum,array("1"))){
			$html = '<span class="badge bg-label-warning rounded-pill">Onay Bekliyor</span>';
		} else if(in_array($durum,array("3","Aktif"))){
			$html = '<span class="badge bg-label-success rounded-pill">Onaylandı</span>';
		}
		
		return $html;
	}

	function fncTokenKontrol($row){
		if($row->TOKEN != $_REQUEST['token']){
			header("refresh:0; url='/views/token_hata.php'");
		}
	}

	function fncCurl($url, $data = array()) {

	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

	    $response = curl_exec($ch);
	    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	    if (curl_errno($ch)) {
	        $error = curl_error($ch);
	        curl_close($ch);
	        return ['error' => $error];
	    }

	    curl_close($ch);
	    
	    return [
	        'httpCode' => $httpCode,
	        'response' => $response
	    ];
	}

	function mungXML($xml) {
	  	$obj = SimpleXML_Load_String($xml);
	    if ($obj === FALSE) return $xml;

	    // GET NAMESPACES, IF ANY
	    $nss = $obj->getNamespaces(TRUE);
	    if (empty($nss)) return $xml;

	    // CHANGE ns: INTO ns_
	    $nsm = array_keys($nss);
	    foreach ($nsm as $key)
	    {
	        // A REGULAR EXPRESSION TO MUNG THE XML
	        $rgx
	        = '#'               // REGEX DELIMITER
	        . '('               // GROUP PATTERN 1
	        . '\<'              // LOCATE A LEFT WICKET
	        . '/?'              // MAYBE FOLLOWED BY A SLASH
	        . preg_quote($key)  // THE NAMESPACE
	        . ')'               // END GROUP PATTERN
	        . '('               // GROUP PATTERN 2
	        . ':{1}'            // A COLON (EXACTLY ONE)
	        . ')'               // END GROUP PATTERN
	        . '#'               // REGEX DELIMITER
	        ;
	        // INSERT THE UNDERSCORE INTO THE TAG NAME
	        $rep
	        = '$1'          // BACKREFERENCE TO GROUP 1
	        . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
	        ;
	        // PERFORM THE REPLACEMENT
	        $xml =  preg_replace($rgx, $rep, $xml);
	    }
	    return $xml;
    	
	}

	function arrayIndex($rows){
		$rows_yeni = array();
		
		foreach($rows as $key => $row){
			$rows_yeni[$row->ID]	= $row;
		}
		
		return $rows_yeni;
	}

	function fncTre($value){
		if (in_array($value,array(NULL,'0'))) {
			return '-';
		}else{
			return $value;
		}
	}

	function fncBackend($path){
		return '/back' . $path;
	}

	function fncFrontend($path){
		return '/front' . $path;
	}

	function fncIndirimHesapla($fiyat, $oran){
		$oran = (100 - $oran) / 100;
		$fiyat = $fiyat * $oran;
		return FormatSayi::sayi($fiyat);
	}

	function fncIndirimHesaplaDB($fiyat, $oran){
		$oran = (100 - $oran) / 100;
		$fiyat = $fiyat * $oran;
		return $fiyat;
	}

	function fncEtiketOlustur($kg) {
	    $urunler = [];
	    $birimler = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1];

	    // 2 kg için özel kural: her 1 kg için etiket oluştur
	    if ($kg == 2) {
	        while ($kg > 0) {
	            $urunler[] = 1;
	            $kg -= 1;
	        }

	    }else if ($kg == 12) {
	        $urunler[] = 10;
	        $urunler[] = 1;
	        $urunler[] = 1;

	    }else if ($kg == 21) {
	        $urunler[] = 10;
	        $urunler[] = 11;

	    }else if ($kg == 22) {
	        $urunler[] = 11;
	        $urunler[] = 11;

	    } else {
	        foreach ($birimler as $birim) {
	            while ($kg >= $birim) {
	                $urunler[] = $birim;
	                $kg -= $birim;
	            }
	        }
	    }

	    return $urunler;
	}

	function fncEnglish($text) {
	    $turkish = ['ç', 'ğ', 'ı', 'İ', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'Ö', 'Ş', 'Ü'];
	    $english = ['c', 'g', 'i', 'i', 'o', 's', 'u', 'C', 'G', 'O', 'S', 'U'];
	    return str_replace($turkish, $english, $text);
	}

	function fncTurkish($text) {
	    $turkish = ['ç', 'ğ', 'ı', 'İ', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'Ö', 'Ş', 'Ü','I'];
	    $english = ['c', 'g', 'i', 'i', 'o', 's', 'u', 'C', 'G', 'O', 'S', 'U','İ'];
	    return str_replace($english, $turkish, $text);
	}

	function fncBuyukHarf($text) {
	    $search  = ['ç','ğ','ı','ö','ş','ü','Ç','Ğ','İ','Ö','Ş','Ü'];
	    $replace = ['C','G','I','O','S','U','C','G','I','O','S','U'];
	    $text = str_replace($search, $replace, $text);
	    return mb_strtoupper($text, 'UTF-8');
	}

	//fncIslemLog($ID, $this->cdbPDO->getSQL($sql, $data), $row, __FUNCTION__, "TABLE", "SAYFA");
	function fncIslemLog ($ID, $KAYIT_SQL, $ROW, $ISLEM, $TABLO, $SAYFA){
		
		$SORGU 	= trim(preg_replace('/\s\s+/', ' ', $KAYIT_SQL));
		$ROW_JSON = json_encode($ROW);

		$data = array();
		$sql = "INSERT INTO ISLEM_LOG SET 	URUN_ID			= :URUN_ID,
											SAYFA			= :SAYFA,
											TABLO			= :TABLO,
											ISLEM			= :ISLEM,
											KULLANICI_ID	= :KULLANICI_ID,
											SORGU			= :SORGU,
											ROW				= :ROW_JSON
											";
		$data[":URUN_ID"] 		= $ID;
		$data[":SAYFA"] 		= $SAYFA;
		$data[":TABLO"] 		= $TABLO;
		$data[":ISLEM"] 		= $ISLEM;
		$data[":KULLANICI_ID"] 	= $_SESSION['kullanici_id'];
		$data[":SORGU"] 		= $SORGU;
		$data[":ROW_JSON"] 		= $ROW_JSON;
		$insert = DB::exec($sql, $data);
	}

	function fncResimBoyut($file, $maxWidth, $maxHeight) {
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
	    $imageExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

	    // Resmin formatına göre açma işlemi
	    switch ($imageExtension) {
	        case 'jpeg':
	        case 'jpg':
	            $source = imagecreatefromjpeg($file);
	            $newImage = imagecreatetruecolor($newWidth, $newHeight);
	            break;
	        case 'png':
	            $source = imagecreatefrompng($file);
	            $newImage = imagecreatetruecolor($newWidth, $newHeight);

	            // PNG için şeffaflığı koruma
	            imagealphablending($newImage, false);
	            imagesavealpha($newImage, true);
	            $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
	            imagefill($newImage, 0, 0, $transparent);
	            break;
	        case 'webp':
	            $source = imagecreatefromwebp($file);
	            $newImage = imagecreatetruecolor($newWidth, $newHeight);

	            // WEBP için şeffaflığı koruma
	            imagealphablending($newImage, false);
	            imagesavealpha($newImage, true);
	            $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
	            imagefill($newImage, 0, 0, $transparent);
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

	function fncYuvarla($sayi, $deger) {
	    return ceil($sayi / $deger) * $deger;
	}

	function fncImgPathFirma($firma, $path, $id, $yil){
        $yol = "img/{$firma}/{$path}/{$id}/{$yil}/";
        return $yol;
    }

    function in_array2($deger, array $gecerliDegerler) {
	    return isset($deger) && in_array($deger, $gecerliDegerler, true);
	}

	function fncSiparisSurecSpan($durum){
		if(in_array($durum,array("1","Tamamlandı"))){
			$html = '<span class="badge bg-label-success rounded-pill">Tamamlandı</span>';
		} else if(in_array($durum,array("2","İptal"))){
			$html = '<span class="badge bg-label-danger rounded-pill">İptal</span>';
		} else if(in_array($durum,array("3","Test Sipariş"))){
			$html = '<span class="badge bg-label-warning rounded-pill">Test Sipariş</span>';
		}
		
		return $html;
	}