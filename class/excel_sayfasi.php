<?
    
    class excelSayfasi {
    	private $Excel = array();
			
		public function sutunEkle($Baslik, $Kolon, $VeriTipi){
			$D = new stdClass();
			$D->Baslik 		= $Baslik;
			$D->Kolon 		= $Kolon;
			$D->VeriTipi 	= $VeriTipi;
			$this->Excel[] 	= $D;
		}
		public function excel(){
			return $this->Excel;
		}
		
	}