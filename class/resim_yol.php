<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');

    class ResimYol{

        private $site;

        function __construct($row_site = ""){
            $this->site         = $row_site;
        }

        public function imgPathFirma($path, $id, $yil){
            $yol = "/img/{$this->site->IMG_PATH}/{$id}/{$yil}/";
            return $yol;
        }
    }

?>