<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

	
	$data = array();
	$sql = "SELECT * FROM SUBE WHERE 1";
	$rows_sube = DB::get($sql, $data);
	//var_dump2($rows_sube);

	$data = array();
	$sql = "SELECT * FROM URUN WHERE 1";
	$rows_urun = DB::get($sql, $data);
	//var_dump2($rows_urun);die;

	$say = 0;
	foreach ($rows_sube as $key => $row_sube) {

		foreach ($rows_urun as $key => $row_urun) {
			
			$data = array();
            $sql = "INSERT INTO URUN_FIYAT SET  URUN_ID         = :URUN_ID,
                                                KATEGORI_ID     = :KATEGORI_ID,
                                                SUBE_ID         = :SUBE_ID,
                                                FIYAT           = :FIYAT,
                                                KAYIT_YAPAN_ID  = :KAYIT_YAPAN_ID
                                                ";
            $data[":URUN_ID"]           = $row_urun->ID;
            $data[":KATEGORI_ID"]       = $row_urun->KATEGORI_ID;
            $data[":SUBE_ID"]           = $row_sube->ID;
            $data[":FIYAT"]             = $row_urun->FIYAT;
            $data[":KAYIT_YAPAN_ID"]    = $_SESSION['kullanici_id'];
            $id = DB::insert($sql, $data);
            if($id > 0) $say++;

		}
	}

	echo $say;
	