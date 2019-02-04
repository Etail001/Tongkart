<?php

//print_r( $_SERVER);
//ini_set('include_path', ini_get('include_path').';../Classes/');
//include('PHPExcel.php');
//include('PHPExcel/Writer/Excel2007.php');

class handleFeedResponse {

    public $responseExcel = 'amazon_feed_response.xlsx';
    public $responseExcelName = '';
    public $responseExcelFileSize = 5000000; //50 MB
    public $content = 'prod_test.xml';
    public $transactionID = '';
    public $feedID = '';
    public $status = '';
//    public $HeaderStyleArray = array('font' => array('bold' => true),
//        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
//        'borders' => array(
//            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
//        )
//    );

    public function __construct() {
        $this->responseExcelName = DIR_FS_AMAZON_FEED . $this->responseExcel;
        $this->content = DIR_FS_AMAZON_FEED . $this->content;
    }

    public function writeResponse($feedID = 8401800242, $content = '') {
        //$responseData = file_get_contents($this->content);
        try {
            $data = $this->xmlToArray(new SimpleXMLElement($content));

            if (is_array($data)) {

                $this->feedID = $feedID;
                try {
                    $this->transactionID = $data['children']['Message'][0]['children']['ProcessingReport'][0]['children']['DocumentTransactionID'][0]['value'];
                    $this->status = $data['children']['Message'][0]['children']['ProcessingReport'][0]['children']['StatusCode'][0]['value'];
                    $responseInfo = $data['children']['Message'][0]['children']['ProcessingReport'][0]['children']['Result'];
//                    if (is_array($responseInfo) && count($responseInfo) > 0) {
//                        $objPHPExcel = new PHPExcel();
//                        $lastRow = 2;
//                        if (file_exists($this->responseExcelName)) {
//                            $objPHPExcel = PHPExcel_IOFactory::load($this->responseExcelName);
//                            $lastRow = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
//                            $lastRow += 1;
//                        } else {
//                            $objPHPExcel->setActiveSheetIndex(0);
//                            $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($this->HeaderStyleArray);
//                            foreach (range('A', 'G') as $columnID) {
//                                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setWidth(20);
//                            }
//                            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
//                            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Feed Id');
//                            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Transaction Id');
//                            $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Date Upload');
//                            $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Feed Status');
//                            $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Result Status');
//                            $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Result Code');
//                            $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Barcode');
//                            $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Description');
//                        }
//                        $date_upload = date('m/d/Y', time());
//                        foreach ($responseInfo as $result) {
//                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $lastRow, $this->feedID);
//                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $lastRow, $this->transactionID);
//                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $lastRow, $date_upload);
//                            $objPHPExcel->getActiveSheet()->setCellValue('D' . $lastRow, $this->status);
//                            $objPHPExcel->getActiveSheet()->setCellValue('E' . $lastRow, $result['children']['ResultCode'][0]['value']);
//                            $objPHPExcel->getActiveSheet()->setCellValue('F' . $lastRow, $result['children']['ResultMessageCode'][0]['value']);
//                            if (isset($result['children']['AdditionalInfo'][0]['children']['SKU'])) {
//                                $objPHPExcel->getActiveSheet()->setCellValue('G' . $lastRow, $result['children']['AdditionalInfo'][0]['children']['SKU'][0]['value']);
//                            } else {
//                                $objPHPExcel->getActiveSheet()->setCellValue('G' . $lastRow, 'NA');
//                            }
//                            $objPHPExcel->getActiveSheet()->setCellValue('H' . $lastRow, $result['children']['ResultDescription'][0]['value']);
//                            $lastRow++;
//                        }
//                        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//                        $objWriter->save($this->responseExcelName);
//                    }
                } catch (Exception $e) {
                    //write code to handle error
                    //echo $e->getMessage();
                }
            }
            //echo '<pre>'; print_r($data); echo '</pre>'; die;
        } catch (Exception $e) {
            
        }

        try {
            if (file_exists($this->responseExcelName)) {
                if (filesize($this->responseExcelName) > $this->responseExcelFileSize) {
                    $file_name_part = explode('.', $this->responseExcelName);
                    array_pop($file_name_part);
                    if (extension_loaded('zip')) {
                        $zip = new ZipArchive();
                        $zip_file_name = implode('.', $file_name_part) . '_' . date('d_m_Y_H_i', time()) . '.zip';
                        if ($zip->open($zip_file_name, ZIPARCHIVE::CREATE) === TRUE) {
                            $zip->addFile($this->responseExcelName, $this->responseExcel);
                        }
                        $zip->close();
                        unlink($this->responseExcelName);
                    }
                }
            }
        } catch (Exception $e) {
            
        }
    }

    public function xmlToArray($xml) {
        $arXML = array();
        $arXML['name'] = trim($xml->getName());
        $arXML['value'] = trim((string) $xml);
        $t = array();
        foreach ($xml->attributes() as $name => $value) {
            $t[$name] = trim($value);
        }
        $arXML['attr'] = $t;
        $t = array();
        foreach ($xml->children() as $name => $xmlchild) {
            $t[$name][] = $this->xmlToArray($xmlchild); //FIX : For multivalued node
        }
        $arXML['children'] = $t;
        return($arXML);
    }

}

//$ts = new handleFeedResponse();
//$ts->writeResponse();
?>