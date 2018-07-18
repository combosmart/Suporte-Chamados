<?php

	/**
    * Function to Make Empty String NULL for MySQL Insertion in PHP
    * @param string $val input string.
    * @return string value.
    */
	function valOrNull($val) {
    	return (trim($val) === '') ? NULL : $val;
	}

	/**
    * Email the confirmation code function
    * @param string $email User email.
    * @return boolean of success.
    */
    function sendConfirmationEmail($email){
        
        $headers  = "From: " . strip_tags(SITEEMAIL) . "\r\n";
		$headers .= "Reply-To: ". strip_tags(SITEEMAIL) . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset='UTF-8'\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion();
        
        $emailsender = '-r suporte@combovideos.com.br';

        if(mail($email->address, 
        		$email->subject, 
        		$email->message, 
        		$headers,
        		$emailsender)) {
            return true;
        } else {
            return false;
        }
    }	

	function exportXls($records) {
		$heading = false;
			if(!empty($records))
			  foreach($records as $row) {
				if(!$heading) {
				  // display field/column names as a first row
				  echo implode("\t", array_keys($row)) . "\n";
				  $heading = true;
				}
				echo implode("\t", array_values($row)) . "\n";
			  }
			exit;
	}

	function validaData($dat) {

		$data = explode("/","$dat");
		$d = $data[0];
		$m = $data[1];
		$y = $data[2];

		return checkdate($m,$d,$y);
		
	}

	/**
    * Função que gera uma senha aleatória de dez posições para o usuário    
    * @return string senha gerada.
    */
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

	/**
    * Função que atribui rótulo de 'ativo' e 'inativo' de acordo com o 
    * valor da coluna 'active' na tabela 
    * @param  string $flag valor da coluna 'active' na tabela.
    * @return string label formatado.
    */
    function label_active($flag) {
        switch ($flag) {
            case 0:
                $label = "<span class='label label-danger'>Inativo</span>";
                break;
            case 1:
                $label = '<span class="label label-success">Ativo</span>';
                break;
            default:
                break;
        }
        
        return $label;
    }    	

    /**
    * Função que atribui rótulo de 'ativo' e 'inativo' de acordo com o 
    * valor da coluna 'active' na tabela 
    * @param  string $flag valor da coluna 'active' na tabela.
    * @return string label formatado.
    */
    function label_active_txt($flag) {
        switch ($flag) {
            case 0:
                $label = "Inativo";
                break;
            case 1:
                $label = 'Ativo';
                break;
            default:
                break;
        }
        
        return $label;
    }    	

    /**
    * Função que atribui rótulo de 'sim' e 'não' de acordo com o 
    * valor da coluna 'active' na tabela 
    * @param  string $flag valor da coluna 'active' na tabela.
    * @return string label formatado.
    */
    function label_yesno_txt($flag) {
        switch ($flag) {
            case 0:
                $label = "Não";
                break;
            case 1:
                $label = 'Sim';
                break;
            default:
                break;
        }
        
        return $label;
    }    	
	
	function lista_meses() {
		$meses = array 
			(
				array("01","Jan"),
				array("02","Fev"),
				array("03","Mar"),
				array("04","Abr"),
				array("05","Mai"),																
				array("06","Jun"),				
				array("07","Jul"),		
				array("08","Ago"),	
				array("09","Set"),	
				array("10","Out"),	
				array("11","Nov"),
				array("12","Dez")																			
			);
		return $meses;	
	}
	
	function mesParaTexto($mes) {
		switch($mes) {
			case 01:
				$msg = "JAN";
				break;
			case 02:
				$msg = "FEV";
				break;	
			case 03:
				$msg = "MAR";
				break;	
			case 04:
				$msg = "ABR";
				break;	
			case 05:
				$msg = "MAI";
				break;	
			case 06:
				$msg = "JUN";
				break;	
			case 07:
				$msg = "JUL";
				break;	
			case 08:
				$msg = "AGO";
				break;	
			case 09:
				$msg = "SET";
				break;	
			case 10:
				$msg = "OUT";
				break;	
			case 11:
				$msg = "NOV";
				break;	
			case 12:
				$msg = "DEZ";
				break;	
			default:
				break;	
		}
		
		return $msg;
	}
	
	function formataMoedaMysql($valorbase){
		$valor = '';
		$tam = strlen($valorbase);
		for ($i = 0; $i < $tam; $i++) {
			$car = $valorbase{$i};
			if($car == ','){
				$valor = $valor . ".";
			}else{
				if($car <> '.'){
					$valor = $valor . $car;
				}
			}	
		}
	
		return $valor;	
	}
	
	function formataMoedaView($valorbase){
		$valor = '';
		$tam = strlen($valorbase);
		for ($i = 0; $i < $tam; $i++) {
			$car = $valorbase{$i};
			if($car == '.'){
				$valor = $valor . ",";
			}else{
				if($car <> ','){
					$valor = $valor . $car;
				}
			}
		}
	
		return $valor;
	}	
	
	function limpaCPF_CNPJ($valor) {
		$valor = trim($valor);
		$valor = str_replace(".", "", $valor);
		$valor = str_replace(",", "", $valor);
		$valor = str_replace("-", "", $valor);
		$valor = str_replace("/", "", $valor);
		return $valor;
	}
	
	function mod($dividendo,$divisor) {
		return round($dividendo - (floor($dividendo/$divisor)*$divisor));
	}
	
	function geraCPF($compontos) {
		$n1 = rand(0,9);
		$n2 = rand(0,9);
		$n3 = rand(0,9);
		$n4 = rand(0,9);
		$n5 = rand(0,9);
		$n6 = rand(0,9);
		$n7 = rand(0,9);
		$n8 = rand(0,9);
		$n9 = rand(0,9);
		$d1 = $n9*2+$n8*3+$n7*4+$n6*5+$n5*6+$n4*7+$n3*8+$n2*9+$n1*10;
		$d1 = 11 - ( mod($d1,11) );
		if ( $d1 >= 10 ) { 
			$d1 = 0 ;
		}
		$d2 = $d1*2+$n9*3+$n8*4+$n7*5+$n6*6+$n5*7+$n4*8+$n3*9+$n2*10+$n1*11;
		$d2 = 11 - ( mod($d2,11) );
		
		if ($d2>=10) { 
			$d2 = 0 ;
		}
		
		$retorno = '';
		
		if ($compontos==1) {
			$retorno = ''.$n1.$n2.$n3.".".$n4.$n5.$n6.".".$n7.$n8.$n9."-".$d1.$d2;
		} else { 
			$retorno = ''.$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$d1.$d2; 
		}
		
		return $retorno;
	}
	
	function calculaIdade($dataNascimento) {
		// Separa em dia, mês e ano
    	list($dia, $mes, $ano) = explode('/', $dataNascimento);
    	// Descobre que dia é hoje e retorna a unix timestamp
    	$hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    	// Descobre a unix timestamp da data de nascimento do fulano
    	$nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
    	// Depois apenas fazemos o cálculo já citado :)
    	$idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
    	return $idade;
	}
	
    function formataData($mySqlDate){
        list($ano, $mes, $dia) = explode("-",$mySqlDate);
        return $dia .'/'. $mes .'/'. $ano;
    }
    
       
    function formataDataMySQL($formDate){
        list($dia,$mes,$ano) = explode("/",$formDate);
        return $ano .'-'. $mes .'-'. $dia;
    }
    
    function formataDataDatePicker($formDate){
        list($dia,$mes,$ano) = explode("/",$formDate);
        return $ano .'-'. $mes .'-'. $dia;
    }
    
    function formataCPF($nbr_cpf) {

		$parte_um     = substr($nbr_cpf, 0, 3);
		$parte_dois   = substr($nbr_cpf, 3, 3);
		$parte_tres   = substr($nbr_cpf, 6, 3);
		$parte_quatro = substr($nbr_cpf, 9, 2);
		
		$monta_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
		
		return $monta_cpf;
    }
	
	function validaCPF($cpf = null) {
        // Verifica se um número foi informado
        if(empty($cpf)) {
            return false;
        }
        // Elimina possivel mascara
        //$cpf = ereg_replace('[^0-9]', '', $cpf);
        //$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999') {
            return false;
         // Calcula os digitos verificadores para verificar se o
         // CPF é válido
         } else {  
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
            return true;
        }
    }

    function greetings() {
    	$ip = $_SERVER['REMOTE_ADDR'];
		$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
		//return $details->city;
		return $details->city . utf8_encode(strftime(', %A, %d de %B de %Y', strtotime('today')));
    }

    function aberto_fechado($flag) {
        switch ($flag) {
            case "F":
                $label = "<span class='label label-danger'>Fechado</span>";
                break;
            case "A":
                $label = "<span class='label label-success'>Aberto</span>";
                break;
            default:
                break;
        }
        
        return $label;
    }  

	function limit_text($text, $limit) {
      if (str_word_count($text, 0) > $limit) {
          $words = str_word_count($text, 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]) . '<i>[mais...]</i>';
      }
      return $text;
    }
	
	function test_url($url){
       $agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";$ch=curl_init();
       curl_setopt ($ch, CURLOPT_URL,$url );
       curl_setopt($ch, CURLOPT_USERAGENT, $agent);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ($ch,CURLOPT_VERBOSE,false);
       curl_setopt($ch, CURLOPT_TIMEOUT, 5);
       curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
       curl_setopt($ch,CURLOPT_SSLVERSION,3);
       curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
       $page=curl_exec($ch);
       //echo curl_error($ch);
       $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
       curl_close($ch);
       if($httpcode>=200 && $httpcode<300) return true;
       else return false;
	}

	/* Função para mascarar qualquer valor */
	/* Usage: mask($cnpj,'##.###.###/####-##'); */
	function mask($val, $mask) {
	 $maskared = '';
	 $k = 0;
	 for($i = 0; $i<=strlen($mask)-1; $i++) {
	 	if($mask[$i] == '#') {
			if(isset($val[$k]))
		 	$maskared .= $val[$k++];
	 	}
	 	else {
			if(isset($mask[$i]))
		 	$maskared .= $mask[$i];
	 	}
	 }
	 
	 return $maskared;
	}
?>
