<?php
	require('config.php'); 
	$filename = "registro_" . time() . ".csv";
	$delimiter = ';';
	$fields = array('Cliente', 'Nome do Ponto', 'Observacao', 'Endereco', 'Numero', 'Bairro', 'Cidade', 'Estado', 'CEP', 'Ativo?');
	$f = fopen('php://memory', 'w');
	fputcsv($f, $fields, $delimiter);
	foreach ($player->export() as $row) {
		 $lineData = array( 
		 					utf8_decode($row['cli_name']),
							utf8_decode($row['pnt_name']),
							utf8_decode($row['pnt_notes']),
							utf8_decode($row['pnt_address']),
							$row['pnt_number'],
							utf8_decode($row['pnt_neighbor']),
							utf8_decode($row['pnt_city']),
							$row['pnt_state'],
							$row['pnt_zip'],
							$row['pnt_active']
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