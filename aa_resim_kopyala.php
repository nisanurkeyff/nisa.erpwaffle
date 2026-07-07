<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	ini_set('max_execution_time', 3000);
  	define('MAX_FILE_SIZE', 60000000);
  	ini_set("memory_limit", "-1");
    
    $filtre = array();
    $sql = "SELECT 
                U.*,
                YEAR(U.TARIH) AS YIL
            FROM URUN AS U
            WHERE 1
            ";
    $rows = DB::get($sql, $data);
    //var_dump2($rows);die;
	
	$dosya_yolu = $_SERVER['DOCUMENT_ROOT'] . "/img/resimler/"; // Dizin yolu
	$dosyalar = array_diff(scandir($dosya_yolu), array('..', '.')); // Geçersiz dizinleri filtrele
	//var_dump2($dosyalar);die;
	foreach ($dosyalar as $dosya) {
	    if (is_file($dosya_yolu . $dosya)) { // Sadece dosyaları listele (klasörleri hariç tut)

	    	$resim_adi = str_replace(array(' ','.jpg','.JPG','.jpeg','.pdf','.PDF','.PNG','.png','.xlsx','FTR'),'',$dosya);	    	
	    	$arr = explode('-',$resim_adi);
	    	$plaka = trim($arr[0]);
	    	$row_dosya = new StdClass();
	    	$row_dosya->PLAKA		= $resim_adi;
	    	$row_dosya->DOSYA_YOLU	= $dosya_yolu . $dosya;;
	    	$row_dosya->DOSYA_ADI	= $dosya;
	        $rows_dosya[$plaka] 	= $row_dosya;
	    }
	}
	
	//var_dump2($rows_dosya);die();
    
    $say = 0;
    foreach($rows as $key => $row){	
        /*
        if(count2($rows_evrak[$row->ID][54]) > 0) { //Ruhsat
        	echo "$key - {$row->PLAKA } evrak var<br>"; continue;
        }
        
        if(is_null($rows_dosya[$row->PLAKA])){
        	echo "$key - {$row->PLAKA } dosya yok<br>"; continue;
        }
        */
        
        $dosya_yolu = $rows_dosya[str_replace(' ','',$row->URUN)]->DOSYA_YOLU;
        if(is_null($dosya_yolu)) continue;
        
        $YOL = $_SERVER['DOCUMENT_ROOT'] . '/img/kahveduragi/urun/' . $row->ID . '/' . $row->YIL . '/';
        
        if(!file_exists($YOL)){
            mkdir($YOL, 0777, true);
        }

        $DOSYA_TAM_AD  = $rows_dosya[str_replace(' ','',$row->URUN)]->DOSYA_ADI;
        $DOSYA         = explode(".", $DOSYA_TAM_AD); 
        $DOSYA_AD      = $DOSYA[0]; 
        $DOSYA_UZANTI  = strtolower($DOSYA[count($DOSYA)-1]);

        // Benzersiz zaman damgası oluştur
        list($usec, $sec) = explode(' ', microtime());  
        $TIMESTAMP = str_replace('.', '', (((float)$usec + (float)$sec)));
        $TIMESTAMP = str_pad($TIMESTAMP, 14, "0", STR_PAD_RIGHT);

        $yeni_path = $YOL . $TIMESTAMP .".". $DOSYA_UZANTI;
        
        $resim_id = 0;
        if(copy($dosya_yolu, $yeni_path)){
            if(file_exists($yeni_path)){
                fncResimBoyut($yeni_path, 500, 500);
                $BOYUT = filesize($yeni_path);

                $data = array();
                $sql = "INSERT INTO URUN_RESIM SET  URUN_ID         = :URUN_ID,
                                                    RESIM_ADI       = :RESIM_ADI,
                                                    RESIM_ADI_ILK   = :RESIM_ADI_ILK,
                                                    BOYUT           = :BOYUT,
                                                    KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                    ";
                $data[":URUN_ID"]         = $row->ID;
                $data[':RESIM_ADI']       = $TIMESTAMP .".". $DOSYA_UZANTI;
                $data[':RESIM_ADI_ILK']   = $DOSYA_TAM_AD;
                $data[':BOYUT']           = $BOYUT;
                $data[":KAYIT_YAPAN_ID"]  = 1;
                $resim_id = DB::insert($sql, $data);
                
                if($resim_id > 0){
                    $say++;
                    if(file_exists($dosya_yolu)){
                        unlink($dosya_yolu);
                    }
                }
            }
        }
        //echo "$key - {$row->PLAKA} {$resim_id}<br>";;
    }
    
    echo "Toplam " . $say . " dosya başarıyla aktarıldı.";
	