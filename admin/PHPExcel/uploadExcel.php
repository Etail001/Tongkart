<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once "PHPExcel/Classes/PHPExcel.php";
if (!empty($_FILES)) 
{  
      
	 //$count = count($_FILES['file']['name']);
	$headers=array();
	$data=array();

	if (count($_FILES['file']['name']) >= 1) 
        {
         $sheetcount = count($_FILES['file']['name']);
        for ($j = 0; $j < $sheetcount; $j++) 
        {
		$tmpfname = $_FILES['file']['tmp_name'][$j];
	 $inputFileType = PHPExcel_IOFactory::identify($tmpfname);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$excelObj = $objReader->load($tmpfname);
		$worksheet = $excelObj->setActiveSheetIndex(3);
		 $rowCount = 1; 

		foreach ($worksheet->getRowIterator() as $row) {
 
	 
    foreach ($row->getCellIterator() as $cell) {
       $cellValue = trim($cell->getCalculatedValue());
	   	if($j===1)
                    {
	     if ($rowCount > 0 && $rowCount < 4)
		 {
		$header[$rowCount][]=$cellValue;
		 }
					}
		  if ($rowCount > 3)
			{
                            $data[$j][$rowCount][]= $cellValue;
							
              
			}
		
    }
		$rowCount++;
	
	//echo $rowCount;
		
}
}
		
}
			//echo '<pre>';
		//print_r($header); die;
		if(count($data) > 1 )
        {
  
    	//$writer = WriterFactory::create(Type::CSV);
        //$writer->setShouldUseInlineStrings(true);
		 $fileName = time() . '.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $fileName);
        
        
        foreach ($header as  $value) {
			
                 fputcsv($fp, $value);//exit;
			
		}
           foreach ($data as  $vrow) {
                foreach ($vrow as  $rvrow) {
           // print_r($rvrow); exit;
                 fputcsv($fp, $rvrow);//exit;
                }
            }
     // print_r($rows);
      
      
    }
}

?>

