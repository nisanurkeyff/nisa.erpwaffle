<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    
    $data = array();
    $sql = "UPDATE KAMPANYA SET DURUM = 0 WHERE STR_TO_DATE(CONCAT(BIT_TARIH, ' ', BIT_SAAT), '%Y-%m-%d %H:%i') < :TARIH_SAAT";
    $data["TARIH_SAAT"] = date("Y-m-d H:i:s");
    $update = DB::exec($sql, $data);

    echo "Süresi Geçen Kampanyalar Kapatıldı";    