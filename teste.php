<?php
	$pass = 'combo123@#';
  	echo password_hash($pass, PASSWORD_DEFAULT) . "<br/><br/>";

	function randomPassword() {
	    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 11; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}

	echo randomPassword() . "<br/><br/>";

	if (password_verify($pass, '$2y$10$Vcn0Q/GGxn/8qR1wdsvX1undZYBUNkaANInOls2rPaz.ttnwQSxdS')) {
	    echo 'Carlos Password is valid!';
	} else {
	    echo 'Carlos Invalid password.';
	}

	echo "<br/><br/>";

	$player = new ArrayObject();
	$player->nome = 'Nome de Teste';
	$player->endereco = 'Endereço de teste';
	$player->idade = 53;
	$player->emails = array("foo", "bar", "hello", "world");;

	echo $player->nome;
	echo "<br/>";
	echo $player->endereco;
	echo "<br/>";
	echo $player->idade;
	echo "<br/>";
	print_r($player->emails);
	echo "<br/>";
	echo "<br/>";


	$subject = 'Confirm your registration';
    $message = 'Please confirm you registration by pasting this code in the confirmation box:';
    $headers = 'X-Mailer: PHP/' . phpversion();

	if(mail('felipe@combovideos.com.br', $subject, $message, $headers,'-r suporte@combovideos.com.br')) {
		echo "email enviado";
		echo "<br/>";		
	} else {
		echo "email não foi enviado";
		echo "<br/>";		
	}
?>
