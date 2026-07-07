<?

require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

class Select2 {
	    
    private $rows;
    private $sql;
	private $secilen;
	private $tumuValue;
	private $tumuName;
	private $name;
	private $class;
	private $data;
	private $secim;
	private $tumu;
	private $clss;

	function __construct() {        
	}

	// Sql kodunun ne olduğunun öğrenmesi adına debug için kullanmaya
    public function setTemizle(){
        $this->secim = "";
		$this->tumu = "";
		$this->name = "";
		$this->tumuValue = "";
        $this->tumuName = "";
        $this->clss = "";
        $this->data = "";
        $this->rows = "";

    }

	// sadece dataların çıplak olarak dönmesini sağlıyor
    public function getData(){
        return $this->rows;
    }

    public function setData($rows) {
        $this->rows = $rows; // Veriler rows'a atanıyor
        return $this;
    }

    // seçilen datanın gösterilmesi
    public function setSecilen($secilen = ""){

    	if(!empty($secilen) OR $secilen == "0") {
    		if(is_array($secilen)){
				$this->secilen = $secilen;

			}else if(strrpos($secilen,',')){
				$this->secilen = explode(',', $secilen);

			}else {
				$this->secilen = $secilen;
			}

    	} else {
			$this->secilen = -1;
		}

        return $this;
    }

    // name verme için radio, checkbox
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    // Class bilgisini verme için radio, checkbox
    public function setClass($class){
        $this->class = $class;
        return $this;
    }

    // Tümü seçeneği değerlerinin olup olmaması
    public function setTumu($tumuValue = "", $tumuName = ""){

    	if(empty($tumuValue)){
			$this->data .= "<option value='-1'>-- " . "Tümü" . " --</option>";

		} else{
			$this->data .= "<option value='$tumuValue'> $tumuName </option>";

		}
        return $this;

    }

    // Tümü seçeneği değerlerinin boş olması
    public function setTumu2(){

        $this->data .= "<option value='-1'> " . "Tümü" . " </option>";

        return $this;

    }

    // Seçiniz seçeneği değerlerinin olup olmaması
    public function setSeciniz($tumuValue = "", $tumuName = ""){

    	if(empty($tumuValue)){
			$this->data .= "<option value='-1'>" . "Seçiniz" . "</option>";

		} else{
			$this->data .= "<option value='$tumuValue'> $tumuName </option>";

		}
        return $this;

    }

	// <option></option> olarak ekrana basılmasına hazır haline getirilmesi
    public function getSelect($value = "ID", $d = "AD", $title = "TITLE"){

        foreach($this->rows as $key=>$row) {
        	if(is_array($this->secilen)){
				if( in_array($row->$value, $this->secilen) )
	        		$this->data .= "<option value=".$row->$value." title='".$row->$title."' selected >".$row->$d."</option>";
	        	else
	        		$this->data .= "<option value=".$row->$value." title='".$row->$title."' >".$row->$d."</option>";

			} else {
				if( ($this->secilen == $row->$value AND isset($this->secilen)) OR in_array($row->$value, explode(",",$this->secilen)) )
	        		$this->data .= "<option value=".$row->$value." title='".$row->$title."' selected >".$row->$d."</option>";
	        	else
	        		$this->data .= "<option value=".$row->$value." title='".$row->$title."' >".$row->$d."</option>";
			}

        }
        return $this->data;

    }

    public function getMultiSelect($value, $d, $title = "TITLE"){

        foreach($this->rows as $key=>$row){
        	if($arr[$row->YETKI]){
				array_push($arr[$row->YETKI], [$d=>$row->$d, $value=>$row->$value]);
			}else{
				$arr[$row->YETKI] = array([$d=>$row->$d, $value=>$row->$value]);
			}
		}

        foreach($arr as $key=>$row) {
        	$this->data .= "<optgroup label='".$key."'>";
        	foreach($row as $k =>$v){
	    		$this->data .= "<option value=".$v[$value]." title='".$v[$title]."' >".$v[$d]."</option>";
			}
			$this->data .= "</optgroup>";
        }

        return $this->data;

    }

    // <input type="checkbox" name="checkbox" value="1" checked>1
    public function getCheckbox($value, $d){

        foreach($this->rows as $key=>$row) {
        	if($this->secilen == $row->$value)
        		$this->data .= "<label> <input type='checkbox' class='".$this->clss."' name='".$this->name."' value=".$row->$value." checked>".$row->$d."</label>";
        	else
        		$this->data .= "<label> <input type='checkbox' class='".$this->clss."' name='".$this->name."' value=".$row->$value.">".$row->$d."</label>";
        }
        return $this->data;
    }

    // <input type="radio" name="radio" value="1" checked>1
    public function getRadio($value, $d){

        foreach($this->rows as $key=>$row) {
        	if($this->secilen == $row->$value)
        		$this->data .= "<label> <input type='radio' class='".$this->clss."' name='".$this->name."' value=".$row->$value." checked>".$row->$d."</label>";
        	else
        		$this->data .= "<label> <input type='radio' class='".$this->clss."' name='".$this->name."' value=".$row->$value.">".$row->$d."</label>";
        }
        return $this->data;
    }

	// Sql kodunun ne olduğunun öğrenmesi adına debug için kullanmaya
    public function getSql(){
        return $this->sql;
    }

	public function FavoriDurum(){

		$rows[] = new cRow("1","Favorilerim");
		$rows[] = new cRow("2","Favorilerim Hariç");

		$this->SetTemizle();
		$this->rows = $rows;
        $this->sql = $sql;
		return $this;

	}

	public function Kategoriler() {
        $data = array();
        $sql = "SELECT
                    K.ID,
                    K.KATEGORI AS AD
                FROM KATEGORI AS K
                WHERE K.DURUM = 1
                ORDER BY 2";

        $rows = DB::get($sql, $data);
        $this->SetTemizle();
		$this->rows = $rows;
        $this->sql = $sql;
		return $this;
    }

    public function Ulkeler() {
        $data = array();
        $sql = "SELECT
                    U.ID,
                    U.ULKE AS AD
                FROM ULKE AS U
                WHERE U.DURUM = 1
                ORDER BY 2
                ";

        $rows = DB::get($sql, $data);
        $this->SetTemizle();
		$this->rows = $rows;
        $this->sql = $sql;
		return $this;
    }

}