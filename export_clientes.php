<?php
	require('config.php'); 
	$filename = "registro_" . time() . ".csv";
	$delimiter = ';';
	$fields = array('Cliente', 'Ativo?');
	$f = fopen('php://memory', 'w');
	fputcsv($f, $fields, $delimiter);
	foreach ($client->export() as $row) {
		 $lineData = array( 
		 					utf8_decode($row['cli_name']),
							$row['cli_active']
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