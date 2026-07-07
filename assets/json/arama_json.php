<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    session_kontrol();
    header('Content-Type: application/json');

    $data = array();
    $sql = "SELECT
                M.MENU AS name,
                A.ICON AS icon,
                CONCAT('/views', M.LINK) AS url
            FROM MENU AS M
                LEFT JOIN ANAMENU AS A ON A.ID = M.ANAMENU_ID
            WHERE FIND_IN_SET(:YETKI_ID, M.YETKI_IDS)
                AND M.DURUM = 1
            ORDER BY M.SIRA, M.MENU";

    $data[":YETKI_ID"] = $_SESSION['yetki_id'];        
    $rows_menuler = DB::get($sql, $data);

    // Sayfa verilerini oluştur
    $pages = array();
    foreach ($rows_menuler as $row) {
        $pages[] = [
            "name" => $row->name,
            "icon" => $row->icon,
            "url"  => $row->url
        ];
    }

    // JSON formatında çıktı
    $json = json_encode(["pages" => $pages], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo $json;
