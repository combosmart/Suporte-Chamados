<?php
	require('config.php'); 
	$filename = "registro_" . time() . ".csv";
	$delimiter = ';';
	$fields = array('Cliente', 'Ponto', 'Tipo', 'Num.Serie', 'ContentManager', 'Team Viewer', 'Configuracao', 'Ativo?');
	$f = fopen('php://memory', 'w');
	fputcsv($f, $fields, $delimiter);
	foreach ($machine->export() as $row) {
		 $lineData = array( 
		 					utf8_decode($row['cli_name']),
							utf8_decode($row['pnt_name']),							
							utf8_decode($row['typ_name']),		 					
							$row['mch_sku'],
							$row['mch_cm_name'],
							$row['mch_tv_name'],		
							utf8_decode($row['mch_config']),							
							utf8_decode($row['mch_active'])					
		 				);
		 fputcsv($f, $lineData, $delimiter);			 
	}

	//move back to beginning of file
	fseek($f, 0);

	//set headers to download file rather than displayed
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename="' . $filename . '";');

	//output all remaining data on a file pointer
	fpassthru($f);	    
	exit;