<?
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

	$rows = $cTrendyol->getSiparisler();
	var_dump2(json_decode($rows["response"]));die;