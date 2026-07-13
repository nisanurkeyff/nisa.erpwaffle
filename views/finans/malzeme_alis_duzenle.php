<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
    if(isset($_GET['id']) && $_GET['id'] > 0) {
        $row = $cMalzemeAlis->getMalzemeAlis($_GET['id']);
        if($row) {
            header("Location: /views/finans/malzeme_alis_detay.php?id=" . $row->ID . "&token=" . $row->TOKEN);
            exit;
        }
    }
    header("Location: /views/finans/malzeme_alis_listesi.php");
    exit;
?>