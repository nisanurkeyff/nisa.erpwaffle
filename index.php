<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
	session_kontrol();
	$arr_yetki				= $cSorgu->getYetki();
	
	require_once ($_SERVER['DOCUMENT_ROOT'] . $arr_yetki[$_SESSION['yetki_id']]->INDEX2);