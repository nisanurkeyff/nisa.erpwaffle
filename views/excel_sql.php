<?
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/class/config.php');
    
    use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
    use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
    use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
    use Box\Spout\Common\Entity\Style\CellAlignment;
    use Box\Spout\Common\Entity\Style\Border;
    
    ini_set('max_execution_time', 300);
    ini_set('memory_limit', -1);
    
    $Table          = $_SESSION["Table"];
    $Excel          = $_SESSION["excel"];
    $ExcelDosyaAdi  = $Table["excelDosyaAdi"] ?? date("YmdHis");
    $sql            = $Table["excel_sql"];
    
    $rows = !empty($sql) ? DB::get($sql) : [];

    $writer = WriterEntityFactory::createXLSXWriter();
    $writer->openToFile($ExcelDosyaAdi . '.xlsx');
    
    // Stil tanımlamaları
    $textStyle = (new StyleBuilder())
        ->setFormat('@')
        ->setShouldWrapText(false)
        ->setCellAlignment(CellAlignment::LEFT)
        ->build();

    $borderThin = (new BorderBuilder())
        ->setBorderTop('000000', Border::WIDTH_THIN)
        ->setBorderBottom('000000', Border::WIDTH_THIN)
        ->setBorderLeft('000000', Border::WIDTH_THIN)
        ->setBorderRight('000000', Border::WIDTH_THIN)
        ->build();
    
    $headerStyle = (new StyleBuilder())
        ->setFontBold()
        ->setFontColor('FFFFFF')
        ->setBackgroundColor('4679cc')
        ->setBorder($borderThin)
        ->build();
    
    $whiteRowStyle = (new StyleBuilder())
        ->setFontColor('000000')
        ->setBackgroundColor('FFFFFF')
        ->setBorder($borderThin)
        ->build();
    
    $coloredRowStyle = (new StyleBuilder())
        ->setFontColor('000000')
        ->setBackgroundColor('dbeaff')
        ->setBorder($borderThin)
        ->build();
    
    $tarih = (new StyleBuilder())
        ->setFormat('dd.mm.yyyy')
        ->setBorder($borderThin)
        ->build();
    
    $tarihSaat = (new StyleBuilder())
        ->setFormat('dd.mm.yyyy hh:mm:ss')
        ->setBorder($borderThin)
        ->build();
    
    $FormatSayiNokta2 = (new StyleBuilder())
        ->setFormat('#,##0.00')
        ->setShouldWrapText(false)
        ->setCellAlignment(CellAlignment::RIGHT)
        ->build();
    
    $FormatSayiVirgul2 = (new StyleBuilder())
        ->setFormat('@')
        ->setShouldWrapText(false)
        ->setCellAlignment(CellAlignment::RIGHT)
        ->build();
    
    $headerRow = array_map(function ($column) {
        return WriterEntityFactory::createCell($column->Baslik);
    }, $Excel);
    
    $writer->addRow(WriterEntityFactory::createRow($headerRow, $headerStyle));
    
    $isWhiteRow = true;
    
    foreach ($rows as $j => $row) {
        
        $dataRow = [];
        foreach ($Excel as $column) {
            
            $Kolon = $column->Kolon;
    
            if($Kolon == 'SIRA') {
                $Hucre = $j + 1;
            }else {
                $Hucre = $row->$Kolon;
            }

            /*
            if(strlen($Hucre) > 32767) {
                $Hucre = substr($Hucre, 0, 32767);
            }
            */
            if($Hucre == "0000-00-00"){
                $Hucre = "";
            }

            if(empty($Hucre) OR is_null($Hucre)){
                $Hucre = "";
            }

            if($column->VeriTipi == "format1" && $Hucre >= "1900-01-01") {
                if(strlen(trim($Hucre)) == 10){
                    $dateTime = \DateTime::createFromFormat('Y-m-d', $Hucre);
                }else{
                    $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $Hucre);
                }
    
                if($dateTime) {
                    $excelDateValue = $dateTime->format('U') / 86400 + 25569;
                    $dataRow[] = WriterEntityFactory::createCell($excelDateValue, $tarih);
                }else {
                    $dataRow[] = WriterEntityFactory::createCell('');
                }
                continue;
            }
    
            if($column->VeriTipi == "format2" && $Hucre >= "1900-01-01") {
                if(strlen(trim($Hucre)) == 10){
                    $dateTime = \DateTime::createFromFormat('Y-m-d', $Hucre);
                }else{
                    $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $Hucre);
                }
    
                if($dateTime) {
                    $dateTime->modify('+3 hours');
                    $excelDateValue = $dateTime->format('U') / 86400 + 25569;
                    $dataRow[] = WriterEntityFactory::createCell($excelDateValue, $tarihSaat);
                }else {
                    $dataRow[] = WriterEntityFactory::createCell('');
                }
                continue;
            }

            if ($column->VeriTipi == "html_temizle") {                
                $Hucre = strip_tags($Hucre);
                $Hucre = html_entity_decode($Hucre);
                $Hucre = trim(preg_replace('/\s+/', ' ', $Hucre));
            } 
    
            if($column->VeriTipi == "Bakiye") {
                $BAKIYE += $row->BORC + $row->ALACAK;
                $Hucre = $BAKIYE;
            }
    
            if($column->VeriTipi == "FormatSayi::virgul2") {
                if(empty($Hucre) OR is_null($Hucre) OR $Hucre == "") $Hucre = 0;
                $formattedValue = number_format($Hucre, 2, ',', '.');
                $dataRow[] = WriterEntityFactory::createCell($formattedValue, $FormatSayiVirgul2);
                continue;
            }
    
            if($column->VeriTipi == "FormatSayi::nokta2") {
                $dataRow[] = WriterEntityFactory::createCell((float) $Hucre, $FormatSayiNokta2);
                continue;
            }
        
            if(empty($Hucre) OR is_null($Hucre)){
                $Hucre = "";
            }

            $dataRow[] = WriterEntityFactory::createCell($Hucre);
        }
        
        $currentRowStyle = $isWhiteRow ? $whiteRowStyle : $coloredRowStyle;
        $writer->addRow(WriterEntityFactory::createRow($dataRow, $currentRowStyle));
        $isWhiteRow = !$isWhiteRow;
    }
    
    $writer->close();
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$ExcelDosyaAdi.xlsx\"");
    header('Cache-Control: max-age=0');
    readfile($ExcelDosyaAdi . '.xlsx');
    unlink($ExcelDosyaAdi . '.xlsx');
    ?>