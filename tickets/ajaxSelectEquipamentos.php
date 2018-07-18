<?php
	require('../config.php'); 	
	$id = $_GET['q'];
	$mode = $_GET['mode'];
	
	if ($mode === 'equip') {
		$select  = '<label class="control-label text-lg-left pt-2">Equipamento</label>';
		$select .= '<select class="form-control mb-3" name="mch_id">';
		foreach ($machine->selectMachinesByPlayer($id) as $k) {
			$select .= '<option value="'. $k['mch_id'] . '">'. $k['mch_sku'] .'</option>';
		}
		$select .= '</select>';
	} else if ($mode === 'prioridade') {
		$obj = $machine->getPrioridade($id);
		$select  = '<label class="control-label text-lg-left pt-2">Prioridade</label>';
		$select .= '<div class="alert alert-default">';
		$select .= $obj['pri_name'];
		$select .= '</div>';
	}
	

	echo $select;
?>
