<?
#################################################################
# 			db Kayit - Database Insert-Update-Delete			#
# 				ConquerorRose - rosesoft						#
#				Özgür Hatipoğlu - 03.09.2024					#
# 	En alt kısımda örnek function olarak oluşturuldu.			#
#################################################################

	require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');	
		
	if(isset($_REQUEST["islem"])) {
		$sonuc 	= array();
		$islem 	= $_REQUEST["islem"];
		
	} else { 
		$sonuc["HATA"] 		= TRUE;
		$sonuc["ACIKLAMA"] 	= "Giriş Yasak.".$_REQUEST["islem"];
		echo json_encode($sonuc); die();
		
	}
	
		if(!in_array($islem, array('giris_yap','mesaj_kaydet','stok_bilgisi','iletisim_kaydet','sepete_ekle','odeme_yap'))) {
		if(!$_SESSION['kullanici_id']){
			$sonuc["HATA"] 		= TRUE;
			$sonuc["ACIKLAMA"] 	= "Üye olmalısınız!";
			echo json_encode($sonuc); die();
		}
	}
	
	if(!method_exists($cKayit, $islem)) {
		$sonuc["HATA"] 		= TRUE;
		$sonuc["ACIKLAMA"] 	= "Fonksiyon bulunamadı.";
		echo json_encode($sonuc); die();
	}
	
	$sonuc = $cKayit->{$islem}();
	
	echo json_encode($sonuc); die();
	
?>