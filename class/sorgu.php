<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class Sorgu2{	
	
	function __construct(){		
	}

	public function getSite() {

		$data = array();
		$sql = "SELECT * FROM SITE WHERE ID = :ID";
		$data[':ID'] = 1;
		$row = DB::getRow($sql, $data);

		return $row;
	}

	public function getKullanici() {

		$data = array();
		$sql = "SELECT 
					K.*,
					CONCAT_WS(' ',K.AD,K.SOYAD) AS UNVAN,
					Y.YETKI,
					Y.`INDEX` AS INDEX_URL,
					A.AVATAR
				FROM KULLANICI AS K 
					LEFT JOIN YETKI AS Y ON Y.ID = K.YETKI_ID
					LEFT JOIN AVATAR AS A ON A.ID = K.AVATAR_ID
				WHERE K.ID = :ID";
		$data[':ID'] 	= $_SESSION["kullanici_id"];
		$row = DB::getRow($sql, $data);

		return $row;
	}

	public function getAnaMenuler() {

		$data = array();
		$sql = "SELECT * FROM ANAMENU WHERE DURUM = :DURUM ORDER BY SIRA ASC";
		$data[':DURUM'] 	= 1;
		$rows = DB::get($sql, $data);

		return $rows;
	}

	public function getMenuler($request = array()){

        $data = array();
        $sql = "SELECT
                    M.*,
                    CONCAT('/views', M.LINK) AS LINK
                FROM MENU AS M
                WHERE FIND_IN_SET(:YETKI_ID, M.YETKI_IDS)
                    AND M.DURUM = 1
                ORDER BY M.SIRA, M.MENU
                ";
        $data[":YETKI_ID"]    = $_SESSION['yetki_id'];        
        $rows = DB::get($sql, $data);
        
        foreach($rows as $key => $row){            
            $d = explode('/', $row->ROUTE);
            if(strpos($row->LINK, '?') === false)   $row->LINK.= "?route=" . $row->ROUTE;            
            else                                    $row->LINK.= "&route=" . $row->ROUTE;            

            $rows2[$d[0]][] = $row;
        }

        return $rows2;
    }

	public function getFirma() {
		
		$data = array();
		$sql = "SELECT * FROM CARI WHERE ID = :ID";
		$data[':ID'] 	= $_SESSION['firma_id'];
		$row = DB::getRow($sql, $data);
		return $row;
	}

	public function getYetki($arrRequest = array()){

        $data = array();
        $sql = "SELECT
                    Y.*,
                    CONCAT('/views', Y.INDEX) AS INDEX2
                FROM YETKI AS Y
                WHERE 1
                GROUP BY 1
                ";

        $rows = DB::get($sql, $data);
        foreach($rows as $key => $row){
			$arr[$row->ID] = $row;
		}
        return $arr;
    }
}