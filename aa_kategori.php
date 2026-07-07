<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

	
	$data = array();
	$sql = "SELECT * FROM URUN WHERE 1";
	$rows = DB::get($sql, $data);

	$say = 0;
	foreach ($rows as $key => $row) {

		$data = array();
		$sql = "SELECT * FROM KATEGORI WHERE KATEGORI LIKE :KATEGORI";
		$data[':KATEGORI'] = "%". $row->KATEGORI . "%";
		$row_kategori = DB::getRow($sql, $data);

		$data = array();
		$sql = "UPDATE URUN SET KATEGORI_ID = :KATEGORI_ID WHERE ID = :ID";
		$data[":KATEGORI_ID"] 	= $row_kategori->ID;
		$data[":ID"] 			= $row->ID;
		$update = DB::exec($sql, $data);
		if($update) $say++;
	}

	echo $say;
	